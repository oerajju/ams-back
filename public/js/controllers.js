// controller.js
(function() {

    'use strict';
    angular.module('app').service('generalService',generalService);
    
    function generalService($http) {
        //var vm = {};
        this.hello = function(text){
            return "hello "+text+" !";
        };
        this.getAllData = function(url){
            $http.get(url).then(function (response) {
                console.log(response.data.data);
                    //vm.data = response.data.data; 
                    return response.data;
//                    vm.totalItems = response.data.total;
//                    vm.currentPage = response.data.current_page;
//                    vm.perPage = response.data.per_page;
                });
        };
        //return vm;
    }

    angular
        .module('app')
        .controller('AuthController', AuthController);


    function AuthController($auth, $state, $http, $rootScope,toastr) {

        var vm = this;
        vm.loginError = false;
        vm.loginErrorText;
            
        vm.login = function() {

            var credentials = {
                email: vm.email,
                password: vm.password
            };
            //$authProvider.loginUrl = $rootScope.baseUrl + 'api/authenticate';
            $auth.login(credentials,{url:$rootScope.baseUrl + 'api/authenticate'}).then(function() {
                $http.get($rootScope.baseUrl +'api/authenticate/user').then(function(response) {

                // Stringify the returned data to prepare it
                // to go into local storage
                var user = JSON.stringify(response.data.user);

                // Set the stringified user data into local storage
                localStorage.setItem('user', user);

                // The user's authenticated state gets flipped to
                // true so we can now show parts of the UI that rely
                // on the user being logged in
                $rootScope.authenticated = true;
                $rootScope.usersName = user.name;

                // Putting the user's data on $rootScope allows
                // us to access it anywhere across the app
                $rootScope.currentUser = response.data.user;

                // Everything worked out so we can now redirect to
                // the users state to view the data
                toastr.success("Logged In Successfully!","Login Success");
                $state.go('app.main',{});
            }); 
            }).catch(function(error){
                toastr.error("Invalid Username Or Password","Login Error");
                vm.loginError = true;
                vm.loginErrorText = error.data.error;
            });
        };

    }
    
    //start of another controller
    
    angular
        .module('app')
        .controller('MainController', MainController);

    function MainController($auth, $state, $rootScope, $http,toastr) {

        var vm = this;
        vm.usersName = JSON.parse(localStorage.getItem('user')).name;
        vm.getPermissions = function(){
            $http.get($rootScope.baseUrl + "api/authenticate/permissions").then(function (response){
                var permissions = JSON.stringify(response.data.permissions);
                localStorage.setItem('permissions',permissions);
            });
        };
        vm.checkPermissions = function(permission){
            var permissions = JSON.parse(localStorage.getItem('permissions'));
            //console.log(permissions);
            if($.inArray(permission,permissions) === -1){
                return false;
            }
            return true;
        };
        vm.getPermissions();
        

        vm.logOut = function () {
            var serverLogoutLink = $rootScope.baseUrl + "api/authenticate/logout";
            //$http.get(serverLogoutLink).then(function (response) {
//                if (response.data === true) {
                    $auth.logout().then(function () {
                        // Remove the authenticated user from local storage
                        localStorage.removeItem('user');

                        // Flip authenticated to false so that we no longer
                        // show UI elements dependant on the user being logged in
                        $rootScope.authenticated = false;
                        

                        // Remove the current user info from rootscope
                        $rootScope.currentUser = null;
                        toastr.success("Logged Out Successfully!","Logout");
                        $state.go('appSimple.login', {});
                    });
//                } else {
//                    toastr.error("Request Not Valid.","App Error");
//                }

//            }).catch(function (response) {
//                console.log(response);
//                toastr.error("Cannot Connect to server.","Server Error");
//            });
        };

    }
    
    //start of another controller
    angular
        .module('app')
        .controller('OrgController', OrgController);
    
    function OrgController($state, $rootScope, $http,toastr){
        var vm = this;
        vm.currentPage='';
        vm.totalItems = '';
        vm.perPage = '';
        vm.reqPerPage=10;
        vm.orgList=[];
        vm.formIsValid = false;
        vm.hiddenParent;
        vm.perPageOptions = [10,20,30];
        vm.searchable = ["all","id","name","code"];
        vm.searchOption='';
        vm.searchTerm='';
        
        vm.formModel = {
            id:'',
            name:'',
            hide_name:'',
            alt_name:'',
            org_type:'',
            ownership:'',
            size_of_org:'',
            logo:'',
            cover_photo:'',
            country:'',
            city:'',
            address:'',
            phone:'',
            fax:'',
            post_box_no:'',
            email:'',
            alternate_email:'',
            website:'',
            cs:'',
            cname:'',
            cemail:'',
            cphone:''
            
            
        };
        vm.submitForm = function () {
            if(vm.formModel.parent==''){
                    vm.formModel.parent = 0;
                }
            if (vm.formModel.id === '') {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'org',
                    data: vm.formModel
                };
            } else {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'org/'+vm.formModel.id,
                    data: vm.formModel
                };
            }

            $http(req).then(function (response) {
                if(vm.formModel.id === ''){
                    vm.orgList.unshift(response.data);
                }else{
                    vm.updateChange(response.data);
                }
                vm.resetForm();
                orgForm.reset();
                toastr.success("Data saved successfully.");
            }).catch(function (response) {
                toastr.error("Data not saved.");
            });
        };
        
        vm.updateChange = function(data){
            for(var i=0;i<vm.orgList.length;i++){
                if(vm.orgList[i].id===data.id){
                    vm.orgList[i] = data;
                    break;
                }
            }
        };

        vm.resetForm = function () {
            for (var k in vm.formModel) {
                vm.formModel[k] = '';
            }
        };

        vm.getDistricts = function () {
            $http.get($rootScope.baseUrl + 'org/districts').then(function (response) {
                vm.districtList = response.data;
            });
        };
        
        vm.getParents = function(){
            var url = $rootScope.baseUrl + "org/parents";
            $http.get(url).then(function (response) {
                vm.parentList = response.data;
            });
        };
        vm.getOrgList = function(){
            
            var url= $rootScope.baseUrl + 'org?page='+(vm.currentPage||1);
            if(vm.reqPerPage !== ''){
                url = $rootScope.baseUrl + 'org?page='+(vm.currentPage||1)+"&perPage="+vm.reqPerPage;
            }
            if(vm.searchTerm !== '' && vm.searchTerm.trim().length > 0){
                url +="&searchOption="+vm.searchOption+"&searchTerm="+vm.searchTerm;
            }
            $http.get(url).then(function (response) {
                vm.orgList = response.data.data;
                vm.totalItems = response.data.total;
                vm.currentPage = response.data.current_page;
                vm.perPage = response.data.per_page;
            });
        };
        
        vm.editForm = function(id){
            if(!vm.hiddenParent == ''){
                vm.unhideParent();
            }
            vm.hideParent(id);
            var url= $rootScope.baseUrl + 'org/'+id+"/edit";
            $http.get(url).then(function (response) {
                for (var k in vm.formModel) {
                    vm.formModel[k] = response.data[k];
                }
            });
        };
        vm.hideParent = function(id){
            for(var i=0;i<vm.parentList.length;i++){
                if(vm.parentList[i].id==id){
                    vm.hiddenParent=i;
                    vm.parentList[i]['hidden']=true;
                }
            }
        };
        vm.unhideParent=function(){
            delete vm.parentList[vm.hiddenParent].hidden;
        };
        //initilize the page
        vm.getDistricts();
        vm.getParents();
        vm.getOrgList();
        
        
    }
    angular
        .module('app')
        .controller('Profile', Profile);
    
    function Profile($state, $rootScope, $http,toastr){
        var vm = this;
        vm.user = JSON.parse(localStorage.getItem('user'));
        vm.old_password = '';
        vm.new_password = '';
        vm.cnew_password = '';
        vm.chUrl = $rootScope.baseUrl + "api/authenticate/change-password";
        vm.resetForm = function () {
            vm.old_password = '';
            vm.new_password = '';
            vm.cnew_password = '';
            
        };
        vm.changePassword = function (){
            if(vm.old_password =='' || vm.new_password == '' || vm.cnew_password==''){
                toastr.error("Please fill the form Correctly","Error");
                return false;
            }
            if(vm.new_password !== vm.cnew_password){
                toastr.error("New Password do not match with Confirm Password","Error");
                return false;
            }
            var data = {
                old_password:vm.old_password,
                new_password:vm.new_password,
                cnew_password:vm.cnew_password
            };
            var req = {
                    method: 'POST',
                    url: vm.chUrl,
                    data: data
                };
            $http(req).then(function (response) {
                vm.resetForm();
                toastr.success(response.data.message,"Success");
            }).catch(function (response) {
                toastr.error(response.data.message,"Error");
            });
            
        };
    }
    
    angular
        .module('app')
        .controller('OwnershipController', OwnershipController);
    
    function OwnershipController($state, $rootScope, $http,toastr,generalService){
        var vm = this;
        var url= $rootScope.baseUrl + 'ownership?page=1';
        console.log(generalService.hello('world'));
//        generalService.getAllData(url).then(function(response){
//            console.log(1);
//            console.log(response);
//        });
        vm.currentPage='';
        vm.totalItems = '';
        vm.perPage = '';
        vm.reqPerPage=10;
        vm.orgList=[];
        vm.formIsValid = false;
        vm.hiddenParent;
        vm.perPageOptions = [10,20,30];
        vm.searchable = ["all","id","name"];
        vm.searchOption='';
        vm.searchTerm='';
        
        vm.formModel = {
            id:'',
            name:'',
        };
        vm.submitForm = function () {
            if (vm.formModel.id === '') {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'ownership',
                    data: vm.formModel
                };
            } else {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'ownership/'+vm.formModel.id,
                    data: vm.formModel
                };
            }

            $http(req).then(function (response) {
                if(vm.formModel.id === ''){
                    vm.orgList.unshift(response.data);
                }else{
                    vm.updateChange(response.data);
                }
                vm.resetForm();
                orgForm.reset();
                toastr.success("Data saved successfully.");
            }).catch(function (response) {
                toastr.error("Data not saved.");
            });
        };
        
        vm.updateChange = function(data){
            for(var i=0;i<vm.orgList.length;i++){
                if(vm.orgList[i].id===data.id){
                    vm.orgList[i] = data;
                    break;
                }
            }
        };

        vm.resetForm = function () {
            for (var k in vm.formModel) {
                vm.formModel[k] = '';
            }
        };
        vm.getOrgList = function(){
            
            var url= $rootScope.baseUrl + 'ownership?page='+(vm.currentPage||1);
            if(vm.reqPerPage !== ''){
                url = $rootScope.baseUrl + 'ownership?page='+(vm.currentPage||1)+"&perPage="+vm.reqPerPage;
            }
            if(vm.searchTerm !== '' && vm.searchTerm.trim().length > 0){
                url +="&searchOption="+vm.searchOption+"&searchTerm="+vm.searchTerm;
            }
            $http.get(url).then(function (response) {
                vm.orgList = response.data.data;
                vm.totalItems = response.data.total;
                vm.currentPage = response.data.current_page;
                vm.perPage = response.data.per_page;
            });
        };
        
        vm.editForm = function(id){
            var url= $rootScope.baseUrl + 'ownership/'+id+"/edit";
            $http.get(url).then(function (response) {
                for (var k in vm.formModel) {
                    vm.formModel[k] = response.data[k];
                }
            });
        };
        //initilize the page
        vm.getOrgList();
    }
    angular
        .module('app')
        .controller('OrgTypeController', OrgTypeController);
    
    function OrgTypeController($state, $rootScope, $http,toastr){
        var vm = this;
        vm.currentPage='';
        vm.totalItems = '';
        vm.perPage = '';
        vm.reqPerPage=10;
        vm.orgList=[];
        vm.formIsValid = false;
        vm.hiddenParent;
        vm.perPageOptions = [10,20,30];
        vm.searchable = ["all","id","name"];
        vm.searchOption='';
        vm.searchTerm='';
        
        vm.formModel = {
            id:'',
            name:'',
        };
        vm.submitForm = function () {
            if (vm.formModel.id === '') {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'org-type',
                    data: vm.formModel
                };
            } else {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'org-type/'+vm.formModel.id,
                    data: vm.formModel
                };
            }

            $http(req).then(function (response) {
                if(vm.formModel.id === ''){
                    vm.orgList.unshift(response.data);
                }else{
                    vm.updateChange(response.data);
                }
                vm.resetForm();
                orgForm.reset();
                toastr.success("Data saved successfully.");
            }).catch(function (response) {
                toastr.error("Data not saved.");
            });
        };
        
        vm.updateChange = function(data){
            for(var i=0;i<vm.orgList.length;i++){
                if(vm.orgList[i].id===data.id){
                    vm.orgList[i] = data;
                    break;
                }
            }
        };

        vm.resetForm = function () {
            for (var k in vm.formModel) {
                vm.formModel[k] = '';
            }
        };
        vm.getOrgList = function(){
            
            var url= $rootScope.baseUrl + 'org-type?page='+(vm.currentPage||1);
            if(vm.reqPerPage !== ''){
                url = $rootScope.baseUrl + 'org-type?page='+(vm.currentPage||1)+"&perPage="+vm.reqPerPage;
            }
            if(vm.searchTerm !== '' && vm.searchTerm.trim().length > 0){
                url +="&searchOption="+vm.searchOption+"&searchTerm="+vm.searchTerm;
            }
            $http.get(url).then(function (response) {
                vm.orgList = response.data.data;
                vm.totalItems = response.data.total;
                vm.currentPage = response.data.current_page;
                vm.perPage = response.data.per_page;
            });
        };
        
        vm.editForm = function(id){
            var url= $rootScope.baseUrl + 'org-type/'+id+"/edit";
            $http.get(url).then(function (response) {
                for (var k in vm.formModel) {
                    vm.formModel[k] = response.data[k];
                }
            });
        };
        //initilize the page
        vm.getOrgList();
    }
    
    angular
        .module('app')
        .controller('OrgsizeController', OrgsizeController);
    
    function OrgsizeController($state, $rootScope, $http,toastr){
        var vm = this;
        vm.currentPage='';
        vm.totalItems = '';
        vm.perPage = '';
        vm.reqPerPage=10;
        vm.orgList=[];
        vm.formIsValid = false;
        vm.hiddenParent;
        vm.perPageOptions = [10,20,30];
        vm.searchable = ["all","id","emp_size"];
        vm.searchOption='';
        vm.searchTerm='';
        
        vm.formModel = {
            id:'',
            emp_size:'',
        };
        vm.submitForm = function () {
            if (vm.formModel.id === '') {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'emp-size',
                    data: vm.formModel
                };
            } else {
                var req = {
                    method: 'POST',
                    url: $rootScope.baseUrl + 'emp-size/'+vm.formModel.id,
                    data: vm.formModel
                };
            }

            $http(req).then(function (response) {
                if(vm.formModel.id === ''){
                    vm.orgList.unshift(response.data);
                }else{
                    vm.updateChange(response.data);
                }
                vm.resetForm();
                orgForm.reset();
                toastr.success("Data saved successfully.");
            }).catch(function (response) {
                toastr.error("Data not saved.");
            });
        };
        
        vm.updateChange = function(data){
            for(var i=0;i<vm.orgList.length;i++){
                if(vm.orgList[i].id===data.id){
                    vm.orgList[i] = data;
                    break;
                }
            }
        };

        vm.resetForm = function () {
            for (var k in vm.formModel) {
                vm.formModel[k] = '';
            }
        };
        vm.getOrgList = function(){
            
            var url= $rootScope.baseUrl + 'emp-size?page='+(vm.currentPage||1);
            if(vm.reqPerPage !== ''){
                url = $rootScope.baseUrl + 'emp-size?page='+(vm.currentPage||1)+"&perPage="+vm.reqPerPage;
            }
            if(vm.searchTerm !== '' && vm.searchTerm.trim().length > 0){
                url +="&searchOption="+vm.searchOption+"&searchTerm="+vm.searchTerm;
            }
            $http.get(url).then(function (response) {
                vm.orgList = response.data.data;
                vm.totalItems = response.data.total;
                vm.currentPage = response.data.current_page;
                vm.perPage = response.data.per_page;
            });
        };
        
        vm.editForm = function(id){
            var url= $rootScope.baseUrl + 'emp-size/'+id+"/edit";
            $http.get(url).then(function (response) {
                for (var k in vm.formModel) {
                    vm.formModel[k] = response.data[k];
                }
            });
        };
        //initilize the page
        vm.getOrgList();
    }
    angular
        .module('app')
        .controller('orgProfileController', orgProfileController);
    function orgProfileController($scope){
        
    }
    angular
        .module('app')
        .controller('orgJobPostController', orgJobPostController);
    function orgJobPostController($scope){
        
    }

})();
