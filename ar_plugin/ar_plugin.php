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
if (! function_exists ('wcckplugin_has_parent_plugin') ) {
  function wcckplugin_has_parent_plugin() {
    if ( is_admin() && ( ! class_exists ('WooCommerce') && current_user_can( 'activate_plugins') ) ) {
      add_action ('admin_notices', create_function( null, 'echo \'<div class="error"><p>\' . sprintf( _(\'Activation failes : <strong> WooCommerce</strong> must be activated to use the <strong>WooCommerce ck </strong> plugin. %sVisit your plugins page to install and activate.\',\'ckPlugin\'),\'<a href="\' . admin_url(\'plugins.php#woocommerce\' ) .\'">\') . \'</a></p></div>\';') );
      deactivate_plugins (plugin_basename (_FILE_));
      if (isset ($_GET['activate'] ) ) {
        unset ($_GET['activate'] );
      }
    }
  }
}

function getProductViews($productID, $uID){ //παιρνει απο την βαση τον counter για καθε προιον και για καθε χρηστη
    $count_key = $uID;
    $count = get_post_meta($productID, $count_key, true); 
    if($count==''){
        delete_post_meta($productID, $count_key);
        add_post_meta($productID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

function getTopViewed($num_posts, $uID, $chuck_pur){ //ταξινομηση των most viewed products
	var_dump($num_posts);
	   $count_key = $uID;
	   $args = array(
            'posts_per_page' =>  4,
            'no_found_rows'  =>  1,
            'post_status'    => 'publish',
            'post_type'      => 'product', 
            'post__not_in'   =>  $chuck_pur,         
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_key'       =>  $count_key,
	   );
	   $args['meta_query'] = array(
	   	   array(
             'key'     => $count_key, 
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
    $count_key = $uID;
    $count = get_post_meta($productID, $count_key, true);
   
    if($count ==''){
        $count = 1;
          echo "add";
        delete_post_meta($productID, $count_key);
        add_post_meta($productID, $count_key, '1');
      
    }else{
        $count++;
        echo "update";
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


function shortcode_create_topViewedProducts($num_posts ){ //show top viewed products
	
	global $uID;
	$uID = get_current_user_id(); //get current user id
  $chuck_pur = validate_top_products();  //παρε τα προιοντα που θες να κανεις exclude
  $top_prod = getTopViewed($num_posts,$uID,$chuck_pur); //get top products
    
    if($top_prod == null){
       echo "no products found";
    }
    else{
    
    	$anna='Top viewed products';
        $result .='<head><div class="section-title"><h2><font size ="5">'.$anna.' </font></h2></div></head>';

    	while($top_prod->have_posts()){
    		$top_prod->the_post();    		 
    		global $product;
           
            $views = getProductViews($product->id, $uID);

            $i_url = wp_get_attachment_image_src(get_post_thumbnail_id($product->id),$size="thumbnail");
            $prod = get_permalink();
            
          
	    	$result .='</body>
	    		<div class = "active" style="width: 254.667px; margin-right:10px; display:inline-block; margin-bottom:20px; ">

		    		<a class="carousel-item" href="'.$prod.'" target="">
			    		<div class="teaser-image">
			    			<img class= "img-responsive" data-src="htteps://" width="124" height="124" src="'.$i_url[0].'" style="opacity:1;" sizes="(max-width:124px) 100vw, 124px">
			    		</div>
		    		</a>
		    		<span><font size ="2">
		    		'.$product->get_price_html().' <br>
		    		</span></font>
		    		<span><font size ="2">
		    		'.$views.' 
		    		</span></font>
		    		
		    		<div >
               
                     
	    		    </div>

	    		
					';			
		 
		  
    	}
    	 $result.='
		   </div>
		   </body>

		   ';
		  
    	
    }
     return $result;
    wp_reset_postdata(); //ensures that the global $post has been restored to the current post in the main query.
  
}


function matched_cart_items($top_prod) { //προιοντα που δεν ειναι στο καλαθι
  
  global $uID;
  global $woocommerce;

 $uID = get_current_user_id();  //get current user id
  
    if (  WC()->cart->cart_contents_count != 0 ) { //αν το καλαθι δεν ειναι αδειο
        
        foreach(WC()->cart->get_cart() as $cart_item ) { //για καθε προιον που υπαρχει στο καλαθι                
          
           $top_prod[] = $cart_item['product_id']; //αποθηκευουμε τα ids των προιοντων που υπαρχουν στο καλαθι      
                   
     }  
    
  } 

  $top_prod = array_unique($top_prod); //βγάλε τα διπλοτυπα  
  return $top_prod;
}


function validate_top_products(){ //ελέγχουμε αν το προιον εχει αγοραστει απο εναν συγκεκριμενο χρηστη ή αν υπαρχει στο καλαθι 
	
  global $uID;
  $exProdID = array();
  $uID = get_current_user_id(); //get current user id
  $args = array(
    'customer_id' => $uID
   );
   $orders = wc_get_orders($args); //παρε ολες τις παραγγελιες απο το συγκεκριμενο χρηστη
 
   foreach($orders as $order){ //για καθε παραγγελια
     $items = $order->get_items();
     foreach ( $items as $item ) {
       
        $product_id = $item->get_product_id(); //παρε το product id
        $exProdID[] = $product_id; //και προσθεσε το στον πινακα exProdID
        
     }
 }
 $exProdID = matched_cart_items($exProdID); //παιρνουμε τα μοναδικά προιοντα που δεν εχει αγοράσει ο χρήστης και δεν ειναι στο καλάθι του
 return $exProdID;
}



//---------------------WIDGET 2--------------------


function get_similarBought($category, $products, $num_posts){
  $anna1 = array() ;
  $temp_cat = array();
  $temp_prod = array();
  $product_cats =array();
  
	$args1 = array(
	   'post_type'      => 'product',
     'post_status'	  => 'publish',   
	   'posts_per_page' =>  -1,
	   'meta_key'       => 'total_sales',    
     'post__not_in'   => $products,
	   'orderby'        => 'meta_value_num',
	   'order'          => 'DESC',
     'meta_query' => array(
            array(
             'key' => 'total_sales',
            'value' => 0,
            'compare' => '>'
            ),
      ),
     'tax_query'   =>  array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    =>  $category,
        ),     
        
     )	  
	   
	);
   $ar_query2 = new WP_Query($args1);    
   $posts = $ar_query2->posts;

   // foreach ($category as $cat ) {
   //  var_dump($cat);
    //foreach ($posts as $post) {
  //while($ar_query2->have_posts()){
    // $product_cats = wp_get_post_terms($post->ID,'product_cat');

    
      //$anna2 = $post->ID;
      //global $product
      //var_dump($product->id);
      //var_dump($anna2->get_category());
      //$terms = get_the_terms( $post->ID, 'product_cat' );
    //  if($terms == $cat){
      //  echo "anna";
     // }
      //var_dump($terms);
     //}
     // var_dump(get_field('category'));
  // }
  // var_dump($product_cats)
   
 //  foreach($posts as $post){  
   
 //  	// foreach ($ as $key => $value) {
 //   //    # code...
 //   //  }
 //  // // 	 $units_sold1 = get_post_meta( $post->ID, 'total_sales', true );  	 
 //    global $product;
 //   	
         
 //  if (is_array($terms) || is_object($terms))
 //     {
	//   	foreach ($terms as $term) { //παιρνουμε την κατηγορια του προιοντος
	// 		   $anna1[] = $term->slug;
         
	// 	 	}
 //  //     $anna1 = array_unique($anna1);
      
 //    	}  

 // }
 // var_dump($anna1);
 //      var_dump($terms); 
//   $customer_orders = get_posts( array(
//     'numberposts' => -1,
//     'meta_key'    => '_customer_user',
//     'meta_value'  => get_current_user_id(),
//     'post_type'   => wc_get_order_types(),
//     'post_status' => array_keys( wc_get_order_statuses() ),
// ) );
 wp_reset_postdata();
 //print_r($ar_query2);
return $ar_query2;
  
}

function user_cartItems($num_posts) { //προιοντα που ειναι στο καλαθι
  global $uID; 
  global $woocommerce;

   $uID = get_current_user_id(); //get current user id
   $prodID = array();
   $product_cat = array();
  
    if (  WC()->cart->cart_contents_count != 0 ) { //αν το καλαθι δεν ειναι αδειο
        
        foreach(WC()->cart->get_cart() as $cart_item ) { //για καθε προιον που υπαρχει στο καλαθι
             	      
  	       $prodID[] = $cart_item['product_id']; //προσθέτουμε στον πίνακα τα id των προιοντων που υπάρχουν στο καλαθι 	   
           $terms = get_the_terms( $cart_item['product_id'], 'product_cat' ); //παίρνουμε για κάθε προιον την κατηγορία στην οποία ανήκει
           //δε θέλουμε τις γονικές κατηγορίες
           $slug_size = sizeof($terms); 
           //ελέγχουμε αν είναι γονική κατηγορία και αποθηκεύουμε στον πίνακα product_cat το slug του προιοντος
           var_dump($slug_size);
            //var_dump($terms);
         if($slug_size>1){ 
         
           $product_cat[] = $terms[$slug_size-1]->slug;
         }
         else{
          $product_cat[] = $terms[0]->slug;
         }			
        	         
     }     
   
     $product_cat = array_unique($product_cat); //βγάλε τα διπλοτυπα
     var_dump($product_cat);
  }
  $overal_prodID = get_userOrders($prodID); //παιρνουμε τα μοναδικά προιοντα που δεν εχει αγοράσει ο χρήστης και δεν ειναι στο καλάθι του
  $finalProd = get_similarBought($product_cat, $overal_prodID, $num_posts);
 
  return $finalProd;
 
}


function get_userOrders($prodID){
	global $uID;
	$uID = get_current_user_id(); //get current user id

	$args = array(
    'customer_id' => $uID
   );
   $orders = wc_get_orders($args); //παρε ολες τις παραγγελιες απο το συγκεκριμενο χρηστη
 
   foreach($orders as $order){ //για καθε παραγγελια
	   $items = $order->get_items();
	   foreach ( $items as $item ) {
		   
		    $product_id = $item->get_product_id(); //παρε το product id
		    $prodID[] = $product_id; //και βάλτο στον αντίστοιχο πινακα
		   
	   }
 }
 $prodID = array_unique($prodID); //έλεγχος για διπλοτυπα
 return $prodID;
}


function shortcode_create_mostSimilarBought($num_posts){  
      $temp_cat = array();
      $temp_prod = array();
     $top_bought = user_cartItems($num_posts); 
     while($top_bought->have_posts()){
        $top_bought->the_post();  
        global $product;
        $terms3 = wp_get_post_terms( $product->id, 'product_cat', array('fields'=>'slugs'));
         $slug_size = sizeof($terms3); 
         var_dump($slug_size);
        print_r($terms3);
          if($slug_size>1){ 
              $product_cat = $terms3[$slug_size-1];
            
            if(!(in_array($product_cat, $temp_cat))){
               var_dump($product_cat);
                $temp_cat[] = $product_cat;
                $temp_prod[] = $product->id;
            }
         
         }
         else{
           $product_cat = $terms3[0];

            if(!(in_array($product_cat, $temp_cat))){
               var_dump($product_cat);
                $temp_cat[] = $product_cat;
                $temp_prod[] = $product->id;
            }

         }  

     }
     var_dump($temp_prod);
     var_dump($temp_cat);
     $anna='Συχνά σε πωλήσεις προιόντα';
        $result1 .='<head><div class="section-title"><h2><font size ="5">'.$anna.' </font></h2></div></head>';  

      ///while($top->have_posts()){
        foreach ($temp_prod as $key) {
          # code...
       
        //$top_bought->the_post();        
        $product = wc_get_product( $key );
            
            $i_url = wp_get_attachment_image_src(get_post_thumbnail_id($key),$size="thumbnail");
            $prod = get_permalink($key);            
            var_dump($prod);
        $result1 .='</body>
          <div class = "active" style="width: 254.667px; margin-right:10px; display:block; margin-bottom:20px; ">
            <a class="carousel-item" href="'.$prod.'" target="">
              <div class="teaser-image">
                <img class= "img-responsive" data-src="htteps://" width="124" height="124" src="'.$i_url[0].'" style="opacity:1;" sizes="(max-width:124px) 100vw, 124px">
              </div>
            </a>
            <span><font size ="2">
            '.$product->get_price_html().' <br>
            </span></font>
            
            
            <div>
               <a class="button product_type_variable add_to_cart_button" href="'.$prod.'" "aria-label="Add"'.$product->get_title().'"to your cart" data-quantity="1" data-product_id="'.$product->get_id().'" data-product_sku="'.$product->get_sku().'" rel="nofollow" > Επιλογή </a></font>
                     
              </div>
          
          ';   
      
      }
       $result1.='
       </div>
       </body>
       ';     

     return $result1;
    wp_reset_postdata(); //ensures that the global $post has been restored to the current post in the main query.
  
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
	require_once('ar_shortcodes.php');

	add_action('widgets_init', 'ar_plugin_register_widgets');
	add_action('woocommerce_after_single_product','setViewInit'); //καλεί την setProductViews
}
add_action('plugins_loaded', 'ar_widget_init');
