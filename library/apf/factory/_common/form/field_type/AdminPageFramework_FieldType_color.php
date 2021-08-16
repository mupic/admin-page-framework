<?php 
/**
	Admin Page Framework v3.8.33 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_Form_Utility {
    public $_sFieldSetType = '';
    public $aFieldTypeSlugs = array('default');
    protected $aDefaultKeys = array();
    protected static $_aDefaultKeys = array('value' => null, 'default' => null, 'repeatable' => false, 'sortable' => false, 'label' => '', 'delimiter' => '', 'before_input' => '', 'after_input' => '', 'before_label' => null, 'after_label' => null, 'before_field' => null, 'after_field' => null, 'label_min_width' => '', 'before_fieldset' => null, 'after_fieldset' => null, 'field_id' => null, 'page_slug' => null, 'section_id' => null, 'before_fields' => null, 'after_fields' => null, 'attributes' => array('disabled' => null, 'class' => '', 'fieldrow' => array(), 'fieldset' => array(), 'fields' => array(), 'field' => array(),),);
    protected $oMsg;
    public function __construct($asClassName = 'admin_page_framework', $asFieldTypeSlug = null, $oMsg = null, $bAutoRegister = true) {
        $this->aFieldTypeSlugs = empty($asFieldTypeSlug) ? $this->aFieldTypeSlugs : ( array )$asFieldTypeSlug;
        $this->oMsg = $oMsg ? $oMsg : AdminPageFramework_Message::getInstance();
        if ($bAutoRegister) {
            foreach (( array )$asClassName as $_sClassName) {
                add_filter('field_types_' . $_sClassName, array($this, '_replyToRegisterInputFieldType'));
            }
        }
        $this->construct();
    }
    protected function construct() {
    }
    protected function isTinyMCESupported() {
        return version_compare($GLOBALS['wp_version'], '3.3', '>=') && function_exists('wp_editor');
    }
    protected function getElementByLabel($asElement, $asKey, $asLabel) {
        if (is_scalar($asElement)) {
            return $asElement;
        }
        return is_array($asLabel) ? $this->getElement($asElement, $this->getAsArray($asKey, true), '') : $asElement;
    }
    protected function getFieldOutput(array $aFieldset) {
        if (!is_object($aFieldset['_caller_object'])) {
            return '';
        }
        $aFieldset['_nested_depth']++;
        $aFieldset['_parent_field_object'] = $aFieldset['_field_object'];
        $_oCallerForm = $aFieldset['_caller_object'];
        $_oFieldset = new AdminPageFramework_Form_View___Fieldset($aFieldset, $_oCallerForm->aSavedData, $_oCallerForm->getFieldErrors(), $_oCallerForm->aFieldTypeDefinitions, $_oCallerForm->oMsg, $_oCallerForm->aCallbacks);
        return $_oFieldset->get();
    }
    protected function geFieldOutput(array $aFieldset) {
        return $this->getFieldOutput($aFieldset);
    }
    public function _replyToRegisterInputFieldType($aFieldDefinitions) {
        foreach ($this->aFieldTypeSlugs as $sFieldTypeSlug) {
            $aFieldDefinitions[$sFieldTypeSlug] = $this->getDefinitionArray($sFieldTypeSlug);
        }
        return $aFieldDefinitions;
    }
    public function getDefinitionArray($sFieldTypeSlug = '') {
        $_aDefaultKeys = $this->aDefaultKeys + self::$_aDefaultKeys;
        $_aDefaultKeys['attributes'] = isset($this->aDefaultKeys['attributes']) && is_array($this->aDefaultKeys['attributes']) ? $this->aDefaultKeys['attributes'] + self::$_aDefaultKeys['attributes'] : self::$_aDefaultKeys['attributes'];
        return array('sFieldTypeSlug' => $sFieldTypeSlug, 'aFieldTypeSlugs' => $this->aFieldTypeSlugs, 'hfRenderField' => array($this, "_replyToGetField"), 'hfGetScripts' => array($this, "_replyToGetScripts"), 'hfGetStyles' => array($this, "_replyToGetStyles"), 'hfGetIEStyles' => array($this, "_replyToGetInputIEStyles"), 'hfFieldLoader' => array($this, "_replyToFieldLoader"), 'hfFieldSetTypeSetter' => array($this, "_replyToFieldTypeSetter"), 'hfDoOnRegistration' => array($this, "_replyToDoOnFieldRegistration"), 'aEnqueueScripts' => $this->_replyToGetEnqueuingScripts(), 'aEnqueueStyles' => $this->_replyToGetEnqueuingStyles(), 'aDefaultKeys' => $_aDefaultKeys,);
    }
    public function _replyToGetField($aField) {
        return '';
    }
    public function _replyToGetScripts() {
        return '';
    }
    public function _replyToGetInputIEStyles() {
        return '';
    }
    public function _replyToGetStyles() {
        return '';
    }
    public function _replyToFieldLoader() {
    }
    public function _replyToFieldTypeSetter($sFieldSetType = '') {
        $this->_sFieldSetType = $sFieldSetType;
    }
    public function _replyToDoOnFieldRegistration($aField) {
    }
    protected function _replyToGetEnqueuingScripts() {
        return array();
    }
    protected function _replyToGetEnqueuingStyles() {
        return array();
    }
    protected function enqueueMediaUploader() {
        add_filter('media_upload_tabs', array($this, '_replyToRemovingMediaLibraryTab'));
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        if (function_exists('wp_enqueue_media')) {
            new AdminPageFramework_Form_View___Script_MediaUploader($this->oMsg);
        } else {
            wp_enqueue_script('media-upload');
        }
        if (in_array($this->getPageNow(), array('media-upload.php', 'async-upload.php',))) {
            add_filter('gettext', array($this, '_replyToReplaceThickBoxText'), 1, 2);
        }
    }
    public function _replyToReplaceThickBoxText($sTranslated, $sText) {
        if (!in_array($this->getPageNow(), array('media-upload.php', 'async-upload.php'))) {
            return $sTranslated;
        }
        if ($sText !== 'Insert into Post') {
            return $sTranslated;
        }
        if ($this->getQueryValueInURLByKey(wp_get_referer(), 'referrer') !== 'admin_page_framework') {
            return $sTranslated;
        }
        if (isset($_GET['button_label'])) {
            return $this->getHTTPQueryGET('button_label', '');
        }
        return $this->oMsg->get('use_this_image');
    }
    public function _replyToRemovingMediaLibraryTab($aTabs) {
        if (!isset($_REQUEST['enable_external_source'])) {
            return $aTabs;
        }
        if (!( boolean )$_REQUEST['enable_external_source']) {
            unset($aTabs['type_url']);
        }
        return $aTabs;
    }
    protected function getLabelContainerAttributes($aField, $asClassAttributes, array $aAttributes = array()) {
        $aAttributes['class'] = $this->getClassAttribute($asClassAttributes, $this->getElement($aAttributes, 'class'));
        $aAttributes['style'] = $this->getStyleAttribute(array('min-width' => $aField['label_min_width'] || '0' === ( string )$aField['label_min_width'] ? $this->getLengthSanitized($aField['label_min_width']) : null,), $this->getElement($aAttributes, 'style'));
        return $this->getAttributes($aAttributes);
    }
    }
    abstract class AdminPageFramework_FieldType extends AdminPageFramework_FieldType_Base {
        public function _replyToFieldLoader() {
            $this->setUp();
        }
        public function _replyToGetScripts() {
            return $this->getScripts();
        }
        public function _replyToGetInputIEStyles() {
            return $this->getIEStyles();
        }
        public function _replyToGetStyles() {
            return $this->getStyles();
        }
        public function _replyToGetField($aField) {
            return $this->getField($aField);
        }
        public function _replyToDoOnFieldRegistration($aField) {
            return $this->doOnFieldRegistration($aField);
        }
        protected function _replyToGetEnqueuingScripts() {
            return $this->getEnqueuingScripts();
        }
        protected function _replyToGetEnqueuingStyles() {
            return $this->getEnqueuingStyles();
        }
        public $aFieldTypeSlugs = array('default',);
        protected $aDefaultKeys = array();
        protected function construct() {
        }
        protected function setUp() {
        }
        protected function getScripts() {
            return '';
        }
        protected function getIEStyles() {
            return '';
        }
        protected function getStyles() {
            return '';
        }
        protected function getField($aField) {
            return '';
        }
        protected function getEnqueuingScripts() {
            return array();
        }
        protected function getEnqueuingStyles() {
            return array();
        }
        protected function doOnFieldRegistration($aField) {
        }
    }
    class AdminPageFramework_FieldType_color extends AdminPageFramework_FieldType {
        public $aFieldTypeSlugs = array('color');
        protected $aDefaultKeys = array('attributes' => array('size' => 10, 'maxlength' => 400, 'value' => 'transparent',),);
        protected function setUp() {
            if (version_compare($GLOBALS['wp_version'], '3.5', '>=')) {
                $this->___enqueueWPColorPicker();
            } else {
                wp_enqueue_style('farbtastic');
                wp_enqueue_script('farbtastic');
            }
        }
        private function ___enqueueWPColorPicker() {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            if (!is_admin()) {
                wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
                wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false, 1);
                wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'), 'current' => __('Current Color'),));
            }
        }
        protected function getStyles() {
            return ".repeatable .colorpicker {display: inline;}.admin-page-framework-field-color .wp-picker-container {vertical-align: middle;}.admin-page-framework-field-color .ui-widget-content {border: none;background: none;color: transparent;}.admin-page-framework-field-color .ui-slider-vertical {width: inherit;height: auto;margin-top: -11px;}.admin-page-framework-field-color .admin-page-framework-repeatable-field-buttons {margin-top: 0;}.admin-page-framework-field-color .wp-color-result {margin: 3px;}";
        }
        protected function getScripts() {
            $_aJSArray = json_encode($this->aFieldTypeSlugs);
            $_sDoubleQuote = '\"';
            return <<<JAVASCRIPTS
registerAdminPageFrameworkColorPickerField = function( osTragetInput, aOptions ) {
    
    var osTargetInput   = 'string' === typeof osTragetInput 
        ? '#' + osTragetInput 
        : osTragetInput;
    var sInputID        = 'string' === typeof osTragetInput 
        ? osTragetInput 
        : osTragetInput.attr( 'id' );

    // Only for the iris color picker.
    var _aDefaults = {
        defaultColor: false, // you can declare a default color here, or in the data-default-color attribute on the input     
        change: function( event, ui ){
            jQuery( this ).trigger( 
                'admin-page-framework_field_type_color_changed',
                [ jQuery( this ), sInputID ]
            ); 
        }, // a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/     
        clear: function( event, ui ) {
            jQuery( this ).trigger(
                'admin-page-framework_field_type_color_cleared',
                [ jQuery( '#' + sInputID ), sInputID ]
            );            
        }, // a callback to fire when the input is emptied or an invalid color
        hide: true, // hide the color picker controls on load
        palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further                
    };
    var _aColorPickerOptions = jQuery.extend( {}, _aDefaults, aOptions );
        
    'use strict';
    /* This if-statement checks if the color picker element exists within jQuery UI
     If it does exist, then we initialize the WordPress color picker on our text input field */
    if( 'object' === typeof jQuery.wp && 'function' === typeof jQuery.wp.wpColorPicker ){
        jQuery( osTargetInput ).wpColorPicker( _aColorPickerOptions );
    }
    else {
        /* We use farbtastic if the WordPress color picker widget doesn't exist */
        jQuery( '#color_' + sInputID ).farbtastic( osTargetInput );
    }
}

/* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
    renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
jQuery( document ).ready( function(){
        
    jQuery().registerAdminPageFrameworkCallbacks( {     
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
                        
            oCloned.find( 'input.input_color' ).each( function( iIterationIndex ) {
                
                var _oNewColorInput = jQuery( this );
                var _oIris          = _oNewColorInput.closest( '.wp-picker-container' );
                // WP 3.5+
                if ( _oIris.length > 0 ) { 
                    // unbind the existing color picker script in case there is.
                    var _oNewColorInput = _oNewColorInput.clone(); 
                }                    
                var _sInputID       = _oNewColorInput.attr( 'id' );
                
                // Reset the value of the color picker.
                var _sInputValue    = _oNewColorInput.val() 
                    ? _oNewColorInput.val() 
                    : _oNewColorInput.attr( 'data-default' );
                var _sInputStyle = _sInputValue !== 'transparent' && _oNewColorInput.attr( 'style' )
                    ? _oNewColorInput.attr( 'style' ) 
                    : '';
                _oNewColorInput.val( _sInputValue ); // set the default value    
                _oNewColorInput.attr( 'style', _sInputStyle ); // remove the background color set to the input field ( for WP 3.4.x or below )  

                // Replace the old color picker elements with the new one.
                // WP 3.5+
                if ( _oIris.length > 0 ) { 
                    jQuery( _oIris ).replaceWith( _oNewColorInput );
                } 
                // WP 3.4.x -     
                else { 
                    oCloned.find( '.colorpicker' )
                        .replaceWith( '<div class=\"colorpicker\" id=\"color_' + _sInputID + '\"></div>' );
                }

                // Bind the color picker event.
                registerAdminPageFrameworkColorPickerField( _oNewColorInput );                
            
            } );                   
        },    
    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;
            
        }
        protected function getField($aField) {
            $aField['value'] = is_null($aField['value']) ? 'transparent' : $aField['value'];
            $aField['attributes'] = $this->_getInputAttributes($aField);
            return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ($aField['label'] && !$aField['repeatable'] ? "<span " . $this->getLabelContainerAttributes($aField, 'admin-page-framework-input-label-string') . ">" . $aField['label'] . "</span>" : "") . "<input " . $this->getAttributes($aField['attributes']) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "<div class='colorpicker' id='color_{$aField['input_id']}'></div>" . $this->_getColorPickerEnablerScript("{$aField['input_id']}") . "</div>" . $aField['after_label'];
        }
        private function _getInputAttributes(array $aField) {
            return array('color' => $aField['value'], 'value' => $aField['value'], 'data-default' => isset($aField['default']) ? $aField['default'] : 'transparent', 'type' => 'text', 'class' => trim('input_color ' . $aField['attributes']['class']),) + $aField['attributes'];
        }
        private function _getColorPickerEnablerScript($sInputID) {
            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    registerAdminPageFrameworkColorPickerField( '{$sInputID}' );
});            
JAVASCRIPTS;
            return "<script type='text/javascript' class='color-picker-enabler-script'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
        }
    }
    