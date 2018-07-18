/*
    Facebook Service
*/
FbService.$inject = ['$http','$rootScope','$window','Upload','$q','Notification'];
export default function FbService($http,$rootScope,$window,Upload,$q,notification) {

    // FB login function
	this.fb_login=()=>{
        var deferred=$q.defer();
        FB.login(function(response) {
            if (response.authResponse){
                var authresp = response.authResponse;
                
                var jsSHA = require("jssha");
                var hash_hmac=(string, secret)=>{
                    let shaObj = new jsSHA("SHA-256", "TEXT");
                    shaObj.setHMACKey(secret, "TEXT");
                    shaObj.update(string);
                    let hmac = shaObj.getHMAC("HEX");
                    return hmac;
                };

                var appsecret_proof= hash_hmac(authresp.accessToken, '94cbbca16f540bdd5f9244dc517216a0'); 
             
                FB.api("https://graph.facebook.com/v2.9/me?fields=name,id,first_name,last_name,email,gender&appsecret_proof="+appsecret_proof+"&access_token="+authresp.accessToken, function(response) {
                    console.log(response);
                    if (!response || response.error) {
                        return 'Error occured';
                    } else {
                        if($window.localStorage.facebook==undefined || $window.localStorage.facebook==''){
                            var facebook=[];
                            facebook.push({user_id:'facebook'+response.name,screen_name:response.name,token:authresp.accessToken});
                        } else {
                            var facebook=JSON.parse($window.localStorage.facebook);
                            facebook.push({user_id:'facebook'+response.name,screen_name:response.name,token:authresp.accessToken});
                        }
                        $window.localStorage.facebook=JSON.stringify(facebook);
                        $rootScope.facebook=JSON.stringify(facebook);

                        FB.api("https://graph.facebook.com/v2.9/me/photos?fields=picture,images,created_time,place&type=uploaded&limit=100&appsecret_proof="+appsecret_proof+"&access_token="+authresp.accessToken,
                            function (resp) {
                            var facebook_media=[];
                            angular.forEach(resp.data,function(data,key){
                                facebook_media.push({
                                    type: 'facebook',
                                    media_type: 'photo',
                                    media_url: (data.images.length?data.images[0].source:data.picture),
                                    id:'facebook' + response.name,
                                    created_at: data.created_time,
                                    location:(data.place?data.place.name:data.place),
                                    channel_type:'Account'
                                });
                            })

                            FB.api("https://graph.facebook.com/v2.9/me/videos?fields=picture,place,created_time,source,updated_time&limit=20&type=uploaded&appsecret_proof="+appsecret_proof+"&access_token="+authresp.accessToken,
                            function (resp) {

                                angular.forEach(resp.data,function(data,key){
                                    facebook_media.push({
                                        type: 'facebook',
                                        media_type: 'video',
                                        media_url: data.source,
                                        id:'facebook' + response.name,
                                        created_at: data.created_time,
                                        location:(data.place?data.place.name:data.place),
                                        channel_type:'Account'
                                    });
                                });
                                facebook_media.sort(function(a,b) {
                                    return a.created_time-b.created_time
                                });
                                var orig_fb_media=[];
                                facebook_media.forEach(function(item) {
                                    if(orig_fb_media.length<100){
                                        orig_fb_media.push(item);
                                    }
                                });
                                $window.localStorage.temp=JSON.stringify(orig_fb_media);
                                FB.logout(function (response) {

                                });
                                setTimeout(function(){
                                   $window.location.reload();
                                },200)
                            });
                        });
                    }
                })
            }
            else{
                return 'Error occured';
            }
        },{scope: 'email,user_photos,user_videos'} );

    }

    this.fb_post_video_init=(data)=>{
        
        debugger;
        var UserResp = JSON.parse($window.localStorage.facebook)[0];
        $http.get('https://graph.facebook.com/v2.9/me/?access_token='+UserResp.token)
        .then(function successCallback(response){
            
            var userID = response.data.id;
            return $http.post($rootScope.apiUrl+'api/post_curl_response_fb',{
                url:"https://graph-video.facebook.com/v2.3/"+userID+"/videos",
                post_data:"upload_phase=start&file_size="+data.size+"&access_token="+UserResp.token,
                command:"init"
            })
            .then(function successCallback(response){
                return response;
              
            }, function errorCallback(response){
                
                return response;
            });

        }, function errorCallback(response){
            return response;
        })
        
    }

    this.fb_post_video_append=(data)=>{
        
        debugger;
            var UserResp = JSON.parse($window.localStorage.facebook)[0];
            var userID = data.userId;
            return $http.post($rootScope.apiUrl+'api/post_curl_response_fb',{
                url:"https://graph-video.facebook.com/v2.3/"+userID+"/videos",
                post_data:"upload_phase=start&file_size="+data.size+"&access_token="+UserResp.token,
                command:"init"
            })
            .then(function successCallback(response){
                return response;
              
            }, function errorCallback(response){
                
                return response;
            });

        
        
    }

    // Facebook share
    this.fb_share=(link)=>{
        var deferred=$q.defer();
        FB.ui({
            method: 'share',
            href: link,
        }, function(response){});
         deferred.resolve(true);
         return deferred.promise;
    }

    // Get facebook user upload images
    this.fb_import_images=(token)=>{

        return $http.get('https://graph.facebook.com/v2.9/me/photos?fields=picture,created_time,name&limit=10&method=get&type=uploaded&access_token='+token)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    // GEt facebook user upload video
    this.fb_import_video=(token)=>{

        return $http.get('https://graph.facebook.com/v2.9/me/videos?limit=10&type=uploaded&access_token='+token)
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                return response;
            })
    }

    this.searchHashtag=(data)=>{
			
      return $http.post($rootScope.apiUrl+'api/facebookHashtagSerach',data)
        .then(function successCallback(response){
            return response;
        }, function errorCallback(response){
            return response;
        })
    }

    this.uploadVideo=(data)=>{
        var deferred=$q.defer();
        FB.login(function(response) {
            if (response.authResponse){
               debugger;
               console.log(response);
               var authresp = response.authResponse;
                 
                return $http.post($rootScope.apiUrl+'api/post_curl_response_fb',{
                    video_url:data.video_url,
                    Authorization:response.authResponse.accessToken,
                    formdata:data
                })
                    .then(function successCallback(response){
                        notification.success({
                            message: 'Video posted succesfully',
                            positionY: 'bottom',
                            positionX: 'right',
                            delay: 3000,
                          });
                        return response;
                    }, function errorCallback(response){
                        return response;
                    })
           }
            else{
                return 'Error occured';
            }
        },{scope: 'email,user_photos,user_videos,publish_actions'} );
    }

    this.fbLikeCount=(id,authToken,appsecret)=>{
        var access_token='EAACEdEose0cBAC93h4561ZBmN5E46PezEs94H44dFhggJg5mDGXL9I87Eh9xKlg0qTJwr4aC9EgCRPRoZACPMUAaIOlFAwXhRrXVES0AfxWTldz7HfZCFM0ur6JgWPP3bz8yz2IfPk7Ow83hbeHp9FXwD6bvHO1tj5dB5tHyuFfx9ees9O0ZCZCHZBKY240Xo3KZBTrLCxH9wZDZD';
        var deferred = $q.defer();

        FB.api(
            "/"+id+"/?fields=comments.limit(100),likes.limit(100),sharedposts.limit(100)&access_token="+authToken+"&appsecret_proof="+appsecret,
            function (response) {
              if (response && !response.error) {
                deferred.resolve(response);
              }
            }
        );
        // FB.api(
        //     "/"+id+"/likes?access_token="+authToken+"&appsecret_proof="+appsecret,
        //     function (response) {
        //       if (response && !response.error) {
        //         deferred.resolve(response);
        //       }
        //     }
        // );
        return deferred.promise;
    }

    this.fbShareCount=(id,authToken,appsecret)=>{
        var access_token='EAACEdEose0cBAC93h4561ZBmN5E46PezEs94H44dFhggJg5mDGXL9I87Eh9xKlg0qTJwr4aC9EgCRPRoZACPMUAaIOlFAwXhRrXVES0AfxWTldz7HfZCFM0ur6JgWPP3bz8yz2IfPk7Ow83hbeHp9FXwD6bvHO1tj5dB5tHyuFfx9ees9O0ZCZCHZBKY240Xo3KZBTrLCxH9wZDZD';
        var deferred = $q.defer();
        FB.api(
            "/"+id+"/sharedposts?access_token="+authToken+"&appsecret_proof="+appsecret,
            function (response) {
              if (response && !response.error) {
               
                deferred.resolve(response);
              }
            }
        );
        return deferred.promise;
    }

    this.fbCommentCount=(id,authToken,appsecret)=>{

        var access_token='EAACEdEose0cBAC93h4561ZBmN5E46PezEs94H44dFhggJg5mDGXL9I87Eh9xKlg0qTJwr4aC9EgCRPRoZACPMUAaIOlFAwXhRrXVES0AfxWTldz7HfZCFM0ur6JgWPP3bz8yz2IfPk7Ow83hbeHp9FXwD6bvHO1tj5dB5tHyuFfx9ees9O0ZCZCHZBKY240Xo3KZBTrLCxH9wZDZD';
  
        var deferred = $q.defer();
        FB.api(
            "/"+id+"/comments?access_token="+authToken+"&appsecret_proof="+appsecret,
            function (response) {
               
              if (response && !response.error) {
               
                deferred.resolve(response);
              }
            }
        );
        return deferred.promise;
    }

    // FB login function
	this.getToken=()=>{
        var deferred=$q.defer();
        FB.login(function(response) {
            if (response.authResponse){

                var authresp = response.authResponse;
                
                var jsSHA = require("jssha");
                var hash_hmac=(string, secret)=>{
                    let shaObj = new jsSHA("SHA-256", "TEXT");
                    shaObj.setHMACKey(secret, "TEXT");
                    shaObj.update(string);
                    let hmac = shaObj.getHMAC("HEX");
                    return hmac;
                };

                var appsecret_proof= hash_hmac(authresp.accessToken, '94cbbca16f540bdd5f9244dc517216a0'); 
            }
            deferred.resolve({authresp:authresp,appsecret:appsecret_proof});
        });
        return deferred.promise;
    }
       
}
