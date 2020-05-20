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

function getProductViews($productID, $uID){ //παιρνει απο την βαση τον counter για καθε προιον και για καθε χρηστη
    $count_key = 'product_views_count';
    $count = get_post_meta($productID, $count_key, true);
    if($count==''){
        delete_post_meta($productID, $count_key);
        add_post_meta($productID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

function getTopViewed($num_posts=4, $uID){ //ταξινομηση των most viewed products
	   $args = array(
            'posts_per_page' => num_posts,
            'post_status'    => 'publish',
            'post_type'      => 'product',            
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_key'       =>  $uID
	   );
	   $args['meta_query'] = array(
	   	   array(
             'key'     => $uID, 
             'value'   => '0',
             'type'    => 'numeric',
             'compare' => '>',
	   	   ),
	   );
    $ar_query = new WP_Query($args);
    return $ar_query;
}

// function to count views.
function setProductViews($productID, $uID) { //βαζει τον counter για καθε προιον και για καθε χρηστη
    $count_key = 'product_views_count';
    $count = get_post_meta($productID, $count_key, true);
    if($count==''){
        $count = 1;
        delete_post_meta($productID, $count_key);
        add_post_meta($productID, $count_key, '1');
    }else{
        $count++;
        update_post_meta($productID, $count_key, $count);
    }
}

function setViewInit(){ //δινουμε id χρηστη και προιοντος
	global $product;
	global $uID;
    $curProdID = $product->get_id(); //παιρνουμε το id του προιοντος
	$uID = get_current_user_id();  //παιρνουμε το id του χρηστη
	if($uID == 0){return;}         //αν δεν εχει κανει login
	setProductViews($curProdID, $uID);
}

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
	add_action('woocommerce_after_single_product','setViewInit'); //καλεί την setProductViews
}
add_action('plugins_loaded', 'ar_widget_init');
