<?php
/*
Plugin Name: Binary MLM
Plugin URI: http://tradebooster.com
Description: The only Free Binary MLM plugin for Wordpress. Run a full blown MLM website from within your favourite CMS. 
Version: 1.0.0
Author: Tradebooster
Author URI: http://tradebooster.com
License: GPL
*/

// Exit if accessed directly
if(!defined('ABSPATH'))
	exit;
	
/** Constants *****************************************************************/
global $wpdb;

// Path and URL
if ( !defined( 'MLM_PLUGIN_DIR' ) )
	define( 'MLM_PLUGIN_DIR', WP_PLUGIN_DIR . '/mlm' );
	
//include the the core funcitons file
require_once(MLM_PLUGIN_DIR. '/mlm-constant.php');

//this file create or update database schema
require_once(MLM_PLUGIN_DIR. '/mlm_core/mlm-core-schema.php');

//include the html functions file
//this file create the registration form
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-registration-page.php');

//this file contain the binary network building code
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-view-network.php');

//this file contatain the overview of network sales like left, right and personal
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-network-details.php');

//this file contaian the left leg sales
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-left-group-details.php');

//this file contaian the right leg sales
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-right-group-details.php');

//this file contaian the personal or direct sales
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-personal-group-details.php');

//this file contain the grand total sales
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-total-sales.php');

//this file contain the payouts
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-my-payout-page.php');

//this file contain the payouts
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-my-payout-details-page.php');


//this file contatin the common functions which is used in other files
require_once(MLM_PLUGIN_DIR.'/common-functions.php');

//this is admin file that contain the creation of the top user of the network
require_once(MLM_PLUGIN_DIR. '/mlm_html/admin-create-first-user.php');

//in this file admin setup the mlm plugin settings
require_once(MLM_PLUGIN_DIR. '/mlm_html/admin-mlm-settings.php');

//in this file payout will be run
require_once( MLM_PLUGIN_DIR.'/mlm_html/admin-pay-cycle.php' );
 
//this file contain the user updadation profile
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-update-profile.php');

//this file user can reset own login passowrd
require_once(MLM_PLUGIN_DIR. '/mlm_html/mlm-change-password.php');

//in this file admin can change the user's profile details
require_once(MLM_PLUGIN_DIR. '/mlm_html/admin-user-update-profile.php');

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'mlm_install');

/* Runs wher plugin is deactivated */
register_deactivation_hook(__FILE__, 'mlm_remove');

//HOOK INTO WORDPRESS
add_action( 'init', 'register_shortcodes');

	

