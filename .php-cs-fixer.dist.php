<?php

$finder = (new PhpCsFixer\Finder())->in(__DIR__.'/src')->append((new PhpCsFixer\Finder())->in(__DIR__.'/tests')->exclude('bundle'));

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
    ])
    ->setFinder($finder);
