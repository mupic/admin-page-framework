<?php 
/**
	Admin Page Framework v3.8.30b02 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Model__FormSubmission__Validator__ContactForm extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 10;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory) {
        if (!$this->_shouldProceed($oFactory, $aSubmits)) {
            return;
        }
        $this->_sendEmailInBackground($aInputs, $this->getElement($aSubmitInformation, 'input_name'), $this->getElement($aSubmitInformation, 'section_id'));
        $this->oFactory->oProp->_bDisableSavingOptions = true;
        $this->deleteTransient('apf_tfd' . md5('temporary_form_data_' . $this->oFactory->oProp->sClassName . get_current_user_id()));
        add_action("setting_update_url_{$this->oFactory->oProp->sClassName}", array($this, '_replyToRemoveConfirmationQueryKey'));
        $_oException = new Exception('aReturn');
        $_oException->aReturn = $aInputs;
        throw $_oException;
    }
    protected function _shouldProceed($oFactory, $aSubmits) {
        if ($oFactory->hasFieldError()) {
            return false;
        }
        return ( bool )$this->_getPressedSubmitButtonData($aSubmits, 'confirmed_sending_email');
    }
    private function _sendEmailInBackground($aInputs, $sPressedInputNameFlat, $sSubmitSectionID) {
        $_sTranskentKey = 'apf_em_' . md5($sPressedInputNameFlat . get_current_user_id());
        $_aEmailOptions = $this->getTransient($_sTranskentKey);
        $this->deleteTransient($_sTranskentKey);
        $_aEmailOptions = $this->getAsArray($_aEmailOptions) + array('to' => '', 'subject' => '', 'message' => '', 'headers' => '', 'attachments' => '', 'is_html' => false, 'from' => '', 'name' => '',);
        $_sTransientKey = 'apf_emd_' . md5($sPressedInputNameFlat . get_current_user_id());
        $_aFormEmailData = array('email_options' => $_aEmailOptions, 'input' => $aInputs, 'section_id' => $sSubmitSectionID,);
        $_bIsSet = $this->setTransient($_sTransientKey, $_aFormEmailData, 100);
        wp_remote_get(add_query_arg(array('apf_action' => 'email', 'transient' => $_sTransientKey,), admin_url($GLOBALS['pagenow'])), array('timeout' => 0.01, 'sslverify' => false,));
        $_bSent = $_bIsSet;
        $this->oFactory->setSettingNotice($this->oFactory->oMsg->get($this->getAOrB($_bSent, 'email_scheduled', 'email_could_not_send')), $this->getAOrB($_bSent, 'updated', 'error'));
    }
    public function _replyToRemoveConfirmationQueryKey($sSettingUpdateURL) {
        return remove_query_arg(array('confirmation',), $sSettingUpdateURL);
    }
    }
    class AdminPageFramework_Model__FormSubmission__Validator__ContactFormConfirm extends AdminPageFramework_Model__FormSubmission__Validator__ContactForm {
        public $sActionHookPrefix = 'try_validation_after_';
        public $iHookPriority = 40;
        public $iCallbackParameters = 5;
        public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory) {
            if (!$this->_shouldProceed($oFactory, $aSubmits)) {
                return;
            }
            $this->oFactory->setLastInputs($aInputs);
            $this->oFactory->oProp->_bDisableSavingOptions = true;
            add_filter("options_update_status_{$this->oFactory->oProp->sClassName}", array($this, '_replyToSetStatus'));
            $_oException = new Exception('aReturn');
            $_oException->aReturn = $this->_confirmSubmitButtonAction($this->getElement($aSubmitInformation, 'input_name'), $this->getElement($aSubmitInformation, 'section_id'), 'email');
            throw $_oException;
        }
        protected function _shouldProceed($oFactory, $aSubmits) {
            if ($oFactory->hasFieldError()) {
                return false;
            }
            return ( bool )$this->_getPressedSubmitButtonData($aSubmits, 'confirming_sending_email');
        }
        public function _replyToSetStatus($aStatus) {
            return array('confirmation' => 'email') + $aStatus;
        }
    }
    