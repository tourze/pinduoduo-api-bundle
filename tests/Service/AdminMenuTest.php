<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testGetMenuItemsReturnsArray(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        $this->assertNotEmpty($menuItems);
        $this->assertGreaterThan(0, count($menuItems));
    }

    public function testGetMenuItemsReturnsCorrectNumberOfItems(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        // 我们期望至少有基础的菜单项
        $this->assertGreaterThan(5, count($menuItems));
    }

    public function testGetMenuItemsForRolesWithAdminRole(): void
    {
        $menuItems = $this->adminMenu->getMenuItemsForRoles(['ROLE_ADMIN']);

        $this->assertNotEmpty($menuItems);
        $this->assertGreaterThan(0, count($menuItems));
    }

    public function testGetMenuItemsForRolesWithSuperAdminRole(): void
    {
        $menuItems = $this->adminMenu->getMenuItemsForRoles(['ROLE_SUPER_ADMIN']);

        $this->assertNotEmpty($menuItems);
        $this->assertGreaterThan(0, count($menuItems));
    }

    public function testGetMenuItemsForRolesWithoutAdminRoleReturnsEmpty(): void
    {
        $menuItems = $this->adminMenu->getMenuItemsForRoles(['ROLE_USER']);

        $this->assertEmpty($menuItems);
        $this->assertCount(0, $menuItems);
    }

    public function testGetMenuItemsForRolesWithEmptyRolesReturnsEmpty(): void
    {
        $menuItems = $this->adminMenu->getMenuItemsForRoles([]);

        $this->assertEmpty($menuItems);
        $this->assertCount(0, $menuItems);
    }

    public function testAdminMenuServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testGetMenuItemsForRolesAcceptsValidRoleArray(): void
    {
        // 测试方法接受有效的角色数组
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $menuItems = $this->adminMenu->getMenuItemsForRoles($roles);

        // 因为包含ROLE_ADMIN，所以应该返回菜单项
        $this->assertNotEmpty($menuItems);
        $this->assertGreaterThan(0, count($menuItems));
    }

    public function testGetMenuItemsConsistentResults(): void
    {
        // 测试多次调用返回一致的结果
        $menuItems1 = $this->adminMenu->getMenuItems();
        $menuItems2 = $this->adminMenu->getMenuItems();

        $this->assertCount(count($menuItems1), $menuItems2);
    }
}
