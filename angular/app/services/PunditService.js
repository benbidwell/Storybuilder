/*
     Pundit SERVICE
 */
PunditService.$inject = ['$http','$rootScope','Upload'];
export default function PunditService($http,$rootScope,Upload) {

    // Service Variable
    this.formdata={
        story_id:undefined,
        audio:undefined,
        video_title:'sdf',
        video_name:'sadfas'
    }
    // Get original video
    this.getOriginalVideo=(id)=>{
    	return $http.get($rootScope.apiUrl+'api/stories/'+id)
        .then(function successCallback(response){
            return response;
        }, function errorCallback(response){
            return response;
        })
    }
    // increment view count of story
    this.addView=(story_id,pundit_id)=>{
        if(pundit_id){
            var url=$rootScope.apiUrl+'api/storyview/'+story_id+'/'+pundit_id;
        } else {
            var url=$rootScope.apiUrl+'api/storyview/'+story_id;
        }
      return $http.get(url)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // increment share count of story
    this.addShare=(story_id,pundit_id)=>{
        if(pundit_id){
            var url=$rootScope.apiUrl+'api/storyshare/'+story_id+'/'+pundit_id;
        } else {
            var url=$rootScope.apiUrl+'api/storyshare/'+story_id;
        }
      return $http.get(url)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // get suggested video
    this.getSuggestedVideo=(story_id,storyOptions)=>{
    	return $http.post($rootScope.apiUrl+'api/pundit_published_story/'+story_id,storyOptions)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // get top rated video
    this.getPopularVideo=(storyOptions)=>{
    	return $http.post($rootScope.apiUrl+'api/popularVideo',storyOptions)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Publish pundit video in case of user not logged in
    this.publishPunditVideo=(formdata)=>{
      return $http.post($rootScope.apiUrl+'api/pundit_video_publish',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Publish pundit video when user logged in
    this.publishPunditVideoLoggedUser=(formdata)=>{
        return $http.post($rootScope.apiUrl+'api/pundit_video_publish_logged_user',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Save user as a pundit
    this.punditRegister=()=>{
        return $http.get($rootScope.apiUrl+'api/pundit_published_story/'+story_id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Create preview of pundit video
    this.createPreview=(formdata)=>{
        return Upload.upload({
            url: $rootScope.apiUrl+'api/pundit_add_audio_on_video',
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
    // Activate pundit account
    this.punditActivation=(formdata)=>{
        return $http.post($rootScope.apiUrl+'api/pundit_activate_account',formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // get pundit video
    this.getPunditVideo=(story_id,pundit_story_id)=>{      
        return $http.get($rootScope.apiUrl+'api/pundit_published_story/'+story_id+'/'+pundit_story_id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
}
