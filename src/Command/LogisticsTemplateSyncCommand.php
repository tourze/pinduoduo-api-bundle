<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\CostType;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.logistics.template.get
 */
#[AsCronTask(expression: '*/15 * * * *')]
#[AsCommand(name: self::NAME, description: '同步运费模板')]
class LogisticsTemplateSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-logistics-template-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly LogisticsTemplateRepository $templateRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Mall $mall */
        foreach ($this->mallRepository->findAll() as $mall) {
            $this->syncLogisticsTemplatesForMall($mall);
        }

        return Command::SUCCESS;
    }

    private function syncLogisticsTemplatesForMall(Mall $mall): void
    {
        $page = 1;
        while (true) {
            $response = $this->fetchLogisticsTemplatePage($mall, $page);
            if (null === $response) {
                break;
            }

            $this->processLogisticsTemplateList($mall, $response);
            ++$page;
        }
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchLogisticsTemplatePage(Mall $mall, int $page): ?array
    {
        try {
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.logistics.template.get', [
                'page' => $page,
            ]);

            if (!isset($response['logistics_template_list']) || [] === $response['logistics_template_list']) {
                return null;
            }

            $templateList = $response['logistics_template_list'];
            assert(is_array($templateList));

            return $templateList;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array<mixed> $templateList
     */
    private function processLogisticsTemplateList(Mall $mall, array $templateList): void
    {
        foreach ($templateList as $item) {
            if (!is_array($item)) {
                continue;
            }
            $this->processLogisticsTemplateItem($mall, $item);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function processLogisticsTemplateItem(Mall $mall, array $item): void
    {
        $template = $this->findOrCreateTemplate($mall, $item);
        $this->updateTemplateData($template, $item);

        $this->entityManager->persist($template);
        $this->entityManager->flush();
    }

    /**
     * @param array<mixed> $item
     */
    private function findOrCreateTemplate(Mall $mall, array $item): LogisticsTemplate
    {
        $template = $this->templateRepository->findOneBy([
            'mall' => $mall,
            'id' => $item['template_id'],
        ]);

        if (null === $template) {
            $template = new LogisticsTemplate();
            $template->setMall($mall);
        }

        return $template;
    }

    /**
     * @param array<mixed> $item
     */
    private function updateTemplateData(LogisticsTemplate $template, array $item): void
    {
        $this->updateTemplateUpdateTime($template, $item);
        $this->updateTemplateCostType($template, $item);
        $this->updateTemplateName($template, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateTemplateUpdateTime(LogisticsTemplate $template, array $item): void
    {
        if (!isset($item['last_updated_time'])) {
            return;
        }

        $lastUpdatedTime = $item['last_updated_time'];
        if (!is_int($lastUpdatedTime) && !is_string($lastUpdatedTime)) {
            return;
        }

        $updateTime = \DateTimeImmutable::createFromFormat('U', (string) $lastUpdatedTime);
        $template->setUpdateTime(false !== $updateTime ? $updateTime : null);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateTemplateCostType(LogisticsTemplate $template, array $item): void
    {
        if (!isset($item['cost_type'])) {
            return;
        }

        $costTypeValue = $item['cost_type'];
        if (!is_int($costTypeValue) && !is_string($costTypeValue)) {
            return;
        }

        $costType = CostType::tryFrom($costTypeValue);
        if (null !== $costType) {
            $template->setCostType($costType);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function updateTemplateName(LogisticsTemplate $template, array $item): void
    {
        if (isset($item['template_name']) && is_string($item['template_name'])) {
            $template->setName($item['template_name']);
        }
    }
}
