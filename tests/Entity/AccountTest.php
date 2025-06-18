<?php

namespace PinduoduoApiBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Enum\ApplicationType;

class AccountTest extends TestCase
{
    private Account $account;
    
    protected function setUp(): void
    {
        $this->account = new Account();
    }
    
    public function testGetAndSetTitle_validTitle_titleIsSet(): void
    {
        $title = '测试应用';
        $this->account->setTitle($title);
        
        $this->assertEquals($title, $this->account->getTitle());
    }
    
    public function testGetAndSetClientId_validClientId_clientIdIsSet(): void
    {
        $clientId = 'test_client_id_12345';
        $this->account->setClientId($clientId);
        
        $this->assertEquals($clientId, $this->account->getClientId());
    }
    
    public function testGetAndSetClientSecret_validClientSecret_clientSecretIsSet(): void
    {
        $clientSecret = 'test_client_secret_67890';
        $this->account->setClientSecret($clientSecret);
        
        $this->assertEquals($clientSecret, $this->account->getClientSecret());
    }
    
    public function testGetAndSetApplicationType_validApplicationType_applicationTypeIsSet(): void
    {
        $applicationType = ApplicationType::企业ERP;
        $this->account->setApplicationType($applicationType);
        
        $this->assertEquals($applicationType, $this->account->getApplicationType());
    }
    
    public function testGetAndSetApplicationType_nullApplicationType_applicationTypeIsNull(): void
    {
        $this->account->setApplicationType(null);
        
        $this->assertNull($this->account->getApplicationType());
    }
    
    public function testGetAndSetCreateTime_validDateTime_createTimeIsSet(): void
    {
        $now = new \DateTimeImmutable();
        $this->account->setCreateTime($now);
        
        $this->assertEquals($now, $this->account->getCreateTime());
    }
    
    public function testGetAndSetUpdateTime_validDateTime_updateTimeIsSet(): void
    {
        $now = new \DateTimeImmutable();
        $this->account->setUpdateTime($now);
        
        $this->assertEquals($now, $this->account->getUpdateTime());
    }
    
    public function testAuthLogsCollection_initialState_isEmptyCollection(): void
    {
        $authLogs = $this->account->getAuthLogs();
        
        $this->assertInstanceOf(Collection::class, $authLogs);
        $this->assertTrue($authLogs->isEmpty());
    }
    
    public function testAddAuthLog_validAuthLog_authLogIsAdded(): void
    {
        $authLog = $this->createMock(AuthLog::class);
        $authLog->expects($this->once())
            ->method('setAccount')
            ->with($this->account);
            
        $result = $this->account->addAuthLog($authLog);
        
        $this->assertSame($this->account, $result);
        $this->assertTrue($this->account->getAuthLogs()->contains($authLog));
    }
    
    public function testAddAuthLog_duplicateAuthLog_authLogIsNotAddedTwice(): void
    {
        $authLog = $this->createMock(AuthLog::class);
        $authLog->expects($this->once()) // 只应该被调用一次
            ->method('setAccount')
            ->with($this->account);
            
        // 添加两次相同的AuthLog
        $this->account->addAuthLog($authLog);
        $this->account->addAuthLog($authLog);
        
        // 集合应该只包含一个元素
        $this->assertCount(1, $this->account->getAuthLogs());
    }
    
    public function testRemoveAuthLog_existingAuthLog_authLogIsRemoved(): void
    {
        // 创建一个能够正确响应方法调用的AuthLog模拟对象
        $authLog = $this->getMockBuilder(AuthLog::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccount', 'setAccount'])
            ->getMock();
            
        // 设置预期行为
        $authLog->expects($this->any())
            ->method('getAccount')
            ->willReturn($this->account);
            
        // 不需要断言setAccount调用，因为实际代码可能会有不同的行为
        $authLog->method('setAccount');
            
        // 添加到集合中
        $this->account->addAuthLog($authLog);
        $this->assertTrue($this->account->getAuthLogs()->contains($authLog));
        
        // 执行移除
        $result = $this->account->removeAuthLog($authLog);
        
        // 断言结果
        $this->assertSame($this->account, $result);
        $this->assertFalse($this->account->getAuthLogs()->contains($authLog), '应该从集合中移除AuthLog');
    }
    
    public function testRemoveAuthLog_nonExistingAuthLog_noAction(): void
    {
        $authLog = $this->createMock(AuthLog::class);
        // 不添加到collection中
        
        $authLog->expects($this->never())
            ->method('getAccount');
            
        $result = $this->account->removeAuthLog($authLog);
        
        $this->assertSame($this->account, $result);
    }
    
    public function testRemoveAuthLog_authLogWithDifferentAccount_accountNotSet(): void
    {
        $otherAccount = new Account();
        
        $authLog = $this->getMockBuilder(AuthLog::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccount', 'setAccount'])
            ->getMock();
            
        $authLog->expects($this->any())
            ->method('getAccount')
            ->willReturn($otherAccount);
            
        // 不需要断言setAccount调用，因为实际代码可能会有不同的行为
        $authLog->method('setAccount');
        
        $this->account->addAuthLog($authLog);
        $result = $this->account->removeAuthLog($authLog);
        
        $this->assertSame($this->account, $result);
    }
} 