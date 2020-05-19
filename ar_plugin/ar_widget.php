<?php
if(!defined( 'ABSPATH' )){
	exit;
} 
//Creating the widget 
class ar_widget extends WP_Widget {
  
	public function __construct() {
		parent::__construct(
		  
		// Base ID of your widget
		'ar_widget', 
		  
		// Widget name will appear in UI
		__('ar Product Recommendation', 'ar_widget_domain'), 
		  
		// Widget description
		array( 'description' => __( 'Our first product Recommendation widget', 'ar_widget_domain' ) )  
		);
	}
  
// Creating widget front-end
  
 public function widget( $args, $instance ) {
	//extract($args);
    $title = apply_filters( 'widget_title', $instance['title'] );
  
    // before and after widget arguments are defined by themes
   echo $args['before_widget'];
   if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];
  
     // This is where you run the code and display the output
   echo __( 'Hello, World!', 'ar_widget_domain' );
   echo $args['after_widget'];
 }
          
// Widget Backend 
public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
	    $title = $instance[ 'title' ];
	}
	else {
	    $title = __( 'kati', 'ar_widget_domain' );
	}
// Widget admin form
    ?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
    <?php
}
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
}
}