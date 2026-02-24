<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->append([
        'bin/console',
    ])
    ->path([
        'bin/',
        'config/',
        'public/',
        'src/',
        'tests/',
    ])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        # rules to keep
        'class_definition' => [
            'multi_line_extends_each_single_line' => true, # https://cs.symfony.com/doc/rules/class_notation/class_definition.html#example-4
        ],
        'concat_space' => ['spacing' => 'one'], # the @PER like PSR12 preserve space
        'no_null_property_initialization' => false,
        # rules to check with sfcs-diff
        'phpdoc_summary' => false, #39
        'yoda_style' => false, #35
        'phpdoc_separation' => false, #24
        'single_quote' => false, #16
        'ordered_imports' => false, #16
        'no_superfluous_phpdoc_tags' => false, #12
        'no_empty_phpdoc' => false, #9
        'trailing_comma_in_multiline' => false, #8
        'phpdoc_align' => false, #7
        'increment_style' => false, #5
        'blank_line_before_statement' => false, #5
        'no_extra_blank_lines' => false, #4
        'single_space_around_construct' => false, #3
    ])
    ->setFinder($finder)
;
