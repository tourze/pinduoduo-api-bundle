<?php

namespace PinduoduoApiBundle\Command;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\TempFileBundle\Service\TemporaryFileService;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.img.upload
 */
#[AsCommand(name: self::NAME, description: '测试-商品图片上传接口')]
class UploadImageCommand extends LockableCommand
{
    public const NAME = 'pdd:upload-image';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly TemporaryFileService $temporaryFileService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mallId', InputArgument::REQUIRED);
        $this->addArgument('url', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mall = $this->mallRepository->find($input->getArgument('mallId'));
        if ($mall === null) {
            throw new \Exception('找不到授权店铺');
        }

        $localFile = $this->temporaryFileService->generateTemporaryFileName('pdd');
        file_put_contents($localFile, file_get_contents($input->getArgument('url')));
        $output->writeln("localFile: {$localFile}");

        $response = $this->sdkService->request($mall, 'pdd.goods.img.upload', [
            'file' => $localFile,
        ]);
        // array:1 [
        //  "goods_img_upload_response" => array:2 [
        //    "request_id" => "17157431736782793"
        //    "url" => "https://img.pddpic.com/open-gw/2024-05-15/c98022b4-f4ea-4025-9d76-ef1e7f5c119b.jpeg"
        //  ]
        // ]
        dump($response);

        return Command::SUCCESS;
    }
}
