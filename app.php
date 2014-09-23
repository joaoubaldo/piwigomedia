<?php
    require_once('../../../wp-load.php');
    require_once('functions.php');

    function setup() {
        $res = array();
        
        $per_page = get_option('piwigomedia_images_per_page', '30');
        
        $res['result'] = array(
            "action" => "setup",
            "sites" => get_sites(),
            "trMap" => get_tr_map(),
            "postId" => $post_id,
            "perPage" => $per_page
        );
        
        return $res;
    }
    
    
    
    $res = array();
    
    
    if ($_GET['a'] == 'setup')
        $res = setup();
    
    
    echo json_encode($res);
?>
