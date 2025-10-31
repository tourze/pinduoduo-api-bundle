<?php

/**
 * 最终修复脚本：处理add/remove collection management方法
 * 这些方法应该返回static而不是void
 */
$files = [
    __DIR__ . '/src/Entity/Account.php',
    __DIR__ . '/src/Entity/Order/Order.php',
    __DIR__ . '/src/Entity/Mall.php',
    __DIR__ . '/src/Entity/Goods/Goods.php',
    __DIR__ . '/src/Entity/Goods/Category.php',
    __DIR__ . '/src/Entity/Goods/Spec.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "文件不存在: {$file}\n";
        continue;
    }

    echo "处理文件: {$file}\n";

    $content = file_get_contents($file);
    if (false === $content) {
        echo "无法读取文件: {$file}\n";
        continue;
    }
    $originalContent = $content;

    // 修复add/remove方法的返回类型
    // 这些方法应该返回static以支持链式调用
    $patterns = [
        // 修复返回void的add方法
        '/public function add(\w+)\(\w+\s+\$\w+\): void\s*{(\s*)\s*}/',
        // 修复返回void的remove方法
        '/public function remove(\w+)\(\w+\s+\$\w+\): void\s*{(\s*)\s*}/',
        // 修复返回void的addChild方法
        '/public function addChild\(self\s+\$\w+\): void\s*{(\s*)\s*}/',
        // 修复返回void的removeChild方法
        '/public function removeChild\(self\s+\$\w+\): void\s*{(\s*)\s*}/',
    ];

    $replacements = [
        'public function add($1($2',
        'public function remove($1($2',
        'public function addChild(self $1): static',
        'public function removeChild(self $1): static',
    ];

    $result = preg_replace($patterns, $replacements, $content);
    if (null === $result) {
        echo "正则替换失败: {$file}\n";
        continue;
    }
    $content = $result;

    // 修复返回void的add/remove方法的实现，确保有return $this;
    $result = preg_replace_callback(
        '/public function (add|remove)(\w+)\(([^)]+)\): void\s*{(.*?)}/s',
        function ($matches) {
            $methodBody = $matches[4];
            // 如果方法体最后没有return $this;，添加它
            if (!str_contains(trim($methodBody), 'return $this;')) {
                $methodBody = rtrim($methodBody) . "\n        return \$this;\n    ";
            }

            return "public function {$matches[1]}{$matches[2]}({$matches[3]}): static {{$methodBody}}";
        },
        $content
    );
    if (null === $result) {
        echo "正则回调替换失败: {$file}\n";
        continue;
    }
    $content = $result;

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "已修复文件: {$file}\n";
    } else {
        echo "无需修复: {$file}\n";
    }
}

echo "修复完成！\n";
