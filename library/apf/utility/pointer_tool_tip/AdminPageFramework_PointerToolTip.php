<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_PointerToolTip extends AdminPageFramework_FrameworkUtility
{
    private static $_bResourceLoaded = false;
    private static $aPointers = array();
    public $sPointerID;
    public $aPointerData;
    public $aScreenIDs = array();
    public function __construct($asScreenIDs, $sPointerID, array $aPointerData)
    {
        if (version_compare($GLOBALS[ 'wp_version' ], '3.3', '<')) {
            return false;
        }
        $this->aScreenIDs = $this->getAsArray($asScreenIDs);
        $this->sPointerID = $sPointerID;
        $this->aPointerData = $aPointerData;
        $this->_setHooks($this->aScreenIDs);
    }
    private function _setHooks($aScreenIDs)
    {
        foreach ($aScreenIDs as $_sScreenID) {
            if (! $_sScreenID) {
                continue;
            }
            add_filter(get_class($this) . '-' . $_sScreenID, array( $this, '_replyToSetPointer' ));
        }
        if (! $this->_hasBeenCalled()) {
            return;
        }
        add_action('admin_enqueue_scripts', array( $this, '_replyToLoadPointers' ), 1000);
    }
    private function _hasBeenCalled()
    {
        if (self::$_bResourceLoaded) {
            return false;
        }
        self::$_bResourceLoaded = true;
        return true;
    }
    public function _replyToSetPointer($aPointers)
    {
        return array( $this->sPointerID => $this->aPointerData ) + $aPointers;
    }
    public function _replyToLoadPointers()
    {
        $_aPointers = $this->_getValidPointers($this->_getPointers());
        if (empty($_aPointers) || ! is_array($_aPointers)) {
            return;
        }
        $this->_enqueueScripts();
        self::$aPointers = $_aPointers + self::$aPointers;
    }
    private function _getPointers()
    {
        $_oScreen = get_current_screen();
        $_sScreenID = $_oScreen->id;
        if (in_array($_sScreenID, $this->aScreenIDs)) {
            return apply_filters(get_class($this) . '-' . $_sScreenID, array());
        }
        if (isset($_GET[ 'page' ])) {
            return apply_filters(get_class($this) . '-' . $this->getHTTPQueryGET('page'), array());
        }
        return array();
    }
    private function _getValidPointers($_aPointers)
    {
        $_aDismissed = explode(',', ( string ) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        $_aValidPointers = array();
        foreach ($_aPointers as $_iPointerID => $_aPointer) {
            $_aPointer = $_aPointer + array( 'target' => null, 'options' => null, 'pointer_id' => null, );
            if ($this->_shouldSkip($_iPointerID, $_aDismissed, $_aPointer)) {
                continue;
            }
            $_aPointer[ 'target' ] = $this->getAsArray($_aPointer[ 'target' ]);
            $_aPointer[ 'pointer_id' ] = $_iPointerID;
            $_aValidPointers[] = $_aPointer;
        }
        return $_aValidPointers;
    }
    private function _shouldSkip($_iPointerID, $_aDismissed, $_aPointer)
    {
        if (in_array($_iPointerID, $_aDismissed)) {
            return true;
        }
        if (empty($_aPointer)) {
            return true;
        }
        if (empty($_iPointerID)) {
            return true;
        }
        if (empty($_aPointer[ 'target' ])) {
            return true;
        }
        if (empty($_aPointer[ 'options' ])) {
            return true;
        }
        return false;
    }
    private function _enqueueScripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-pointer');
        wp_enqueue_style('wp-pointer');
        add_action('admin_print_footer_scripts', array( $this, '_replyToInsertInternalScript' ));
    }
    public function _replyToInsertInternalScript()
    {
        echo "<script type='text/javascript' class='admin-page-framework-pointer-tool-tip'>" . '/* <![CDATA[ */' . $this->_getInternalScript(self::$aPointers) . '/* ]]> */' . "</script>";
    }
    public function _getInternalScript($aPointers=array())
    {
        $_aJSArray = json_encode($aPointers);
        return <<<JAVASCRIPTS
(function(jQuery){jQuery(document).ready(function(jQuery){jQuery.each($_aJSArray,function(iIndex,_aPointer){var _aOptions=jQuery.extend(_aPointer.options,{close:function(){jQuery.post(ajaxurl,{pointer:_aPointer.pointer_id,action:'dismiss-wp-pointer'})}});jQuery.each(_aPointer.target,function(iIndex,_sTarget){var _oTarget=jQuery(_sTarget);if(_oTarget.length<=0){return!0}
var _oResult=jQuery(_sTarget).pointer(_aOptions).pointer('open');if(_oResult.length>0){return!1}})})})}(jQuery))
JAVASCRIPTS;
    }
}
