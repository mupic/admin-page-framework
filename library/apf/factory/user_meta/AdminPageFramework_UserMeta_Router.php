<?php
/**
 Admin Page Framework v3.7.9b02 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AdminPageFramework_UserMeta_Router extends AdminPageFramework_Factory {
    public function __construct($oProp) {
        parent::__construct($oProp);
        if ($this->oProp->bIsAdmin) {
            $this->oUtil->registerAction('current_screen', array($this, '_replyToDetermineToLoad'));
        }
    }
    public function _isInThePage() {
        if (!$this->oProp->bIsAdmin) {
            return false;
        }
        return in_array($this->oProp->sPageNow, array('user-new.php', 'user-edit.php', 'profile.php'));
    }
    public function _replyToDetermineToLoad() {
        if (!$this->_isInThePage()) {
            return;
        }
        $this->_setUp();
        $this->oUtil->addAndDoAction($this, "set_up_{$this->oProp->sClassName}", $this);
        add_action('show_user_profile', array($this, '_replyToPrintFields'));
        add_action('edit_user_profile', array($this, '_replyToPrintFields'));
        add_action('user_new_form', array($this, '_replyToPrintFields'));
        add_action('personal_options_update', array($this, '_replyToSaveFieldValues'));
        add_action('edit_user_profile_update', array($this, '_replyToSaveFieldValues'));
        add_action('user_register', array($this, '_replyToSaveFieldValues'));
    }
}