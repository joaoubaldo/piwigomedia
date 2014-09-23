<?php
    require_once('../../../wp-load.php');

    // Verify post id
    $error = null;

    // Check permissions
    if (get_current_user_id() == 0)
        die('no access');

    $sites = array();
    foreach (explode("\n", get_option('piwigomedia_piwigo_urls', '')) as $u) {
        $tu = trim($u);
        if (!empty($tu))
            $sites[] = $tu;
    }

    if (count($sites) < 1)
        $error = 'not_configured';

    $per_page = get_option('piwigomedia_images_per_page', '30');

    $error_msg = array(
        'not_configured' => __('PiwigoMedia must be configured.', 'piwigomedia')
    );

    $tr_map = array(
        'Error while reading from' => __('Error while reading from', 'piwigomedia'),
        'Please verify PiwigoMedia\'s configuration and try again.' => __('Please verify PiwigoMedia\'s configuration and try again.', 'piwigomedia'),
        'Error reading image information, please try again.' => __('Error reading image information, please try again.', 'piwigomedia'),
        'Loading...' => __('Loading...', 'piwigomedia')
    );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>PiwigoMedia</title>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <script type='text/javascript' src='<?php echo get_bloginfo('wpurl');?>/wp-includes/js/tinymce/tiny_mce_popup.js'></script>  
        <script type='text/javascript' src='js/jquery-1.5.min.js'></script>  
        <script type='text/javascript'>
	    var sites = <?php echo json_encode($sites); ?>;
	    var post_id = <?php echo json_encode($post_id); ?>;
	    var per_page = <?php echo json_encode($per_page); ?>;
	    var tr_map = <?php echo json_encode($tr_map); ?>;
        var this_page = 0;
        var site_idx = 0;
        var this_cat = 0;
        var cats = new Array();
        var selection = new Array();
        var images = new Array();
        </script>
        <script type='text/javascript' src='js/piwigomedia.js'></script>
        <link rel='stylesheet' href='css/popup.css' type='text/css' />
    </head>
    <body>
        <h1><span class="piwigo-text">Piwigo</span><span class="media-text">Media</span></h1>
            <div class="messages-section section">
                <ul>
                    <?php 
                    if (!is_null($error))
                        echo "<li class=\"error\">".$error_msg[$error]."</li>";
                    ?>
                </ul>
            </div>
            <div class="site-category-section section">
                <p class="instruction"><?php _e('Choose a site', 'piwigomedia') ?></p>
                <form method="get" class="site-selection" action="">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <?php _e('Piwigo site:', 'piwigomedia') ?>
                    <select name="site">
                        <?php
                        $i = 0;
                        foreach($sites as $s) {
                            echo '<option value="'.$i++.'">'.$s.'</option>';
                        }
                        ?>
                    </select><input type="button" class="submit" value="<?php _e('Select', 'piwigomedia') ?>" onclick="refresh_site();" />
                </form>

            <?php if (is_null($error)) { ?>
                <p class="instruction"><?php _e('Select a category', 'piwigomedia') ?></p>
                <?php
                echo "<form method=\"get\" class=\"category-selection\"  action=\"\">";
                echo __('Category:', 'piwigomedia').' <select name="category"></select>';
                echo "<input type=\"button\" class=\"submit\" onclick=\"refresh_category();\" value=\"".__('Select', 'piwigomedia')."\"/>";
                echo "</form>";
                ?>
            <?php } ?>
            </div>

            <div class="images-section section">
                <p class="instruction"><?php _e('Select the images', 'piwigomedia') ?></p>
                    <div class='current-category'>
                      <p class='arrow'>&gt;</p><ol></ol>
                      <div style="clear: both;"></div>
                    </div>

                    <div class="page-selection"><ol></ol></div>
                    <ul class="image-selector">&nbsp;</ul>
                    <div style="clear: both;"></div>
            </div>

            <div class="style-section section">
                <p class="instruction"><?php _e('Customize', 'piwigomedia') ?></p>
                <fieldset>
                    <legend><?php _e('Insert:', 'piwigomedia') ?></legend>
                    <?php _e('Thumbnail', 'piwigomedia') ?> <input type="radio" name="whatinsert" value="thumb" checked="checked"/>
                    <?php _e('Fullsize image', 'piwigomedia') ?> <input type="radio" name="whatinsert" value="fullsize"/>
                </fieldset>
                <fieldset>
                    <legend><?php _e('Alignment:', 'piwigomedia') ?></legend>
                    <?php _e('None', 'piwigomedia') ?> <input type="radio" name="alignment" value="none" checked="checked"/>
                    <?php _e('Left', 'piwigomedia') ?> <input type="radio" name="alignment" value="left"/>
                    <?php _e('Center', 'piwigomedia') ?> <input type="radio" name="alignment" value="center"/>
                    <?php _e('Right', 'piwigomedia') ?> <input type="radio" name="alignment" value="right"/>
                </fieldset>
                <fieldset>
                    <legend><?php _e('Link to:', 'piwigomedia') ?></legend>
                    <?php _e('Image page', 'piwigomedia') ?> <input type="radio" name="url" value="page" checked="checked"/>
                    <?php _e('Fullsize image', 'piwigomedia') ?> <input type="radio" name="url" value="fullsize"/>
                </fieldset>
                <fieldset>
                    <legend><?php _e('Link target:', 'piwigomedia') ?></legend>
                    <?php _e('New window', 'piwigomedia') ?> <input type="radio" name="target" value="new" checked="checked"/>
                    <?php _e('Same window', 'piwigomedia') ?> <input type="radio" name="target" value="same"/>
                </fieldset>
            </div>

            <div class="confirmation-section section">
                <div class="confirm-button">
                    <a href="#" onclick="insert_selected();tinyMCEPopup.close();"><?php _e('Insert into post', 'piwigomedia') ?></a>
                </div>
                <div style="clear: both;"></div>
            </div>

           <div class="footer">PiwigoMedia 2012 - <a href="http://joaoubaldo.com/" target="_blank"><?php _e('author website', 'piwigomedia') ?></a></div>
    </body>
</html>
