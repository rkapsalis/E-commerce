<?php
if(!defined( 'ABSPATH' )){
	exit;
} 

function arplugin_topViewedProducts_shortcode($atts){
      $a = shortcode_atts( 
      	array(
            'top_products' => '4',
      	),
      	$atts
      );

}
