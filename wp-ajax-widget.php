<?php
/*
Plugin Name: WP Ajax Widget
Plugin URI: https://github.com/iGARET/wp-ajax-widget
Description: A simple plugin that adds a simple widget
Version: 1.0
Author: iGARET
Author URI: http://igaret.com/
License: GPL2
*/




/**
 * Include and initialize the updater.
 */
include_once('updater.php');

if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'plugin-name', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/repos/iGARET/wp-ajax-widget', // the github API url of your github repo
        'raw_url' => 'https://raw.github.com/iGARET/wp-ajax-widget/master', // the github raw url of your github repo
        'github_url' => 'https://github.com/iGARET/wp-ajax-widget', // the github url of your github repo
        'zip_url' => 'https://github.com/iGARET/wp-ajax-widget/zipball/master', // the zip url of the github repo
        'sslverify' => true // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '3.0', // which version of WordPress does your plugin require?
        'tested' => '3.5.1', // which version of WordPress is your plugin tested up to?
        'readme' => 'readme.md', // which file to use as the readme for the version number
        'access_token' => '', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
    );
    new WP_GitHub_Updater($config);
}
?>