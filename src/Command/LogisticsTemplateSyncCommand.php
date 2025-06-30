<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Enum\CostType;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
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
        private readonly SdkService $sdkService,
        private readonly LogisticsTemplateRepository $templateRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->mallRepository->findAll() as $mall) {
            // 推广优化、打单、进销存、商品优化分析、搬家上货、虚拟商家后台系统、企业ERP、商家后台系统、订单处理、电子凭证商家后台系统、跨境企业ERP报关版
            $sdk = $this->sdkService->getMallSdk($mall, ApplicationType::搬家上货);
            if ($sdk === null) {
                continue;
            }

            $page = 1;
            while (true) {
                $response = $sdk->auth_api->request('pdd.goods.logistics.template.get', [
                    'page' => $page,
                ]);
                if (!isset($response['goods_logistics_template_get_response'])) {
                    break;
                }
                if (empty($response['goods_logistics_template_get_response']['logistics_template_list'])) {
                    break;
                }
                // dump($response);

                foreach ($response['goods_logistics_template_get_response']['logistics_template_list'] as $item) {
                    $template = $this->templateRepository->findOneBy([
                        'mall' => $mall,
                        'id' => $item['template_id'],
                    ]);
                    if ($template === null) {
                        $template = new LogisticsTemplate();
                        $template->setMall($mall);
                    }
                    $template->setUpdateTime(\DateTimeImmutable::createFromFormat('U', (string) $item['last_updated_time']));
                    $template->setCostType(CostType::tryFrom($item['cost_type']));
                    $template->setName($item['template_name']);
                    $this->entityManager->persist($template);
                    $this->entityManager->flush();
                }
                ++$page;
            }
        }

        return Command::SUCCESS;
    }
}
