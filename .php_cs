<?php
$header = <<<'EOF'
(c)Copyright 2007-%s UGroupMedia Inc. <dev@ugroupmedia.com>
This source file is part of PNP Project and is subject to 
copyright. It can not be copied and/or distributed without 
the express permission of UGroupMedia Inc.
If you get a copy of this file without explicit authorization, 
please contact us to the email above.
EOF;
$header = sprintf($header, date('Y'));
$finder = PhpCsFixer\Finder::create()
    ->exclude('bin')
    ->exclude('tests')
    ->exclude('vendor')
    ->in(__DIR__);
$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHPUnit60Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        'declare_strict_types' => true,
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'blank_line_before_statement' => true,
        'phpdoc_annotation_without_dot' => false,
        'align_multiline_comment' => false,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'array_indentation' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'comment_to_phpdoc' => true,
        'compact_nullable_typehint' => true,
        'escape_implicit_backslashes' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'final_internal_class' => true,
        'fully_qualified_strict_types' => true,
        'function_to_constant' => [
            'functions' => [
                'get_class',
                'get_called_class',
                'php_sapi_name',
                'phpversion',
                'pi',
            ],
        ],
        'header_comment' => [
            'header' => $header,
        ],
        'heredoc_to_nowdoc' => true,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'logical_operators' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'method_chaining_indentation' => true,
        'multiline_comment_opening_closing' => true,
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
        ],
        'no_alternative_syntax' => true,
        'no_binary_string' => true,
        'no_extra_blank_lines' => [
            'break',
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'switch',
            'throw',
            'use',
            'useTrait',
            'use_trait',
        ],
        'no_null_property_initialization' => true,
        'no_short_echo_tag' => true,
        'no_superfluous_elseif' => false,
        'no_unreachable_default_argument_value' => true,
        'no_unset_on_property' => false,
        'no_useless_else' => false,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => true,
        'return_assignment' => true,
        'string_line_ending' => true,
        'increment_style' => false,
        'class_attributes_separation' => [
            'elements' => [
                'method',
                'property',
            ],
        ],
        'fopen_flags' => [
            'b_mode' => true,
        ],

        // THIS could be nice if we can ad some exception like ID property always on top.
        //          'ordered_class_elements' => [
        //            'order' => [
        //                'use_trait',
        //                'public',
        //                'protected',
        //                'private',
        //                'constant',
        //                'constant_public',
        //                'constant_protected',
        //                'constant_private',
        //                'property',
        //                'property_static',
        //                'property_public',
        //                'property_protected',
        //                'property_private',
        //                'property_public_static',
        //                'property_protected_static',
        //                'property_private_static',
        //                'construct',
        //                'method',
        //                'method_static',
        //                'method_public',
        //                'method_protected',
        //                'method_private',
        //                'method_public_static',
        //                'method_protected_static',
        //                'method_private_static',
        //                'magic',
        //                'destruct',
        //                'phpunit',
        //            ],
        //            'sortAlgorithm' => 'alpha'
        //        ],

        // DONT ENABLE ALL THE TIME. DO TIME TO TIME CHECK WITH THOSE ENABLED
        'strict_param' => false,
        'strict_comparison' => false,

        // FOLLOWING JUST CREATE TOO MUCH BREAKING FOR NOW.
        //'php_unit_internal_class' => true,
        //'php_unit_ordered_covers' => true,
        //'php_unit_set_up_tear_down_visibility' => true,
        //'php_unit_strict' => true,
        //'php_unit_test_annotation' => true,
        //'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        //'php_unit_test_class_requires_covers' => true,
    ])
    ->setFinder($finder);

return $config;