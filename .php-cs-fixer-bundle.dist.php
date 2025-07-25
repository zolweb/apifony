<?php

$finder = (new PhpCsFixer\Finder())->in(__DIR__.'/tests/bundle');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHPUnit30Migration:risky' => true,
        '@PHPUnit32Migration:risky' => true,
        '@PHPUnit35Migration:risky' => true,
        '@PHPUnit43Migration:risky' => true,
        '@PHPUnit48Migration:risky' => true,
        '@PHPUnit50Migration:risky' => true,
        '@PHPUnit52Migration:risky' => true,
        '@PHPUnit54Migration:risky' => true,
        '@PHPUnit55Migration:risky' => true,
        '@PHPUnit56Migration:risky' => true,
        '@PHPUnit57Migration:risky' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        '@PHP56Migration:risky' => true,
        '@PHP70Migration:risky' => true,
        '@PHP71Migration:risky' => true,
        '@PHP74Migration:risky' => true,
        '@PHP80Migration:risky' => true,
        '@PHP82Migration' => true,
        '@PER-CS2.0' => true,
        '@PER-CS2.0:risky' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'yoda_style' => false,
        'php_unit_test_class_requires_covers' => false, // The rule inserted comments that are deprecated by phpunit
        'class_attributes_separation' => false,
        'declare_parentheses' => false,
        'single_line_after_imports' => false,
        'blank_lines_before_namespace' => false,
        'single_blank_line_at_eof' => false,
    ])
    ->setFinder($finder);
