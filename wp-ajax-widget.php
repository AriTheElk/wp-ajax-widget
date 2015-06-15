<?php
/*
Plugin Name: WP Super Ajax
Plugin URI: https://github.com/iGARET/wp-ajax-widget
Description: Plugin for loading remote content into a widget.
Version: 1.3
Author: iGARET
Author URI: http://igaret.com
License: GPL2
*/

/*  Copyright 2013  iGARET  (email : garetmckinley@me.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * The widget class
 * @author Garet McKinley <garetmckinley@me.com>
 */
class wp_ajax_widget extends WP_Widget {

	# class constructor
	function wp_ajax_widget() {
		parent::WP_Widget(false, $name = __('WP AJAX Widget', 'wp_ajax_widget') );
	}

	# create the widget form
	function form($instance) {	
		# Check values
		if( $instance) {
			 $title = esc_attr($instance['title']);
			 $url = esc_attr($instance['url']);
		} else {
			 $title = '';
			 $url = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" />
		</p>
		<?php
	}

	# update the widget options
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		# Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		return $instance;
	}

	# display the widget
	function widget($args, $instance) {
        $widget_id = $this->id;
        $widget_loading_html_id = "wp_ajax_widget_loading" . $widget_id;
        $widget_return_html_id = "wp_ajax_widget_return" . $widget_id;
		extract($args);
		wp_enqueue_script('jquery');
		echo $before_widget;
		if (!empty($instance['title']))
			echo sprintf('<h3 class="widget-title">%s</h3>', $instance['title']).PHP_EOL;
		if (!empty($instance['url'])) {
			$script = sprintf('jQuery.get("%s?request=%s", function(data){jQuery("#' . $widget_return_html_id . '").append(data).fadeIn();jQuery("#' . $widget_loading_html_id . '").fadeOut();});', plugins_url( 'ajax-request.php' , __FILE__ ), urlencode($instance['url'])).PHP_EOL;
			echo '<script type="text/javascript">'.PHP_EOL;
			echo sprintf('jQuery(document).ready(function() {%s});', $script).PHP_EOL;
			echo '</script>'.PHP_EOL;
			echo sprintf('<div class="wp_ajax_widget"><img class="wp_ajax_widget_loading" id="'. $widget_loading_html_id . '" src="%s"><span class="wp_ajax_widget_return" id="' . $widget_return_html_id . '"></span></div>', plugins_url( 'loading.gif' , __FILE__ )).PHP_EOL;
        }
		echo $after_widget;
	}

	# register/enqueue the required scripts
	function register_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_style('wp_ajax_widget', plugins_url( 'style.css' , __FILE__ ));
	}
}

# register the widget
add_action('widgets_init', create_function('', 'return register_widget("wp_ajax_widget");'));
add_action('init', array("wp_ajax_widget", "register_scripts"));

# register the options
function register_mysettings() {
	//register our settings
	get_option('wpsa_aa_position', 'below-content');
	get_option('wpsa_aa_url', 'google.com');
	register_setting( 'wpsa-settings', 'wpsa_aa_position' );
	register_setting( 'wpsa-settings', 'wpsa_aa_url' );
}

if ( is_admin() ){ // admin actions
  add_action( 'admin_init', 'register_mysettings' );
}

# register the submenu page
function register_superajax_menu_page() {
	$page = add_submenu_page( 'options-general.php', 'WP Super Ajax', 'WP Super Ajax', 'manage_options', 'wp_super_ajax', 'createWPSuperAjaxPage' ); 
}

function createWPSuperAjaxPage() {
	require(sprintf("%s/%s", __DIR__, "settings.php"));
}
add_action('admin_menu', 'register_superajax_menu_page');


function callback($buffer) {
  // modify buffer here, and then return the updated code
  return $buffer;
}
 
function buffer_start() { ob_start("callback"); }
 
function buffer_end() { 
	echo sprintf('<div style="text-align:center;margin-bottom:20px;">%s</div>', '©2013');
}
 
add_action('wp_head', 'buffer_start');
add_action('wp_footer', 'buffer_end');


/**
 * Handle Shortcodes.
 */
function displayRemoteContent( $atts ){
	extract( shortcode_atts( array(
			'url' => 'something'
		), $atts ) );

	if (!empty($url)) {
		$id = md5(time() . $url);
		$script = sprintf('jQuery.get("%s?request=%s", function(data){jQuery(".wp_ajax_widget_return_%s").append(data).fadeIn();jQuery(".wp_ajax_widget_loading").fadeOut();});', plugins_url( 'ajax-request.php' , __FILE__ ), str_replace("http://", "", $url), $id).PHP_EOL;
		$output = '<script type="text/javascript">'.PHP_EOL;
		$output .= sprintf('jQuery(document).ready(function() {%s});', $script).PHP_EOL;
		$output .= '</script>'.PHP_EOL;
		$output .= sprintf('<div class="wp_ajax_widget"><img class="wp_ajax_widget_loading" src="%s"><span class="wp_ajax_widget_return_%s"></span></div>', plugins_url( 'loading.gif' , __FILE__ ), $id).PHP_EOL;
	}	
	return $output;
}
add_shortcode( 'ajax', 'displayRemoteContent' );


/**
 * Include and initialize the updater.
 */
include_once('updater.php');

if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
	$config = array(
		'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
		'proper_folder_name' => 'wp-ajax-widget', // this is the name of the folder your plugin lives in
		'api_url' => 'https://api.github.com/repos/iGARET/wp-ajax-widget', // the github API url of your github repo
		'raw_url' => 'https://raw.github.com/iGARET/wp-ajax-widget/master', // the github raw url of your github repo
		'github_url' => 'https://github.com/iGARET/wp-ajax-widget', // the github url of your github repo
		'zip_url' => 'https://github.com/iGARET/wp-ajax-widget/zipball/master', // the zip url of the github repo
		'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' => '3.0', // which version of WordPress does your plugin require?
		'tested' => '3.5.1', // which version of WordPress is your plugin tested up to?
		'readme' => 'readme.md', // which file to use as the readme for the version number
		'access_token' => '', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
	);
	new WP_GitHub_Updater($config);
}
?>