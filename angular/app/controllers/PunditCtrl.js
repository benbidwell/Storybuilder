// Pundit Ctrl
PunditCtrl.$inject = [
  '$rootScope',
  '$scope',
  '$state',
  '$window',
  '$timeout',
  '$uibModal',
  'PunditService',
  'AuthService',
  'jwtHelper',
  '$http',
  'Notification',
  'FbService',
  'GoogleApiService',
  'OAuthService',
];
export default function PunditCtrl(
  $rootScope,
  $scope,
  $state,
  $window,
  $timeout,
  $uibModal,
  punditService,
  authService,
  jwtHelper,
  $http,
  notification,
  fbService,
  googleApiService,
  oAuthService,
) {
  // variable declration & Initialization
  $scope.is_recording_done = false;
  $scope.shareVideoStatus = false;
  $scope.audioPlayStatus = false;
  let video = document.getElementById('video');
  let audio = document.getElementById('audio');
  punditService.formdata.story_id = $state.params.storyId;
  $scope.storyId = $state.params.storyId;



  // Constructor
  const Init = () => {
    $timeout(function() {
      // $('.viewScroll').mCustomScrollbar({
      //   axis: 'y',
      //   setHeight: 200,
      // });
      // $('.viewScroll1').mCustomScrollbar({
      //   axis: 'y',
      //   setHeight: 200,
      // });
    }, 500);
  };
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
            $scope.video.popularVideos.pundit_published_videos.push(item);
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
        nextSuggestedPageLoad = false;
        $scope.nextSuggestedPageloader=false;
        if (res.data.data.length == 0) {
        
          lastSuggestedPage = false;
        }
        res.data.data.forEach(function(item) {
          $scope.video.suggestedVideos.push(item);
  
        });
        console.log($scope.video.suggestedVideos);
      })
    };
  

  var host;
  if (window.location.port == 80 || window.location.port == '') {
    host = window.location.protocol + '//' + window.location.hostname;
  } else {
    host = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port;
  }

  //get video
  $scope.video = {
    suggestedVideos: [],
    popularVideos: [],
    // Get original video
    getOriginalVideo: storyId => {
      punditService.getOriginalVideo(storyId).then(
        res => {
          $scope.video.originalVideo = res.data.data;
          if(!$state.params.punditId){
          $scope.videoUrl =
            host + '/story_videos/story_' + $scope.video.originalVideo.id + '/' + $scope.video.originalVideo.story_published_video_url;
          }
          $scope.videoDownloadURL =
            host +
            '/story_videos/story_' +
            $scope.video.originalVideo.id +
            '/' +
            $scope.video.originalVideo.story_published_video_url;
          $scope.pageurl = host + '/pundit-story/' + $scope.video.originalVideo.id;
        },
        err => {},
      );
    },
    // get pundit video
    getPunditVideo: (storyId, videoId) => {
      punditService.getPunditVideo(storyId, videoId).then(
        res => {
          
          $scope.video.originalVideo = {
            id: storyId,
            story_published_video_url: res.data.data.new_video_url,
            story_title: res.data.data.video_name,
          };
          if($state.params.punditId){
            $scope.videoUrl =
              host + '/story_videos/story_' + $scope.video.originalVideo.id + '/' + $scope.video.originalVideo.story_published_video_url;
          }
            $scope.pageurl = host + '/pundit-story/' + storyId+'/'+videoId;
        },
        err => {},
      );
    },
    // get suggested video
    getSuggestedVideo: storyId => {
      punditService.getSuggestedVideo(storyId,$scope.storySuggestedOptions).then(res => {
        nextSuggestedPageLoad=false;
        $scope.video.suggestedVideos = res.data.data;
      });
   
    },
    // Get Popular video
    getPopularVideo: storyId => {
      nextPageLoad = true;
      punditService.getPopularVideo($scope.storyOptions).then(res => {
        $scope.video.popularVideos = res.data.data;
        nextPageLoad = false;
      });
    },

    // videoPreview
    videoPreview: () => {
      var modalInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        size: 'md',
        templateUrl: 'videoPreview.html',
        controller: [
          '$rootScope',
          '$scope',
          '$uibModalInstance',
          '$window',
          'PunditService',
          function($rootScope, $scope, $uibModalInstance, $window, punditService) {
            // $scope.item = item;
            $scope.formdata = {};
            $scope.loading = true;
            punditService.createPreview(punditService.formdata).then(
              res => {
                $scope.loading = false;
                $scope.pudnitVideo = res.data.data;
                $scope.story_id = punditService.formdata.story_id;
              },
              err => {
                $scope.loading = false;
              },
            );

            $scope.close = function() {
              $uibModalInstance.dismiss('cancel');
            };
            $scope.publishVideo = function() {
              $uibModalInstance.dismiss('cancel');
              $rootScope.punditRegistrationForm();
            };
          },
        ],
      });
    },
  };

  // Recording
  $scope.recording = {
    show_panel: false,
    is_pause: false,

    startPre:()=>{
       $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        size: 'md',
        templateUrl: 'CountDown.html',
        controller: [
          '$rootScope',
          '$scope',
          '$uibModalInstance',
          '$window',
          function($rootScope, $scope, $uibModalInstance, $window) {
            // $scope.item = item;
            $scope.countDown=3;
            var myVar = setInterval(myTimer, 1000);
            function myTimer() {
             
                $scope.countDown-=1;
                document.getElementById("countD").innerHTML = $scope.countDown;
            }
            setTimeout(function(){
              
              window.clearInterval(myVar)
              $uibModalInstance.dismiss('cancel');
            }, 3000);
           
          },
        ],
      });
      setTimeout(function(){
              
        $scope.recording.start();
      }, 3000);
      
    },
    // Start Recording
    start: () => {
      Fr.voice.record(false, function() {
        if (Fr.voice.stream) {
          video.play();
          video.muted = true;
          //$scope.is_recording_done=false
          $scope.recording.show_panel = true;
          $scope.recording.is_pause = false;
          $scope.audioPlayStatus = true;
        }
      });
    },

    play: () => {
      Fr.voice.resume();
      video.play();
      $scope.recording.is_pause = false;
    },

    // Reset Recording
    reset: () => {
      video.pause();
      video.currentTime = 0;
      video.play();
      Fr.voice.record(false, function() {});
      $scope.audioPlayStatus = true;
      $scope.recording.show_panel = true;
    },

    // pause Recording
    pause: () => {
      Fr.voice.pause();
      video.pause();
      $scope.recording.is_pause = true;
      $scope.recording.show_panel = true;
    },

    // Stop Recording
    stop: () => {
      Fr.voice.pause();
      video.pause();
      video.currentTime = 0;
      $scope.recording.show_panel = false;
      $scope.confirmationBox();
    },

    // Export
    export: () => {
      $scope.loading = true;
      Fr.voice.export(function(blob) {
        $scope.loading = false;
        $scope.is_recording_done = true;
        $scope.$digest();
        Fr.voice.stop();
        punditService.formdata.audio = blob;
        console.log(blob);
      }, 'blob');
    },
    //Export Mp3
    exportMP3: () => {
      $scope.loading = true;

      Fr.voice.exportMP3(blob => {
        Fr.voice.stop();
        $scope.loading = false;
        $scope.is_recording_done = true;
        $scope.$digest();
        punditService.formdata.audio = blob;
      }, 'blob');
    },

    // only audio pause
    audio_pause: () => {
      Fr.voice.muted();
      $scope.audioPlayStatus = false;
    },

    // only audio play
    audio_resume: () => {
      $scope.audioPlayStatus = true;
      Fr.voice.unmuted();
    },
  };

  // Pundit
  $rootScope.punditRegistrationForm = () => {
    var modalInstance = $uibModal.open({
      animation: true,
      ariaLabelledBy: 'modal-title',
      ariaDescribedBy: 'modal-body',
      size: 'lg',
      templateUrl: 'punditRegistrationForm.html',
      controller: [
        '$rootScope',
        '$scope',
        '$uibModalInstance',
        '$window',
        function($rootScope, $scope, $uibModalInstance, $window) {
          // Formdata variable for registration form
          $scope.pundit = {
            video_title: '',
            first_name: '',
            last_name: '',
            email: '',
            story_id: punditService.formdata.story_id,
            audio: punditService.formdata.audio,
          };

          var publishPunditVideo = () => {
            punditService.publishPunditVideo($scope.pundit).then(res => {
              $scope.loader = false;
              if (res.status == 200) {
                notification.success({
                  message: res.data.message,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
                $scope.close();
              } else {
                if (res.data.data) {
                  const errors = res.data.data;
                  $scope.err = [];
                  Object.keys(errors).forEach(function(key, index) {
                    $scope.err[key] = true;
                  });
                  $scope.errors = res.data.data;
                }
                const msg = res.data.message ? res.data.message : 'Error';

                notification.error({
                  message: msg,
                  templateUrl: 'custom_template.html',
                  scope: $scope,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
              }
            });
          };
          var publishPunditVideoLoggedUser = () => {
            punditService.publishPunditVideoLoggedUser($scope.pundit).then(res => {
              $scope.loader = false;
              if (res.status == 200) {
                notification.success({
                  message: res.data.message,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
                $scope.close();
                $state.go('SharePunditVideo', { storyId: $scope.pundit.story_id, punditId: res.data.data.id });
              } else {
                if (res.data.data) {
                  const errors = res.data.data;
                  $scope.err = [];
                  Object.keys(errors).forEach(function(key, index) {
                    $scope.err[key] = true;
                  });
                  $scope.errors = res.data.data;
                }
                const msg = res.data.message ? res.data.message : 'Error';
                notification.error({
                  message: msg,
                  templateUrl: 'custom_template.html',
                  scope: $scope,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
              }
            });
          };

          $scope.punditRegister = () => {
            $scope.loader = true;
            punditService.createPreview($scope.pundit).then(
              res => {
                $scope.pundit.video_url = res.data.data.video_url;
                $scope.pundit.background_sound_file_url = res.data.data.background_sound_file_url;
                if ($rootScope.user) {
                  publishPunditVideoLoggedUser();
                } else {
                  publishPunditVideo();
                }
              },
              err => {
                $scope.loader = false;
                notification.error({ message: 'Error', positionY: 'bottom', positionX: 'right', delay: 3000 });
              },
            );
          };

          $scope.close = function() {
            $uibModalInstance.dismiss('cancel');
          };
        },
      ],
    });
  };

  // Publish Video
  $scope.publishVideo = () => {
    punditService.formdata.story_id = $state.params.storyId;
    $rootScope.punditRegistrationForm();
  };

  $scope.confirmationBox = () => {
    console.log('I am calling confrimation box');
    var scope = $scope;
    $scope.modalInstance = $uibModal
      .open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        size: 'sm',
        templateUrl: 'confirmBox.html',
        controller: [
          '$rootScope',
          '$scope',
          '$uibModalInstance',
          '$window',
          function($rootScope, $scope, $uibModalInstance, $window) {
            $scope.confirm = value => {
              if (value == true) {
                $scope.status = true;
                $uibModalInstance.close($scope.status);
              } else {
                $scope.status = false;
                $uibModalInstance.close($scope.status);
              }
            };
          },
        ],
        resolve: {
          status: function() {
            return $scope.status;
          },
        },
      })
      .result.then(function(result) {
        if (result == true) {
          $scope.recording.export();
        } else {
          Fr.voice.stop();
        }
      });
  };

  // Call constructor
  Init();

  // Check Authentication
  if ($rootScope.user == undefined) {
    authService.getUserDetails().then(res => {
      if (res.status == 200) {
        $rootScope.user = res.data.data;
        var decodeToken = jwtHelper.decodeToken($window.localStorage.ud);
        $rootScope.user.id = decodeToken.sub;

        if (
          $rootScope.user.profile_picture != null &&
          $rootScope.user.profile_picture != undefined &&
          $rootScope.user.profile_picture != ''
        ) {
          $rootScope.user.profile_picture = '/profile_pictures/' + $rootScope.user.profile_picture;
        }
      } else {
        $window.localStorage.clear();
        $http.defaults.headers.common.Authorization = undefined;
      }
    });
  }
  // Signout function
  $scope.logout = () => {
    $window.localStorage.clear();
    $rootScope.user = null;

    $rootScope = null;
    $http.defaults.headers.common.Authorization = undefined;
    $state.go('public.login');
  };

  if ($state.params.punditId) {
    $scope.isOriginalVideo = false;
    $scope.video.getPunditVideo($state.params.storyId, $state.params.punditId);
  } else {
    $scope.isOriginalVideo = true;
    $scope.video.getOriginalVideo($state.params.storyId);
  }

  $scope.video.getSuggestedVideo($state.params.storyId);
  $scope.video.getPopularVideo($state.params.storyId);

  video.addEventListener('timeupdate', function() {
    // check whether we have passed 5 minutes,
    $scope.videoTime = this.currentTime;
    if (this.currentTime >= this.duration) {
      // pause the audio
      $scope.recording.stop();
      $scope.$digest();
    }
  });

  video.addEventListener('play', function() {
    // check whether we have passed 5 minutes,
    if ($state.params.punditId) {
      punditService.addView($scope.storyId, $state.params.punditId).then(res => {});
    } else {
      punditService.addView($scope.storyId).then(res => {});
    }
  });

  $scope.sharefb = () => {
    if ($state.params.punditId) {
      punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
    } else {
      punditService.addShare($scope.storyId).then(res => {});
    }

    var data = {};
    data.video_url = $scope.videoUrl;

    data.pageurl = $scope.pageurl;
    data.story_des = $scope.video.originalVideo.story_details;
    data.story_id = $scope.video.originalVideo.id;
    data.title = $scope.video.originalVideo.story_title;

    fbService.uploadVideo(data);
  };

  $scope.sharegplus = () => {
    var oReq = new XMLHttpRequest();
    oReq.open('GET', $scope.videoUrl, true);
    oReq.responseType = 'arraybuffer';

    oReq.onload = function(oEvent) {
      var arrayBuffer = oReq.response; // Note: not oReq.responseText
      var blob = new Blob([oReq.response], { type: 'video/mp4' });
      var myFile = new File([blob], 'video.mp4');

      var reader = new FileReader();
      reader.onload = function() {
        var f = new Uint8Array(reader.result);
        googleApiService.gpluspostmedia({ size: myFile.size, media: f, media_type: 'video/mp4' }).then(res => {
          var response = JSON.parse(res.data[0]);
          $scope.media_id = response.media_id_string;
          var countIterations = 0;
        });
      };
      reader.readAsArrayBuffer(myFile);
    };
    oReq.send(null);
  };

  $scope.shareYouTube = () => {
    if ($state.params.punditId) {
      punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
    } else {
      punditService.addShare($scope.storyId).then(res => {});
    }
    var oReq = new XMLHttpRequest();

    oReq.open('GET', $scope.videoUrl, true);
    oReq.responseType = 'arraybuffer';

    oReq.onload = function(oEvent) {
      var arrayBuffer = oReq.response; // Note: not oReq.responseText
      var blob = new Blob([oReq.response], { type: 'video/mp4' });
      var myFile = new File([blob], 'video.mp4');

      var reader = new FileReader();
      reader.onload = function() {
        var data = {};

        data.pageurl = $scope.pageurl;
        data.story_des = $scope.video.originalVideo.story_details;
        data.title = $scope.video.originalVideo.story_title;
        data.id = $scope.video.originalVideo.id;

        var f = new Uint8Array(reader.result);
        googleApiService.youtubepostmedia({
          size: myFile.size,
          id: $scope.video.originalVideo.id,
          media: myFile,
          media_type: 'video/mp4',
          data: data,
        });
      };
      reader.readAsArrayBuffer(myFile);
    };

    oReq.send(null);
  };

  $scope.checkTwitterAuth = () => {
    if ($window.localStorage.twit_token && $window.localStorage.twit_token_s) {
      $scope.sharetwitter();
    } else {
      oAuthService.tw_access_token('POST', 'https://api.twitter.com/oauth/request_token', '').then(res => {
        var token = res.data.split('&');
        window.open(
          'https://api.twitter.com/oauth/authenticate?' +
            token[0] +
            '&oauth_callback=' +
            host +
            '/twitter-callback?upload=video&screen_name=&force_login=true',
          '',
          'width=600,height=500',
        );
      });
    }
  };

  $scope.sharetwitter = () => {
    if ($state.params.punditId) {
      punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
    } else {
      punditService.addShare($scope.storyId).then(res => {});
    }
    var oauth_token = $window.localStorage.twit_token;
    var oauth_secret = $window.localStorage.twit_token_s;
    var oReq = new XMLHttpRequest();
    oReq.open('GET', $scope.videoUrl, true);
    oReq.responseType = 'arraybuffer';

    oReq.onload = function(oEvent) {
      var arrayBuffer = oReq.response; // Note: not oReq.responseText
      var blob = new Blob([oReq.response], { type: 'video/mp4' });
      var myFile = new File([blob], 'video.mp4');
      //console.log(myFile);
      oAuthService
        .postVideoInit({
          data: $scope.video.originalVideo,
          size: myFile.size,
          oauth_token: oauth_token,
          oauth_secret: oauth_secret,
        })
        .then(res => {
          var response = JSON.parse(res.data[0]);
          $scope.media_id = response.media_id_string;
          var countIterations = 0;
          var cntIte = 0;
          parseFile(myFile, function(data) {
            oAuthService
              .postVideoAppend({
                media_id: $scope.media_id,
                media: data,
                segment: countIterations,
                oauth_token: oauth_token,
                oauth_secret: oauth_secret,
              })
              .then(res => {
                cntIte += 1;
                if (cntIte == $scope.iterations) {
                  oAuthService
                    .postVideoFinalize({
                      media_id: $scope.media_id,
                      oauth_token: oauth_token,
                      oauth_secret: oauth_secret,
                    })
                    .then(res => {
                      oAuthService
                        .postVideoStatus({
                          media_id: $scope.media_id,
                          title: $scope.video.originalVideo.video_name,
                          oauth_token: oauth_token,
                          oauth_secret: oauth_secret,
                        })
                        .then(res => {
                          var data = {};
                          if ($scope.isOriginalVideo == true) {
                            data.pageurl = $scope.pageurl;
                            data.story_des = $scope.video.originalVideo.story_details;
                            data.title = $scope.video.originalVideo.story_title;
                          } else {
                            data.title = $scope.video.originalVideo.video_name;
                          }
                          oAuthService
                            .createVideoPost({
                              media_id: $scope.media_id,
                              oauth_token: oauth_token,
                              oauth_secret: oauth_secret,
                              pageurl: data.pageurl,
                              title: data.story_title,
                            })
                            .then(res => {
                              if (res.data[1] == 400) {
                                var error = JSON.parse(res.data[0]);
                                console.log(error);
                                notification.success({
                                  message: error.errors[0].message,
                                  positionY: 'bottom',
                                  positionX: 'right',
                                  delay: 3000,
                                });
                              } else {
                                if ($scope.isOriginalVideo == true) {
                                  var data = JSON.parse(res.data[0]);
                                  storyService
                                    .updateStroy2({ id: $scope.video.originalVideo.id, twitter: data.id })
                                    .then(resss => {});
                                }
                                notification.success({
                                  message: 'Video posted successfully',
                                  positionY: 'bottom',
                                  positionX: 'right',
                                  delay: 3000,
                                });
                              }
                            });
                        });
                    });
                }
              });
            countIterations += 1;
          });
        });
    };

    oReq.send(null);
  };

  function parseFile(file, callback) {
    var fileSize = file.size;
    var chunkSize = Math.ceil(file.size / 3); // bytes
    var offset = 0;
    var self = this; // we need a reference to the current object
    var chunkReaderBlock = null;
    $scope.iterations = 3;

    var readEventHandler = function(evt) {
      if (evt.target.error == null) {
        offset += evt.target.result.length;

        callback(evt.target.result); // callback for handling read chunk
      } else {
        console.log('Read error: ' + evt.target.error);
        return;
      }
      if (offset >= fileSize) {
        // Document end
        return;
      }

      // of to the next chunk
      chunkReaderBlock(offset, chunkSize, file);
    };

    chunkReaderBlock = function(_offset, length, _file) {
      var r = new FileReader();
      var blob = _file.slice(_offset, length + _offset);
      r.onload = readEventHandler;
      r.readAsBinaryString(blob);
      //r.readAsDataURL(blob);
    };
    // now let's start the read with the first block
    chunkReaderBlock(offset, chunkSize, file);
  }

  $scope.inc_share=()=>{
		if ($state.params.punditId) {
			punditService.addShare($scope.storyId, $state.params.punditId).then(res => {});
		} else {
			punditService.addShare($scope.storyId).then(res => {});
		}
	}
}
