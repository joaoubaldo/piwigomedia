<?php

require_once('../../../wp-load.php');
require_once('utils.php');

if (get_current_user_id() == 0)
    die(__('No access', 'piwigomedia'));
    

/*
View: initial request to fetch essential data, including sites list, string 
translation map.
*/
function setup() {
    $res = array();
    
    $per_page = get_option('piwigomedia_images_per_page', '30');
    
    $res['result'] = array(
        "sites" => get_sites(),
        "trMap" => get_tr_map(),
        "postId" => $post_id,
        "perPage" => $per_page
    );
    
    return json_encode($res);
}

/*
View: forward http request to Piwigo site and return result.
*/
function forward_http_request() {
    if ($_GET['__url__'] == '')
        die('_');
        
    # TODO: validate $site
    $sites = array();
    foreach (explode("\n", get_option('piwigomedia_piwigo_urls', '')) as $u) {
        $tu = trim($u);
        if (!empty($tu))
            $sites[] = $tu;
    }
    $site = $_GET['__url__'];

    $params = array();
    foreach($_GET as $k=>$v) {
        if (($k == "__url__") || ($k == "__a__"))
            continue;
        $params[$k] = $v;
    }
    
    $res = pwm_curl_get($site."/ws.php", $params);
    
    return $res;
}


/*
Calls the correct view based on request.
*/
function process_request() {
    $res = array();    
    
    if ($_GET['__a__'] == 'setup')
        $res = setup();
    elseif ($_GET['__a__'] == 'forward')
        $res = forward_http_request();
    
    echo $res;
}


process_request();
  
?>
