<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(
        [
            'vendor',
        ]
    )
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config
    ->setRules(
        [
            '@Symfony' => true,
            'strict_param' => false,
            'array_syntax' => ['syntax' => 'short'],
            'concat_space' => ['spacing' => 'one'],
            'phpdoc_align' => ['align' => 'left'],
            'phpdoc_summary' => false,
            'void_return' => false,
            'phpdoc_var_without_name' => false,
            'phpdoc_to_comment' => false,
            'single_line_throw' => false,
        ]
    )
    ->setFinder($finder);
