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

function getTopViewed($num_posts, $uID){ //ταξινομηση των most viewed products
	var_dump($num_posts);
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
      var_dump($post);
   }
 
    return $ar_query;
}

// function to count views.
function setProductViews($productID, $uID) { //βαζει τον counter για καθε προιον και για καθε χρηστη
    $count_key = $uID;
    $count = get_post_meta($productID, $count_key, true);
    var_dump($count);
    var_dump($uID);
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
	ob_start(); // prevent premature outputting of html
	global $uID;
	$uID = get_current_user_id();
    var_dump($num_posts);
    $top_prod  = getTopViewed($num_posts,$uID); //get top products
    $chuck_pur = validate_top_products($top_prod); //validate top products
    //print_r($chuck_pur);
   // print_r($top_prod);
    if($chuck_pur == null){
       echo "no products found";
    }
    else{
    	echo '<ul class="woo-most-viewed product_list_widget">';
    	while($chuck_pur->have_posts()){
    		$chuck_pur->the_post();    		 
    		global $product;
            //var_dump($post->ID);
            
    		?>
			<li>
				<a href="<?php echo esc_url( get_permalink( $product->id ) ); ?>"
				   title="<?php echo esc_attr( $product->get_title() ); ?>">
					<?php echo $product->get_image(); ?>
					<span class="product-title"><?php echo $product->get_title(); ?></span>
				</a>
				<?php // echo wcmvp_get_view_count_html( $product->id ); ?>
				<?php //echo $product->get_price_html(); ?>
			</li>
			<?php
    	}
    	echo '</ul>';
    }
    wp_reset_postdata(); //ensures that the global $post has been restored to the current post in the main query.
    $content = ob_get_clean();
    return $content;
}

function matched_cart_items($top_prod) { //προιοντα που δεν ειναι στο καλαθι
   $temp1_array = array();

    if ( ! WC()->cart->is_empty() ) { //αν το καλαθι δεν ειναι αδειο
        
        foreach(WC()->cart->get_cart() as $cart_item ) { //για καθε προιον που υπαρχει στο καλαθι
            // Handling also variable products and their products variations         
  	       // global $product;            
            //if($value != $cart_item['product_id']){ //αν δεν υπαρχει στο καλαθι
        	if(in_array($cart_item['product_id'], $top_prod)){
               unset($top_prod[array_search($cart_item['product_id'],$top_prod)] ); //βαλτο στον πινακα            	
            	//print_r($product->id);
               echo "to ceid einai teleio";
           }              
     }
  }
  var_dump($top_prod);
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
    var_dump($customer_email);
  while($top_prod->have_posts()){ //όσο υπαρχουν προιοντα 
  	$top_prod->the_post(); //παρε ενα προιον
  	echo 'sth';
  	global $product;
    //print_r($product->id);
    var_dump(wc_customer_bought_product($customer_email, $user_id, $product->id )); 
  	if(has_bought_items($user_id, $product->id)==false){ //αν ο χρηστης δεν εχει αγορασει το προιον
      $temp_array[] = $product->id; //βαλε το προιον στον πινακα
		
      echo "Has not bought the product yet";
    } 
  }
   
 // var_dump(wc_get_orders($user_id));
  var_dump($temp_array);
  $validated_top = matched_cart_items($temp_array);
  return $validated_top; //επεστρεψε τον πινακα
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
