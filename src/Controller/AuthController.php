<?php

namespace PinduoduoApiBundle\Controller;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Command\MallInfoSyncCommand;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\Symfony\Async\Message\RunCommandMessage;
use WeuiBundle\Service\NoticeService;

#[Route('/pinduoduo/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SdkService $sdkService,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly MallRepository $mallRepository,
        private readonly AuthLogRepository $authLogRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly NoticeService $noticeService,
    ) {
    }

    #[Route('/redirect/{id}', name: 'pinduoduo-auth-redirect')]
    public function actionRedirect(string $id, Request $request): Response
    {
        $account = $this->accountRepository->findOneBy([
            'id' => $id,
        ]);
        if (!$account) {
            throw new NotFoundHttpException('找不到应用');
        }

        $sdk = $this->sdkService->getMerchantSdk($account);
        $url = $sdk->pre_auth->authorizationUrl();
        $response = $this->redirect($url);

        // 需要保存当时回调的地址
        if ($request->query->has('callbackUrl')) {
            $request->getSession()->set('callbackUrl', $request->query->get('callbackUrl'));
        } else {
            $request->getSession()->remove('callbackUrl');
        }

        return $response;
    }

    #[Route('/callback/{id}', name: 'pinduoduo-auth-callback')]
    public function actionCallback(string $id, Request $request): Response
    {
        $account = $this->accountRepository->findOneBy([
            'id' => $id,
        ]);
        if (!$account) {
            throw new NotFoundHttpException('找不到应用');
        }

        $code = $request->query->get('code');
        if (empty($code)) {
            throw new NotFoundHttpException('找不到回调code');
        }

        $sdk = $this->sdkService->getMerchantSdk($account);
        $token = $sdk->pre_auth->getAccessToken($code);
        // 也可以通过上面得到的 refresh_token 去刷新令牌
        // $token = $pinduoduo->pre_auth->refreshToken($token['refresh_token']);
        $this->logger->info('获得拼多多授权回调', [
            'code' => $code,
            'token' => $token,
            'account' => $account,
        ]);
        if (!isset($token['access_token'])) {
            return $this->noticeService->weuiError($token['error_response']['error_msg'], $token['error_response']['error_code']);
        }

        // 创建授权店铺的基础信息
        $mall = $this->mallRepository->find(intval($token['owner_id']));
        if (!$mall) {
            $mall = new Mall();
            $mall->setId($token['owner_id']);
            $mall->setName($token['owner_name']);
        }
        $this->entityManager->persist($mall);
        $this->entityManager->flush();

        // 创建授权关系记录
        $authLog = $this->authLogRepository->findOneBy([
            'account' => $account,
            'mall' => $mall,
        ]);
        if (!$authLog) {
            $authLog = new AuthLog();
            $authLog->setAccount($account);
            $authLog->setMall($mall);
        }
        $authLog->setScope($token['scope'] ?? null);
        $authLog->setAccessToken($token['access_token']);
        $authLog->setRefreshToken($token['refresh_token']);
        $authLog->setTokenExpireTime(Carbon::now()->addSeconds($token['expires_in']));
        $authLog->setContext($token);
        $this->entityManager->persist($authLog);
        $this->entityManager->flush();

        // 最后尝试主动同步一次
        $message = new RunCommandMessage();
        $message->setCommand(MallInfoSyncCommand::NAME);
        $message->setOptions([
            'mallId' => $mall->getId(),
        ]);
        $this->messageBus->dispatch($message);

        if ($request->getSession()->has('callbackUrl')) {
            return $this->redirect($request->getSession()->get('callbackUrl'));
        }

        return $this->noticeService->weuiSuccess('授权成功');
    }
}
