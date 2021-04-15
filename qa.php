<?php
/*
Plugin Name: PS Q&A - WordPress Fragen und Antworten Plugin
Plugin URI: http://premium.psource.org/project/qa-wordpress-questions-and-answers-plugin
Description: Q&A ermöglichen es jeder WordPress-Site, einen voll ausgestatteten Bereich mit Fragen und Antworten zu haben - genau wie StackOverflow, Yahoo Answers, Quora und mehr...
Author: WMS N@W
Version: 1.4.7
Author URI: https://n3rds.work
Text Domain: qa
*/

/*

Authors - DerN3rd

Copyright 2020-2021 WMS N@W, (https://n3rds.work)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

require 'psource-plugin-update/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://n3rds.work//wp-update-server/?action=get_metadata&slug=ps-qa', 
	__FILE__, 
	'ps-qa' 
);
// Check if we already have Q&A or Q&A Lite installed and running
if ( !class_exists( 'QA_Core' ) ) {

	// The plugin version
	define( 'QA_VERSION', '1.4.7' );

	// The full url to the plugin directory
	define( 'QA_PLUGIN_URL', plugin_dir_url(__FILE__) );

	// The full path to the plugin directory
	define( 'QA_PLUGIN_DIR', plugin_dir_path(__FILE__) );

	// The text domain for strings localization
	define( 'QA_TEXTDOMAIN', 'qa' );

	// The key for the options array
	define( 'QA_OPTIONS_NAME', 'qa_options' );

	// The minimum number of seconds between two user posts
	if (!defined('QA_FLOOD_SECONDS')) define( 'QA_FLOOD_SECONDS', 10 );

	// Rewrite slugs
	if (!defined('QA_SLUG_ROOT')) define( 'QA_SLUG_ROOT','questions' );
	if (!defined('QA_SLUG_ASK')) define( 'QA_SLUG_ASK', 'ask' );
	if (!defined('QA_SLUG_EDIT')) define( 'QA_SLUG_EDIT', 'edit' );
	if (!defined('QA_SLUG_UNANSWERED')) define( 'QA_SLUG_UNANSWERED', 'unanswered' );
	if (!defined('QA_SLUG_TAGS')) define( 'QA_SLUG_TAGS', 'tags' );
	if (!defined('QA_SLUG_CATEGORIES')) define( 'QA_SLUG_CATEGORIES', 'categories' );
	if (!defined('QA_SLUG_USER')) define( 'QA_SLUG_USER', 'user' );

	// Reputation multipliers
	if (!defined('QA_ANSWER_ACCEPTED')) define( 'QA_ANSWER_ACCEPTED', 15 );
	if (!defined('QA_ANSWER_ACCEPTING')) define( 'QA_ANSWER_ACCEPTING', 2 );
	if (!defined('QA_ANSWER_UP_VOTE')) define( 'QA_ANSWER_UP_VOTE', 10 );
	if (!defined('QA_QUESTION_UP_VOTE')) define( 'QA_QUESTION_UP_VOTE', 5 );
	if (!defined('QA_DOWN_VOTE')) define( 'QA_DOWN_VOTE', -2 );
	if (!defined('QA_DOWN_VOTE_PENALTY')) define( 'QA_DOWN_VOTE_PENALTY', -1 );

	if (!defined('QA_DEFAULT_TEMPLATE_DIR')) define( 'QA_DEFAULT_TEMPLATE_DIR', 'default-templates' );

	global $qa_email_notification_content, $qa_email_notification_subject;

	$qa_email_notification_subject = "[SITE_NAME] New Question";  // SITE_NAME
	$qa_email_notification_content = "Hallo TO_USER,

	Eine neue Frage wurde auf SITE_NAME gestellt.

	QUESTION_TITLE

	QUESTION_DESCRIPTION

	Du hast die richtige Antwort und kannst weiterhelfen? Dann folge QUESTION_LINK.

	Danke,
	SITE_NAME";

	// Load plugin files
	include_once QA_PLUGIN_DIR . 'core/core.php';
	include_once QA_PLUGIN_DIR . 'core/answers.php';
	include_once QA_PLUGIN_DIR . 'core/edit.php';
	include_once QA_PLUGIN_DIR . 'core/votes.php';
	include_once QA_PLUGIN_DIR . 'core/subscriptions.php';
	include_once QA_PLUGIN_DIR . 'core/functions.php';
	include_once QA_PLUGIN_DIR . 'core/template-tags.php';
	include_once QA_PLUGIN_DIR . 'core/widgets.php';
	include_once QA_PLUGIN_DIR . 'core/ajax.php';
	include_once QA_PLUGIN_DIR . 'core/class.virtualpage.php';
	
	function qa_bp_integration() {
		include_once QA_PLUGIN_DIR . 'core/buddypress.php';
	}
	add_action( 'bp_loaded', 'qa_bp_integration' );

	if ( is_admin() ) {
		include_once QA_PLUGIN_DIR . 'core/admin.php';
	}
}
else {
	if ( is_multisite() )
		add_action( 'network_admin_notices', 'psource_qa_duplicate' );
	else
		add_action( 'admin_notices', 'psource_qa_duplicate' );
}

if ( !function_exists( 'psource_qa_duplicate' ) ) {
	function psource_qa_duplicate() {
		echo '<div class="error fade"><p>' .
			__("<b>[Q&A]</b> Es gibt bereits eine laufende Version von Q&A. Bitte überprüfe zuvor, ob Du Q&A oder Q&A Lite bereits installiert hast. Du musst die andere Version deaktivieren, um diese zu installieren und auszuführen.", QA_TEXTDOMAIN) .
			'</p></div>';

	}
}

if ( !function_exists( 'psource_qa_uninstall' ) ) {
	function psource_qa_uninstall() {
		remove_role( 'visitor' );
		/* Uninstall options only if Q&A Lite is not installed
		In other words, uninstall when Q&A is installed alone
		*/
		if ( !file_exists( WP_PLUGIN_DIR ."/qa-lite/qa-lite.php" ) ) {
			delete_option( 'qa_no_visit' );
			delete_option( 'qa_installed_version' );
			delete_option( 'qa_capabilties_set' );
			delete_option( QA_OPTIONS_NAME );
			delete_option( 'qa_email_notification_subject' );
			delete_option( 'qa_email_notification_content' );
		}
	}
}
register_uninstall_hook(  __FILE__ , 'psource_qa_uninstall' );