/*
  Instagram Service
*/
InstaService.$inject = ['$http','$rootScope','$window','Upload','$q'];
export default function InstaService($http,$rootScope,$window,Upload,$q) {
  //Instagram Login
  this.login = (link) => {
    var host;
    if(window.location.port==80 || window.location.port==''){

      host=window.location.protocol+'//'+window.location.hostname
    } else{
      host=window.location.protocol+'//'+window.location.hostname+':'+window.location.port
    }

    window.open("https://api.instagram.com/oauth/authorize/?client_id=bda0f70c8b834b3e9fc3d7230a459b94&redirect_uri="+host+"/instagram-callback/s&response_type=token&scope=public_content","", "width=600,height=500");
  };

  // Send instagram request to server
  this.instagramRequest = (url,token) => {
    return $http.post($rootScope.apiUrl+'api/get_curl_response', {
        url: url,
        access_token:token })
        .then(function(res){
          return res;
        })
  };
  // Send instagram request to server
  this.instagramRequest2 = (url) => {
    return $http.post($rootScope.apiUrl+'api/get_curl_response', {
        url: url})
        .then(function(res){
          return res;
        })
  };

  // Hashtag search in instagram
  this.searchHashtag=(keyword)=>{
      // return $http.post($rootScope.apiUrl+'api/instaHashtagSerach',keyword)
      //   .then(function successCallback(response){
      //       return response;
      //   }, function errorCallback(response){
      //       return response;
      //   })
      var url='https://api.instagram.com/v1/tags/'+keyword.keyword+'/media/recent?access_token=5809862739.3a81a9f.7b5023c32bb84e5ebe151737b241d9f7&count=150';
        return $http.post($rootScope.apiUrl+'api/get_curl_response_insta', {
            url: url})
            .then(function(res){
              return res;
            })
            // return $http.get(url)
            //   .then(function successCallback(response){
            //       return response;
            //   }, function errorCallback(response){
            //       return response;
            //   })
    // keyword=keyword.keyword.replace('#','');
    // $window.localStorage.keyword=keyword;
    // window.open("https://api.instagram.com/oauth/authorize/?client_id=bda0f70c8b834b3e9fc3d7230a459b94&redirect_uri=http://localhost:8002/insta-tag-callback/s&response_type=token&scope=public_content","_blank", "width=600,height=500");
  };
}
