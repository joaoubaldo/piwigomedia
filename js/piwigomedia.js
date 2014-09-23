var app = angular.module("PiwigoMediaApp", []);


app.controller(
    "PiwigoController",
    ["$scope", "$http", "$location",
    function($scope, $http, $location) {
        $scope.sites = [];
        $scope.site = '';
        $scope.page = 0;
        $scope.postId = -1;
        $scope.basket = [];
        $scope.perPage = 0;
        $scope.messages = [];
	    $scope.trMap = {};
	    $scope.category = -1;
	    $scope.categories = [];
	    $scope.images = [];
        $scope.imagesOrder = [];
    
	    $scope.setup = function() {
	        $http.get('app.php?a=setup').success(function(data) {
	            angular.forEach(data.result, function(value, key) {
	                this[key] = value;
	            }, $scope);
	        });
	    };
    
	    $scope.setup();
	    

        $scope.refreshSite = function() {
        };
        
        $scope.addMessage = function(message, type) {
        };
        
        $scope.inBasket = function(image_id) {
            if ($scope.findInBasket(image_id) == -1)
                return false;
            return true;
        };
        
        $scope.findInBasket = function(image_id) {
            for(i in $scope.basket) {
                if ($scope.basket[i].id == image_id) {
                    return i;
                }            
            }            
            return -1;
        };
        
        $scope.toFromBasket = function(image_id) {
            var idx = $scope.findInBasket(image_id);
            if (idx == -1) {
                $scope.basket.push($scope.images[image_id]);
            } else {
                $scope.basket.splice(idx, 1);
            }
            console.log($scope.basket);
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

            config = {
                "params": {"__url__": $scope.site, 
                    "format": "json", 
                    "method": "pwg.categories.getList", 
                    "recursive": true}
            };
            
            $http.get('query_remote_pwg.php', config).success(
                function(data) {
                    if ((data == undefined) || data["stat"] != "ok") {
                        var msg = $scope.trMap["Error while reading from"] + " " + $scope.site + ". " +
                            $scope.trMap["Please verify PiwigoMedia\'s configuration and try again."];
                        $scope.addMessage(msg, 'error');
                        return;
                    }
                    
                    $scope.categories = {};
                    $scope.category = -1;
                    angular.forEach(data.result.categories, 
                        function(value, key) {
                            $scope.categories[value.id] = value;
                        }, 
                        $scope);
                }
            );
            
	    });
	    
	    $scope.$watch("category", function(new_value, old_value) {
    	    var config = {
                "params": {"__url__": $scope.site, 
                    "format": "json", 
                    "method": "pwg.categories.getImages", 
                    "cat_id": $scope.category, 
                    "page": $scope.page, 
                    "per_page": $scope.perPage}
            };
	        
            $http.get('query_remote_pwg.php', config).success(
                function(data) {
                    if ((data == undefined) || data["stat"] != "ok") {
                        var msg = $scope.trMap["Error reading image information, please try again."];
                        $scope.addMessage(msg, 'error');
                        return;
                    }
                    $scope.images = {};
                    $scope.imagesOrder = [];
                    angular.forEach(data.result.images, 
                        function(value, key) {
                            $scope.images[value.id] = value;
                            $scope.imagesOrder.push(value.id);
                        }, 
                        $scope);
                }
            );
	    });
	    
       
        

    }]);

