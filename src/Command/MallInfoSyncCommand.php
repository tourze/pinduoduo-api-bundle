<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
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
#[WithMonologChannel(channel: 'pinduoduo_api')]
final class MallInfoSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-mall-info-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly PinduoduoClient $pinduoduoClient,
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
        $mallIdArg = $input->getArgument('mallId');
        if (null !== $mallIdArg && is_string($mallIdArg)) {
            $malls = $this->mallRepository->findBy([
                'id' => explode(',', $mallIdArg),
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

    /**
     * @internal
     */
    public function syncInfo(Mall $mall): void
    {
        $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.mall.info.get');
        $this->logger->info('同步PDD店铺信息', [
            'response' => $response,
            'mall' => $mall,
        ]);

        $mallInfo = $response['mall_info_get_response'] ?? [];
        assert(is_array($mallInfo));

        $mallName = $mallInfo['mall_name'] ?? null;
        if (is_string($mallName) && '' !== $mallName) {
            $mall->setName($mallName);
        }
        $mall->setDescription(is_string($mallInfo['mall_desc'] ?? null) ? $mallInfo['mall_desc'] : null);
        $mall->setLogo(is_string($mallInfo['logo'] ?? null) ? $mallInfo['logo'] : null);

        $merchantType = $mallInfo['merchant_type'] ?? null;
        $mall->setMerchantType((is_int($merchantType) || is_string($merchantType)) ? MerchantType::tryFrom($merchantType) : null);

        $mallCharacter = $mallInfo['mall_character'] ?? null;
        $mall->setMallCharacter((is_int($mallCharacter) || is_string($mallCharacter)) ? MallCharacter::tryFrom($mallCharacter) : null);

        $this->entityManager->persist($mall);
        $this->entityManager->flush();
    }
}
