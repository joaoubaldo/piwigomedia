<?php
/*
Plugin Name: PiwigoMedia
Plugin URI: http://joaoubaldo.com
Description: This plugins allows media from a Piwigo site to be inserted into WordPress posts.
Version: 1.1.4
Author: João C.
Author URI: http://joaoubaldo.com
License: GPL2 (see attached LICENSE file)
*/

require_once("shortcode.php");
//require_once("widget.php");


function register_piwigomedia_tinymce_button($buttons) {
    array_push($buttons, 'separator', 'piwigomedia');
    return $buttons;
}

function register_piwigomedia_tinymce_plugin($plugin_array) {
    $plugin_array['piwigomedia'] = WP_PLUGIN_URL . 
        '/piwigomedia/tinymce/editor_plugin.js';
    return $plugin_array;
}

function register_piwigomedia_plugin() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
        return;
    if (get_user_option('rich_editing') != 'true')
        return;

    load_plugin_textdomain('piwigomedia', null, 'piwigomedia/languages/');

    add_filter('mce_buttons', 'register_piwigomedia_tinymce_button');
    add_filter('mce_external_plugins', 'register_piwigomedia_tinymce_plugin');

    add_shortcode('pwg-image', 'pwg_image');
    add_shortcode('pwg-category', 'pwg_category');
    add_shortcode('pwg-gallery', 'pwg_gallery');

    //wp_register_sidebar_widget("piwigomedia-images", "Piwigo Images", "piwigomedia_widget", 
    //array("description" => __("Display Piwigo media on a sidebar widget", "piwigomedia"),
    //"site" => "s", "category" => "cate")); 
}

function load_piwigomedia_headers() {
    wp_enqueue_style('piwigomedia', WP_PLUGIN_URL.'/piwigomedia/css/piwigomedia.css', false, '1.0', 'all');
    wp_enqueue_style('galleria', WP_PLUGIN_URL.'/piwigomedia/js/galleria/themes/classic/galleria.classic.css', false, '1.0', 'all');
    wp_register_script('galleria-min', plugins_url( '/js/galleria/galleria-1.2.9.min.js', __FILE__ ) );
    wp_enqueue_script('galleria-min');
}

add_action('init', 'register_piwigomedia_plugin');
//add_action('widgets_init', function() { return register_widget( "PiwigoMediaWidget" ); });
add_action('wp_enqueue_scripts', 'load_piwigomedia_headers');  

require_once('piwigomedia_admin.php');
?>
