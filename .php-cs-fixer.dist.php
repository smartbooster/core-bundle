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
            'phpdoc_summary' => false, #39
            'yoda_style' => false, #35
            'phpdoc_separation' => false, #24
            'concat_space' => false, #18
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
            'no_unused_imports' => false, #2
            'class_attributes_separation' => false, #2
            'cast_spaces' => false, #2
            'blank_line_between_import_groups' => false, #2
            'whitespace_after_comma_in_array' => false, #1
            'trim_array_spaces' => false, #1
            'statement_indentation' => false, #1
            'single_line_throw' => false, #1
            'phpdoc_trim' => false, #1
            'operator_linebreak' => false, #1
            'no_useless_else' => false, #1
            'no_unneeded_control_parentheses' => false, #1
            'no_trailing_whitespace' => false, #1
            'no_null_property_initialization' => false, #1
            'no_empty_statement' => false, #1
            'no_alias_language_construct_call' => false, #1
    ])
    ->setFinder($finder)
;
