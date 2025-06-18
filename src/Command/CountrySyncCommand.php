<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Country;
use PinduoduoApiBundle\Repository\CountryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.country.get
 */
#[AsCronTask('20 */2 * * *')]
#[AsCommand(name: CountrySyncCommand::NAME, description: '同步商品地区/国家')]
class CountrySyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-country-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly CountryRepository $countryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->mallRepository->findAll() as $mall) {
            try {
                $response = $this->sdkService->request($mall, 'pdd.goods.country.get');
            } catch (\Throwable $exception) {
                $output->writeln("同步出错：{$exception}");
                continue;
            }
            if (!isset($response['goods_country_get_response'])) {
                continue;
            }
            dump($response['goods_country_get_response']);

            foreach ($response['goods_country_get_response']['country_list'] as $item) {
                $country = $this->countryRepository->find($item['country_id']);
                if ($country === null) {
                    $country = new Country();
                    $country->setId($item['country_id']);
                }
                $country->setName($item['country_name']);

                $this->entityManager->persist($country);
                $this->entityManager->flush();
                $this->entityManager->detach($country);
            }
        }

        return Command::SUCCESS;
    }
}
