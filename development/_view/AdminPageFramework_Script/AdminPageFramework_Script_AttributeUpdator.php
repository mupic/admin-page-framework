<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_AttributeUpdator' ) ) :
/**
 * Provides JavaScript scripts to update attribute values.
 * 
 * @since 3.0.0     
 * @package AdminPageFramework
 * @subpackage JavaScript
 * @internal
 */
class AdminPageFramework_Script_AttributeUpdator {

    static public function getjQueryPlugin() {
        
        /**
         * Attribute increment/decrement jQuery Plugin
         */     
        return "(function ( $ ) {
        
            /**
             * Increments a first/last found digit with the prefix of underscore in a specified attribute value.
             * if the biFirstOccurence is false, the last found one will be replaced.
             */
            $.fn.incrementIDAttribute = function( sAttribute, biFirstOccurence ) {     
                return this.attr( sAttribute, function( iIndex, sValue ) {    
                    return updateID( iIndex, sValue, 1, biFirstOccurence );
                }); 
            };
            /**
             * Increments a first/last found digit enclosed in [] in a specified attribute value.
             */
            $.fn.incrementNameAttribute = function( sAttribute, biFirstOccurence ) {     
                return this.attr( sAttribute, function( iIndex, sValue ) {    
                    return updateName( iIndex, sValue, 1, biFirstOccurence );
                }); 
            };
    
            /**
             * Decrements a first/last found digit with the prefix of underscore in a specified attribute value.
             */
            $.fn.decrementIDAttribute = function( sAttribute, biFirstOccurence ) {
                return this.attr( sAttribute, function( iIndex, sValue ) {
                    return updateID( iIndex, sValue, -1, biFirstOccurence );
                }); 
            };     
            /**
             * Decrements a first/last found digit enclosed in [] in a specified attribute value.
             */
            $.fn.decrementNameAttribute = function( sAttribute, biFirstOccurence ) {
                return this.attr( sAttribute, function( iIndex, sValue ) {
                    return updateName( iIndex, sValue, -1, biFirstOccurence );
                }); 
            };     
            
            /* Sets the current index to the ID attribute. Used for sortable fields. */
            $.fn.setIndexIDAttribute = function( sAttribute, iIndex, biFirstOccurence ){
                return this.attr( sAttribute, function( i, sValue ) {
                    return updateID( iIndex, sValue, 0, biFirstOccurence );
                });
            };
            /* Sets the current index to the name attribute. Used for sortable fields. */
            $.fn.setIndexNameAttribute = function( sAttribute, iIndex, biFirstOccurence ){
                return this.attr( sAttribute, function( i, sValue ) {
                    return updateName( iIndex, sValue, 0, biFirstOccurence );
                });
            };     
            
            /* Local Function Literals */    
            var updateID = function( iIndex, sID, iIncrementType, biFirstOccurence ) {
                if ( 'undefined' === typeof sID ) { return sID; }
                var sNeedlePrefix   = ( typeof biFirstOccurence === 'undefined' ) || ! biFirstOccurence ? '(.+)': '(.+?)';
                var sNeedle         = new RegExp( sNeedlePrefix + '__(\\\d+)(?=([_-]|$))' ); // triple escape - not sure why but on a separate test script, double escape was working
                return sID.replace( sNeedle, function ( sFullMatch, m0, m1 ) {
                    if ( 1 === iIncrementType ) {
                        return m0 + '__' + ( Number( m1 ) + 1 );
                    } else if ( -1 === iIncrementType ) {
                        return m0 + '__' + ( Number( m1 ) - 1 );
                    } else {
                        return m0 + '__' + ( iIndex );
                    }
                });
            }
            var updateName = function( iIndex, sName, iIncrementType, biFirstOccurence ) {
                if ( 'undefined' === typeof sName ) { return sName; }
                var sNeedlePrefix   = ( typeof biFirstOccurence === 'undefined' ) || ! biFirstOccurence ? '(.+)': '(.+?)';
                var sNeedle         = biFirstOccurence === -1    
                    ? new RegExp( '(.+)' + '\\\[(\\\d+)(?=\\\].+\\\[\\\d+(?=\\\]))' ) // -1 is for the second occurrence from the last; for taxonomy field type
                    : new RegExp( sNeedlePrefix + '\\\[(\\\d+)(?=\\\])' ); // triple escape - not sure why but on a separate test script, double escape was working
                return sName.replace( sNeedle, function ( sFullMatch, m0, m1 ) {
                    if ( iIncrementType === 1 ) {
                        return m0 + '[' + ( Number( m1 ) + 1 );
                    } else if ( iIncrementType === -1 ) {
                        return m0 + '[' + ( Number( m1 ) - 1 );
                    } else {
                        return m0 + '[' + ( iIndex );
                    }
                });
            }
                
        }( jQuery ));";     
        
    }

}
endif;