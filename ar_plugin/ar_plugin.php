<?php
/**
 *@package ar_plugin
 * Plugin Name: ar_plugin
 * Plugin URI: http://wordpress.org/plugins/
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Your Name
 * Author URI: http://www.mywebsite.com
 * License:
 * License URI:
 */
//security: blocking direct access to our plugin PHP files
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function ar_plugin_register_widgets(){
	register_widget('ar_widget');
}
function ar_widget_init()
{
	if (! class_exists('WooCommerce') ) {
	return;
	}
     
	require_once('ar_widget.php');
	add_action('widgets_init', 'ar_plugin_register_widgets');
}
add_action('plugins_loaded', 'ar_widget_init');