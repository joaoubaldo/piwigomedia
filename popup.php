<?php
    require_once('../../../wp-load.php');

    // Verify post id
    $error = null;

    // Check permissions
    #if (get_current_user_id() == 0)
    #    die('no access');

    if (count($sites) < 1)
        $error = 'not_configured';

    $error_msg = array(
        'not_configured' => __('PiwigoMedia must be configured.', 'piwigomedia')
    );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" ng-app="PiwigoMediaApp">
    <head>
        <title>PiwigoMedia</title>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <script type='text/javascript' src='<?php echo get_bloginfo('wpurl');?>/wp-includes/js/tinymce/tiny_mce_popup.js'></script>
        <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.min.js'></script>
        <script type='text/javascript' src='js/piwigomedia.js'></script>

        <link rel='stylesheet' href='css/bootstrap.min.css' type='text/css' />
        <link rel='stylesheet' href='css/popup.css' type='text/css' />
    </head>
    
    <body ng-controller="PiwigoController">
        <h1 class="text-center"><span class="piwigo-text">Piwigo</span><span class="media-text">Media</span></h1>
        
        <div class="btn-group-sm">
            <button type="button" class="btn btn-default">
                <select ng-model="site" ng-options="s for s in sites">
                </select>
            </button>
        
            <button type="button" class="btn btn-default">
                <select ng-model="category" ng-options="c.id as getFullPath(c.id) for (k, c) in categories">
                </select>
            </button>
            
            <button type="button" class="btn btn-primary" ng-if="basket.length > 0"><span class="badge">{{basket.length}}</span> Insert</button>

        </div>
        
        <div ng-show="images" class="row"> 
            <div ng-repeat="(id, image) in images" class="col-xs-2 col-xs-2">
                <a href="#" class="thumbnail" ng-class="{selected: inBasket(id)}">
                    <img ng-src="{{image.derivatives.thumb.url}}" ng-click="imageClick(id)">
                </a>
            </div>
        </div>

       <div class="footer">PiwigoMedia 2014 - <a href="http://b.joaoubaldo.com/" target="_blank"><?php _e('author website', 'piwigomedia') ?></a>, <a href="http://b.joaoubaldo.com/" target="_blank">github</a> </div>
       <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
       <script type='text/javascript' src='js/bootstrap.min.js'></script>
    </body>
</html>
