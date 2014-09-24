<?php
    require_once('../../../wp-load.php');

    // Verify post id
    $error = null;

    // Check permissions
    if (get_current_user_id() == 0)
        die('no access');

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
        <h1 class="text-center"><span class="piwigo-text">Piwigo</span><span class="media-text">Media</span> <small>for WP</small></h1>
        
        <div class="form-group">
            <label class="col-sm-1">Site</label>
            <div class="col-sm-11">
                <select class="form-control" ng-model="site" ng-options="s for s in sites">
                </select>
            </div>
        </div>
        
        <div class="form-group"  ng-show="categoryCount() > 0 && !loading">
            <label class="col-sm-1">Category</label>
            <div class="col-sm-11">
                <select ng-model="category" ng-options="c.id as getFullPath(c.id) for (k, c) in categories" class="form-control">
                </select>
            </div>
        </div>

        <div class="clearfix"></div>
        
        <div class="loader text-center" ng-if="loading"><img src="loader.gif"></div>
        
        <div class="panel">
            <p>{{m.message}}</p>
            <div class="alert pointer" role="alert" ng-repeat="m in messages" ng-class="{'alert-danger': m.type=='error'}" ng-click="removeMessage($index)">
                <span class="glyphicon glyphicon-remove text-right"></span> {{m.message}}
            </div>
        </div>

        <div class="operations-panel">
            <div class="btn-group" ng-if="basketOrder.length > 0">
                <!--<button type="button" class="btn btn-default" ng-if="basketOrder.length > 0">Review</button>-->
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Image type <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li ng-repeat="(k, v) in imageTypeList" ng-class="{active: k==imageType}"><a href="#" ng-click="setImageType(k)">{{v}}</a></li>
                  </ul>
                </div>
                
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Link to <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                      <li ng-repeat="(k, v) in linkToList" ng-class="{active: k==linkTo}"><a href="#" ng-click="setLinkTo(k)">{{v}}</a></li>
                  </ul>
                </div>
                
                <button type="button" class="btn btn-primary" ng-click="insertPost()"><span class="badge">{{basketOrder.length}}</span> Insert</button>
           </div>
        </div>
        
        <div class="grid-container">
            <div ng-show="!loading" class="row" ng-repeat="row in imagesOrder|splitEvery:4">
                <div class="col-xs-3" ng-repeat="id in row" ng-click="imageClick(id)">
                    <a href="#" class="thumbnail" ng-class="{selected: inBasket(id)}">
                        <img ng-src="{{images[id].derivatives.thumb.url}}">
                    </a>
                </div>
            </div>
        </div>
        
       <hr>
       <p class="text-right italic-text footer">
        PiwigoMedia <a href="http://b.joaoubaldo.com/" target="_blank">blog</a> | <a href="https://github.com/joaoubaldo/piwigomedia" target="_blank">github</a>
       </p>
       
       <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
       <script type='text/javascript' src='js/bootstrap.min.js'></script>
    </body>
</html>
