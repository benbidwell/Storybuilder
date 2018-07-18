//CurateCtrl Ctrollers
CurateCtrl.$inject = [
  '$rootScope',
  '$scope',
  '$state',
  'Notification',
  '$window',
  'OAuthService',
  'LocalStorageService',
  'DropboxService',
  'GoogleApiService',
  'StoryService',
  'FbService',
  'Upload',
  '$timeout',
  'InstaService',
  '$uibModal',
];

export default function CurateCtrl(
  $rootScope,
  $scope,
  $state,
  notification,
  $window,
  oAuthService,
  LocalStorageService,
  dropboxService,
  googleApiService,
  storyService,
  fbService,
  Upload,
  $timeout,
  InstaService,
  $uibModal,
) {
  $timeout(function() {
    $('.test').mCustomScrollbar({
      axis: 'y',
      setHeight: 120,
      mouseWheel: true,
    });
    $('.test1').mCustomScrollbar({
      theme: 'minimal',
    });
  }, 1000);

  $scope.social_type = '';
  $scope.cloud_type = '';
  $scope.show_social = false;
  $scope.show_cloud = false;
  $scope.show_local = false;
  $scope.uploaded_picture = [];
  $scope.hashtag = { value: '' };
  $scope.keyword = { value: '' };
  $scope.newAccount = { value: '' };
  $scope.select_media_type = { image: true, video: true };
  $scope.local_present = { image: false, video: false };

  $scope.filter = {
    filterCheck: [],
    filterValue: [],
  };

  $scope.media = [];
  $scope.mediaType = '';
  $scope.select_type = ['gplus', 'twitter'];

  var init = () => {
    // get data from local storage and save to $rootScope
    // Initailze rootscope.newStory variable;
    if ($window.localStorage.newStory == undefined || $window.localStorage.newStory == '') {
      $state.go('master.event');
    }
    $rootScope.newStory = JSON.parse($window.localStorage.newStory);

    // $rootScope.newStory.media = [];
    $scope.story_id = $rootScope.newStory.id;

    //Initailize $rootscope.twitter array variable
    if ($window.localStorage.twitter) {
      console.log(JSON.parse($window.localStorage.twitter));
      $rootScope.twitter = JSON.parse($window.localStorage.twitter);
      $scope.filter.filterValue = $rootScope.twitter.map(function(item) {
        $scope.filter.filterCheck[item.user_id] = true;
        return item.user_id;
      });
      console.log($scope.filter);
    }

    //Initailize $rootscope.dropbox array variable
    if ($window.localStorage.dropbox) {
      $rootScope.dropbox = JSON.parse($window.localStorage.dropbox);
      var temp = $rootScope.dropbox.map(function(item) {
        $scope.filter.filterCheck[item.user_id] = true;
        return item.user_id;
      });
      $scope.filter.filterValue = $scope.filter.filterValue.concat(temp);
    }

    //Initailize $rootscope.gdrive array variable
    if ($window.localStorage.gdrive) {
      $rootScope.gdrive = JSON.parse($window.localStorage.gdrive);
      var temp = $rootScope.gdrive.map(function(item) {
        $scope.filter.filterCheck[item.user_id] = true;
        return item.user_id;
      });
      $scope.filter.filterValue = $scope.filter.filterValue.concat(temp);
    }

    //Initailize $rootscope.GPLUS array variable
    if ($window.localStorage.gplus) {
      $rootScope.gplus = JSON.parse($window.localStorage.gplus);
      var temp = $rootScope.gplus.map(function(item) {
        $scope.filter.filterCheck[item.user_id] = true;
        return item.user_id;
      });
      $scope.filter.filterValue = $scope.filter.filterValue.concat(temp);
    }

    //Initailize $rootscope.facebook array variable
    if ($window.localStorage.facebook) {
      $rootScope.facebook = JSON.parse($window.localStorage.facebook);
      var temp = $rootScope.facebook.map(function(item) {
        $scope.filter.filterCheck[item.user_id] = true;
        return item.user_id;
      });
      $scope.filter.filterValue = $scope.filter.filterValue.concat(temp);
    }

    //Initailize $rootscope.instagram array variable
    if ($window.localStorage.instagram) {
      $rootScope.instagram = JSON.parse($window.localStorage.instagram);
      var temp = $rootScope.instagram.map(function(item) {
        $scope.filter.filterCheck[item.user_id] = true;
        return item.user_id;
      });
      $scope.filter.filterValue = $scope.filter.filterValue.concat(temp);
    }

    // Get local storage (images and videos) media from server
    LocalStorageService.get_localStorage($scope.story_id).then(function(res) {
      $scope.media = res.data.data.story_media;
      $rootScope.newStory.local = $scope.media;
      $scope.continue_story_status = false;
      if ($scope.media) {
        angular.forEach($scope.media, function(value, key) {
          if (value.media_type == 'image') {
            $scope.local_image_present = true;
          } else if (value.media_type == 'video') {
            $scope.local_video_present = true;
          }
          $scope.local_present = {
            image: $scope.local_image_present,
            video: $scope.local_video_present,
          };
        });
      }
    });
    // Javascript or jquery function
    var acc = document.getElementsByClassName('accordion');
    var i;
    for (i = 0; i < acc.length; i++) {
      acc[i].onclick = function() {
        this.classList.toggle('active');
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + 'px';
        }
      };
    }

    $(document).ready(function() {
      $('#DropDownSVed').click(function() {
        $('#DopListVedio').toggleClass('dppee1');
      });
      $('#DropDownS').click(function() {
        $('#DopList').toggleClass('dppee');
      });

      $('.cldstorageoption').click(function() {
        $('#DopListVedio').toggleClass('dppee1');
      });

      $('.scilchhnl').click(function() {
        $('#DopList').toggleClass('dppee');
      });
      $('#swtch').click(function() {
        $('#gump').toggle();
      });
    });
  };
  init();

  $scope.checkActive = (value, index) => {
    if (value > 0) {
      var acc = document.getElementsByClassName('accordion');
      acc[index].classList.toggle('active');
      var panel = acc[index].nextElementSibling;

      panel.style.maxHeight = '100%';
    }
  };
  var countvalue = 0;

  if ($rootScope.dropbox)
    if ($rootScope.gdrive) countvalue = $rootScope.gdrive.length + $rootScope.dropbox.length;
    else countvalue = $rootScope.dropbox.length;
  else if ($rootScope.gdrive) countvalue = $rootScope.gdrive.length;

  $scope.checkActive(countvalue, 1);
  // Filter for images
  $scope.filterImage = (item, check) => {
    if (check == true) {
      let index = $scope.filter.filterValue.indexOf(item);
      if ((index = -1)) $scope.filter.filterValue.push(item);
    } else {
      let index = $scope.filter.filterValue.indexOf(item);
      if (index != -1) $scope.filter.filterValue.splice(index, 1);
    }
    $scope.continue_story_status = false;
  };

  // Selector Model box
  $scope.selectorModelBox = () => {
    var modalInstance = $uibModal.open({
      animation: true,
      ariaLabelledBy: 'modal-title',
      ariaDescribedBy: 'modal-body selectedImages',
      size: 'md',
      templateUrl: 'imageSelector.html',
      controller: [
        '$rootScope',
        '$scope',
        '$state',
        '$uibModalInstance',
        '$window',
        'EventService',
        function($rootScope, $scope, $state, $uibModalInstance, $window, eventService) {
          if ($window.localStorage.next_query && $window.localStorage.next_query != 'undefined') {
            $scope.next_query = JSON.parse($window.localStorage.next_query);
          }
          console.log(document.getElementById('checkimagselect'));
          angular.element($window).scroll(function() {
            console.log($window.screenTop);
          });
          $scope.images = JSON.parse($window.localStorage.temp);
          if ($scope.images.length === 0) {
            setTimeout(function() {
              $scope.close();
            }, 700);
          }
          console.log($window.localStorage.temp);
          console.log($window.localStorage.twitter);
          $scope.loader = true;

          $scope.selectedImages = [];
          $scope.close = function() {
            $window.localStorage.temp = undefined;
            if ($window.localStorage.next_query && $window.localStorage.next_query != 'undefined')
              $window.localStorage.next_query = undefined;
            $uibModalInstance.dismiss('cancel');

            if ($scope.images.length > 0) {
              if ($scope.images[0].type == 'twitter') {
                var testTwit = JSON.parse($window.localStorage.twitter);
                var newTwit = testTwit.filter(function(item) {
                  return item.user_id != $scope.images[0].id;
                });
                $window.localStorage.twitter = JSON.stringify(newTwit);
              }

              if ($scope.images[0].type == 'facebook') {
                var testFace = JSON.parse($window.localStorage.facebook);
                var newFace = testFace.filter(function(item) {
                  return item.user_id != $scope.images[0].id;
                });
                $window.localStorage.facebook = JSON.stringify(newFace);
              }

              if ($scope.images[0].type == 'instagram') {
                var testInsta = JSON.parse($window.localStorage.instagram);
                var newInsta = testInsta.filter(function(item) {
                  return item.user_id != $scope.images[0].id;
                });
                $window.localStorage.instagram = JSON.stringify(newInsta);
              }

              if ($scope.images[0].type == 'gplus') {
                var testgplus = JSON.parse($window.localStorage.gplus);
                var newgplus = testgplus.filter(function(item) {
                  return item.user_id != $scope.images[0].id;
                });
                $window.localStorage.gplus = JSON.stringify(newgplus);
              }
            }

            $window.location.reload();
          };

          $scope.ok = function() {
            $rootScope.newStory = JSON.parse($window.localStorage.newStory);
            if ($rootScope.newStory.media == undefined) {
              $rootScope.newStory.media = [];
            }
            if ($window.localStorage.next_query && $window.localStorage.next_query != 'undefined')
              $window.localStorage.next_query = undefined;
            $rootScope.newStory.media = $rootScope.newStory.media.concat($scope.selectedImages);
            $window.localStorage.newStory = JSON.stringify($rootScope.newStory);
            $window.localStorage.temp = undefined;
            console.log($rootScope.newStory.media);

            $uibModalInstance.dismiss('cancel');
          };
          $scope.changefun = img => {
            let index = $scope.selectedImages.map(el => el.media_url).indexOf(img.media_url);
            if (index === -1) {
              $scope.selectedImages.push(img);
            } else {
              $scope.selectedImages.splice(index, 1);
            }
            console.log($scope.selectedImages);
          };

          $scope.nextcurl = function() {
            if ($scope.next_query != undefined) {
              $scope.curlbusy = true;
              oAuthService.keywordReSearch($scope.next_query.keyword, $scope.next_query.max_id).then(res => {
                res.data.statuses.forEach(item => {
                  if (item.extended_entities != undefined) {
                    var supertemp = [];
                    item.extended_entities.media.forEach(item2 => {
                      var indexvalue;
                      if (item2.type == 'photo') {
                        indexvalue = $scope.images.map(el => el.media_url).indexOf(item2.media_url);
                      } else if (item2.type == 'video') {
                        indexvalue = $scope.images.map(el => el.media_url).indexOf(item2.video_info.variants[1].url);
                      }

                      if (supertemp.length < 100 && indexvalue < 0) {
                        if (item2.type == 'photo') {
                          $scope.images.push({
                            type: 'twitter',
                            media_type: 'photo',
                            media_url: item2.media_url,
                            created_at: item.created_at,
                            id: 'Twitter' + $scope.next_query.keyword,
                          });
                        } else if (item2.type == 'video') {
                          $scope.images.push({
                            type: 'twitter',
                            media_type: 'video',
                            media_url: item2.video_info.variants[1].url,
                            created_at: item.created_at,
                            thumb_url: item2.media_url,
                            id: 'Twitter' + $scope.next_query.keyword,
                          });
                        }
                      }
                    });
                    if (res.data.search_metadata != undefined) {
                      if (res.data.search_metadata.next_results != undefined) {
                        var temp = res.data.search_metadata.next_results.replace('?max_id=', '');
                        var hashtag = $scope.next_query.keyword;
                        temp = temp.split('&');
                        $scope.next_query = {
                          max_id: temp[0],
                          keyword: hashtag.replace('#', '%23'),
                        };
                      } else {
                        $scope.next_query = undefined;
                      }
                    }
                  }
                });
                $scope.curlbusy = false;
              });
            }
          };
          setTimeout(function() {
            $scope.loader = false;
          }, 500);
        },
      ],
    });
  };

  //
  if ($window.localStorage.temp && $window.localStorage.temp != undefined && $window.localStorage.temp != 'undefined') {
    $scope.selectorModelBox();
  }

  // Select media type
  $scope.media_check = function(type, val) {
    if (val.image == true && val.video == true) {
      $scope.mediaType = '';
    } else if (val.image == false && val.video == false) {
      $scope.mediaType = 'hide';
    } else if (type == 'image' && (val.image == false && val.video == true)) {
      $scope.mediaType = 'video';
      $scope.select_media_type.image = false;
    } else if (type == 'video' && (val.image == true && val.video == false)) {
      $scope.mediaType = 'image';
      $scope.select_media_type.video = false;
    } else if (type == 'video' && (val.image == false && val.video == true)) {
      $scope.mediaType = 'video';
      $scope.select_media_type.video = true;
    } else if (type == 'image' && (val.image == true && val.video == false)) {
      $scope.mediaType = 'image';
      $scope.select_media_type.image = true;
    }
    $scope.continue_story_status = false;
  };

  $scope.Cnnlsclc = function(x) {
    $scope.social_type = x;
    $scope.show_social = true;
    $scope.show_cloud = false;
    $scope.show_local = false;
  };
  $scope.claudStrg = x => {
    $scope.cloud_type = x;
    $scope.show_social = false;
    $scope.show_cloud = true;
    $scope.show_local = false;
  };
  $scope.local_media = function() {
    $scope.show_social = false;
    $scope.show_cloud = false;
    $scope.show_local = true;
  };

  // Upload images & videos (local storage)
  $scope.upload = function(files) {
    $scope.count = 0;
    $scope.count_load = 0;
    $scope.file_count = 0;
    angular.forEach(files, function(value, key) {
      var a = [];
      a[0] = value;
      a[1] = 0;
      $scope.uploaded_picture.push(a);
    });
    var eventProcess = evt => {
      var progressPercentage = parseInt((100.0 * evt.loaded) / evt.total);
      if (evt.type == 'progress') {
        $scope.uploaded_picture[$scope.count][1] = progressPercentage;
        $scope.count = $scope.count + 1;
      } else {
        $scope.uploaded_picture[$scope.file_count][1] = progressPercentage;
      }
    };

    var file_upload = resp => {
      $scope.file_count = $scope.file_count + 1;

      if ($scope.uploaded_picture.length == $scope.file_count) {
        LocalStorageService.get_localStorage($scope.story_id).then(function(res) {
          $timeout(function() {
            $scope.uploaded_picture = [];
            var acc = document.getElementsByClassName('accordion');
            acc[2].click();
            acc[2].click();
          }, 500);

          $scope.media = res.data.data.story_media;
          $rootScope.newStory.local = $scope.media;
          $scope.continue_story_status = false;

          angular.forEach($scope.media, function(value, key) {
            if (value.media_type == 'image') {
              $scope.local_image_present = true;
              $scope.select_media_type.image = true;
            } else if (value.media_type == 'video') {
              $scope.local_video_present = true;
              $scope.select_media_type.video = true;
            }
            $scope.local_present = {
              image: $scope.local_image_present,
              video: $scope.local_video_present,
            };
          });
        });
      }
      return resp.data;
    };

    if (files != '') {
      for (var i = 0; i < files.length; i++) {
        var formdata = { media: [files[i]], story_id: $scope.story_id };
        // $timeout(function () {
        Upload.upload({
          url: $rootScope.apiUrl + 'api/story_media_upload',
          data: formdata,
        }).then(
          file_upload,
          function(err) {
            return err;
          },
          eventProcess,
        );
      }
    }
  };

  // Authenticate user to access media from social profile
  $scope.login = (type, username) => {
    $scope.loading = true;
    var supertemp = [];
    var social_type = type;

    if (
      (social_type == 'Google Drive' && $scope.filter.filterValue.includes('GDrive' + username)) ||
      (social_type == 'Dropbox' && $scope.filter.filterValue.includes('Dropbox' + username)) ||
      (social_type == 'Facebook' && $scope.filter.filterValue.includes('Facebook' + username)) ||
      (social_type == 'Twitter' && $scope.filter.filterValue.includes('Twitter' + username)) ||
      (social_type == 'Google' && $scope.filter.filterValue.includes('GPlus' + username))
    ) {
      $scope.newAccount.value = '';
      notification.error({
        message: 'Username already present!',
        positionY: 'bottom',
        positionX: 'right',
        delay: 3000,
      });
      $scope.loader = false;
    } else {
      if (type == 'Facebook') {
        fbService.fb_login();
      } else if (type == 'Twitter') {
        oAuthService.tw_access_token('POST', 'https://api.twitter.com/oauth/request_token', '').then(res => {
          var token = res.data.split('&');
          window.open(
            'https://api.twitter.com/oauth/authenticate?' + token[0] + '&screen_name=' + username + '&force_login=true',
            '',
            'width=600,height=500',
          );
          let accessToken = token[0].split('=');
          let accessTokenSecret = token[1].split('=');
          $rootScope.accessToken = accessToken;
          $rootScope.accessTokenSecret = accessTokenSecret;
        });
      } else if (type == 'Google') {
        googleApiService.gPlusLogin(username);
      } else if (type == 'Google Drive') {
        googleApiService.gdriveLogin(username);
      } else if (type == 'Dropbox') {
        dropboxService.dropboxLogin(username);
      } else if (type == 'Instagram') {
        InstaService.login(username);
      }
    }
  };

  // Delete an Existing Account
  $scope.deleteAccount = (social_type, AccountId) => {
    $scope.st = social_type;
    $scope.aid = AccountId;
    var modalInstance = $uibModal.open({
      animation: true,
      ariaLabelledBy: 'modal-title',
      ariaDescribedBy: 'modal-body',
      size: 'lg',
      templateUrl: 'deleteAccount.html',
      controller: [
        '$rootScope',
        '$scope',
        '$state',
        '$uibModalInstance',
        '$window',
        'EventService',
        function($rootScope, $scope, $state, $uibModalInstance, $window, eventService) {
          $scope.close = function() {
            $uibModalInstance.dismiss('cancel');
          };

          $scope.ok = function() {
            $rootScope.deleteAccount = true;
            $uibModalInstance.dismiss('cancel');
          };
        },
      ],
    });
    // watch rootScope.deleteAccount variable
    $rootScope.$watch(
      function(rootScope) {
        return rootScope.deleteAccount;
      },
      function(newValue, oldValue) {
        console.log(newValue + '-' + oldValue);
        if (newValue == true) {
          let index = $scope.filter.filterValue.indexOf($scope.aid);
          $scope.filter.filterValue.splice(index, 1);
          storyService.deleteSocialAccount($scope.st, $scope.aid);
        }
      },
      true,
    );
  };

  // Import media using keyword
  $scope.searchKeyword = (social_type, keyword) => {
    $scope.loader = true;
    $scope.loading = true;
    var supertemp = [];
    if (
      (social_type == 'Twitter' && $scope.filter.filterValue.indexOf('Twitter' + keyword) != -1) ||
      (social_type == 'Google' && $scope.filter.filterValue.indexOf('GPlus' + keyword) != -1)
    ) {
      $scope.keyword.value = '';
      notification.error({ message: 'Keyword already present!', positionY: 'bottom', positionX: 'right', delay: 3000 });
      $scope.loader = false;
      $scope.loading = false;
    } else {
      if (social_type == 'Twitter') {
        oAuthService.keywordSearch(keyword).then(res => {
          $scope.loader = false;
          if ($window.localStorage.twitter == undefined || $window.localStorage.twitter == '') {
            var twitter = [];
            twitter.push({ user_id: 'Twitter' + keyword, screen_name: keyword, channel_type: 'Keyword' });
          } else {
            var twitter = JSON.parse($window.localStorage.twitter);
            if (
              twitter
                .map(function(el) {
                  return el['user_id'];
                })
                .indexOf('Twitter' + keyword) != -1
            ) {
              return;
            } else {
              twitter.push({ user_id: 'Twitter' + keyword, screen_name: keyword, channel_type: 'Keyword' });
            }
          }
          $rootScope.twitter = twitter;
          $window.localStorage.twitter = JSON.stringify(twitter);
          res.data.statuses.forEach(item => {
            if (item.extended_entities != undefined) {
              item.extended_entities.media.forEach(item2 => {
                var indexval;
                if (item2.type == 'photo') {
                  indexval = supertemp.map(el => el.media_url).indexOf(item2.media_url);
                } else if (item2.type == 'video') {
                  indexval = supertemp.map(el => el.media_url).indexOf(item2.video_info.variants[1].url);
                }

                if (supertemp.length < 500 && indexval < 0) {
                  if (item2.type == 'photo') {
                    supertemp.push({
                      type: 'twitter',
                      media_type: 'photo',
                      media_url: item2.media_url,
                      created_at: item.created_at,
                      id: 'Twitter' + keyword,
                    });
                  } else if (item2.type == 'video') {
                    supertemp.push({
                      type: 'twitter',
                      media_type: 'video',
                      media_url: item2.video_info.variants[1].url,
                      created_at: item.created_at,
                      thumb_url: item2.media_url,
                      id: 'Twitter' + keyword,
                    });
                  }
                }
              });
            }
          });
          $window.localStorage.temp = JSON.stringify(supertemp);
          $window.location.reload();
        });
      } else if (social_type == 'Google') {
        googleApiService.gPlusKeyword({ keyword: keyword, type: 'keyword' }).then(function(res) {
          if ($window.localStorage.gplus == undefined || $window.localStorage.gplus == '') {
            var gplus = [];
            gplus.push({ user_id: 'GPlus' + keyword, screen_name: keyword, channel_type: 'Keyword' });
          } else {
            var gplus = JSON.parse($window.localStorage.gplus);
            if (
              gplus
                .map(function(el) {
                  return el['user_id'];
                })
                .indexOf('GPlus' + keyword) != -1
            ) {
              return;
            } else {
              gplus.push({ user_id: 'GPlus' + keyword, screen_name: keyword, channel_type: 'Keyword' });
            }
          }
          $rootScope.gplus = gplus;
          $window.localStorage.gplus = JSON.stringify(gplus);

          var arr = [];
          if (res.data.data && res.data.data.length > 0) {
            res.data.data.forEach(function(item) {
              item = item.replace(/^(\/\/)/, 'http://');

              if (supertemp.length < 50) {
                arr.push({
                  type: 'gplus',
                  media_type: 'photo',
                  media_url: item,
                  id: 'GPlus' + keyword,
                });
              }
            });
          }
          $window.localStorage.temp = JSON.stringify(arr);
          $window.location.reload();
        });
      }
      $scope.loader = false;
    }
  };

  // Import media using hashtag
  $scope.searchHashtag = (social_type, hashtag) => {
    $scope.loading = true;
    $scope.loader = true;
    var supertemp = [];
    var keyword = hashtag;
    if (
      (social_type == 'Twitter' && $scope.filter.filterValue.includes('Twitter' + hashtag)) ||
      (social_type == 'Google' && $scope.filter.filterValue.includes('GPlus' + hashtag))
    ) {
      $scope.hashtag.value = '';
      notification.error({ message: 'Hashtag already present!', positionY: 'bottom', positionX: 'right', delay: 3000 });
      $scope.loader = false;
      $scope.loading = false;
    } else {
      if (social_type == 'Twitter') {
        oAuthService.keywordSearch(hashtag.replace('#', '%23')).then(res => {
          if ($window.localStorage.twitter == undefined || $window.localStorage.twitter == '') {
            var twitter = [];
            twitter.push({ user_id: 'Twitter' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
          } else {
            var twitter = JSON.parse($window.localStorage.twitter);
            if (
              twitter
                .map(function(el) {
                  return el['user_id'];
                })
                .indexOf('Twitter' + keyword) != -1
            ) {
              return;
            } else {
              twitter.push({ user_id: 'Twitter' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
            }
          }
          $rootScope.twitter = twitter;
          $window.localStorage.twitter = JSON.stringify(twitter);
          res.data.statuses.forEach(item => {
            if (item.extended_entities != undefined) {
              item.extended_entities.media.forEach(item2 => {
                var indexval;
                if (item2.type == 'photo') {
                  indexval = supertemp.map(el => el.media_url).indexOf(item2.media_url);
                } else if (item2.type == 'video') {
                  indexval = supertemp.map(el => el.media_url).indexOf(item2.video_info.variants[1].url);
                }

                if (supertemp.length < 500 && indexval < 0) {
                  if (item2.type == 'photo') {
                    supertemp.push({
                      type: 'twitter',
                      media_type: 'photo',
                      media_url: item2.media_url,
                      created_at: item.created_at,
                      id: 'Twitter' + keyword,
                    });
                  } else if (item2.type == 'video') {
                    supertemp.push({
                      type: 'twitter',
                      media_type: 'video',
                      media_url: item2.video_info.variants[1].url,
                      thumb_url: item2.media_url,
                      created_at: item.created_at,
                      id: 'Twitter' + keyword,
                    });
                  }
                }
              });
            }
          });
          if (res.data.search_metadata != undefined) {
            if (res.data.search_metadata.next_results != undefined) {
              var temp = res.data.search_metadata.next_results.replace('?max_id=', '');
              temp = temp.split('&');
              var next_query = {
                max_id: temp[0],
                keyword: hashtag.replace('#', '%23'),
              };
              $window.localStorage.next_query = JSON.stringify(next_query);
            } else {
              $window.localStorage.next_query = undefined;
            }
          }
          $window.localStorage.temp = JSON.stringify(supertemp);

          // $rootScope.newStory=JSON.parse($window.localStorage.newStory);
          // if($rootScope.newStory.media==undefined)
          //   $rootScope.newStory.media=[];
          // $rootScope.newStory.media=$rootScope.newStory.media.concat(supertemp);
          // $window.localStorage.newStory=JSON.stringify($rootScope.newStory);
          $window.location.reload();
        });
      } else if (social_type == 'Google') {
        var newkeyword = keyword.replace('#', '');
        googleApiService.gPlusKeyword({ keyword: newkeyword, type: 'Hashtag' }).then(function(res) {
          if ($window.localStorage.gplus == undefined || $window.localStorage.gplus == '') {
            var gplus = [];
            gplus.push({ user_id: 'GPlus' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
          } else {
            var gplus = JSON.parse($window.localStorage.gplus);
            if (
              gplus
                .map(function(el) {
                  return el['user_id'];
                })
                .indexOf('GPlus' + keyword) != -1
            ) {
              return;
            } else {
              gplus.push({ user_id: 'GPlus' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
            }
          }
          $rootScope.gplus = gplus;
          $window.localStorage.gplus = JSON.stringify(gplus);

          var arr = [];
          if (res.data.data && res.data.data.length > 0) {
            res.data.data.forEach(function(item) {
              item = item.replace(/^(\/\/)/, 'http://');

              if (supertemp.length < 50) {
                arr.push({
                  type: 'gplus',
                  media_type: 'photo',
                  media_url: item,
                  id: 'GPlus' + keyword,
                });
              }
            });
          }
          $window.localStorage.temp = JSON.stringify(arr);
          $window.location.reload();
        });
      } else if (social_type == 'Instagram') {
        // InstaService.searchHashtag(keyword)
        // .then(res=>{

        // })
        var newkeyword = keyword.replace('#', '');
        InstaService.searchHashtag({ keyword: newkeyword, type: 'Hashtag' }).then(function(res) {
          console.log(res);
          if ($window.localStorage.instagram == undefined || $window.localStorage.instagram == '') {
            var instagram = [];
            instagram.push({ user_id: 'instagram' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
          } else {
            var instagram = JSON.parse($window.localStorage.instagram);
            if (
              instagram
                .map(function(el) {
                  return el['user_id'];
                })
                .indexOf('instagram' + keyword) != -1
            ) {
              return;
            } else {
              instagram.push({ user_id: 'instagram' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
            }
          }
          $rootScope.instagram = instagram;
          $window.localStorage.instagram = JSON.stringify(instagram);

          var arr = [];
          if (res.data && res.data.length > 0) {
            res.data.forEach(function(item) {
              console.log(item);
              //item=item.replace(/^(\/\/)/,"http://");
              if (arr.length < 170) {
                if (item.type == 'image') {
                  arr.push({
                    type: 'instagram',
                    media_type: 'photo',
                    media_url: item.images.standard_resolution.url,
                    id: 'instagram' + keyword,
                    created_at: item.created_time,
                    location: item.location ? item.location.name : item.location,
                  });
                } else if (item.type == 'video') {
                  arr.push({
                    type: 'instagram',
                    media_type: 'video',
                    media_url: item.videos.standard_resolution.url,
                    id: 'instagram' + keyword,
                    created_at: item.created_time,
                    thumb_url: item.images.low_resolution.url,
                    location: item.location ? item.location.name : item.location,
                  });
                } else if (item.type == 'carousel') {
                  if (item.images) {
                    arr.push({
                      type: 'instagram',
                      media_type: 'photo',
                      media_url: item.images.standard_resolution.url,
                      id: 'instagram' + keyword,
                      created_at: item.created_time,
                      location: item.location ? item.location.name : item.location,
                    });
                  } else if (item.videos) {
                    arr.push({
                      type: 'instagram',
                      media_type: 'video',
                      media_url: item.videos.standard_resolution.url,
                      id: 'instagram' + keyword,
                      created_at: item.created_time,
                      location: item.location ? item.location.name : item.location,
                    });
                  }
                }
              }
            });
          }
          $window.localStorage.temp = JSON.stringify(arr);
          $window.location.reload();
        });
      } else if (social_type == 'Facebook') {
        var newkeyword = keyword.replace('#', '');
        fbService.searchHashtag({ keyword: newkeyword, type: 'Hashtag' }).then(function(res) {
          if ($window.localStorage.facebook == undefined || $window.localStorage.facebook == '') {
            var facebook = [];
            facebook.push({ user_id: 'facebook' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
          } else {
            var facebook = JSON.parse($window.localStorage.facebook);
            if (
              facebook
                .map(function(el) {
                  return el['user_id'];
                })
                .indexOf('facebook' + keyword) != -1
            ) {
              return;
            } else {
              facebook.push({ user_id: 'facebook' + keyword, screen_name: keyword, channel_type: 'Hashtag' });
            }
          }
          $rootScope.facebook = facebook;
          $window.localStorage.facebook = JSON.stringify(facebook);

          var arr = [];
          if (res.data.data && res.data.data.length > 0) {
            res.data.data.forEach(function(item) {
              item = item.replace(/^(\/\/)/, 'http://');

              if (supertemp.length < 50) {
                arr.push({
                  type: 'facebook',
                  media_type: 'photo',
                  media_url: item,
                  id: 'facebook' + keyword,
                });
              }
            });
          }
          $window.localStorage.temp = JSON.stringify(arr);
          $window.location.reload();
        });
      }
      $scope.loader = false;
    }
  };
  $scope.select_media = function(filter, media, local_media_type) {
    $scope.data = [];
    var filter = filter;
    var social_media = media.media;
    var local_media = media.local;
    angular.forEach(filter, function(value, key) {
      angular.forEach(social_media, function(value1, key1) {
        if (value == value1.id) {
          $scope.data.push(value1);
        }
      });
    });
    angular.forEach(local_media, function(value1, key1) {
      if (local_media_type.image && value1.media_type == 'image') {
        $scope.data.push({
          media_type: 'photo',
          media_url: '/story_media/story_' + value1.story_id + '/' + value1.media_url,
          type: value1.media_source,
          id: value1.id,
        });
      } else if (local_media_type.video && value1.media_type == 'video') {
        $scope.data.push({
          media_type: value1.media_type,
          media_url: '/story_media/story_' + value1.story_id + '/' + value1.media_url,
          type: value1.media_source,
          id: value1.id,
        });
      }
    });
    $rootScope.newStory.select_media = $scope.data;
    $window.localStorage.select_media = JSON.stringify($scope.data);
    $state.go('master.selectMedia');
  };

  $scope.continue_story = () => {
    $scope.continue_story_status = $('.localmedia').length == 0 && $('.newStory').length == 0 ? true : false;
  };
}
