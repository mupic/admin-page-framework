<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_RegisterClasses
{
    public $_aClasses = array();
    protected static $_aStructure_Options = array( 'is_recursive' => true, 'exclude_dir_paths' => array(), 'exclude_dir_names' => array( 'asset', 'assets', 'css', 'js', 'image', 'images', 'license', 'document', 'documents' ), 'allowed_extensions' => array( 'php', ), 'include_function' => 'include', 'exclude_class_names' => array( ), );
    public function __construct($asScanDirPaths, array $aOptions=array(), array $aClasses=array())
    {
        $_aOptions = $aOptions + self::$_aStructure_Options;
        $this->_aClasses = $aClasses + $this->_getClassArray($asScanDirPaths, $_aOptions);
        $this->_registerClasses($_aOptions[ 'include_function' ]);
    }
    private function _getClassArray($asScanDirPaths, array $aSearchOptions)
    {
        if (empty($asScanDirPaths)) {
            return array();
        }
        $_aFilePaths = array();
        foreach (( array ) $asScanDirPaths as $_sClassDirPath) {
            if (realpath($_sClassDirPath)) {
                $_aFilePaths = array_merge($this->getFilePaths($_sClassDirPath, $aSearchOptions), $_aFilePaths);
            }
        }
        $_aClasses = array();
        foreach ($_aFilePaths as $_sFilePath) {
            $_sClassNameWOExt = pathinfo($_sFilePath, PATHINFO_FILENAME);
            if (in_array($_sClassNameWOExt, $aSearchOptions['exclude_class_names'])) {
                continue;
            }
            $_aClasses[ $_sClassNameWOExt ] = $_sFilePath;
        }
        return $_aClasses;
    }
    protected function _constructClassArray($asScanDirPaths, array $aSearchOptions)
    {
        return $this->_getClassArray($asScanDirPaths, $aSearchOptions);
    }
    protected function getFilePaths($sClassDirPath, array $aSearchOptions)
    {
        $sClassDirPath = rtrim($sClassDirPath, '\\/') . DIRECTORY_SEPARATOR;
        $_aAllowedExtensions = $aSearchOptions['allowed_extensions'];
        $_aExcludeDirPaths = ( array ) $aSearchOptions['exclude_dir_paths'];
        $_aExcludeDirNames = ( array ) $aSearchOptions['exclude_dir_names'];
        $_bIsRecursive = $aSearchOptions[ 'is_recursive' ];
        if (defined('GLOB_BRACE')) {
            $_aFilePaths = $_bIsRecursive ? $this->doRecursiveGlob($sClassDirPath . '*.' . $this->_getGlobPatternExtensionPart($_aAllowedExtensions), GLOB_BRACE, $_aExcludeDirPaths, $_aExcludeDirNames) : ( array ) glob($sClassDirPath . '*.' . $this->_getGlobPatternExtensionPart($_aAllowedExtensions), GLOB_BRACE);
            return array_filter($_aFilePaths);
        }
        $_aFilePaths = array();
        foreach ($_aAllowedExtensions as $__sAllowedExtension) {
            $__aFilePaths = $_bIsRecursive ? $this->doRecursiveGlob($sClassDirPath . '*.' . $__sAllowedExtension, 0, $_aExcludeDirPaths, $_aExcludeDirNames) : ( array ) glob($sClassDirPath . '*.' . $__sAllowedExtension);
            $_aFilePaths = array_merge($__aFilePaths, $_aFilePaths);
        }
        return array_unique(array_filter($_aFilePaths));
    }
    protected function _getGlobPatternExtensionPart(array $aExtensions=array( 'php', 'inc' ))
    {
        return empty($aExtensions) ? '*' : '{' . implode(',', $aExtensions) . '}';
    }
    protected function doRecursiveGlob($sPathPatten, $nFlags=0, array $aExcludeDirs=array(), array $aExcludeDirNames=array())
    {
        $_aFiles = glob($sPathPatten, $nFlags);
        $_aFiles = is_array($_aFiles) ? $_aFiles : array();
        $_aDirs = glob(dirname($sPathPatten) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT);
        $_aDirs = is_array($_aDirs) ? $_aDirs : array();
        foreach ($_aDirs as $_sDirPath) {
            if (in_array($_sDirPath, $aExcludeDirs)) {
                continue;
            }
            if (in_array(pathinfo($_sDirPath, PATHINFO_DIRNAME), $aExcludeDirNames)) {
                continue;
            }
            $_aFiles = array_merge($_aFiles, $this->doRecursiveGlob($_sDirPath . DIRECTORY_SEPARATOR . basename($sPathPatten), $nFlags, $aExcludeDirs));
        }
        return $_aFiles;
    }
    protected function _registerClasses($sIncludeFunction)
    {
        spl_autoload_register(array( $this, '_replyToAutoLoad_' . $sIncludeFunction ));
    }
    public function _replyToAutoLoad_include($sCalledUnknownClassName)
    {
        if (! isset($this->_aClasses[ $sCalledUnknownClassName ])) {
            return;
        }
        include($this->_aClasses[ $sCalledUnknownClassName ]);
    }
    public function _replyToAutoLoad_include_once($sCalledUnknownClassName)
    {
        if (! isset($this->_aClasses[ $sCalledUnknownClassName ])) {
            return;
        }
        include_once($this->_aClasses[ $sCalledUnknownClassName ]);
    }
    public function _replyToAutoLoad_require($sCalledUnknownClassName)
    {
        if (! isset($this->_aClasses[ $sCalledUnknownClassName ])) {
            return;
        }
        require($this->_aClasses[ $sCalledUnknownClassName ]);
    }
    public function _replyToAutoLoad_require_once($sCalledUnknownClassName)
    {
        if (! isset($this->_aClasses[ $sCalledUnknownClassName ])) {
            return;
        }
        require_once($this->_aClasses[ $sCalledUnknownClassName ]);
    }
}
