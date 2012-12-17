<?php
/**
 * Unregister some the default WordPress widgets on startup and register our
 * slightly modified versions
 *
 * @since 3.1.0
 */
function mlm_widgets_init()
{
	if ( ! is_blog_installed() ) {
		return;
	}

	unregister_widget( 'WP_Nav_Menu_Widget' );

	register_widget( 'MLM_Nav_Menu_Widget' );
}
add_action( 'widgets_init', 'mlm_widgets_init', 1 );

/**
 * Navigation Menu widget class
 *
 * @since 3.1.0
 */
 class MLM_Nav_Menu_Widget extends WP_Nav_Menu_Widget
 {
	function MLM_Nav_Menu_Widget()
	{
		$widget_ops = array( 'description' => __('Use this widget to add one of your custom menus as a widget.') );
		parent::WP_Widget( 'nav_menu', __('Custom Menu'), $widget_ops );
	}

	function widget( $args, $instance )
	{
		if ( isset( $instance['mlm_visible_by_members'] ) && $instance['mlm_visible_by_members'] && ! is_user_logged_in() ) {
			return;
		}
		
		if ( isset( $instance['mlm_visible_by_nonmembers'] ) && $instance['mlm_visible_by_nonmembers'] && is_user_logged_in() ) {
			return;
		}
		
		// Get menu
		$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] );

		if ( !$nav_menu )
			return;

		echo $args['before_widget'];

		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu ) );

		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		$instance['mlm_visible_by_members'] = 0;
		$instance['mlm_visible_by_nonmembers'] = 0;
		
		if ( isset( $new_instance['mlm_visible_by_members'] ) ) {
			$instance['mlm_visible_by_members'] = 1;
		}
		
		if ( isset( $new_instance['mlm_visible_by_nonmembers'] ) ) {
			$instance['mlm_visible_by_nonmembers'] = 1;
		}
		
		return $instance;
	}
}
?>