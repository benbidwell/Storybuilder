//SharingCtrl
SharingCtrl.$inject =  ['$rootScope' ,'$scope', '$state','$window','$timeout','FbService','GoogleApiService', 'ShareService','AuthService','jwtHelper','PunditService','StoryService','$http','OAuthService','Notification','AppService'];
export default function SharingCtrl($rootScope ,$scope, $state,$window, $timeout,fbService,googleApiService, shareService,authService,jwtHelper,punditService,storyService,$http,oAuthService,notification,appService){

	$scope.storyId=$state.params.storyId;

	$timeout(function() {
		// $(".viewScroll").mCustomScrollbar({
		// 	axis: "y",
		// 	setHeight:200
		// });
		// $(".viewScroll1").mCustomScrollbar({
		// 	axis: "y",
		// 	setHeight:200
		// });
	}, 500);

	$scope.storyOptions = {
		limit:2,
		offset: 0
	  };
	  $scope.storySuggestedOptions = {
		limit:2,
		offset: 0
	  };
	  var nextPageLoad;
	  var lastPage;
	  var nextSuggestedPageLoad;
	  var lastSuggestedPage;
	  $(window).scroll(function() {
		var top_of_element = $("#pundit_loading").offset().top;
		var bottom_of_element = $("#pundit_loading").offset().top + $("#pundit_loading").outerHeight();
		var bottom_of_screen = $(window).scrollTop() + window.innerHeight;
		var top_of_screen = $(window).scrollTop();

		if((bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element)){
		//if($("#pundit_loading").is(":visible")){
		  if (nextPageLoad == false && lastPage != false) {
		   console.log('pundit');
		   console.log(nextPageLoad)
		   console.log(lastPage)
			$scope.nextPage();
		  }
		}


		var top_of_element1 = $("#suggested_loading").offset().top;
		var bottom_of_element1 = $("#suggested_loading").offset().top + $("#suggested_loading").outerHeight();
		var bottom_of_screen1 = $(window).scrollTop() + window.innerHeight;
		var top_of_screen1 = $(window).scrollTop();
		if((bottom_of_screen1 > top_of_element1) && (top_of_screen1 < bottom_of_element1)){
			if (nextSuggestedPageLoad == false && lastSuggestedPage != false) {
			 console.log('suggested')
			  $scope.nextSuggestedPage();
			}
		}

		
	  });
	
	  $scope.nextPage = () => {
		nextPageLoad = true;
		$scope.nextPageloader = true;
	  
		  $scope.storyOptions.offset += $scope.storyOptions.limit;
		  $scope.storyOptions.limit = 2;
		  punditService.getPopularVideo($scope.storyOptions).then(res => {
			$scope.nextPageloader = false;
			if (res.status == 200) {
			  nextPageLoad = false;
			  
			  if (res.data.data.pundit_published_videos.length == 0) {
			  
				lastPage = false;
			  }
			  res.data.data.pundit_published_videos.forEach(function(item) {
				$scope.popularVideos.push(item);
			  });
			}
		  });
	  };

	$scope.nextSuggestedPage = () => {
	nextSuggestedPageLoad = true;
	$scope.nextSuggestedPageloader = true;
	
		$scope.storySuggestedOptions.offset += $scope.storySuggestedOptions.limit;
		$scope.storySuggestedOptions.limit = 2;
		punditService.getSuggestedVideo($state.params.storyId,$scope.storySuggestedOptions)
		.then((res)=>{
			console.log(res.data.data)
			nextSuggestedPageLoad = false;
			$scope.nextSuggestedPageloader=false;
			if (res.data.data.length == 0) {
			
				lastSuggestedPage = false;
			}
			res.data.data.forEach(function(item) {
				$scope.suggestedVideos.push(item);

			});
			console.log($scope.suggestedVideos);
		})
	};



	var host;
    if(window.location.port==80 || window.location.port==''){
      host=window.location.protocol+'//'+window.location.hostname
    } else{
      host=window.location.protocol+'//'+window.location.hostname+':'+window.location.port
	}
	
	if($rootScope.user==undefined){	
	    authService.getUserDetails()
	    .then((res)=>{
	      	if(res.status==200){
	        	$rootScope.user=res.data.data;
	        	var decodeToken = jwtHelper.decodeToken($window.localStorage.ud);
	        	$rootScope.user.id=decodeToken.sub;

	        	if($rootScope.user.profile_picture!=null && $rootScope.user.profile_picture!=undefined && $rootScope.user.profile_picture!=''){
	        	  	$rootScope.user.profile_picture='/profile_pictures/'+$rootScope.user.profile_picture;
	        	}
	      	} else {
	        	$window.localStorage.clear();
	        	$http.defaults.headers.common.Authorization=undefined; 
	      	}
	    })
  	}
  	

  	if($state.current.name=='SharePunditVideo'){
		$scope.isOriginalVideo=false;
		$scope.pageurl=host+'/pundit-story/'+$state.params.storyId+'/'+$state.params.punditId;
		
  		punditService.getPunditVideo($state.params.storyId,$state.params.punditId)
  		.then((res)=>{
  			console.log(res);
			$scope.storyDetails=res.data.data;
  			$scope.videoUrl='/story_videos/story_'+$state.params.storyId+"/"+res.data.data.new_video_url;
  	
  		}) 
  	} else if($state.current.name=='ShareVideo'){
		$scope.isOriginalVideo=true;
		//$scope.pageurl='http://clipcrowd.cdemo.in/687898845.mp4';
  		$scope.pageurl=host+'/pundit-story/'+$state.params.storyId;
  		storyService.getStroy($state.params.storyId)
  		.then((res)=>{
			$scope.storyDetails=res.data.data;
  			$scope.videoUrl='/story_videos/story_'+$state.params.storyId+"/"+res.data.data.story_published_video_url;
  		})
	  } 

	$scope.fb_share=()=>{
		if ($state.params.punditId) {
			punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
		  } else {
			punditService.addShare($scope.storyId).then(res => {});
		  }
		fbService.fb_share($scope.pageurl)
		.then((res)=>{});
	};

	punditService.getSuggestedVideo($state.params.storyId,$scope.storySuggestedOptions)
	.then((res)=>{
		nextSuggestedPageLoad=false;
		$scope.suggestedVideos=res.data.data;
	
	})

	punditService.getPopularVideo($scope.storyOptions)
	.then((res)=>{
		nextPageLoad=false;
		$scope.popularVideos=res.data.data.pundit_published_videos;
		console.log($scope.popularVideos)
	})
	   
	appService.getAdminSettings()
	.then((res)=>{
		$scope.pundit_title_text=res.data.data.pundit_title_text;
	})

	$scope.logout=()=>{
		$window.localStorage.clear();
		$rootScope.user = null;
		$rootScope = undefined;
		$http.defaults.headers.common.Authorization=undefined; 
		$state.go('public.login'); 
	};

	$scope.sharefb=()=> {
		if ($state.params.punditId) {
			punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
		  } else {
			punditService.addShare($scope.storyId).then(res => {});
		  }
		var data={}
		data.video_url=$scope.videoUrl;
	
		if($scope.isOriginalVideo==true){
			data.pageurl=$scope.pageurl;
			data.story_des=$scope.storyDetails.story_details;
			data.story_id=$scope.storyDetails.id;
			data.title=$scope.storyDetails.story_title;
		} else {
			
			data.title=$scope.storyDetails.video_name;
		}
		fbService.uploadVideo(data)
	
		// var oReq = new XMLHttpRequest();
		// 	oReq.open("GET", "/story_videos/1516180533-video.mp4", true);
		// 	oReq.responseType = "arraybuffer";
			
		// 	oReq.onload = function (oEvent) {
				
		// 	  	var arrayBuffer = oReq.response; // Note: not oReq.responseText
		// 	  	var blob = new Blob([oReq.response], {type: "video/mp4"});
		// 	  	var myFile = new File([blob], "video.mp4");
				
		// 		fbService.fb_post_video_init({data:$scope.storyDetails,size:myFile.size})
		// 		.then(res =>{
		// 			console.log(res);
		// 			var response=JSON.parse(res.data[0]);
		// 			$scope.media_id=response.media_id_string
		// 			var countIterations=0;
					
		// 			parseFile(myFile,function(data){				
		// 				fbService.fb_post_video_append({media_id:$scope.media_id,media:data,segment:countIterations})
		// 				.then(res=>{
		// 					countIterations+=1;
		// 					if(countIterations==$scope.iterations){
		// 						fbService.fb_post_video_finish({media_id:$scope.media_id})
		// 						.then(res=>{							
		// 						});
		// 					}
		// 				});
		// 			})
		// 		})
		// 	};
		// oReq.send(null);
		
	}

	$scope.sharegplus=()=> {
		var oReq = new XMLHttpRequest();
			oReq.open("GET", $scope.videoUrl, true);
			oReq.responseType = "arraybuffer";
			
			oReq.onload = function (oEvent) {
				
			  	var arrayBuffer = oReq.response; // Note: not oReq.responseText
			  	var blob = new Blob([oReq.response], {type: "video/mp4"});
				var myFile = new File([blob], "video.mp4");
				
				var reader = new FileReader();
				reader.onload = function() {
					var f = new Uint8Array(reader.result);
				  	googleApiService.gpluspostmedia({size:myFile.size, media:f, media_type: 'video/mp4'})
					.then(res =>{
						var response=JSON.parse(res.data[0]);
						$scope.media_id=response.media_id_string
						var countIterations=0;
					});
				};
				reader.readAsArrayBuffer(myFile);
			};
		oReq.send(null);
	}

	$scope.inc_share=()=>{
		if ($state.params.punditId) {
			punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
		} else {
			punditService.addShare($scope.storyId).then(res => {});
		}
	}
	$scope.shareYouTube=()=>{
		if ($state.params.punditId) {
			punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
		  } else {
			punditService.addShare($scope.storyId).then(res => {});
		  }

		var oReq = new XMLHttpRequest();
			oReq.open("GET", $scope.videoUrl, true);
			oReq.responseType = "arraybuffer";
			
			oReq.onload = function (oEvent) {
				
			  	var arrayBuffer = oReq.response; // Note: not oReq.responseText
			  	var blob = new Blob([oReq.response], {type: "video/mp4"});
				var myFile = new File([blob], "video.mp4");
				
				var reader = new FileReader();
				reader.onload = function() {
					var data={};
					if($scope.isOriginalVideo==true){
						data.pageurl=$scope.pageurl;
						data.story_des=$scope.storyDetails.story_details;
						data.title=$scope.storyDetails.story_title;
						data.id=$scope.storyDetails.id;
					} else {
						
						data.title=$scope.storyDetails.video_name;
					}
					
					var f = new Uint8Array(reader.result);
				  	googleApiService.youtubepostmedia({size:myFile.size,id:$scope.storyDetails.id, media:myFile, media_type: 'video/mp4',data:data})
				};
				reader.readAsArrayBuffer(myFile);
			};

		oReq.send(null);
	}
	$scope.checkTwitterAuth=()=>{
		if($window.localStorage.twit_token && $window.localStorage.twit_token_s){
			$scope.sharetwitter();
		} else {
			oAuthService.tw_access_token('POST','https://api.twitter.com/oauth/request_token','')
			.then(res=>{
				var token=res.data.split("&");
				window.open("https://api.twitter.com/oauth/authenticate?"+token[0]+"&oauth_callback="+host+"/twitter-callback?upload=video&screen_name=&force_login=true","", "width=600,height=500");
			
			})
		}
		
	}
	$scope.sharetwitter=()=>{
		if ($state.params.punditId) {
			punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
		  } else {
			punditService.addShare($scope.storyId).then(res => {});
		  }
		var oauth_token=$window.localStorage.twit_token;
		var oauth_secret=$window.localStorage.twit_token_s;
		var oReq = new XMLHttpRequest();
		oReq.open("GET", $scope.videoUrl, true);
		oReq.responseType = "arraybuffer";
		
		oReq.onload = function (oEvent) {
			
			var arrayBuffer = oReq.response; // Note: not oReq.responseText
			var blob = new Blob([oReq.response], {type: "video/mp4"});
			var myFile = new File([blob], "video.mp4");
			//console.log(myFile);
			oAuthService.postVideoInit({data:$scope.storyDetails,size:myFile.size,oauth_token:oauth_token,oauth_secret:oauth_secret})
			.then(res=>{

				var response=JSON.parse(res.data[0]);
				$scope.media_id=response.media_id_string
				var countIterations=0;
				var cntIte=0;
				parseFile(myFile,function(data){
					
					oAuthService.postVideoAppend({media_id:$scope.media_id,media:data,segment:countIterations,oauth_token:oauth_token,oauth_secret:oauth_secret})
					.then(res=>{
						cntIte+=1;
						if(cntIte==$scope.iterations){
							oAuthService.postVideoFinalize({media_id:$scope.media_id,oauth_token:oauth_token,oauth_secret:oauth_secret})
							.then(res=>{
								
								oAuthService.postVideoStatus({media_id:$scope.media_id,title:$scope.storyDetails.video_name,oauth_token:oauth_token,oauth_secret:oauth_secret})
								.then(res=>{	
									var data={};
									if($scope.isOriginalVideo==true){
										data.pageurl=$scope.pageurl;
										data.story_des=$scope.storyDetails.story_details;
										data.title=$scope.storyDetails.story_title;
									} else {
										
										data.title=$scope.storyDetails.video_name;
									}
									oAuthService.createVideoPost({media_id:$scope.media_id,oauth_token:oauth_token,oauth_secret:oauth_secret,pageurl:data.pageurl,title:data.story_title})
									.then(res=>{
										if(res.data[1]==400){
											var error=JSON.parse(res.data[0]);
											console.log(error);
											notification.success({message:error.errors[0].message,positionY: 'bottom', positionX: 'right',delay: 3000});
										} else {
											if($scope.isOriginalVideo==true){
												var data=JSON.parse(res.data[0]);
												storyService.updateStroy2({id:$scope.storyDetails.id,twitter:data.id})
												.then(resss=>{

												})
											}
										notification.success({message:'Video posted successfully',positionY: 'bottom', positionX: 'right',delay: 3000});
										}
									})
								});
							});
						}
					});
					countIterations+=1;
				})

			});

		};

		oReq.send(null);
	}

	function parseFile(file, callback) {

		var fileSize   = file.size;
		var chunkSize  = Math.ceil(file.size/3); // bytes
		var offset     = 0;
		var self       = this; // we need a reference to the current object
		var chunkReaderBlock = null;
		$scope.iterations = 3;

	
		var readEventHandler = function(evt) {
			if (evt.target.error == null) {
				
				offset += evt.target.result.length;
				
				callback(evt.target.result); // callback for handling read chunk
			} else {
				console.log("Read error: " + evt.target.error);
				return;
			}
			if (offset >= fileSize) {
				// Document end
				return;
			}
	
			// of to the next chunk
			chunkReaderBlock(offset, chunkSize, file);
		}
		
	
		chunkReaderBlock = function(_offset, length, _file) {
			var r = new FileReader();
			var blob = _file.slice(_offset, length + _offset);
			r.onload = readEventHandler;
			r.readAsBinaryString(blob);
			//r.readAsDataURL(blob);
		}
		// now let's start the read with the first block
		chunkReaderBlock(offset, chunkSize, file);
	}  
	if($window.localStorage.twit_token && $window.localStorage.twit_token_s && $window.localStorage.twit_test==1){
		$window.localStorage.twit_test=undefined;
		$scope.sharetwitter();
	}
	var video=document.getElementById('video');
	video.addEventListener("play", function(){
        // check whether we have passed 5 minutes,
        if($state.params.punditId){
            punditService.addView($scope.storyId,$state.params.punditId)
        .then((res)=>{})
        } else {
            punditService.addView($scope.storyId)
        .then((res)=>{})
        }
        
    });
}   