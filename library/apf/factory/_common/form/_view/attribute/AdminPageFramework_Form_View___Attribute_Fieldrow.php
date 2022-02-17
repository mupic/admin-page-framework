<?php 
/**
	Admin Page Framework v3.9.0b14 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<https://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_View___Attribute_Fieldrow extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {
    public $sContext = 'fieldrow';
    protected function _getFormattedAttributes() {
        $_aAttributes = parent::_getFormattedAttributes();
        if ($this->aArguments['hidden']) {
            $_aAttributes['style'] = $this->getStyleAttribute($this->getElement($_aAttributes, 'style', array()), 'display:none');
        }
        return $_aAttributes;
    }
}