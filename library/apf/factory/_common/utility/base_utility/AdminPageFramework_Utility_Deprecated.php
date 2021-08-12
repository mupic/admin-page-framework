<?php 
/**
	Admin Page Framework v3.9.0b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_Utility_Deprecated {
    static public function minifyCSS($sCSSRules) {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getCSSMinified()');
        return AdminPageFramework_Utility_String::getCSSMinified($sCSSRules);
    }
    static public function sanitizeLength($sLength, $sUnit = 'px') {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getLengthSanitized()');
        return AdminPageFramework_Utility_String::getLengthSanitized($sLength, $sUnit);
    }
    public static function getCorrespondingArrayValue($vSubject, $sKey, $sDefault = '', $bBlankToDefault = false) {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getElement()');
        if (!isset($vSubject)) {
            return $sDefault;
        }
        if ($bBlankToDefault && $vSubject == '') {
            return $sDefault;
        }
        if (!is_array($vSubject)) {
            return ( string )$vSubject;
        }
        if (isset($vSubject[$sKey])) {
            return $vSubject[$sKey];
        }
        return $sDefault;
    }
    static public function isAssociativeArray(array $aArray) {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__);
        return ( bool )count(array_filter(array_keys($aArray), 'is_string'));
    }
    public static function getArrayDimension($array) {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__);
        return (is_array(reset($array))) ? self::getArrayDimension(reset($array)) + 1 : 1;
    }
    protected function getFieldElementByKey($asElement, $sKey, $asDefault = '') {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getElement()');
        if (!is_array($asElement) || !isset($sKey)) {
            return $asElement;
        }
        $aElements = & $asElement;
        return isset($aElements[$sKey]) ? $aElements[$sKey] : $asDefault;
    }
    static public function shiftTillTrue(array $aArray) {
        AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__);
        foreach ($aArray as & $vElem) {
            if ($vElem) {
                break;
            }
            unset($vElem);
        }
        return array_values($aArray);
    }
    static public function getAttributes(array $aAttributes) {
        AdminPageFramework_Utility::showDeprecationNotice(__METHOD__, 'AdminPageFramework_WPUtility::getAttributes()');
        $_sQuoteCharactor = "'";
        $_aOutput = array();
        foreach ($aAttributes as $sAttribute => $sProperty) {
            if (in_array(gettype($sProperty), array('array', 'object'))) {
                continue;
            }
            $_aOutput[] = "{$sAttribute}={$_sQuoteCharactor}{$sProperty}{$_sQuoteCharactor}";
        }
        return implode(' ', $_aOutput);
    }
    }
    