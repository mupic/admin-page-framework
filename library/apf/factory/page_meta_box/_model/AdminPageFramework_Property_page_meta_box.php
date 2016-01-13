<?php 
/**
	Admin Page Framework v3.7.10 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Property_page_meta_box extends AdminPageFramework_Property_post_meta_box {
    public $_sPropertyType = 'page_meta_box';
    public $aPageSlugs = array();
    public $oAdminPage;
    public $aHelpTabs = array();
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    public function __construct($oCaller, $sClassName, $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework', $sStructureType = 'page_meta_box') {
        unset($this->oAdminPage, $this->aHelpTabs);
        parent::__construct($oCaller, $sClassName, $sCapability, $sTextDomain, $sStructureType);
        $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] = $this->getElementAsArray($GLOBALS, array('aAdminPageFramework', 'aMetaBoxForPagesClasses'));
        $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'][$sClassName] = $oCaller;
    }
    protected function _getOptions() {
        return $this->oAdminPage->oProp->aOptions;
    }
    public function _getScreenIDOfPage($sPageSlug) {
        $_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug);
        return $_oAdminPage ? $_oAdminPage->oProp->aPages[$sPageSlug]['_page_hook'] . (is_network_admin() ? '-network' : '') : '';
    }
    public function isPageAdded($sPageSlug = '') {
        $_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug);
        return $_oAdminPage ? $_oAdminPage->oProp->isPageAdded($sPageSlug) : false;
    }
    public function isCurrentTab($sTabSlug) {
        $_sCurrentPageSlug = $this->getElement($_GET, 'page');
        if (!$_sCurrentPageSlug) {
            return false;
        }
        $_sCurrentTabSlug = $this->getElement($_GET, 'tab', $this->getDefaultInPageTab($_sCurrentPageSlug));
        return ($sTabSlug === $_sCurrentTabSlug);
    }
    public function getCurrentPageSlug() {
        return isset($_GET['page']) ? $_GET['page'] : '';
    }
    public function getCurrentTabSlug($sPageSlug) {
        $_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug);
        return $_oAdminPage ? $_oAdminPage->oProp->getCurrentTabSlug($sPageSlug) : '';
    }
    public function getCurretTab($sPageSlug) {
        return $this->getCurrentTabSlug($sPageSlug);
    }
    public function getDefaultInPageTab($sPageSlug) {
        if (!$sPageSlug) {
            return '';
        }
        return ($_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug)) ? $_oAdminPage->oProp->getDefaultInPageTab($sPageSlug) : '';
    }
    public function getOptionKey($sPageSlug) {
        if (!$sPageSlug) {
            return '';
        }
        return ($_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug)) ? $_oAdminPage->oProp->sOptionKey : '';
    }
    private function _getOwnerObjectOfPage($sPageSlug) {
        $_aPageClasses = $this->getElementAsArray($GLOBALS, array('aAdminPageFramework', 'aPageClasses'));
        foreach ($_aPageClasses as $_oAdminPage) {
            if ($_oAdminPage->oProp->isPageAdded($sPageSlug)) {
                return $_oAdminPage;
            }
        }
        return null;
    }
    public function __get($sName) {
        if ('oAdminPage' === $sName) {
            $this->oAdminPage = $this->_getOwnerObjectOfPage($_GET['page']);
            if (is_object($this->oAdminPage)) {
                $this->oAdminPage->oProp->bEnableForm = true;
            }
            return $this->oAdminPage;
        }
        if ('aHelpTabs' == $sName) {
            $this->aHelpTabs = $this->oAdminPage->oProp->aHelpTabs;
            return $this->aHelpTabs;
        }
        return parent::__get($sName);
    }
}
