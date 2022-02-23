<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_default extends AdminPageFramework_FieldType
{
    public $aDefaultKeys = array();
    public function _replyToGetField($aField)
    {
        return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ($aField['label'] && ! $aField['repeatable'] ? "<span " . $this->getLabelContainerAttributes($aField, 'admin-page-framework-input-label-string') . ">" . $aField[ 'label' ] . "</span>" : "") . $aField['value'] . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label'] ;
    }
}
