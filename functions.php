<?php
function pwm_curl_post($url, array $post = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POSTFIELDS => http_build_query($post),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

function pwm_curl_get($url, array $get = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). 
            http_build_query($get, '', '&'),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}


function get_sites() {
    $sites = array();
    foreach (explode("\n", get_option('piwigomedia_piwigo_urls', '')) as $u) {
        $tu = trim($u);
        if (!empty($tu))
            $sites[] = $tu;
    }

    return $sites;
}

function get_tr_map() {
    $tr_map = array(
        'Error while reading from' => __('Error while reading from', 'piwigomedia'),
        'Please verify PiwigoMedia\'s configuration and try again.' => __('Please verify PiwigoMedia\'s configuration and try again.', 'piwigomedia'),
        'Error reading image information, please try again.' => __('Error reading image information, please try again.', 'piwigomedia'),
        'Loading...' => __('Loading...', 'piwigomedia')
    );
    
    return $tr_map;
}

?>