function mlm_install()
{
	mlm_core_install_users();
	mlm_core_install_leftleg();
	mlm_core_install_rightleg();
	mlm_core_install_country();
	mlm_core_insert_into_country();
	mlm_core_install_currency();
	mlm_core_insert_into_currency();
	mlm_core_install_bonus();
	mlm_core_install_commission();
	mlm_core_install_payout_master();
	mlm_core_install_payout();
	
	
	//this code add the registration page
	//1st agru is the TITLE & second is CONTENT
	$post_id = register_page(MLM_REGISTRATION_TITLE, MLM_REGISTRATIN_SHORTCODE);

	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
 		update_post_meta($post_id, 'mlm_registration_page', 'mlm_registration_page');

	//this code add the view binary network page
	$post_id = register_page(MLM_VIEW_NETWORK_TITLE, MLM_VIEW_NETWORK_SHORTCODE);

	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_network_page', 'mlm_network_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	//this code add the view binary network genealogy
	$post_id = register_page(MLM_VIEW_GENEALOGY_TITLE, MLM_VIEW_GENEALOGY_SHORTCODE);

	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_network_genealogy_page', 'mlm_network_genealogy_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	

	//this code add the network details page
	$post_id = register_page(MLM_NETWORK_DETAILS_TITLE, MLM_NETWORK_DETAILS_SHORTCODE);

	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_network_details_page', 'mlm_network_details_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	//this code add the my left group details page
	$post_id = register_page(MLM_LEFT_GROUP_DETAILS_TITLE, MLM_LEFT_GROUP_DETAILS_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_left_group_details_page', 'mlm_left_group_details_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	//this code add the my right group details page
	$post_id = register_page(MLM_RIGHT_GROUP_DETAILS_TITLE, MLM_RIGHT_GROUP_DETAILS_SHORTCODE);

	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_right_group_details_page', 'mlm_right_group_details_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}

	//this code add the my right group details page
	$post_id = register_page(MLM_PERSONAL_GROUP_DETAILS_TITLE, MLM_PERSONAL_GROUP_DETAILS_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_personal_group_details_page', 'mlm_personal_group_details_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	//this code add the my total consultant details page
	$post_id = register_page(MLM_MY_CONSULTANT_TITLE, MLM_MY_CONSULTANT_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_consultant_details_page', 'mlm_consultant_details_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	//this code add the my total consultant details page
	$post_id = register_page(MLM_MY_PAYOUTS, MLM_MY_PAYOUTS_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_my_payout_page', 'mlm_my_payout_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	
	//this code add the my total consultant details page
	$post_id = register_page(MLM_MY_PAYOUT_DETAILS, MLM_MY_PAYOUT_DETAILS_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_my_payout_details_page', 'mlm_my_payout_details_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	
	
	//this code add the update profile page
	$post_id = register_page(MLM_UPDATE_PROFILE_TITLE, MLM_UPDATE_PROFILE_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_update_profile_page', 'mlm_update_profile_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	//this code add the change password page
	$post_id = register_page(MLM_CHANGE_PASSWORD_TITLE, MLM_CHANGE_PASSWORD_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_change_password_page', 'mlm_change_password_page');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
	}
	
	/************** this block code for distributing bonus and payout *****************/
	/*$post_id = register_page(MLM_DISTRIBUTE_COMMISSION_TITLE, MLM_DISTRIBUTE_COMMISSION_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_distribute_commission', 'mlm_distribute_commission');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
		update_post_meta($post_id, '_mlm_restricted_to', 'administrator');
	}*/
	
/*	$post_id = register_page(MLM_DISTRIBUTE_BONUS_TITLE, MLM_DISTRIBUTE_BONUS_SHORTCODE);
	
	//if post is successully inserted then post_id inserted into wp_postmeta table
	if($post_id!=0)
	{
 		update_post_meta($post_id, 'mlm_distribute_bonus', 'mlm_distribute_bonus');
		update_post_meta($post_id, '_mlm_is_members_only', 'true');
		update_post_meta($post_id, '_mlm_restricted_to', 'administrator');
	}*/
	/************** end code for distributing bonus and payout *****************/
}

//shows custom message after plugin activation
add_action('admin_notices', 'show_message_after_plugin_activation');

function mlm_remove()
{
	mlm_core_drop_tables();
	//$mlmPages contain the meta_key of the created mlm plugin pages
	$mlmPages = array(
						'mlm_registration_page', 'mlm_network_page', 'mlm_network_genealogy_page','mlm_network_details_page', 'mlm_left_group_details_page',
						'mlm_right_group_details_page', 'mlm_personal_group_details_page', 'mlm_consultant_details_page',
						'mlm_my_payout_page','mlm_my_payout_details_page',
						'mlm_update_profile_page', 'mlm_change_password_page', 'mlm_distribute_bonus', 'mlm_distribute_commission'
					);
	//delete post from wp_posts and wp_postmeta table
	foreach($mlmPages as $value)
	{
		$post_id = get_post_id($value);
		wp_delete_post( $post_id, true );
	}
	
	//delete the data from wp_options table
	delete_option('wp_mlm_general_settings'); 
	delete_option('wp_mlm_eligibility_settings'); 
	delete_option('wp_mlm_payout_settings');
	delete_option('wp_mlm_bonus_settings');
	
	delete_option('menu_check');
	
	$theme_slug = get_option( 'stylesheet' );
	delete_option("theme_mods_$theme_slug");
	
	//delete the menu name form wp_terms table
	$term = get_term_by( 'name', MENU_NAME, 'nav_menu' ) ;
	wp_delete_term( $term->term_id, 'nav_menu');
}

if ( is_admin() )
{
	/* Call the html code */
	add_action('admin_menu', 'mlm_admin_menu');
}		

require_once('mlm-access-control.php');

// this action shows on the admin section registration form's custom fields and edit
add_action( 'show_user_profile', 'my_show_extra_profile_fields' ); // shows on user interface
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' ); // shows on admin interace
add_action( 'personal_options_update', 'my_save_extra_profile_fields' ); //apply on user interface
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' ); //apply on admin interface

//create nav menu and its item
$run_once = get_option('menu_check');
if (!$run_once)
{
	add_action('init', 'createTheMlmMenu');
}


/*Array*/
$paymenntStatusArr = array(0=>'Unpaid', 1=>'Paid');


add_action('init','load_javascript');



?>