<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.mall.info.get
 */
#[AsCronTask(expression: '*/30 */2 * * *')]
#[AsCommand(name: self::NAME, description: '同步店铺信息')]
class MallInfoSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-mall-info-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SdkService $sdkService,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mallId', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('mallId') !== null) {
            $malls = $this->mallRepository->findBy([
                'id' => explode(',', $input->getArgument('mallId')),
            ]);
        } else {
            $malls = $this->mallRepository->findAll();
        }

        foreach ($malls as $mall) {
            try {
                $this->syncInfo($mall);
            } catch (\Throwable $exception) {
                $output->writeln('同步PDD店铺信息出错:' . $exception);
                $this->logger->error('同步PDD店铺信息出错', [
                    'exception' => $exception,
                ]);
            }
        }

        return Command::SUCCESS;
    }

    public function syncInfo(Mall $mall): void
    {
        $response = $this->sdkService->request($mall, 'pdd.mall.info.get');
        $this->logger->info('同步PDD店铺信息', [
            'response' => $response,
            'mall' => $mall,
        ]);
        $response = $response['mall_info_get_response'];
        $mall->setName($response['mall_name']);
        $mall->setDescription($response['mall_desc']);
        $mall->setLogo($response['logo']);
        $mall->setMerchantType(MerchantType::tryFrom($response['merchant_type']));
        $mall->setMallCharacter(MallCharacter::tryFrom($response['mall_character']));
        $this->entityManager->persist($mall);
        $this->entityManager->flush();
    }
}
