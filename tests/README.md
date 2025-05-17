# 拼多多API Bundle 测试文档

## 测试概述

本测试套件为 `pinduoduo-api-bundle` 提供单元测试，覆盖了核心功能、实体、服务和枚举类型。测试采用行为驱动+边界覆盖的风格，确保代码质量和稳定性。

## 测试范围

当前测试覆盖以下组件：

1. **异常类**
   - `PddApiException` - 测试异常的构造、消息处理和子消息功能

2. **实体类**
   - `Account` - 测试属性设置、获取和集合管理功能

3. **服务类**
   - `SdkService` - 测试SDK实例创建和API请求功能
   - `UploadService` - 测试图片上传功能，包括缓存和错误处理
   - `CategoryService` - 测试商品类目规格同步功能

4. **枚举类**
   - `ApplicationType` - 测试枚举值和标签功能

## 测试限制

测试过程中遇到以下限制：

1. **第三方SDK依赖**
   - 无法完全模拟 `PinDuoDuo` SDK 的行为，部分测试被跳过或仅测试到调用前

2. **实体类设计问题**
   - `Spec` 类没有 `setId` 方法，但 `CategoryService` 中使用了该方法，相关测试被跳过

## 运行测试

在项目根目录执行以下命令运行测试：

```bash
./vendor/bin/phpunit packages/pinduoduo-api-bundle/tests
```

## 测试结果

当前测试结果：
- 测试总数：32
- 断言总数：97
- 警告数：5 (与模拟对象相关的警告)
- 跳过测试：4 (由于上述限制)

## 改进建议

1. 修复 `Spec` 类，添加缺失的 `setId` 方法，或修改 `CategoryService` 的实现方式
2. 考虑重构 `SdkService` 以提高可测试性，例如使用依赖注入模式注入SDK实例
3. 为其他未覆盖的类和方法添加测试用例 