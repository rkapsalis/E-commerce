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
    $count_key = $uID;
    $count = get_post_meta($productID, $count_key, true);
    if($count==''){
        delete_post_meta($productID, $count_key);
        add_post_meta($productID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

function getTopViewed($num_posts, $uID){ //ταξινομηση των most viewed products
	//var_dump($num_posts);
	   $count_key = $uID;
	   $args = array(
            'posts_per_page' =>  $num_posts,
            'no_found_rows'  =>  1,
            'post_status'    => 'publish',
            'post_type'      => 'product',            
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
    $posts = $ar_query->posts;
  foreach($posts as $post){
      //print_r($post);
      // var_dump($post->$count_key);
  }

    return $ar_query;
}

// function to count views.
function setProductViews($productID, $uID) { //βαζει τον counter για καθε προιον και για καθε χρηστη
    $count_key = $uID;
    $count = get_post_meta($productID, $count_key, true);
    //var_dump($count);
    //var_dump($uID);
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
	//ob_start(); // prevent premature outputting of html
	global $uID;
	$uID = get_current_user_id();
    //var_dump($num_posts);
    $top_prod  = getTopViewed($num_posts,$uID); //get top products
    $chuck_pur = validate_top_products($top_prod); //validate top products
    //print_r($chuck_pur);
   // print_r($top_prod);
    if($chuck_pur == null){
       echo "no products found";
    }
    else{
    	//echo '<ul class="woo-most-viewed product_list_widget">';
    	$anna='Top viewed products';
        $result .='<head><div class="section-title"><h2><font size ="5">'.$anna.' </font></h2></div></head>';

    	while($top_prod->have_posts()){
    		$top_prod->the_post();    		 
    		global $product;
            //var_dump($product->id);
            $views = getProductViews($product->id, $uID);
            $i_url = wp_get_attachment_image_src(get_post_thumbnail_id($product->id),$size="thumbnail");
            $prod = get_permalink();
            
            if(in_array($product->id, $chuck_pur)){
	    		var_dump($prod);
	    	$result .='</body>
	    		<div class = "active" style="width: 254.667px; margin-right:10px; display:block; margin-bottom:20px; ">

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
		    		
		    		<div class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
               
                      <a class="button product_type_variable add_to_cart_button" href="'.$prod.'" "aria-label="Add"'.$product->get_title().'"to your cart" data-quantity="1" data-product_id="'.$product->get_id().'" data-product_sku="'.$product->get_sku().'" rel="nofollow" > Επιλογή </a></font>
	    		    </div>

	    		
					';
				
				
		   }
		  
    	}
    	 $result.='
		   </div>
		   </body>

		   ';
		  
    	
    }
     return $result;
    wp_reset_postdata(); //ensures that the global $post has been restored to the current post in the main query.
    //$content = ob_get_clean();
   // return $content;
}

function matched_cart_items($top_prod) { //προιοντα που δεν ειναι στο καλαθι
   $temp1_array = array();

    if ( ! WC()->cart->is_empty() ) { //αν το καλαθι δεν ειναι αδειο
        
        foreach(WC()->cart->get_cart() as $cart_item ) { //για καθε προιον που υπαρχει στο καλαθι
            // Handling also variable products and their products variations         
  	       // global $product;            
            //if($value != $cart_item['product_id']){ 
        	if(in_array($cart_item['product_id'], $top_prod)){ //αν υπαρχει στο καλαθι
               unset($top_prod[array_search($cart_item['product_id'],$top_prod)] ); //βγαλτο απο τον πινακα
            	//wp_delete_post($id, false);
            	echo "to ceid einai teleio";
           }              
     }
  }
  //var_dump($top_prod);
  return $top_prod;
}

function validate_top_products($top_prod){ //ελέγχουμε αν το προιον εχει αγοραστει απο εναν συγκεκριμενο χρηστη ή αν υπαρχει στο καλαθι 
	$temp_array = array();
	//print_r($top_prod);
	echo "this is top prod";
	//print_r($top_prod);
	$user = wp_get_current_user();
    $user_id = $user->ID; // Get the user ID
    $customer_email = $user->user_email; // Get the user email
   // var_dump($customer_email);
  while($top_prod->have_posts()){ //όσο υπαρχουν προιοντα 
  	$top_prod->the_post(); //παρε ενα προιον
  	echo 'sth';
  	global $product;
    //print_r($product->id);
    if(wc_customer_bought_product($customer_email, $user_id, $product->id ) ==false){ //αν ο χρηστης δεν εχει αγορασει το προιον
      $temp_array[] = $product->id; //βαλε το προιον στον πινακα
		//wp_delete_post($id1, false);
      echo "Has not bought the product yet";
    } 
  }
   //wp_reset_postdata ();   
 // var_dump(wc_get_orders($user_id));
 // var_dump($temp_array);
  $validated_top = matched_cart_items($temp_array);
  return $validated_top; //επεστρεψε τον πινακα
}



//---------------------WIDGET 2--------------------


function get_similarBought($category, $products, $num_posts){
	var_dump($category);
  print_r($products);
  var_dump(gettype($products));
  //$total_items = WC()->cart->get_cart_contents_count();

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
     var_dump($ar_query2);
   wp_reset_postdata();
   //global $product;
  //$units_sold = get_post_meta( 113, 'total_sales', true );
  //var_dump($units_sold);
   $posts = $ar_query2->posts;
  foreach($posts as $post){
    // echo '<p>' . get_the_title() . ' (';
    //         echo get_post_meta( get_the_id(), 'total_sales', true) . ')</p>';

      //var_dump($post);
  	global $product;
  	 $units_sold1 = get_post_meta( $post->ID, 'total_sales', true );
  	 var_dump($units_sold1);
  	//var_dump($product->id);
  	//echo $product->get_total_sales();
  	$terms = get_the_terms( $product->id, 'product_cat' );
           //var_dump($terms);
			foreach ($terms as $term) { //παιρνουμε την κατηγορια του προιοντος
			   $anna1[] = $term->slug;
			}
  	
  	//var_dump($anna1);
      //var_dump($post->'meta_value_num');
  	
  	var_dump($post->ID);
  }
     
  $customer_orders = get_posts( array(
    'numberposts' => -1,
    'meta_key'    => '_customer_user',
    'meta_value'  => get_current_user_id(),
    'post_type'   => wc_get_order_types(),
    'post_status' => array_keys( wc_get_order_statuses() ),
) );
  //var_dump($customer_orders);

return $ar_query2;
  
}

function user_cartItems($num_posts) { //προιοντα που ειναι στο καλαθι
   $prodID = array();
   $product_cat = array();

    if ( ! WC()->cart->is_empty() ) { //αν το καλαθι δεν ειναι αδειο
        
        foreach(WC()->cart->get_cart() as $cart_item ) { //για καθε προιον που υπαρχει στο καλαθι
            // Handling also variable products and their products variations         
  	       //global $product;  
  	       $prodID[] = $cart_item['product_id'];  
  	       //var_dump($prodID);
           $terms = get_the_terms( $cart_item['product_id'], 'product_cat' );
           $slug_size = sizeof($terms);
           var_dump($slug_size);
          if($slug_size>1){
           //var_dump($terms[1]);
           $product_cat[] = $terms[$slug_size-1]->slug;

         }
         else{
          $product_cat[] = $terms[0]->slug;
         }
			// foreach ($terms as $term) { //παιρνουμε την κατηγορια του προιοντος
			   
   //       //var_dump($product_cat);
			// }

			

        	         
     }
     
     //var_dump($prodID); 
     $product_cat = array_unique($product_cat); //βγάλε τα διπλοτυπα
     //var_dump( $product_cat) ;
  }
  $overal_prodID = get_userOrders($prodID);
  $finalProd = get_similarBought($product_cat, $overal_prodID, $num_posts);
  //var_dump($finalProd);
  return $finalProd;
 
}

function get_userOrders($prodID){
	global $uID;
	$uID = get_current_user_id();
	$args = array(
    'customer_id' => $uID
   );
   $orders = wc_get_orders($args); //παρε ολες τις παραγγελιες απο το συγκεκριμενο χρηστη
   //var_dump($orders);
   echo sizeof($orders);
   //$order = wc_get_order( $order_id );
   foreach($orders as $order){ //για καθε παραγγελια
	   $items = $order->get_items();
	   foreach ( $items as $item ) {
		   
		    $product_id = $item->get_product_id(); //παρε το product id
		    $prodID[] = $product_id;
		    //$product_variation_id = $item->get_variation_id();
	   }
 }
 $prodID = array_unique($prodID);
 //var_dump($prodID);
 return $prodID;
}

function shortcode_create_mostSimilarBought($num_posts){    
    user_cartItems($num_posts);
    
    
    
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
