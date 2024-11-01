<?php
/**
 * Currency Converter widget class
 *
 * @since 3.7.1
 */
class WPSC_Widget_Currency_Converter extends WP_Widget {

	function WPSC_Widget_Currency_Converter() {

		$widget_ops = array('classname' => 'widget_wpsc_currency_chooser', 'description' => __('Product Currency Chooser Widget', 'wpsc'));
		$this->WP_Widget('wpsc_currency', __('Currency Chooser','wpsc'), $widget_ops);
	}

	function widget( $args, $instance ) {
	  global $wpdb, $wpsc_theme_path,$wpsc_cart;
		extract( $args );
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Currency Chooser' ) : $instance['title']);
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		//Display Currency Info:
		$sql ="SELECT * FROM `".WPSC_TABLE_CURRENCY_LIST."` WHERE `visible`='1' ORDER BY `country` ASC";
//		echo $sql;
		$countries = $wpdb->get_results($sql, ARRAY_A);
		$output .= '<form method="post" action="">';
		$output .='<select name="currency_option" style="width:200px;">';
			foreach($countries as $country){
				if($_SESSION['wpsc_currency_code'] == $country['id']){
					$output .="<option selected='selected' value=".$country['id'].">".$country['country']."</option>";
				}else{
					$output .="<option value=".$country['id'].">".$country['country']."</option>";
				}
			}
		$output .="</select><br />";
		$output .='<input type="hidden" value="change_currency_country" class="button-primary" name="wpsc_admin_action" />';
		if($instance['show_reset'] == 1){
			$output .='<input type="submit" value="'.__('Reset Price to ','wpsc').$_SESSION['wpsc_base_currency_code'].'"  name="reset" />';
		}
		$output .='<input type="submit" value="'.__('Convert','wpsc').'" class="button-primary" name="submit" />';
		$output .='</form>';
		if($instance['show_conversion'] == 1){
			if($wpsc_cart->currency_conversion != 0 && $wpsc_cart->use_currency_converter){		
				$output .='<p><strong>Base Currency:</strong> '.$_SESSION['wpsc_base_currency_code'].'</p>';
				if($wpsc_cart->selected_currency_code != ''){
					$output .='<p><strong>Current Currency:</strong> '.$wpsc_cart->selected_currency_code.'</p>';
				}
				$output .='<p>1 '.$_SESSION['wpsc_base_currency_code'].' = '.$wpsc_cart->currency_conversion.' '.$wpsc_cart->selected_currency_code.'</p><br />';
			}elseif($wpsc_cart->currency_conversion == 0 && $_SESSION['wpsc_currency_code'] != '' ){
				
			}	
		}

		echo $output.$after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
	//	exit('<pre>'.print_r($new_instance,true).'</pre>');
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_conversion'] = $new_instance['show_conversion'];
		$instance['show_reset'] = $new_instance['show_reset'];

		return $instance;
	}

	function form( $instance ) {
	  global $wpdb;
	    $title = esc_attr($instance['title']);
	    $show_conversion =$instance['show_conversion'];
	    $show_reset =$instance['show_reset'];
	    if($show_conversion == 1){
	    	$checked = 'checked="checked"';
	    }else{
   	    	$checked = '';
	    }
	    if($show_reset == 1){
	    	$show_reset_check = 'checked="checked"';
	    }else{
   	    	$show_reset_check = '';
	    }
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('show_conversion'); ?>"><?php _e('Show Conversion Rate:'); ?> <input  id="<?php echo $this->get_field_id('show_conversion'); ?>" name="<?php echo $this->get_field_name('show_conversion'); ?>" type="checkbox" value="1" <?php echo $checked; ?> /></label></p>
        <p><label for="<?php echo $this->get_field_id('show_reset'); ?>"><?php _e('Show Reset Button:'); ?> <input  id="<?php echo $this->get_field_id('show_reset'); ?>" name="<?php echo $this->get_field_name('show_reset'); ?>" type="checkbox" value="1" <?php echo $show_reset_check; ?> /></label></p>
        <?php 
	
	}

}

add_action('widgets_init', create_function('', 'return register_widget("WPSC_Widget_Currency_Converter");'));
?>