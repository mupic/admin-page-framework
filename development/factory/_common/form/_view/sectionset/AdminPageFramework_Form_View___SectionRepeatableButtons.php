<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides JavaScript utility scripts.
 *
 * @since       3.9.0   Moved from `AdminPageFramework_Form_View___Script_RepeatableSection`.
 * @package     AdminPageFramework/Common/Form/View/Sectionset
 * @internal
 */
class AdminPageFramework_Form_View___SectionRepeatableButtons extends AdminPageFramework_Form_Utility {

    /**
     * Returns the repeatable section button outputs.
     *
     * @since       3.9.0
     * @return      string
     */
    static public function get( $sContainerTagID, $iSectionCount, $asArguments, $oMsg ) {

        if ( empty( $asArguments ) ) {
            return '';
        }
        if ( self::hasBeenCalled( 'repeatable_section_' . $sContainerTagID ) ) {
            return '';
        }

        $_oFormatter    = new AdminPageFramework_Form_Model___Format_RepeatableSection( $asArguments, $oMsg );
        $_aArguments    = $_oFormatter->get();
        $_aArguments[ 'id' ] = $sContainerTagID;
        $_sButtons      = self::___getRepeatableSectionButtons( $_aArguments, $oMsg, $sContainerTagID, $iSectionCount );
        return "<div class='hidden repeatable-section-buttons-model' " . self::getDataAttributes( $_aArguments ) . ">"
                . $_sButtons
            . "</div>";

    }
        /**
         * @return string
         * @since   3.8.22
         */
        static private function ___getRepeatableSectionButtons( $_aArguments, $oMsg, $sContainerTagID, $iSectionCount ) {
            $_sIconRemove   = '-';
            $_sIconAdd      = '+';
            if ( version_compare( $GLOBALS[ 'wp_version' ], '5.3', '>=' ) ) {
                $_sIconRemove   = "<span class='dashicons dashicons-minus'></span>";
                $_sIconAdd      = "<span class='dashicons dashicons-plus-alt2'></span>";
            }
            return "<div class='admin-page-framework-repeatable-section-buttons-outer-container'>"
                . "<div " . self::___getContainerAttributes( $_aArguments, $oMsg ) . ' >'
                    . "<a " . self::___getRemoveButtonAttributes( $sContainerTagID, $oMsg, $iSectionCount ) . ">"
                        . $_sIconRemove
                    . "</a>"
                    . "<a " . self::___getAddButtonAttributes( $sContainerTagID, $oMsg, $_aArguments ) . ">"
                        . $_sIconAdd
                    . "</a>"
                . "</div>"
            . "</div>"
            . AdminPageFramework_Form_Utility::getModalForDisabledRepeatableElement(
                    'repeatable_section_disabled_' . $sContainerTagID,
                    $_aArguments[ 'disabled' ]
                );
        }
        /**
         * @param   array  $aArguments
         * @param   AdminPageFramework_Message $oMsg
         * @return  string
         * @since   3.8.13
         */
        static private function ___getContainerAttributes( array $aArguments, $oMsg ) {
            $_aAttributes = array(
                'class' => self::getClassAttribute(
                    'admin-page-framework-repeatable-section-buttons',
                    empty( $aArguments[ 'disabled' ] ) ? '' : 'disabled'
                ),
            );
            unset( $aArguments[ 'disabled' ][ 'message' ] );    // this element can contain HTML tags.
            // Needs to remove it if it is empty as its data attribute will be checked in the JavaScript script.
            if ( empty( $aArguments[ 'disabled' ] ) ) {
                unset( $aArguments[ 'disabled' ] );
            }
            return self::getAttributes( $_aAttributes ) . ' ' . self::getDataAttributes( $aArguments );
        }
        /**
         * @return  string
         * @sicne   3.8.13
         */
        static private function ___getRemoveButtonAttributes( $sContainerTagID, $oMsg, $iSectionCount ) {
            return self::getAttributes(
                    array(
                    'class'     => 'repeatable-section-remove-button button-secondary '
                                   . 'repeatable-section-button button button-large',
                    'title'     => $oMsg->get( 'remove_section' ),
                    'style'     => $iSectionCount <= 1
                        ? 'display:none'
                        : null,
                    'data-id'   => $sContainerTagID,
                )
            );
        }

        /**
         * @since  3.8.13
         * @return string
         */
        static private function ___getAddButtonAttributes( $sContainerTagID, $oMsg, $aArguments ) {
            return self::getAttributes(
                array(
                    'class'     => 'repeatable-section-add-button button-secondary '
                        . 'repeatable-section-button button button-large',
                    'title'     => $oMsg->get( 'add_section' ),
                    'data-id'   => $sContainerTagID,
                    'href'      => ! empty( $aArguments[ 'disabled' ] )
                        ? '#TB_inline?width=' . $aArguments[ 'disabled' ][ 'box_width' ]
                            . '&height=' . $aArguments[ 'disabled' ][ 'box_height' ]
                            . '&inlineId=' . 'repeatable_section_disabled_' . $sContainerTagID
                        : null,
                )
            );
        }

}