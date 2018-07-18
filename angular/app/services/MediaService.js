/* 
    Media Service
*/
MediaService.$inject = ['$http','$rootScope','$window','Upload',]; 
export default function MediaService($http,$rootScope,$window,Upload) {
    // Upload media 
	this.upload_media= (files,formdata) => {
        return Upload.upload({
            url: $rootScope.apiUrl+'api/events',
            data:  formdata,
            }).then(function (resp){
                return resp;
            }, function (resp) {
                    return resp;
            }, function (evt) {
                var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                $rootScope.progressPercentage=progressPercentage;
            });
    };
}