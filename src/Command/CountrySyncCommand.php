<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Country;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\CountryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.country.get
 */
#[AsCronTask(expression: '20 */2 * * *')]
#[AsCommand(name: self::NAME, description: '同步商品地区/国家')]
class CountrySyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-country-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly CountryRepository $countryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $malls = $this->mallRepository->findAll();
        foreach ($malls as $mall) {
            $this->syncCountriesForMall($mall, $output);
        }

        return Command::SUCCESS;
    }

    private function syncCountriesForMall(Mall $mall, OutputInterface $output): void
    {
        $response = $this->fetchCountryDataFromApi($mall, $output);
        if (null === $response) {
            return;
        }

        $this->processCountryList($response['country_list']);
    }

    /**
     * @return array{country_list: array<mixed>}|null
     */
    private function fetchCountryDataFromApi(Mall $mall, OutputInterface $output): ?array
    {
        try {
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.country.get');
        } catch (\Throwable $exception) {
            $output->writeln("同步出错：{$exception}");

            return null;
        }

        if (!isset($response['country_list']) || !is_array($response['country_list'])) {
            return null;
        }

        return $response;
    }

    /**
     * @param array<mixed> $countryList
     */
    private function processCountryList(array $countryList): void
    {
        foreach ($countryList as $item) {
            if (!$this->isValidCountryItem($item)) {
                continue;
            }
            assert(is_array($item));

            /** @var array<string, mixed> $validItem */
            $validItem = $item;
            $this->saveCountryItem($validItem);
        }
    }

    private function isValidCountryItem(mixed $item): bool
    {
        return is_array($item) && isset($item['country_id'], $item['country_name']);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function saveCountryItem(array $item): void
    {
        $countryId = is_string($item['country_id']) ? $item['country_id'] : null;
        $countryName = is_string($item['country_name']) ? $item['country_name'] : '';

        $country = $this->findOrCreateCountry($countryId);
        $country->setName($countryName);

        $this->entityManager->persist($country);
        $this->entityManager->flush();
        $this->entityManager->detach($country);
    }

    private function findOrCreateCountry(?string $countryId): Country
    {
        $country = $this->countryRepository->find($countryId);
        if (null === $country) {
            $country = new Country();
            $country->setId($countryId);
        }

        return $country;
    }
}
