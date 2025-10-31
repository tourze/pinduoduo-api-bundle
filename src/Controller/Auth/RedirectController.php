<?php

namespace PinduoduoApiBundle\Controller\Auth;

use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class RedirectController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    #[Route(path: '/pinduoduo/auth/redirect/{id}', name: 'pinduoduo-auth-redirect')]
    public function __invoke(string $id, Request $request): Response
    {
        $account = $this->accountRepository->findOneBy([
            'id' => $id,
        ]);
        if (!$account instanceof Account) {
            throw new NotFoundHttpException('找不到应用');
        }

        $url = $this->pinduoduoClient->generateAuthUrl($account);
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
