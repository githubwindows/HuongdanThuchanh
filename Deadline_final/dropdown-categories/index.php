<?php

/*
Plugin Name: Dropdown Categories
Plugin URI: http://walkingalone.info
Description: Show Drop Down Category list
Version: 1.0.0
Author: Truong Dinh Duy
Author URI: http://walkingalone.info
*/

add_action( 'widgets_init', 'my_widget' );

function my_widget() {
	register_widget( 'MY_Widget' );
}

class MY_Widget extends WP_Widget {

	function MY_Widget() {
		$widget_ops = array( 'classname' => 'example', 'description' => __('Show Drop Down Category list ', 'example') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'example-widget' );
		
		$this->WP_Widget( 'example-widget', __('Drop Down Category', 'example'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : 'id';
		$show_count = isset( $instance['show_count'] ) ? $instance['show_count'] : 1;
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : 0;

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo $before_title . $title . $after_title;

		echo $this->ltb_wp_list_categories(array('show_count' => $show_count, 'orderby' => $orderby, 'hide_empty' => $hide_empty));

		echo $after_widget;
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['orderby'] = 'id';
		if ( in_array( $new_instance['orderby'], array( 'name', 'id'  ) ) ) {
			$instance['orderby'] = $new_instance['orderby'];
		} else {
			$instance['orderby'] = 'id';
		}
		$instance['show_count'] = 1;
		if ( in_array( $new_instance['show_count'], array( 1, 0  ) ) ) {
			$instance['show_count'] = $new_instance['show_count'];
		} else {
			$instance['show_count'] = 1;
		}
		$instance['hide_empty'] = 0;
		if ( in_array( $new_instance['hide_empty'], array( 1, 0  ) ) ) {
			$instance['hide_empty'] = $new_instance['hide_empty'];
		} else {
			$instance['hide_empty'] = 0;
		}


		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Example', 'example'), 'orderby' => 'id', 'show_count' => 1, 'hide_empty' => 0);
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e( 'Order by:' ); ?></label>
			<select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				<option value="name"<?php selected( $instance['orderby'], 'name' ); ?>><?php _e( 'Title' ); ?></option>
				<option value="id"<?php selected( $instance['orderby'], 'id' ); ?>><?php _e( 'ID' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_count'); ?>"><?php _e( 'Show Post Count:' ); ?></label>
			<select name="<?php echo $this->get_field_name('show_count'); ?>" id="<?php echo $this->get_field_id('show_count'); ?>" class="widefat">
				<option value="1"<?php selected( $instance['show_count'], 1 ); ?>><?php _e( 'Yes' ); ?></option>
				<option value="0"<?php selected( $instance['show_count'], 0 ); ?>><?php _e( 'No' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php _e( 'Hide Empty:' ); ?></label>
			<select name="<?php echo $this->get_field_name('hide_empty'); ?>" id="<?php echo $this->get_field_id('hide_empty'); ?>" class="widefat">
				<option value="1"<?php selected( $instance['hide_empty'], 1 ); ?>><?php _e( 'Yes' ); ?></option>
				<option value="0"<?php selected( $instance['hide_empty'], 0 ); ?>><?php _e( 'No' ); ?></option>
			</select>
		</p>


	<?php
	}


	function ltb_wp_list_categories( $args = '' ) {
		$defaults = array(
			'show_option_all' => '', 'show_option_none' => __('No categories'),
			'orderby' => $args['orderby'], 'order' => 'ASC',
			'style' => 'list',
			'show_count' => $args['show_count'], 'hide_empty' => $args['hide_empty'],
			'use_desc_for_title' => 1, 'child_of' => 0,
			'feed' => '', 'feed_type' => '',
			'feed_image' => '', 'exclude' => '',
			'exclude_tree' => '', 'current_category' => 0,
			'hierarchical' => true, 'title_li' => __( 'Categories' ),
			'echo' => 1, 'depth' => 0,
			'taxonomy' => 'category'
		);

		$r = wp_parse_args( $args, $defaults );

		if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] )
			$r['pad_counts'] = true;

		if ( true == $r['hierarchical'] ) {
			$r['exclude_tree'] = $r['exclude'];
			$r['exclude'] = '';
		}

		if ( !isset( $r['class'] ) )
			$r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];

		extract( $r );

		if ( !taxonomy_exists($taxonomy) )
			return false;

		$categories = get_categories( $r );

		$output = '';
		if ( $title_li && 'list' == $style )
				$output = '<li class="' . esc_attr( $class ) . '">' . '' . '<ul class="ct_dropdown">';

		if ( empty( $categories ) ) {
			if ( ! empty( $show_option_none ) ) {
				if ( 'list' == $style )
					$output .= '<li>' . $show_option_none . '</li>';
				else
					$output .= $show_option_none;

			}
		} else {
			if ( ! empty( $show_option_all ) ) {
				$posts_page = ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
				$posts_page = esc_url( $posts_page );
				if ( 'list' == $style )
					$output .= "<li><a href='$posts_page'>$show_option_all</a></li>";
				else
					$output .= "<a href='$posts_page'>$show_option_all</a>";
			}

			if ( empty( $r['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
				$current_term_object = get_queried_object();
				if ( $r['taxonomy'] == $current_term_object->taxonomy )
					$r['current_category'] = get_queried_object_id();
			}

			if ( $hierarchical )
				$depth = $r['depth'];
			else
				$depth = -1; // Flat.

			$output .= walk_category_tree( $categories, $depth, $r );
		}

		if ( $title_li && 'list' == $style )
			$output .= '</ul></li>';

		$output = apply_filters( 'wp_list_categories', $output, $args );

		if ( $echo )
			echo $output;
		else
			return $output;
	}
}

function my_scripts_method() {
	wp_enqueue_script('custom-script', plugins_url( '/custom_script.js', __FILE__ ) , array('jquery'), '', false);
	wp_register_style( 'custom-style', plugins_url( '/custom-style.css', __FILE__ ), array(), '1.0.0', 'all' );
	wp_enqueue_style( 'custom-style' );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

?>