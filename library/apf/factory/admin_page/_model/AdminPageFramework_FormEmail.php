<?php 
/**
	Admin Page Framework v3.8.15 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2017, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_FormEmail extends AdminPageFramework_FrameworkUtility {
    public $aEmailOptions = array();
    public $aInput = array();
    public $sSubmitSectionID;
    private $_aPathsToDelete = array();
    public function __construct(array $aEmailOptions, array $aInput, $sSubmitSectionID) {
        $this->aEmailOptions = $aEmailOptions;
        $this->aInput = $aInput;
        $this->sSubmitSectionID = $sSubmitSectionID;
        $this->_aPathsToDelete = array();
    }
    public function send() {
        $aEmailOptions = $this->aEmailOptions;
        $aInput = $this->aInput;
        $sSubmitSectionID = $this->sSubmitSectionID;
        if ($_bIsHTML = $this->_getEmailArgument($aInput, $aEmailOptions, 'is_html', $sSubmitSectionID)) {
            add_filter('wp_mail_content_type', array($this, '_replyToSetMailContentTypeToHTML'));
        }
        if ($this->_sEmailSenderAddress = $this->_getEmailArgument($aInput, $aEmailOptions, 'from', $sSubmitSectionID)) {
            add_filter('wp_mail_from', array($this, '_replyToSetEmailSenderAddress'));
        }
        if ($this->_sEmailSenderName = $this->_getEmailArgument($aInput, $aEmailOptions, 'name', $sSubmitSectionID)) {
            add_filter('wp_mail_from_name', array($this, '_replyToSetEmailSenderAddress'));
        }
        $_bSent = wp_mail($this->_getEmailArgument($aInput, $aEmailOptions, 'to', $sSubmitSectionID), $this->_getEmailArgument($aInput, $aEmailOptions, 'subject', $sSubmitSectionID), $_bIsHTML ? $this->getReadableListOfArrayAsHTML(( array )$this->_getEmailArgument($aInput, $aEmailOptions, 'message', $sSubmitSectionID)) : $this->getReadableListOfArray(( array )$this->_getEmailArgument($aInput, $aEmailOptions, 'message', $sSubmitSectionID)), $this->_getEmailArgument($aInput, $aEmailOptions, 'headers', $sSubmitSectionID), $this->_formatAttachements($this->_getEmailArgument($aInput, $aEmailOptions, 'attachments', $sSubmitSectionID)));
        remove_filter('wp_mail_content_type', array($this, '_replyToSetMailContentTypeToHTML'));
        remove_filter('wp_mail_from', array($this, '_replyToSetEmailSenderAddress'));
        remove_filter('wp_mail_from_name', array($this, '_replyToSetEmailSenderAddress'));
        foreach ($this->_aPathsToDelete as $_sPath) {
            unlink($_sPath);
        }
        return $_bSent;
    }
    private function _formatAttachements($asAttachments) {
        if (empty($asAttachments)) {
            return '';
        }
        $_aAttachments = $this->getAsArray($asAttachments);
        foreach ($_aAttachments as $_iIndex => $_sPathORURL) {
            if (is_file($_sPathORURL)) {
                continue;
            }
            if (false !== filter_var($_sPathORURL, FILTER_VALIDATE_URL)) {
                if ($_sPath = $this->_getPathFromURL($_sPathORURL)) {
                    $_aAttachments[$_iIndex] = $_sPath;
                    continue;
                }
            }
            unset($_aAttachments[$_iIndex]);
        }
        return $_aAttachments;
    }
    private function _getPathFromURL($sURL) {
        $_sPath = realpath(str_replace(get_bloginfo('url'), ABSPATH, $sURL));
        if ($_sPath) {
            return $_sPath;
        }
        $_sPath = $this->download($sURL, 10);
        if (is_string($_sPath)) {
            $this->_aPathsToDelete[$_sPath] = $_sPath;
            return $_sPath;
        }
        return '';
    }
    public function _replyToSetMailContentTypeToHTML($sContentType) {
        return 'text/html';
    }
    function _replyToSetEmailSenderAddress($sEmailSenderAddress) {
        return $this->_sEmailSenderAddress;
    }
    function _replyToSetEmailSenderName($sEmailSenderAddress) {
        return $this->_sEmailSenderName;
    }
    private function _getEmailArgument($aInput, array $aEmailOptions, $sKey, $sSectionID) {
        if (is_array($aEmailOptions[$sKey])) {
            return $this->getArrayValueByArrayKeys($aInput, $aEmailOptions[$sKey]);
        }
        if (!$aEmailOptions[$sKey]) {
            return $this->getArrayValueByArrayKeys($aInput, array($sSectionID, $sKey));
        }
        return $aEmailOptions[$sKey];
    }
}
