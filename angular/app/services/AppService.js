/*
    Auth SERVICE
 */
AppService.$inject = ['$http','$rootScope','$window','Upload','$state'];
export default function AppService($http,$rootScope,$window,Upload,$state) {

    this.getAdminSettings   = () => {
        return $http.get($rootScope.apiUrl+'api/getAdminSettings')
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
}
