<?php
/*
PiwigoMedia Wordpress plugin
Copyright (C) 2014  Joao Coutinho

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

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
        'Loading...' => __('Loading...', 'piwigomedia'),
        'Image type' => __('Image type', 'piwigomedia'),
        'Link to' => __('Link to', 'piwigomedia'),
        'Insert' => __('Insert', 'piwigomedia'),
        'Post' => __('Post', 'piwigomedia'),
        'Category' => __('Category', 'piwigomedia'),
        'Site' => __('Site', 'piwigomedia'),
        'Nothing' => __('Nothing', 'piwigomedia'),
        'Page' => __('Page', 'piwigomedia'),
        'Fullsize' => __('Fullsize', 'piwigomedia'),
        'Thumbnail' => __('Thumbnail', 'piwigomedia'),
        'No access' => __('No access', 'piwigomedia'),
        'PiwigoMedia must be configured.' => __('PiwigoMedia must be configured.', 'piwigomedia'),
        'Total images inserted:' => __('Total images inserted:', 'piwigomedia')
    );
   
    
    return $tr_map;
}

?>
