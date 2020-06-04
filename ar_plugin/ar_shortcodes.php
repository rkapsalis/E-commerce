<?php
if(!defined( 'ABSPATH' )){
	exit;
} 

function arplugin_topViewedProducts_shortcode($atts){
      $atts = shortcode_atts( 
      	array(
            'top_products' => '4',
      	),
      	$atts
      );
     $content =  shortcode_create_topViewedProducts($atts['top_products']);
     return $content;
}
function arplugin_mostSimilarBought_shortcode($a){
      $a = shortcode_atts( 
      	array(
            'top_products' => '4',
      	),
      	$a
      );
     $content =  shortcode_create_mostSimilarBought($a['top_products']);
     return $content;
}
add_shortcode('ar_shortcode1', 'arplugin_topViewedProducts_shortcode');
add_shortcode('ar_shortcode2', 'arplugin_mostSimilarBought_shortcode');
