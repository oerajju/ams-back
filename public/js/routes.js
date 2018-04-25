angular
    .module('app')
    .config(['$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider', '$breadcrumbProvider','$authProvider','$provide','$httpProvider', function($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, $breadcrumbProvider,$authProvider,$provide,$httpProvider) {
        
        function redirectWhenLoggedOut($q, $injector) {

                return {

                    responseError: function(rejection) {

                        // Need to use $injector.get to bring in $state or else we get
                        // a circular dependency error
                        var $state = $injector.get('$state');

                        // Instead of checking for a status code of 400 which might be used
                        // for other reasons in Laravel, we check for the specific rejection
                        // reasons to tell us if we need to redirect to the login state
                        var rejectionReasons = ['token_not_provided', 'token_expired', 'token_absent', 'token_invalid'];

                        // Loop through each rejection reason and redirect to the login
                        // state if one is encountered
                        angular.forEach(rejectionReasons, function(value, key) {

                            if(rejection.data.error === value) {

                                // If we get a rejection corresponding to one of the reasons
                                // in our array, we know we need to authenticate the user so 
                                // we can remove the current user from local storage
                                localStorage.removeItem('user');

                                // Send the user to the auth state so they can login
                                $state.go('appSimple.login');
                            }
                        });

                        return $q.reject(rejection);
                    }
                }
            }

            // Setup for the $httpInterceptor
            $provide.factory('redirectWhenLoggedOut', redirectWhenLoggedOut);

            // Push the new factory onto the $http interceptor array
            $httpProvider.interceptors.push('redirectWhenLoggedOut');
            
        $authProvider.loginUrl = 'api/authenticate';
        $urlRouterProvider.otherwise('/dashboard');

        $ocLazyLoadProvider.config({
            // Set to true if you want to see what and when is dynamically loaded
            debug: true
        });

        $breadcrumbProvider.setOptions({
            prefixStateName: 'app.main',
            includeAbstract: true,
            template: '<li class="breadcrumb-item" ng-repeat="step in steps" ng-class="{active: $last}" ng-switch="$last || !!step.abstract"><a ng-switch-when="false" href="{{step.ncyBreadcrumbLink}}">{{step.ncyBreadcrumbLabel}}</a><span ng-switch-when="true">{{step.ncyBreadcrumbLabel}}</span></li>'
        });

        $stateProvider
            .state('app', {
                abstract: true,
                templateUrl: 'views/common/layouts/full.html',
                controller:'MainController as mc',
                //page title goes here
                ncyBreadcrumb: {
                    label: 'Root',
                    skip: true
                },
                resolve: {
                    loadCSS: ['$ocLazyLoad', function($ocLazyLoad) {
                        // you can lazy load CSS files
                        return $ocLazyLoad.load([{
                            serie: true,
                            name: 'Font Awesome',
                            files: ['css/font-awesome.min.css']
                        },{
                            serie: true,
                            name: 'Simple Line Icons',
                            files: ['css/simple-line-icons.css']
                        }]);
                    }],
                }
            })
            .state('app.main', {
                url: '/dashboard',
                templateUrl: 'views/main.html',
                //page title goes here
                ncyBreadcrumb: {
                    label: 'Home',
                }
            })
            .state('app.office',{
                url:'/office',
                template: '<ui-view></ui-view>',
                abstracct:true
            })
            .state('app.office.organization',{
                url:'/organization',
                templateUrl: 'views/org/org.html',
                controller:'OrgController as org'
            })
            .state('app.office.ownership',{
                url:'/org-ownership',
                templateUrl: 'views/org/org-ownership.html',
                controller:'OwnershipController as own'
            })
            .state('app.office.type',{
                url:'/org-type',
                templateUrl: 'views/org/org-type.html',
                controller:'OrgTypeController as typ'
            })
            .state('app.office.size',{
                url:'/org-size',
                templateUrl: 'views/org/org-size.html',
                controller:'OrgsizeController as orgsize'
            })
            .state('app.office.profile',{
                url:'/org-profile',
                templateUrl: 'views/org/org-profile.html',
                controller:'orgProfileController as oprofile'
            })
            .state('app.office.jobpost',{
                url:'/org-jobpost',
                templateUrl: 'views/org/org-job-post.html',
                controller:'orgJobPostController as ojobpost'
            })
            
            .state('app.profile',{
                url:'/profile',
                templateUrl: 'views/profile.html',
                controller:'Profile as pfl'
            })
            
            .state('appSimple', {
                abstract: true,
                templateUrl: 'views/common/layouts/simple.html',
                resolve: {
                    loadPlugin: ['$ocLazyLoad', function ($ocLazyLoad) {
                        // you can lazy load files for an existing module
                        return $ocLazyLoad.load([{
                            serie: true,
                            name: 'Font Awesome',
                            files: ['css/font-awesome.min.css']
                        },{
                            serie: true,
                            name: 'Simple Line Icons',
                            files: ['css/simple-line-icons.css']
                        }]);
                    }],
                }
            })

            // Additional Pages
            .state('appSimple.login', {
                url: '/login',
                templateUrl: 'views/pages/login.html',
                controller:'AuthController as auth'
            })
            .state('appSimple.register', {
                url: '/register',
                templateUrl: 'views/pages/register.html'
            })
            .state('appSimple.404', {
                url: '/404',
                templateUrl: 'views/pages/404.html'
            })
            .state('appSimple.500', {
                url: '/500',
                templateUrl: 'views/pages/500.html'
            })
    }]);
