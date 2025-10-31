<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Measurement;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\MeasurementRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
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
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly MeasurementRepository $measurementRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->mallRepository->findAll() as $mall) {
            $this->syncMeasurementForMall($mall);
        }

        return Command::SUCCESS;
    }

    private function syncMeasurementForMall(Mall $mall): void
    {
        $measurementList = $this->fetchMeasurementList($mall);
        if (null === $measurementList) {
            return;
        }

        $this->processMeasurementList($measurementList);
    }

    /**
     * @param array<mixed> $measurementList
     */
    private function processMeasurementList(array $measurementList): void
    {
        foreach ($measurementList as $item) {
            if (!is_array($item)) {
                continue;
            }
            $this->processMeasurementItem($item);
        }
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchMeasurementList(Mall $mall): ?array
    {
        try {
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.gooods.sku.measurement.list');
        } catch (\Exception $e) {
            return null;
        }

        $measurementList = $response['measurement_list'] ?? null;

        return is_array($measurementList) ? $measurementList : null;
    }

    /**
     * @param array<mixed> $item
     */
    private function processMeasurementItem(array $item): void
    {
        $code = $item['code'] ?? null;
        $measurement = $this->findOrCreateMeasurement($code);

        $desc = $item['desc'] ?? null;
        $measurement->setDescription(is_string($desc) ? $desc : null);

        $this->entityManager->persist($measurement);
        $this->entityManager->flush();
    }

    /**
     * @param mixed $code
     */
    private function findOrCreateMeasurement($code): Measurement
    {
        $measurement = $this->measurementRepository->findOneBy(['code' => $code]);
        if ($measurement instanceof Measurement) {
            return $measurement;
        }

        $measurement = new Measurement();
        if (is_string($code)) {
            $measurement->setCode($code);
        }

        return $measurement;
    }
}
