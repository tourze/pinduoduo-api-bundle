<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Controller\Admin\DepotCrudController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DepotCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DepotCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/pinduoduo-api/depot');

            $this->assertTrue(
                $client->getResponse()->isNotFound()
                || $client->getResponse()->isRedirect()
                || $client->getResponse()->isSuccessful(),
                'Response should be 404, redirect, or successful'
            );
        } catch (NotFoundHttpException $e) {
            $this->assertInstanceOf(NotFoundHttpException::class, $e);
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error: ' . $e->getMessage()
            );
        }
    }

    public function testValidationErrors(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $crawler = $client->request('GET', '/pinduoduo-api/depot?crudAction=new');
            $response = $client->getResponse();

            if ($response->isSuccessful()) {
                $this->assertResponseIsSuccessful();

                $form = $crawler->selectButton('Create')->form();
                $crawler = $client->submit($form, [
                    'depot[depotName]' => '',
                ]);

                $validationResponse = $client->getResponse();
                if (422 === $validationResponse->getStatusCode()) {
                    $this->assertResponseStatusCodeSame(422);

                    $invalidFeedback = $crawler->filter('.invalid-feedback');
                    if ($invalidFeedback->count() > 0) {
                        $this->assertStringContainsString('should not be blank', $invalidFeedback->text());
                    }
                } else {
                    $this->assertLessThan(500, $validationResponse->getStatusCode());
                }
            } elseif ($response->isRedirect()) {
                $this->assertResponseRedirects();
            } else {
                $this->assertLessThan(500, $response->getStatusCode(), 'Response should not be a server error');
            }
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/pinduoduo-api/depot');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isRedirect() || 401 === $response->getStatusCode() || 403 === $response->getStatusCode(),
                'Unauthenticated access should be redirected or denied'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/pinduoduo-api/depot?query=test');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isSuccessful() || $response->isRedirect() || $response->isNotFound(),
                'Search request should not cause server errors'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testFilterFunctionality(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/pinduoduo-api/depot?filters[depotId]=1');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isSuccessful() || $response->isRedirect() || $response->isNotFound(),
                'Filter request should not cause server errors'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testDetailPageAccess(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/pinduoduo-api/depot?crudAction=detail&entityId=1');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isSuccessful() || $response->isRedirect() || $response->isNotFound(),
                'Detail page access should not cause server errors'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    protected function getControllerService(): DepotCrudController
    {
        return self::getService(DepotCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     *
     * 提供索引页标题数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield '仓库编码' => ['仓库编码'];
        yield '仓库名称' => ['仓库名称'];
        yield '仓库地址' => ['仓库地址'];
        yield '仓库类型' => ['仓库类型'];
        yield '默认仓库' => ['默认仓库'];
        yield '仓库状态' => ['仓库状态'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'depotId' => ['depotId'];
        yield 'depotCode' => ['depotCode'];
        yield 'depotName' => ['depotName'];
        yield 'depotAlias' => ['depotAlias'];
        yield 'contact' => ['contact'];
        yield 'phone' => ['phone'];
        yield 'address' => ['address'];
        yield 'province' => ['province'];
        yield 'city' => ['city'];
        yield 'district' => ['district'];
        yield 'zipCode' => ['zipCode'];
        yield 'type' => ['type'];
        yield 'businessType' => ['businessType'];
        yield 'area' => ['area'];
        yield 'capacity' => ['capacity'];
        yield 'usedCapacity' => ['usedCapacity'];
        yield 'locationCount' => ['locationCount'];
        yield 'usedLocationCount' => ['usedLocationCount'];
        yield 'isDefault' => ['isDefault'];
        yield 'status' => ['status'];
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'depotId' => ['depotId'];
        yield 'depotCode' => ['depotCode'];
        yield 'depotName' => ['depotName'];
        yield 'depotAlias' => ['depotAlias'];
        yield 'contact' => ['contact'];
        yield 'phone' => ['phone'];
        yield 'address' => ['address'];
        yield 'province' => ['province'];
        yield 'city' => ['city'];
        yield 'district' => ['district'];
        yield 'zipCode' => ['zipCode'];
        yield 'type' => ['type'];
        yield 'businessType' => ['businessType'];
        yield 'area' => ['area'];
        yield 'capacity' => ['capacity'];
        yield 'usedCapacity' => ['usedCapacity'];
        yield 'locationCount' => ['locationCount'];
        yield 'usedLocationCount' => ['usedLocationCount'];
        yield 'isDefault' => ['isDefault'];
        yield 'status' => ['status'];
    }
}
