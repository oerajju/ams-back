// Default colors
var brandPrimary =  '#20a8d8';
var brandSuccess =  '#4dbd74';
var brandInfo =     '#63c2de';
var brandWarning =  '#f8cb00';
var brandDanger =   '#f86c6b';

var grayDark =      '#2a2c36';
var gray =          '#55595c';
var grayLight =     '#818a91';
var grayLighter =   '#d1d4d7';
var grayLightest =  '#f8f9fa';

angular
    .module('app', [
        'ui.router',
        'oc.lazyLoad',
        'ncy-angular-breadcrumb',
        'angular-loading-bar',
        'satellizer',
        'ngAnimate', 'toastr',
        'ngResource',
        'angularValidator',
        'ui.bootstrap'
    ])
    .config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
        cfpLoadingBarProvider.includeSpinner = false;
        cfpLoadingBarProvider.latencyThreshold = 1;
    }])
    .run(['$rootScope', '$state', '$stateParams', function($rootScope, $state, $stateParams) {
            $rootScope.baseUrl = "";
        $rootScope.$on('$stateChangeStart',function(event, toState){
            var user = JSON.parse(localStorage.getItem('user'));
            if(user){
                $rootScope.authenticated = true;
                 $rootScope.currentUser = user;
                  if(toState.name === "appSimple.login") {
                        event.preventDefault();
                        $state.go('app.main');
                    }    
            }else if(toState.name !== "appSimple.login"){
                event.preventDefault();
                $state.go('appSimple.login');
            }
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        });
        $rootScope.$state = $state;
        return $rootScope.$stateParams = $stateParams;
    }]);

    