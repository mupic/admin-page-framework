<?php 
/**
	Admin Page Framework v3.9.0b06 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_Model___FormatSectionset extends AdminPageFramework_Form_Utility {
    static public $aStructure = array('section_id' => '_default', 'page_slug' => null, 'tab_slug' => null, 'section_tab_slug' => null, 'title' => null, 'description' => null, 'capability' => null, 'if' => true, 'order' => null, 'help' => null, 'help_aside' => null, 'repeatable' => false, 'sortable' => false, 'attributes' => array('class' => null, 'style' => null, 'tab' => array(),), 'class' => array('tab' => array(),), 'hidden' => false, 'collapsible' => false, 'save' => true, 'content' => null, 'tip' => null, '_fields_type' => null, '_structure_type' => null, '_is_first_index' => false, '_is_last_index' => false, '_section_path' => '', '_section_path_array' => '', '_nested_depth' => 0, '_caller_object' => null, 'show_debug_info' => null,);
    public $aSectionset = array();
    public $sSectionPath = '';
    public $sStructureType = '';
    public $sCapability = 'manage_options';
    public $iCountOfElements = 0;
    public $oCaller = null;
    public $bShowDebugInfo = true;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSectionset, $this->sSectionPath, $this->sStructureType, $this->sCapability, $this->iCountOfElements, $this->oCaller, $this->bShowDebugInfo,);
        $this->aSectionset = $_aParameters[0];
        $this->sSectionPath = $_aParameters[1];
        $this->sStructureType = $_aParameters[2];
        $this->sCapability = $_aParameters[3];
        $this->iCountOfElements = $_aParameters[4];
        $this->oCaller = $_aParameters[5];
        $this->bShowDebugInfo = $_aParameters[6];
    }
    public function get() {
        $_aSectionPath = explode('|', $this->sSectionPath);
        $_aSectionset = $this->uniteArrays(array('_fields_type' => $this->sStructureType, '_structure_type' => $this->sStructureType, '_section_path' => $this->sSectionPath, '_section_path_array' => $_aSectionPath, '_nested_depth' => count($_aSectionPath) - 1,) + $this->aSectionset + array('capability' => $this->sCapability, 'show_debug_info' => $this->bShowDebugInfo,), self::$aStructure);
        $_aSectionset['order'] = $this->getAOrB(is_numeric($_aSectionset['order']), $_aSectionset['order'], $this->iCountOfElements + 10);
        $_oCollapsibleArgumentFormatter = new AdminPageFramework_Form_Model___Format_CollapsibleSection($_aSectionset['collapsible'], $_aSectionset['title']);
        $_aSectionset['collapsible'] = $_oCollapsibleArgumentFormatter->get();
        $_aSectionset['class'] = $this->getAsArray($_aSectionset['class']);
        $_aSectionset['_caller_object'] = $this->oCaller;
        return $_aSectionset;
    }
    }
    