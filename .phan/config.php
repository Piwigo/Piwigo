<?php
declare(strict_types=1);

use Phan\Issue;

return [
    'target_php_version' => null,
    'pretend_newer_core_functions_exist' => true,
    'allow_missing_properties' => false,
    'null_casts_as_any_type' => false,
    'null_casts_as_array' => false,
    'array_casts_as_null' => false,
    'strict_method_checking' => true,
    'quick_mode' => false,
    'simplify_ast' => false,
    'suppress_issue_types' => [
        'PhanDeprecatedFunction',  // Comment out this line to find all deprecated calls
        'PhanUnreferencedClosure',
        'PhanPluginNoCommentOnProtectedMethod',
        'PhanPluginDescriptionlessCommentOnProtectedMethod',
        'PhanPluginNoCommentOnPrivateMethod',
        'PhanPluginDescriptionlessCommentOnPrivateMethod',
        'PhanPluginDescriptionlessCommentOnPrivateProperty',
        'PhanPluginRedundantClosureComment',
        'PhanUndeclaredGlobalVariable',
        'PhanMissingRequireFile',
        'PhanTypeArraySuspiciousNullable',
        'PhanTypeMismatchArgumentNullableInternal',
        'PhanUndeclaredVariableDim',
        'PhanTypeInvalidDimOffset',
        'PhanTypeMismatchDimAssignment',
        'PhanTypeMismatchDimFetch',
        'PhanTypeArraySuspiciousNull',
        'PhanEmptyForeach',
        'PhanUndeclaredFunctionInCallable',
        'PhanPossiblyNonClassMethodCall',
        'PhanTypeObjectUnsetDeclaredProperty'
    ],
    'file_list' => [ ],
    'enable_include_path_checks' => true,
    'include_paths' => ['.'],
    'exclude_file_regex' => '@^tools/.*@',
    'exclude_file_list' => [
        'admin/include/pclzip.lib.php',
        'include/cssmin.class.php',
        'include/dblayer/functions_mysql.inc.php',
        '.phan/config.php'
    ],
    'autoload_internal_extension_signatures' => [
        'mysql'      => '.phan/internal_stubs/mysql.phan_php',
    ],
    'processes' => 1,
    'directory_list' => [
        '.',
    ],
    'analyzed_file_extensions' => ['php'],
    'exclude_analysis_directory_list' => [ 'language','tools','vendor' ],
    'skip_slow_php_options_warning' => false,
    'ignore_undeclared_functions_with_known_signatures' => false,
];
