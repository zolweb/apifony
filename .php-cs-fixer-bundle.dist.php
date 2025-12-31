<?php

$finder = (new PhpCsFixer\Finder())->in(__DIR__.'/tests/bundle');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@auto' => true,
        '@auto:risky' => true,
        '@autoPHPMigration' => true,
        '@autoPHPMigration:risky' => true,
        '@autoPHPUnitMigration:risky' => true,
        '@PSR1' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'yoda_style' => false,
        'class_attributes_separation' => false,
        'class_definition' => false,
        'string_implicit_backslashes' => false,
        'declare_parentheses' => false,
        'single_line_after_imports' => false,
        'return_type_declaration' => false,
        'blank_lines_before_namespace' => false,
        'single_blank_line_at_eof' => false,
        'braces_position' => false,
        'function_declaration' => false,
        'blank_line_before_statement' => false,
        'phpdoc_align' => false,
        'no_superfluous_phpdoc_tags' => false,
        'trailing_comma_in_multiline' => false,
        'no_extra_blank_lines' => false,
        'no_whitespace_in_blank_line' => false,
        'binary_operator_spaces' => false,
        'ordered_imports' => false,
    ])
    ->setFinder($finder);
