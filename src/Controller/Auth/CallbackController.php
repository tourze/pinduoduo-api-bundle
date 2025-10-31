<?php

namespace PinduoduoApiBundle\Controller\Auth;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Command\MallInfoSyncCommand;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use WeuiBundle\Service\NoticeService;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'pinduoduo_api')]
final class CallbackController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly MallRepository $mallRepository,
        private readonly AuthLogRepository $authLogRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly NoticeService $noticeService,
    ) {
    }

    #[Route(path: '/pinduoduo/auth/callback/{id}', name: 'pinduoduo-auth-callback')]
    public function __invoke(string $id, Request $request): Response
    {
        $account = $this->accountRepository->findOneBy(['id' => $id]);
        if (null === $account) {
            throw new NotFoundHttpException('找不到应用');
        }

        $code = $this->validateAndGetCode($request);
        $token = $this->getAccessTokenSafely($account, $code);
        if ($token instanceof Response) {
            return $token;
        }

        $mall = $this->findOrCreateMall($token);
        $authLog = $this->findOrCreateAuthLog($account, $mall);
        $this->updateAuthLogFromToken($authLog, $token);

        $this->entityManager->flush();

        $this->triggerMallSync($mall);

        return $this->handleCallbackRedirect($request);
    }

    private function validateAndGetCode(Request $request): string
    {
        $code = $request->query->get('code');
        if (null === $code || '' === $code || !is_string($code)) {
            throw new NotFoundHttpException('找不到回调code');
        }

        return $code;
    }

    /**
     * @return array<string, mixed>|Response
     */
    private function getAccessTokenSafely(Account $account, string $code): array|Response
    {
        try {
            $token = $this->pinduoduoClient->getAccessTokenByCode($account, $code);
            $this->logger->info('获得拼多多授权回调', [
                'code' => $code,
                'token' => $token,
                'account' => $account,
            ]);

            return $token;
        } catch (\Exception $e) {
            return $this->noticeService->weuiError($e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $token
     */
    private function findOrCreateMall(array $token): Mall
    {
        $ownerId = $token['owner_id'] ?? null;
        $mall = is_scalar($ownerId) ? $this->mallRepository->find((int) $ownerId) : null;

        if (null !== $mall) {
            return $mall;
        }

        $mall = new Mall();
        $ownerName = $token['owner_name'] ?? null;
        if (is_string($ownerName) && '' !== $ownerName) {
            $mall->setName($ownerName);
        }

        $this->entityManager->persist($mall);
        $this->entityManager->flush();

        return $mall;
    }

    private function findOrCreateAuthLog(Account $account, Mall $mall): AuthLog
    {
        $authLog = $this->authLogRepository->findOneBy([
            'account' => $account,
            'mall' => $mall,
        ]);

        if (null !== $authLog) {
            return $authLog;
        }

        $authLog = new AuthLog();
        $authLog->setAccount($account);
        $authLog->setMall($mall);
        $this->entityManager->persist($authLog);

        return $authLog;
    }

    /**
     * @param array<string, mixed> $token
     */
    private function updateAuthLogFromToken(AuthLog $authLog, array $token): void
    {
        $this->updateAuthLogScope($authLog, $token);
        $this->updateAuthLogTokens($authLog, $token);
        $this->updateAuthLogExpiration($authLog, $token);

        $authLog->setContext($token);
    }

    /**
     * @param array<string, mixed> $token
     */
    private function updateAuthLogScope(AuthLog $authLog, array $token): void
    {
        $scope = $token['scope'] ?? null;
        if (!is_array($scope)) {
            $authLog->setScope(null);

            return;
        }

        $stringScope = array_map(fn (mixed $v): string => is_scalar($v) ? (string) $v : '', $scope);
        $authLog->setScope($stringScope);
    }

    /**
     * @param array<string, mixed> $token
     */
    private function updateAuthLogTokens(AuthLog $authLog, array $token): void
    {
        $accessToken = $token['access_token'] ?? null;
        $authLog->setAccessToken(is_string($accessToken) ? $accessToken : null);

        $refreshToken = $token['refresh_token'] ?? null;
        $authLog->setRefreshToken(is_string($refreshToken) ? $refreshToken : null);
    }

    /**
     * @param array<string, mixed> $token
     */
    private function updateAuthLogExpiration(AuthLog $authLog, array $token): void
    {
        $expiresIn = $token['expires_in'] ?? null;
        if (!is_int($expiresIn) && !is_float($expiresIn)) {
            return;
        }

        $authLog->setTokenExpireTime(CarbonImmutable::now()->addSeconds((int) $expiresIn));
    }

    private function triggerMallSync(Mall $mall): void
    {
        $message = new RunCommandMessage();
        $message->setCommand(MallInfoSyncCommand::NAME);
        $message->setOptions(['mallId' => $mall->getId()]);
        $this->messageBus->dispatch($message);
    }

    private function handleCallbackRedirect(Request $request): Response
    {
        if (!$request->getSession()->has('callbackUrl')) {
            return $this->noticeService->weuiSuccess('授权成功');
        }

        $callbackUrl = $request->getSession()->get('callbackUrl');
        if (is_string($callbackUrl)) {
            return $this->redirect($callbackUrl);
        }

        return $this->noticeService->weuiSuccess('授权成功');
    }
}
