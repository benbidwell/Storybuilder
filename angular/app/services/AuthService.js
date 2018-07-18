/*
    Auth SERVICE
 */
AuthService.$inject = ['$http','$rootScope','$window','Upload','$state'];
export default function AuthService($http,$rootScope,$window,Upload,$state) {

    //Add new user
    this.registration = (formdata) => {
        return $http.post($rootScope.apiUrl+'api/register', formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    //Forgot Password
    this.forgotPassword = (formdata) => {
        return $http.post($rootScope.apiUrl+'api/forgot_password',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // User login
    this.login = (formdata) => {
        return $http.post($rootScope.apiUrl+'api/login',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Check authentication
    this.auth   = () => {
        if($window.localStorage.ud=='' || $window.localStorage.ud==undefined){
           $window.location.href='/login';
            // return false;
        }
        else {
            return true;
        }
    }
    // get user details
    this.getUserDetails   = () => {
        return $http.get($rootScope.apiUrl+'api/edit_profile')
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    // Edit Profile
    this.editProfile=(formdata)=>{
        return Upload.upload({
            url: $rootScope.apiUrl+'api/update_profile',
                processData: false,
                data:  formdata,
            }).then(function (resp){
                return resp;
            }, function (resp) {
                    return resp;
            }, function (evt) {
                var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                $rootScope.progressPercentage=progressPercentage;
            });
    }
    // Reset Password
    this.resetPassword=(formdata)=>{
        return $http.post($rootScope.apiUrl+'api/reset_password',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Change Password
    this.changePassword=(formdata)=>{
        return $http.post($rootScope.apiUrl+'api/change_password',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
}
