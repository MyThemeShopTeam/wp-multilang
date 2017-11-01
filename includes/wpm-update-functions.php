<?php
/**
 * WP Multilang Updates
 *
 * Functions for updating data, used by updating.
 *
 * @category Core
 * @package  WPM/Functions
 */

use WPM\Includes\WPM_Install;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update language flag
 */
function wpm_update_180_flags() {
	$languages         = get_option( 'wpm_languages', array() );
	$updated_languages = array();

	foreach ( $languages as $locale => $language ) {
		$language['flag']             = $language['flag'] . '.png';
		$updated_languages[ $locale ] = $language;
	}

	update_option( 'wpm_languages', $updated_languages );
}

/**
 * Update DB Version.
 */
function wpm_update_180_db_version() {
	WPM_Install::update_db_version( '1.8.0' );
}

/**
 * Update date and time format foe languages.
 */
function wpm_update_178_datetime_format() {
	$languages         = get_option( 'wpm_languages', array() );
	$updated_languages = array();

	foreach ( $languages as $locale => $language ) {
		$language['date']             = $language['date'] ? $language['date'] : '';
		$language['time']             = $language['time'] ? $language['time'] : '';
		$updated_languages[ $locale ] = $language;
	}

	update_option( 'wpm_languages', $updated_languages );
}

/**
 * Update DB Version.
 */
function wpm_update_178_db_version() {
	WPM_Install::update_db_version( '1.7.8' );
}
