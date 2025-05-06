<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var'])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PhpCsFixer' => true,
        'blank_line_before_statement' => ['statements' => ['return']],
        'yoda_style' => false,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
        ],
        'global_namespace_import' => [
            'import_constants' => false,
            'import_functions' => false,
            'import_classes' => true,
        ],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
        ],
        'php_unit_test_class_requires_covers' => false,
        'php_unit_internal_class' => false,
        'phpdoc_line_span' => ['const' => 'single', 'property' => 'single', 'method' => 'single']
    ])
    ->setFinder($finder)
;
