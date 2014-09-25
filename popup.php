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

    require_once('../../../wp-load.php');

    if (get_current_user_id() == 0)
        die(__('No access', 'piwigomedia'));
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
        
        <div class="form-group" ng-show="sites.length > 0">
            <label class="col-sm-1"><span class="glyphicon glyphicon-camera"></span> {{trMap["Site"]}}</label>
            <div class="col-sm-11">
                <select class="form-control" ng-model="site" ng-options="s for s in sites" ng-change="changeSite()">
                </select>
            </div>
        </div>
        
        <div class="form-group"  ng-show="categoryCount() > 0 && !loading">
            <label class="col-sm-1"><span class="glyphicon glyphicon-book"></span> {{trMap["Category"]}}</label>
            <div class="col-sm-11">
                <select ng-model="category" class="form-control" ng-change="changeCategory()">
                    <option value="{{c.id}}" ng-repeat="(k, c) in categories" ng-if="c.nb_images > 0">{{getFullPath(c.id)}}</option>
                </select>
            </div>
        </div>

        <div class="clearfix"></div>
        
        <div class="loader text-center" ng-if="loading"><img src="loader.gif"></div>
        
        <div class="panel">
            <p>{{m.message}}</p>
            <div class="alert pointer" role="alert" ng-repeat="m in messages" ng-class="{'alert-danger': m.type=='error', 'alert-success': m.type=='success'}" ng-click="removeMessage($index)">
                <span class="glyphicon text-right" ng-class="{'glyphicon-remove': m.type=='error', 'glyphicon glyphicon-ok': m.type=='success'}"></span> {{m.message}}
                
            </div>
        </div>

        <div class="operations-panel">
            <div class="btn-group" ng-if="pages.length > 0">
                <!-- page selector -->
                <div class="btn-group pointer">
                  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                    {{page+1}} <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li ng-repeat="pa in pages" ng-click="setPage(pa)" ng-class="{active: $index == page}"><a>{{pa+1}}</a>
                  </ul>
                </div>
                
                <!-- per page selector -->
                <div class="btn-group pointer">
                  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                    x {{perPage}} <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                      <li ng-repeat="count in [10,30,50,200,500]" ng-click="setPerPage(count)" ng-class="{active: perPage == count}"><a>{{count}}</a></li>
                  </ul>
                </div>
            </div>
                        
        
            <div class="btn-group">
              <ul class="dropdown-menu" role="menu">
                <li ng-repeat="(k, v) in imageTypeList" ng-class="{active: k==imageType}"><a href="#" ng-click="setImageType(k)">{{v}}</a></li>
              </ul>
            </div>
            <div class="btn-group" ng-if="basketOrder.length > 0">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    {{trMap["Image type"]}} <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li ng-repeat="(k, v) in imageTypeList" ng-class="{active: k==imageType}"><a href="#" ng-click="setImageType(k)">{{v}}</a></li>
                  </ul>
                </div>
                
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    {{trMap["Link to"]}} <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                      <li ng-repeat="(k, v) in linkToList" ng-class="{active: k==linkTo}"><a href="#" ng-click="setLinkTo(k)">{{v}}</a></li>
                  </ul>
                </div>
                
                <button type="button" class="btn btn-primary" ng-click="insertPost()"><span class="badge">{{basketOrder.length}}</span> {{trMap["Insert"]}}</button>
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
