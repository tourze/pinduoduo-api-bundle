<?php

/**
 * 验证脚本：确认所有修复都是正确的
 * - setter方法应该返回void
 * - add/remove collection management方法应该返回static
 */
$files = [
    __DIR__ . '/src/Entity/Account.php',
    __DIR__ . '/src/Entity/Mall.php',
    __DIR__ . '/src/Entity/Goods/Goods.php',
    __DIR__ . '/src/Entity/Goods/Category.php',
    __DIR__ . '/src/Entity/Goods/Spec.php',
    __DIR__ . '/src/Entity/Order/Order.php',
];

echo "=== 验证修复结果 ===\n\n";

$allGood = true;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "❌ 文件不存在: {$file}\n";
        $allGood = false;
        continue;
    }

    $basename = basename($file);
    echo "📄 检查文件: {$basename}\n";

    $content = file_get_contents($file);
    if (false === $content) {
        echo "  ❌ 无法读取文件内容\n";
        $allGood = false;
        continue;
    }

    // 检查是否还有返回static的setter方法
    $matchCount = preg_match_all('/public function set(\w+)\([^)]*\): static/', $content, $matches);
    if (false === $matchCount) {
        echo "  ❌ 正则匹配失败\n";
        $allGood = false;
        continue;
    }
    if ($matchCount > 0) {
        echo "  ❌ 发现返回static的setter方法:\n";
        foreach ($matches[0] as $method) {
            echo "    - {$method}\n";
        }
        $allGood = false;
    } else {
        echo "  ✅ 没有返回static的setter方法\n";
    }

    // 检查add/remove方法是否正确返回static
    $matchCount = preg_match_all('/public function (add|remove)(\w+)\([^)]*\): static/', $content, $matches);
    if (false === $matchCount) {
        echo "  ❌ 正则匹配失败\n";
        $allGood = false;
        continue;
    }
    if ($matchCount > 0) {
        echo "  ✅ 发现返回static的collection管理方法:\n";
        foreach ($matches[0] as $method) {
            echo "    - {$method}\n";
        }
    }

    // 检查add/remove方法是否错误返回void
    $matchCount = preg_match_all('/public function (add|remove)(\w+)\([^)]*\): void/', $content, $matches);
    if (false === $matchCount) {
        echo "  ❌ 正则匹配失败\n";
        $allGood = false;
        continue;
    }
    if ($matchCount > 0) {
        echo "  ❌ 发现返回void的collection管理方法:\n";
        foreach ($matches[0] as $method) {
            echo "    - {$method}\n";
        }
        $allGood = false;
    } else {
        echo "  ✅ 没有返回void的collection管理方法\n";
    }

    echo "\n";
}

if ($allGood) {
    echo "🎉 所有文件验证通过！\n";
    echo "✅ setter方法正确返回void\n";
    echo "✅ collection管理方法正确返回static\n";
} else {
    echo "⚠️  发现问题，需要进一步修复\n";
}

echo "\n=== 验证完成 ===\n";
