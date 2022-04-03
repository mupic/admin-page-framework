<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * If accessed from a console, include the registry class to load 'AdminPageFramework_Registry_Base'.
 */
if ( php_sapi_name() === 'cli' ) {
    $_sFrameworkFilePath = dirname( dirname( __FILE__ ) ) . '/admin-page-framework.php';
    if ( file_exists( $_sFrameworkFilePath ) ) {
        include_once( $_sFrameworkFilePath );
    }
}

/**
 * Provides header information of the framework for the minified version.
 *
 * The script creator will include this file ( but it does not include WordPress ) to use the reflection class to generate the header comment section.
 *
 * @since       3.1.3
 * @since       3.9.0   Renamed from `AdminPageFramework_InclusionClassFilesHeader`
 * @package     AdminPageFramework/Property
 * @internal
 */
final class AdminPageFramework_ClassMapHeader extends AdminPageFramework_Registry_Base {

    const NAME          = 'Admin Page Framework - Class Map';
    const DESCRIPTION   = 'Generated by PHP Class Map Generator <https://github.com/michaeluno/php-classmap-generator>';

}