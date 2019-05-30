<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides JavaScript scripts for repeatable individual fields.
 *
 * @since       3.0.0
 * @since       3.3.0       Extends `AdminPageFramework_Form_View___Script_Base`.
 * @package     AdminPageFramework/Common/Form/View/JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_RepeatableField extends AdminPageFramework_Form_View___Script_Base {

    /**
     * Returns an inline JavaScript script.
     *
     * @since       3.2.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */
    static public function getScript( /* $oMsg */ ) {

        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];
        $sCannotAddMore     = $_oMsg->get( 'allowed_maximum_number_of_fields' );
        $sCannotRemoveMore  = $_oMsg->get( 'allowed_minimum_number_of_fields' );

        return <<<JAVASCRIPTS
(function ( $ ) {
        
    /**
     * Bind field-repeating events to repeatable buttons for individual fields.
     * @remark      This method can be called from a fields container or a cloned field container.
     */
    $.fn.updateAdminPageFrameworkRepeatableFields = function( aSettings ) {
        
        var nodeThis            = this;
        // @todo check if this find() may be appropriate to determine the fields container when there are nested fields.   
        var _sFieldsContainerID = nodeThis.find( '.repeatable-field-add-button' ).first().data( 'id' );
        var _oFieldsContainer   = $( '#' + _sFieldsContainerID );
        
        /* Store the fields specific options */
        var _aOptions = $.extend({    
            // These are the defaults.
            max: 0, 
            min: 0,
            fadein: 500,
            fadeout: 500,
            disabled: false,    // 3.8.13+
			preserve_values: 0, // 3.8.19+
            }, aSettings );
        if ( ! _oFieldsContainer.data( 'repeatable' ) ) {
            _oFieldsContainer.data( 'repeatable', _aOptions );
        }   
        
        /* Set the option values in the data attributes so that when a section is repeated and creates a brand new field container, it can refer to the options */       
        var _oRepeatableButtons = $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' )
            .filter( function() {                                       
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
            });       
        _oRepeatableButtons.attr( 'data-max', _aOptions[ 'max' ] );
        _oRepeatableButtons.attr( 'data-min', _aOptions[ 'min' ] );
        _oRepeatableButtons.attr( 'data-fadein', _aOptions[ 'fadein' ] );
        _oRepeatableButtons.attr( 'data-fadeout', _aOptions[ 'fadeout' ] );
        _oRepeatableButtons.attr( 'data-preserve_values', _aOptions[ 'preserve_values' ] );
        
        /**
         * The Add button behavior - if the tag id is given, multiple buttons will be selected. 
         * Otherwise, a field node is given and a single button will be selected. 
         */
        var _oRepeatableAddButtons = $( nodeThis ).find( '.repeatable-field-add-button' );
             
        _oRepeatableAddButtons.unbind( 'click' );
        _oRepeatableAddButtons.click( function() {
// @todo event.preventDefault();        
            // 3.8.13+ 
            if ( $( this ).parent().data( 'disabled' ) ) {
                var _aDisabled = $( this ).parent().data( 'disabled' );
                tb_show( _aDisabled[ 'caption' ], $( this ).attr( 'href' ) );    
                return false;
            }        
        
            $( this ).addAdminPageFrameworkRepeatableField();
            return false; // will not click after that
        });

        /* The Remove button behavior */
        var _oRepeatableRemoveButton = $( nodeThis ).find( '.repeatable-field-remove-button' );
                              
        _oRepeatableRemoveButton.unbind( 'click' );
        _oRepeatableRemoveButton.click( function() {
// @todo event.preventDefault();        
            $( this ).removeAdminPageFrameworkRepeatableField();
            return false; // will not click after that
        });

        /* If the number of fields is less than the set minimum value, add fields. */
        var _sFieldID           = _oRepeatableAddButtons.first().closest( '.admin-page-framework-field' ).attr( 'id' );
        var _nCurrentFieldCount = $( '#' + _sFieldsContainerID ).find( '.admin-page-framework-field' )
            .filter( function() {                 
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
            })        
            .length;
        if ( _aOptions[ 'min' ] > 0 && _nCurrentFieldCount > 0 ) {
            if ( ( _aOptions[ 'min' ] - _nCurrentFieldCount ) > 0 ) {     
                $( '#' + _sFieldID ).addAdminPageFrameworkRepeatableField( _sFieldID );  
            }
        }
        
    };
    
    /**
     * Adds a repeatable field.
     * 
     * This method is called when the user presses the + repeatable button.
     */
    $.fn.addAdminPageFrameworkRepeatableField = function( sFieldContainerID ) {
        
        if ( 'undefined' === typeof sFieldContainerID ) {
            var sFieldContainerID = $( this ).closest( '.admin-page-framework-field' ).attr( 'id' );
        }

        var nodeFieldContainer  = $( '#' + sFieldContainerID );
        var nodeNewField        = nodeFieldContainer.clone(); // clone without bind events.
        var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );
        var _sFieldsContainerID = nodeFieldsContainer.attr( 'id' );

        var _aOptions = nodeFieldsContainer.data( 'repeatable' ); 

        // If the set maximum number of fields already exists, do not add.
        if ( ! _aOptions ) {     
            var _nodeButtonContainer = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' )
                .filter( function() {                 
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
                })
                .first();
            _aOptions = {
                max: _nodeButtonContainer.attr( 'data-max' ), // These are the defaults.
                min: _nodeButtonContainer.attr( 'data-min' ),
                fadein: _nodeButtonContainer.attr( 'data-fadein' ),
                fadeout: _nodeButtonContainer.attr( 'data-fadeout' ),                
                preserve_values: _nodeButtonContainer.attr( 'data-preserve_values' ), // 3.8.19                
            };               
        }  
       
        var _iFadein  = _aOptions[ 'fadein' ];
        var _iFadeout = _aOptions[ 'fadeout' ];

        // Show a warning message if the user tries to add more fields than the number of allowed fields.
        var sMaxNumberOfFields  = _aOptions[ 'max' ];
        var _oInnerFields       = nodeFieldsContainer.find( '.admin-page-framework-field' ) 
                                    .filter( function() {                 
                                        return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
                                    });
        var _oRepeatableButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' )
                                    .filter( function() {                 
                                        return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
                                    }); 
        if ( sMaxNumberOfFields != 0 && _oInnerFields.length >= sMaxNumberOfFields ) {
            var nodeLastRepeaterButtons = _oRepeatableButtons.last();
            var sMessage                = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + _sFieldsContainerID + '\" >' + sMessage + '</span>' );
            if ( nodeFieldsContainer.find( '#repeatable-error-' + _sFieldsContainerID ).length > 0 ) {
                nodeFieldsContainer.find( '#repeatable-error-' + _sFieldsContainerID ).replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;     
        }
        
        // Empty values.        
        if ( ! _aOptions[ 'preserve_values' ] ) {
            nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value     
            nodeNewField.find( 'input[type=checkbox]' ).prop( 'checked', false ); // uncheck checkboxes.        
        }  
        nodeNewField.find( '.repeatable-error' ).remove(); // remove error messages.
        
        // Add the cloned new field element.
        if ( _iFadein ) {
            nodeNewField
                .hide()
                .insertAfter( nodeFieldContainer )
                .delay( 100 )
                .fadeIn( _iFadein );
        } else {            
            nodeNewField.insertAfter( nodeFieldContainer );    
        }

        // 3.6.0+ Increment name and id attributes of the newly cloned field.
        _incrementFieldAttributes( nodeNewField, nodeFieldsContainer );
               
        /** 
         * Rebind the click event to the + and - buttons - important to update AFTER inserting the clone to the document node since the update method needs to count the fields. 
         * Also do this after updating the attributes since the script needs to check the last added id for repeatable field options such as 'min'.
         */
        nodeNewField.updateAdminPageFrameworkRepeatableFields();
        
        // It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone.
        nodeFieldContainer.find( 'input[type=radio][checked=checked]' ).prop( 'checked', 'checked' );
        
        // Call back the registered functions.
        
        // @deprecated 3.8.8 Kept for backward compatibility as some custom field types rely on this method.
        nodeNewField.trigger( 
            'admin-page-framework_added_repeatable_field', 
            [ 
                nodeNewField.data( 'type' ), // field type slug
                nodeNewField.attr( 'id' ),   // element tag id
                0, // call type // call type, 0 : repeatable fields, 1: repeatable sections, 2: nested repeatable fields.
                0, // section index - @todo find the section index
                0  // field index - @todo find the field index
            ]
        );
        
        // 3.8.8+ _nested and inline_mixed field types have nested fields. 
        // @todo check if this is okay as this applies to all inner fields including nested ones. 
        $( nodeNewField ).find( '.admin-page-framework-field' ).addBack().trigger( 
            'admin-page-framework_repeated_field', 
            [ 
                0, // call type, 0 : repeatable fields, 1: repeatable sections
                jQuery( nodeNewField ).closest( '.admin-page-framework-fields' )    // model container
            ]
        );
        
        // If more than one fields are created, show the Remove button.
        var nodeRemoveButtons = nodeFieldsContainer
            .find( '.repeatable-field-remove-button' )
            .filter( function() {                      
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;                
            })
        if ( nodeRemoveButtons.length > 1 ) { 
            nodeRemoveButtons.css( 'visibility', 'visible' ); 
        }

        // Display/hide delimiters.
        nodeFieldsContainer.children( '.admin-page-framework-field' ).children( '.delimiter' ).show().last().hide();
        
        // Return the newly created element. The media uploader needs this 
        return nodeNewField; 
        
    };
    
        /**
         * Increments digits in field attributes.
         * @since       3.8.0
         */
        var _incrementFieldAttributes = function( oElement, oFieldsContainer ) {
                
            var _iFieldCount            = Number( oFieldsContainer.attr( 'data-largest_index' ) );
            var _iIncrementedFieldCount = _iFieldCount + 1;
            oFieldsContainer.attr( 'data-largest_index', _iIncrementedFieldCount );
         
            var _sFieldTagIDModel    = oFieldsContainer.attr( 'data-field_tag_id_model' );
            var _sFieldNameModel     = oFieldsContainer.attr( 'data-field_name_model' );
            var _sFieldFlatNameModel = oFieldsContainer.attr( 'data-field_name_flat_model' );
            var _sFieldAddressModel  = oFieldsContainer.attr( 'data-field_address_model' );

            oElement.incrementAttribute(
                'id', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );
            oElement.find( 'label' ).incrementAttribute(
                'for', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );
            oElement.find( 'input,textarea,select,option' ).incrementAttribute(
                'id', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );       
            oElement.find( 'input,textarea,select' ).incrementAttribute(
                'name', // attribute name
                _iFieldCount, // increment from
                _sFieldNameModel // digit model
            );
            
            // Update the hidden input elements that contain field names for nested elements.
            oElement.find( 'input[type=hidden].element-address' ).incrementAttributes(
                [ 'name', 'value', 'data-field_address_model' ], // attribute names - these elements contain id values in the 'name' attribute.
                _iFieldCount,
                _sFieldAddressModel // digit model - this is
            );              
            
            // For checkbox, select, and radio input types
            oElement.find( 'input[type=radio][data-id],input[type=checkbox][data-id],select[data-id]' ).incrementAttribute(
                'data-id', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );                
            
            // 3.8 For nested repeatable fields
            oElement.find( '.admin-page-framework-field,.admin-page-framework-fields,.admin-page-framework-fieldset' ).incrementAttributes(
                [ 'id', 'data-field_tag_id_model', 'data-field_id' ],
                _iFieldCount,
                _sFieldTagIDModel
            );
            oElement.find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_name_model' ],
                _iFieldCount,
                _sFieldNameModel
            );            
            oElement.find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_name_flat', 'data-field_name_flat_model' ],
                _iFieldCount,
                _sFieldFlatNameModel
            );                 
            oElement.find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_address', 'data-field_address_model' ],
                _iFieldCount,
                _sFieldAddressModel
            );            
            
        }    
        
    
    /**
     * Removes a repeatable field.
      This method is called when the user presses the - repeatable button.
     */
    $.fn.removeAdminPageFrameworkRepeatableField = function() {
        
        /* Need to remove the element: the field container */
        var nodeFieldContainer  = $( this ).closest( '.admin-page-framework-field' );
        var nodeFieldsContainer = $( this ).closest( '.admin-page-framework-fields' );
        var _sFieldsContainerID = nodeFieldsContainer.attr( 'id' );
        var _aOptions = nodeFieldsContainer.data( 'repeatable' );
        
        /* If the set minimum number of fields already exists, do not remove */        
        var sMinNumberOfFields  = _aOptions  
            ? _aOptions[ 'min' ]
            : 0;
        var _oInnerFields        = nodeFieldsContainer.find( '.admin-page-framework-field' )
            .filter( function() {                                       
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
            });                   
        if ( sMinNumberOfFields != 0 && _oInnerFields.length <= sMinNumberOfFields ) {
            var _oRepeatableButtons     = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' )
                .filter( function() {                                       
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
                });            
            var nodeLastRepeaterButtons = _oRepeatableButtons.last();
            var sMessage                = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + _sFieldsContainerID + '\">' + sMessage + '</span>' );
            var _repeatableErrors       = nodeFieldsContainer.find( '#repeatable-error-' + _sFieldsContainerID )
                .filter( function() {                                       
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.                
                });
            if ( _repeatableErrors.length > 0 ) {
                _repeatableErrors.replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            var _iFadeout = _aOptions ? _aOptions[ 'fadeout' ] : 500;
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;     
        }     
        
        /* Remove the field */
        var _iFadeout = _aOptions ? _aOptions[ 'fadeout' ] : 500;
        nodeFieldContainer.fadeOut( _iFadeout, function() { 
            $( this ).remove(); 
            var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove-button' )           
                .filter( function() {                   
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;
                });            
            if ( 1 === nodeRemoveButtons.length ) { 
                nodeRemoveButtons.css( 'visibility', 'hidden' ); 
            }            
        } );
            
    };
        
}( jQuery ));
JAVASCRIPTS;

    }

}
