<?php
/*
Plugin Name: PiwigoMedia
Plugin URI: http://joaoubaldo.com
Description: This plugins allows media from a Piwigo site to be inserted into WordPress posts.
Version: 1.9.0
Author: Joao Coutinho
Author URI: http://b.joaoubaldo.com
License: GPL2 (see http://www.gnu.org/licenses/)
*/


require_once("utils.php");

/*
 * Main hooks
 */
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



/*
 * Admin hooks
 */
function register_my_menu() {
    if (!current_user_can('manage_options'))
        return;
    add_options_page('PiwigoMedia', 'PiwigoMedia', 'manage_options', 
        'piwigomedia-plugin', 'render_options_html');
}

function register_my_settings() {
    if (!current_user_can('manage_options'))
        return;
    register_setting('piwigomedia-options', 'piwigomedia_piwigo_url');
    register_setting('piwigomedia-options', 'piwigomedia_piwigo_urls');
    register_setting('piwigomedia-options', 'piwigomedia_images_per_page', 
        'absint');
}

function render_options_html() {
    ?>
    <div class="wrap"> 
	    <div id="icon-options-general" class="icon32"><br />
    </div> 
    <h2><?php _e('PiwigoMedia settings', 'piwigomedia') ?></h2> 

    <form action="options.php" method="post">
        <?php settings_fields('piwigomedia-options'); ?>
        <h3><?php _e('Primary settings', 'piwigomedia') ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Piwigo sites', 'piwigomedia') ?> <span class="description">(<?php _e('Required field', 'piwigomedia') ?>)</span></th>
                <td>
                    <textarea rows="4" cols="40" name="piwigomedia_piwigo_urls"><?php echo get_option('piwigomedia_piwigo_urls') ?></textarea>
                    <br/>
                    <span class="description">(<?php _e('Multiple Piwigo sites are allowed by inserting one URL per line.', 'piwigomedia').' '._e('You can select the default Piwigo site by placing its URL on top of the list.', 'piwigomedia') ?>)</span>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Images per page', 'piwigomedia') ?></th>
                <td><input type="text" name="piwigomedia_images_per_page" 
                    value="<?php echo get_option('piwigomedia_images_per_page', '30'); ?>" class="small-text"/>
                    <span class="description">(<?php _e('Number of images to display per page, in the selection screen.', 'piwigomedia') ?>)</span>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save changes', 'piwigomedia') ?>" />
        </p>
    </form>
    <?php
}



/*
 * Shortcode hooks
 */
function pwg_gallery( $atts ) {
    extract( shortcode_atts( array(
            'site'=>NULL, 'id'=>NULL, 'images'=>10, 'page'=>0, 'height'=>400
    ), $atts ) );

$params = array(
	"format" => "json", 
	"method" => "pwg.categories.getImages",
	"cat_id" => $id, 
	"page" => $page, 
	"per_page" => $images);
    $res = pwm_curl_get($site."/ws.php", $params);
    $res = json_decode($res);
    if ($res->stat != "ok")
	return;
$out = "";
if (count($res->result->images) > 0) {
	$out .= "<div id=\"piwigomedia-gallery-$id\" style=\"height: ".$height."px;\">";
	foreach($res->result->images as $img) {
		$out .= "<a href=\"".$img->element_url."\" target=\"_blank\"><img src=\"".$img->derivatives->thumb->url."\" data-title=\"".$img->name."\" data-link=\"".$img->derivatives->xxlarge->url."\"></a>";
	}
	$out .= "</div>";
}
    return "$out <script>Galleria.loadTheme('wp-content/plugins/piwigomedia/js/galleria/themes/classic/galleria.classic.min.js');Galleria.run('#piwigomedia-gallery-$id');</script>";
}


function pwg_category( $atts ) {

    extract( shortcode_atts( array(
            'site'=>NULL, 'id'=>NULL, 'images'=>10, 'page'=>0
    ), $atts ) );

    $params = array(
            "format" => "json",
            "method" => "pwg.categories.getImages",
            "cat_id" => $id,
            "page" => $page,
            "per_page" => $images);
    $res = pwm_curl_get($site."/ws.php", $params);

    $res = json_decode($res);
    if ($res->stat != "ok")
            return;

    $out = "";

    if (count($res->result->images) > 0) {
            $out .= "<ul class=\"piwigomedia-category-preview\">";
            foreach($res->result->images as $img) {
                    $out .= "<li><a class=\"piwigomedia-single-image\" href=\"".$img->element_url."\" target=\"_blank\"><img src=\"".$img->derivatives->thumb->url."\"></a></li>";
            }
            $out .= "</ul>";
    }
    return "$out";
}


function pwg_image( $atts ) {
    extract( shortcode_atts( array(
            'site'=>NULL, 'id'=>NULL, 
    ), $atts ) );

    $params = array(
            "format" => "json", 
            "method" => "pwg.images.getInfo",
            "image_id" => $id, 
            "comments_page" => 0);
    $res = pwm_curl_get($site."/ws.php", $params);
    $res = json_decode($res);
    if ($res->stat != "ok")
            return;
    $out = "<a class=\"piwigomedia-single-image\" href=\"".$res->result->element_url."\" target=\"_blank\"><img src=\"".$res->result->derivatives->thumb->url."\"></a>";
    return "$out";
}

 
add_action('init', 'register_piwigomedia_plugin');
//add_action('widgets_init', function() { return register_widget( "PiwigoMediaWidget" ); });
add_action('wp_enqueue_scripts', 'load_piwigomedia_headers');  
add_action('admin_menu', 'register_my_menu');
add_action('admin_init', 'register_my_settings');

?>
