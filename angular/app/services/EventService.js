/*
    Event Service
*/
EventService.$inject = ['$http','$rootScope','$window','Upload',];
export default function EventService($http,$rootScope,$window,Upload) {

	//Add new Event
    this.createEvent = (files,formdata) => {

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
    }

    // Get listing of events
    this.getAllEvent=(formdata=undefined)=>{

        var url='api/events/limit/'+formdata.limit+'/offset/'+formdata.offset;
        return $http.get($rootScope.apiUrl+url)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    this.getDeleteMsg=()=>{

        return $http.get($rootScope.apiUrl+'api/events/confirmationmessage/delete')
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Search Events
    this.getserachEvent= () => {

        return $http.get($rootScope.apiUrl+'api/events/autosearch')
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    // Get particular event details
    this.getEvent=(id)=>{

        return $http.get($rootScope.apiUrl+'api/events/'+id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    // Delete Event
    this.deleteEvent=(id)=>{

        return $http.delete($rootScope.apiUrl+'api/events/'+id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    // Remove Event Image
    this.remove_image=(id)=>{

        return $http.post($rootScope.apiUrl+'api/events/update_event_picture/'+id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    // Update Event
    this.updateEvent=(files,formdata)=>{

        return Upload.upload({
            url: $rootScope.apiUrl+'api/events/'+formdata.id,
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

    // Search for a event
    this.searchEvent=(search)=>{

        return $http.get($rootScope.apiUrl+'api/events/search/'+search)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    this.event_analysis=(id)=>{
        return $http.post($rootScope.apiUrl+'api/event_analysis',{id:id})
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

}
