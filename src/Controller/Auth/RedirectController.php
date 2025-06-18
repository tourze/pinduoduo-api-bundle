<?php

namespace PinduoduoApiBundle\Controller\Auth;

use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Service\SdkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class RedirectController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SdkService $sdkService,
    ) {
    }

    #[Route('/pinduoduo/auth/redirect/{id}', name: 'pinduoduo-auth-redirect')]
    public function __invoke(string $id, Request $request): Response
    {
        $account = $this->accountRepository->findOneBy([
            'id' => $id,
        ]);
        if ($account === null) {
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
}