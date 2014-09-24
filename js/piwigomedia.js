var app = angular.module("PiwigoMediaApp", []);

app.filter('splitEvery', function() {
    var cache = {};
    var filter = function(arr, size) {
        if (!arr) { 
            return; 
        }
        var newArr = [];
        for (var i=0; i<arr.length; i+=size) {
            newArr.push(arr.slice(i, i+size));
        }
        var arrString = JSON.stringify(arr);
        var fromCache = cache[arrString+size];
        if (JSON.stringify(fromCache) === JSON.stringify(newArr)) {
            return fromCache;
        }
        cache[arrString+size] = newArr;
        return newArr;
    };
    return filter;
});


app.controller(
    "PiwigoController",
    ["$scope", "$http", "$location",
    function($scope, $http, $location) {
        $scope.sites = [];
        $scope.site = '';
        $scope.page = 0;
        $scope.postId = -1;
        $scope.basket = {};
        $scope.basketOrder = [];
        $scope.perPage = 0;
        $scope.messages = [];
	    $scope.trMap = {};
	    $scope.category = -1;
	    $scope.categories = [];
	    $scope.images = [];
        $scope.imagesOrder = [];
        $scope.linkTo = 'page';
        $scope.imageType = 'thumb';
        $scope.loading = false;
        
        $scope.linkToList = {
            'none': 'Nothing',
            'page': 'Page',
            'fullsize': 'Fullsize'
        };
        
        $scope.imageTypeList = {
            'thumb': 'Thumbnail',
            'fullsize': 'Fullsize'            
        };
        
    
	    $scope.setup = function() {
            $scope.loading = true;
	        $http.get('app.php?__a__=setup').success(function(data) {
	            angular.forEach(data.result, function(value, key) {
	                this[key] = value;
	            }, $scope);
	            $scope.loading = false;
	            console.log($scope.sites);
	            if ($scope.sites.length < 1)
	                $scope.addMessage(
	                    $scope.trMap['PiwigoMedia must be configured.'],
	                    'error');
	        });
	    };
    
	    $scope.setup();
	    
        $scope.addMessage = function(message, type) {
            $scope.messages.push({'message': message, 'type': type})
        };
        
        $scope.removeMessage = function(index) {
            $scope.messages.splice(index, 1);
        };
        
        $scope.setLinkTo = function(val) {
            // TODO: validate
            $scope.linkTo = val;
        };
        
        $scope.setImageType = function(val) {
            // TODO: validate
            $scope.imageType = val;
        };
        
        $scope.inBasket = function(image_id) {
            return $scope.basketOrder.indexOf(image_id) != -1
        };
        
        $scope.toFromBasket = function(image_id) {
            var idx = $scope.basketOrder.indexOf(image_id);
            if (idx != -1) {
                $scope.basketOrder.splice(idx, 1);
                delete $scope.basket[image_id];
            } else {
                $scope.basket[image_id] = $scope.images[image_id];
                $scope.basketOrder.push(image_id);
            }
        };        
        
        $scope.insertPost = function() {
            var target = "_blank";
            var align = "";
            
            angular.forEach($scope.basketOrder, function(value, key) { 
                var img = $scope.basket[value];
                var url;
                var thumbUrl;
                
                if ($scope.imageType == 'thumb')
                    thumbUrl = img.derivatives.thumb.url;
                else if ($scope.imageType == 'fullsize')
                    thumbUrl = img.element_url;
                    
                if ($scope.linkTo == 'page')
                    url = img.page_url;
                else if ($scope.linkTo == 'fullsize')
                    url = img.element_url;
                else if ($scope.linkTo == 'none')
                    url = '';
                    
                if (url) {
                    html = '<a href="'+url+'" target="'+target+'" '+
                        'class="piwigomedia-single-image">'+
                            '<img src="'+thumbUrl+'" class="'+align+'" />'+
                        '</a>';
                } else {
                    html = '<a class="piwigomedia-single-image">'+
                            '<img src="'+thumbUrl+'" class="'+align+'" />'+
                        '</a>';
                }
                    
                window.parent.tinyMCE.execCommand('mceInsertContent',
                    false, html);
                    
            }, null);
            
            $scope.emptyBasket();
        };
        
        $scope.emptyBasket = function() {
            $scope.basket = {};
            $scope.basketOrder = [];
        };
        
        $scope.categoryCount = function() {
            return Object.keys($scope.categories).length;
        };
        
        $scope.getFullPath = function(category_id) {
            if (!category_id) return;
            
            var cat = $scope.categories[category_id];
            var res = cat.name;
            
            while (cat.id_uppercat != null) {
                cat = $scope.categories[cat.id_uppercat];
                res = cat.name + '/' + res;
            }
            return res;
        };

        $scope.imageClick = function(image_id) {
            $scope.toFromBasket(image_id);
        };

	    $scope.$watch("site", function(new_value, old_value) {
	        if (!$scope.site)
	            return;

            $scope.loading = true;
            
            config = {
                "params": {
                    "__url__": $scope.site, 
                    "__a__": "forward", 
                    "format": "json", 
                    "method": "pwg.categories.getList", 
                    "recursive": true}
            };

            $scope.categories = {};
            $scope.category = -1;
            
            $http.get('app.php', config).success(
                function(data) {
                    console.log(data);
                    if ((data == undefined) || data["stat"] != "ok") {
                        var msg = $scope.trMap["Error while reading from"] + " " + $scope.site + ". " +
                            $scope.trMap["Please verify PiwigoMedia\'s configuration and try again."];
                        $scope.addMessage(msg, 'error');
                        $scope.loading = false;
                        return;
                    }
                    
                    angular.forEach(data.result.categories, 
                        function(value, key) {
                            $scope.categories[value.id] = value;
                        }, 
                        $scope);
                        
                    $scope.loading = false;
                }
            );
	    });

	    $scope.$watch("category", function(new_value, old_value) {
            if ($scope.category == -1)
                return;

            $scope.loading = true;
            
    	    var config = {
                "params": {"__url__": $scope.site,
                    "__a__": "forward",  
                    "format": "json", 
                    "method": "pwg.categories.getImages", 
                    "cat_id": $scope.category, 
                    "page": $scope.page, 
                    "per_page": $scope.perPage}
            };

            $scope.images = {};
            $scope.imagesOrder = [];
                
            $http.get('app.php', config).success(
                function(data) {
                    if ((data == undefined) || data["stat"] != "ok") {
                        var msg = $scope.trMap["Error reading image information, please try again."];
                        $scope.addMessage(msg, 'error');
                        $scope.loading = false;
                        return;
                    }
                    angular.forEach(data.result.images, 
                        function(value, key) {
                            $scope.images[value.id] = value;
                            $scope.imagesOrder.push(value.id);
                        }, 
                        $scope);
                        
                    $scope.loading = false;
                }
            );
	    });
        

    }]);

