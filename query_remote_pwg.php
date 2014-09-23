<?php
    require_once('../../../wp-load.php');
    require_once('functions.php');

    // Check permissions
    if (get_current_user_id() == 0)
        die('no access');

    if ($_GET['__url__'] == '')
        die('_');

    $sites = array();
    foreach (explode("\n", get_option('piwigomedia_piwigo_urls', '')) as $u) {
        $tu = trim($u);
        if (!empty($tu))
            $sites[] = $tu;
    }
    $site_idx = $_GET['__url__'] != "" ? $_GET['__url__'] : 0;
    $site_idx = abs(intval($site_idx));
    if ($site_idx > count($sites)-1)
        $site_idx = 0;

    $site = null;
    if (!empty($sites))
        $site = $sites[$site_idx];

    if ($site == null)
      die();

    $params = array();
    foreach($_GET as $k=>$v) {
        if ($k == "__url__")
            continue;
        $params[$k] = $v;
    }
    $res = pwm_curl_get($site."/ws.php", $params);
    echo $res;
?>
