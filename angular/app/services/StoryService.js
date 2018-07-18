/*
    Story Services
*/
StroyService.$inject = ['$http','$rootScope','$window','Upload',];
export default function StroyService($http,$rootScope,$window,Upload) {

	//Add new Story
    this.createStory = (files,formdata) => {

        return Upload.upload({
            url: $rootScope.apiUrl+'api/stories',
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

    // listing of stories
    this.getAllStroy=(formdata=undefined)=>{

        var url='api/stories/'+formdata.event_id+'/allstories';
        if(formdata!=undefined && formdata.limit)
            url+='?limit='+formdata.limit;
        if(formdata!=undefined && formdata.offset!=undefined)
            url+='&offset='+formdata.offset;

        if(formdata!=undefined && formdata.offset!=undefined)
            url+='&event_id='+formdata.event_id;

        return $http.get($rootScope.apiUrl+url)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){

                return response;
            })
    }
    // get details of particular story
    this.getStroy=(id)=>{
        return $http.get($rootScope.apiUrl+'api/stories/'+id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Delete Story
    this.deleteStory=(id)=>{
        // debugger
        return $http.delete($rootScope.apiUrl+'api/stories/'+id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Delete Msg
    this.getDeleteMsg=()=>{
        return $http.get($rootScope.apiUrl+'api/stories/confirmationmessage/delete')
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Update or edit story
    this.updateStroy=(files,formdata)=>{
        return Upload.upload({
            url: $rootScope.apiUrl+'api/stories/'+formdata.id,
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
    // Update or edit story
    this.updateStroy2=(formdata)=>{
        return $http.post($rootScope.apiUrl+'api/stories2/'+formdata.id,formdata)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Search story
    this.searchStory=(search,event)=>{
        return $http.get($rootScope.apiUrl+'api/event/'+event+'/stories/search/'+search)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Delete story account
    this.deleteSocialAccount=(social_type,AccountId)=>{
        let index;
        if(social_type=='Twitter'){
            index=$rootScope.twitter.map(function(el){return el['user_id'];}).indexOf(AccountId);

            if(index!=-1){
                $rootScope.twitter.splice(index,1);
                $window.localStorage.twitter=JSON.stringify($rootScope.twitter);
            }
        }
        else if(social_type=='Dropbox'){
                let index=$rootScope.dropbox.map(function(el){return el['user_id'];}).indexOf(AccountId);
                if(index!=-1){
                    $rootScope.dropbox.splice(index,1);
                    $window.localStorage.dropbox=JSON.stringify($rootScope.dropbox);
                }
        }
        else if(social_type=='Google Drive'){
                let index=$rootScope.gdrive.map(function(el){return el['user_id'];}).indexOf(AccountId);
                if(index!=-1){
                    $rootScope.gdrive.splice(index,1);
                    $window.localStorage.gdrive=JSON.stringify($rootScope.gdrive);
                }
        } else if(social_type=='Google'){
                let index=$rootScope.gplus.map(function(el){return el['user_id'];}).indexOf(AccountId);
                if(index!=-1){
                    $rootScope.gplus.splice(index,1);
                    $window.localStorage.gplus=JSON.stringify($rootScope.gplus);
                }
        }  else if(social_type=='Facebook'){
                let index=$rootScope.facebook.map(function(el){return el['user_id'];}).indexOf(AccountId);
                if(index!=-1){
                    $rootScope.facebook.splice(index,1);
                    $window.localStorage.facebook=JSON.stringify($rootScope.facebook);
                }
        }  else if(social_type=='Instagram'){
                let index=$rootScope.instagram.map(function(el){return el['user_id'];}).indexOf(AccountId);
                if(index!=-1){
                    $rootScope.instagram.splice(index,1);
                    $window.localStorage.instagram=JSON.stringify($rootScope.instagram);
                }
        }

        if(index!=-1){
            $rootScope.newStory.media=$rootScope.newStory.media.filter(function(item){
                            return item.id!=AccountId;
                        });
            $window.localStorage.newStory=JSON.stringify($rootScope.newStory);
        }
        $rootScope.deleteAccount=false;
    }

    // edit story media
    this.uploadSound = (data,id) => {
        var formdata = { audio: data, story_id: 1}
        return Upload.upload({
            url: $rootScope.apiUrl+'api/story_audio/',
            data:  formdata,
            }).then(function (resp){
                return resp;
            }, function (resp) {
                return resp;
            });
    }

    // Get uploaded story audio list
    this.getAudioList = (id) =>{
        return $http.get($rootScope.apiUrl+'api/get_story_audio/'+id)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Create preview of story video
    this.createPreview=(data,storyId,sound)=>{
        return $http.post('api/story_make_video',{video_array: data,story_id:storyId,sound:sound})
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
    // Add audio to video
    this.addAudioToVideo=(data)=>{
        return Upload.upload({
            url: $rootScope.apiUrl+'api/story_add_audio_on_video',
            data:  data,
            }).then(function (resp){
                return resp;
            }, function (resp) {
                    return resp;
            }, function (evt) {
                var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                $rootScope.progressPercentage=progressPercentage;
            });
    }
    // published video
    this.publishVideo=(data)=>{
        return $http.post('api/story_add_water_mark',data)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }
}
