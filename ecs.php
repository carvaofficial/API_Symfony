<?php

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    // A. full sets
    $ecsConfig->sets([SetList::PSR_12]);

    // B. standalone rule
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
    $ecsConfig->ruleWithConfiguration(MethodArgumentSpaceFixer::class, [
        'on_multiline' => 'ensure_fully_multiline',
    ]);
    $ecsConfig->ruleWithConfiguration(NativeFunctionInvocationFixer::class, [
        'scope' => 'namespaced',
        'include' => ['@compiler_optimized']
    ]);
    $ecsConfig->rule(NoUnusedImportsFixer::class);

    // alternative to CLI arguments, easier to maintain and extend
    $ecsConfig->paths([__DIR__ . '/src']);
    // bear in mind that this will override SetList skips if one was previously imported
    // this is result of design decision in symfony https://github.com/symfony/symfony/issues/26713
    $ecsConfig->skip([
        // skip paths with legacy code
        __DIR__ . '/src/Migrations',
        __DIR__ . '/tests'
    ]);
};
