<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(['src', 'test'])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'const', 'function'],
        ],
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'yoda_style' => true,
        'concat_space' => ['spacing' => 'one'],
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'no_null_property_initialization' => true,
        'native_function_invocation' => ['include' => ['@compiler_optimized'], 'scope' => 'all', 'strict' => false],
        'ordered_class_elements' => true,
        'php_unit_method_casing' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile('.php_cs.' . (string) getenv('PHP_VERSION') . '.cache')
    ->setUsingCache(true);
