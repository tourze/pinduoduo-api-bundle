<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Measurement;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Repository\Goods\MeasurementRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.gooods.sku.measurement.list
 */
#[AsCronTask(expression: '* 7 * * *')]
#[AsCommand(name: self::NAME, description: '同步商品sku计量单位')]
class MeasurementSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-measurement-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SdkService $sdkService,
        private readonly MeasurementRepository $measurementRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->mallRepository->findAll() as $mall) {
            // 进销存、商品优化分析、搬家上货、虚拟商家后台系统、企业ERP、商家后台系统、电子凭证商家后台系统、跨境企业ERP报关版
            $sdk = $this->sdkService->getMallSdk($mall, ApplicationType::搬家上货);
            if ($sdk === null) {
                continue;
            }

            $response = $sdk->auth_api->request('pdd.gooods.sku.measurement.list');
            if (!isset($response['gooods_sku_measurement_list_response'])) {
                continue;
            }

            foreach ($response['gooods_sku_measurement_list_response']['measurement_list'] as $item) {
                $measurement = $this->measurementRepository->findOneBy(['code' => $item['code']]);
                if ($measurement === null) {
                    $measurement = new Measurement();
                    $measurement->setCode($item['code']);
                }
                $measurement->setDescription($item['desc']);
                $this->entityManager->persist($measurement);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
