<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'default',

    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    | Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
    | "atom", "vscode".
    |
    | If you have another IDE that is not in this list but which provide an
    | url-handler, you could fill this config with a pattern like this:
    |
    | myide://open?url=file://%f&line=%l
    |
    */

    'ide' => 'phpstorm',

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [//  'path/to/directory-or-file'
    ],

    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],

    'remove' => [
        //  ExampleInsight::class,
        //SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff::class,
        // We have to use $GLOBALS to access some WordPress vars.
        PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        // We have to modify our code before we can allow this rule
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff::class,
        //SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff::class,
        // Enabling this could break other plugins
        SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class,
        // Todo: maybe avoid globals
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants::class,
        // Fixme due our WordPress filter & hooks
        //SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        // Fixme Property type hints only work with PHP 7.4
        SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        // It's bugged in PHPInsights
        PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class,
        // Cyclomatic complexity cannot be fixed easily
        NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class,
        // We like class names with Exception, Interface, Model, Repository as suffix.
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
    ],

    'config' => [
        //  ExampleInsight::class => [
        //      'key' => 'value',
        //  ],
        ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff::class => [
            'minLength' => 3,
            'allowedShortNames' => ['I', 'i', 'id', 'l', 's', 'h', 'js', 'wp'],
        ],
        ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff::class => [
            'maxLength' => 32,
        ],
        ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff::class => [
            'maxNestingLevel' => 4,
        ],
        LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 300,
            'ignoreComments' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define a level you want to reach per `Insights` category.
    | When a score is lower than the minimum level defined, then an error
    | code will be returned. This is optional and individually defined.
    |
    */

    'requirements' => [
//        'min-quality' => 0,
//        'min-complexity' => 0,
//        'min-architecture' => 0,
//        'min-style' => 0,
//        'disable-security-check' => false,
    ],

];
