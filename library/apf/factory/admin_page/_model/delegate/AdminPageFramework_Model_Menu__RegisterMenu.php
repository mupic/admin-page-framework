<?php 
/**
	Admin Page Framework v3.8.11b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Model_Menu__RegisterMenu extends AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public function __construct($oFactory, $sActionHook = 'admin_menu') {
        $this->oFactory = $oFactory;
        add_action($sActionHook, array($this, '_replyToRegisterRootMenu'), 98);
        add_action($sActionHook, array($this, '_replyToRegisterSubMenuItems'), 99);
        add_action($sActionHook, array($this, '_replyToRemoveRootMenuPage'), 100);
        add_action($sActionHook, array($this, 'sortAdminSubMenu'), 9999);
        $GLOBALS['_apf_sub_menus_to_sort'] = isset($GLOBALS['_apf_sub_menus_to_sort']) ? $GLOBALS['_apf_sub_menus_to_sort'] : array();
    }
    public function _replyToRegisterRootMenu() {
        if (!$this->oFactory->oProp->aRootMenu['fCreateRoot']) {
            return;
        }
        $this->_registerRootMenuPage();
    }
    private function _registerRootMenuPage() {
        $this->oFactory->oProp->aRootMenu['_page_hook'] = add_menu_page($this->oFactory->oProp->sClassName, $this->oFactory->oProp->aRootMenu['sTitle'], $this->oFactory->oProp->sCapability, $this->oFactory->oProp->aRootMenu['sPageSlug'], '', $this->oFactory->oProp->aRootMenu['sIcon16x16'], $this->getElement($this->oFactory->oProp->aRootMenu, 'iPosition', null));
    }
    public function _replyToRegisterSubMenuItems() {
        $_aPages = $this->addAndApplyFilter($this->oFactory, "pages_{$this->oFactory->oProp->sClassName}", $this->oFactory->oProp->aPages);
        $this->oFactory->oProp->sDefaultPageSlug = $this->_getDefaultPageSlug($_aPages);
        $_iParsedIndex = 0;
        $_aFormattedPages = array();
        foreach ($_aPages as $_aSubMenuItem) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuItem($_aSubMenuItem, $this->oFactory, ++$_iParsedIndex);
            $_aSubMenuItem = $_oFormatter->get();
            $_aSubMenuItem['_page_hook'] = $this->_registerSubMenuItem($_aSubMenuItem);
            $_sKey = isset($_aSubMenuItem['href']) ? $_aSubMenuItem['href'] : $_aSubMenuItem['page_slug'];
            $_aFormattedPages[$_sKey] = $_aSubMenuItem;
        }
        $this->oFactory->oProp->aPages = $_aFormattedPages;
    }
    private function _getDefaultPageSlug(array $aPages) {
        foreach ($aPages as $_aPage) {
            if (!isset($_aPage['page_slug'])) {
                continue;
            }
            return $_aPage['page_slug'];
        }
        return '';
    }
    private function _registerSubMenuItem(array $aArgs) {
        if (!current_user_can($aArgs['capability'])) {
            return '';
        }
        $_sRootPageSlug = $this->oFactory->oProp->aRootMenu['sPageSlug'];
        $_sRootMenuSlug = $this->_getRootMenuSlug($_sRootPageSlug);
        if ('page' === $aArgs['type']) {
            return $this->_addPageSubmenuItem($_sRootPageSlug, $_sRootMenuSlug, $aArgs['page_slug'], $this->getElement($aArgs, 'page_title', $aArgs['title']), $this->getElement($aArgs, 'menu_title', $aArgs['title']), $aArgs['capability'], $aArgs['show_in_menu'], $aArgs['order']);
        }
        if ('link' === $aArgs['type']) {
            return $this->_addLinkSubmenuItem($_sRootMenuSlug, $aArgs['title'], $aArgs['capability'], $aArgs['href'], $aArgs['show_in_menu'], $aArgs['order']);
        }
        return '';
    }
    private function _getRootMenuSlug($sRootPageSlug) {
        if (isset(self::$_aRootMenuSlugCache[$sRootPageSlug])) {
            return self::$_aRootMenuSlugCache[$sRootPageSlug];
        }
        self::$_aRootMenuSlugCache[$sRootPageSlug] = plugin_basename($sRootPageSlug);
        return self::$_aRootMenuSlugCache[$sRootPageSlug];
    }
    static private $_aRootMenuSlugCache = array();
    private function _addPageSubmenuItem($sRootPageSlug, $sMenuSlug, $sPageSlug, $sPageTitle, $sMenuTitle, $sCapability, $bShowInMenu, $nOrder) {
        if (!$sPageSlug) {
            return '';
        }
        $_sPageHook = add_submenu_page($sRootPageSlug, $sPageTitle, $sMenuTitle, $sCapability, $sPageSlug, array($this->oFactory, '_replyToRenderPage'));
        $this->_setPageHooks($_sPageHook, $sPageSlug);
        $_nSubMenuPageIndex = $this->_getSubMenuPageIndex($sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug);
        if (null === $_nSubMenuPageIndex) {
            return $_sPageHook;
        }
        $_aRemovedMenuItem = $this->_removePageSubmenuItem($_nSubMenuPageIndex, $sMenuSlug, $sPageSlug, $sMenuTitle);
        if (!$bShowInMenu && !$this->_isCurrentPage($sPageSlug)) {
            return $_sPageHook;
        }
        $this->_setSubMenuPageByIndex($nOrder, $_aRemovedMenuItem, $sMenuSlug);
        $GLOBALS['_apf_sub_menus_to_sort'][$sMenuSlug] = $sMenuSlug;
        return $_sPageHook;
    }
    private function _isCurrentPage($sPageSlug) {
        return isset($_GET['page']) && $sPageSlug === $_GET['page'];
    }
    private function _setPageHooks($sPageHook, $sPageSlug) {
        if (isset($this->oFactory->oProp->aPageHooks[$sPageHook])) {
            return;
        }
        add_action('current_screen', array($this->oFactory, "load_pre_" . $sPageSlug), 20);
        add_action("load_" . $sPageSlug, array($this->oFactory, '_replyToFinalizeInPageTabs'), 9999);
        add_action("load_after_" . $sPageSlug, array($this->oFactory, '_replyToEnqueuePageAssets'));
        add_action("load_after_" . $sPageSlug, array($this->oFactory, '_replyToEnablePageMetaBoxes'));
        $this->oFactory->oProp->aPageHooks[$sPageSlug] = $this->getAOrB(is_network_admin(), $sPageHook . '-network', $sPageHook);
    }
    private function _setSubMenuPageByIndex($nOrder, $aSubMenuItem, $sMenuSlug) {
        $_nNewIndex = $this->getUnusedNumericIndex($this->getElementAsArray($GLOBALS, array('submenu', $sMenuSlug)), $nOrder, 5);
        $GLOBALS['submenu'][$sMenuSlug][$_nNewIndex] = $aSubMenuItem;
    }
    private function _getSubMenuPageIndex($sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug) {
        foreach ($this->getElementAsArray($GLOBALS, array('submenu', $sMenuSlug)) as $_iIndex => $_aSubMenu) {
            if (!isset($_aSubMenu[3])) {
                continue;
            }
            $_aA = array($_aSubMenu[0], $_aSubMenu[3], $_aSubMenu[2],);
            $_aB = array($sMenuTitle, $sPageTitle, $sPageSlug,);
            if ($_aA !== $_aB) {
                continue;
            }
            return $_iIndex;
        }
        return null;
    }
    private function _removePageSubmenuItem($nSubMenuPageIndex, $sMenuSlug, $sPageSlug, $sMenuTitle) {
        $_aRemovedMenuItem = $this->_removePageSubMenuItemByIndex($nSubMenuPageIndex, $sMenuSlug, $sPageSlug);
        $this->oFactory->oProp->aHiddenPages[$sPageSlug] = $sMenuTitle;
        return $_aRemovedMenuItem;
    }
    private function _removePageSubMenuItemByIndex($_iIndex, $sMenuSlug, $sPageSlug) {
        $_aSubMenuItem = $this->getElementAsArray($GLOBALS, array('submenu', $sMenuSlug, $_iIndex));
        unset($GLOBALS['submenu'][$sMenuSlug][$_iIndex]);
        return $_aSubMenuItem;
    }
    private function _addLinkSubmenuItem($sMenuSlug, $sTitle, $sCapability, $sHref, $bShowInMenu, $nOrder) {
        if (!$bShowInMenu) {
            return;
        }
        $_aSubMenuItems = $this->getElementAsArray($GLOBALS, array('submenu', $sMenuSlug));
        $_nIndex = $this->getUnusedNumericIndex($_aSubMenuItems, $nOrder, 5);
        $_aSubMenuItems[$_nIndex] = array($sTitle, $sCapability, $sHref,);
        $GLOBALS['submenu'][$sMenuSlug] = $_aSubMenuItems;
        $GLOBALS['_apf_sub_menus_to_sort'][$sMenuSlug] = $sMenuSlug;
    }
    public function _replyToRemoveRootMenuPage() {
        if (!$this->oFactory->oProp->aRootMenu['fCreateRoot']) {
            return;
        }
        remove_submenu_page($this->oFactory->oProp->aRootMenu['sPageSlug'], $this->oFactory->oProp->aRootMenu['sPageSlug']);
    }
}
