<?php
    require_once('../../../wp-load.php');
    require_once('functions.php');

    // Check permissions
    if (get_current_user_id() == 0)
        die('no access');

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
        if ($k == "__url__")
            continue;
        $params[$k] = $v;
    }
    
    $res = pwm_curl_get($site."/ws.php", $params);
    echo $res;
?>
