/*
	Local Storage Service
*/
LocalStorageService.$inject = ['$http','$rootScope','$window','Upload',];
export default function LocalStorageService($http,$rootScope,$window,Upload) {
    // Get local Storage Media
    this.get_localStorage = (story_id)=>{
    	return $http.get($rootScope.apiUrl+'api/story_media/'+story_id+'/limit/50/offset/0')
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
}
