//Story Ctrollers
StoryCtrl.$inject = [
  '$rootScope',
  '$scope',
  '$state',
  'EventService',
  'AuthService',
  'StoryService',
  'Notification',
  'jwtHelper',
  '$window',
  '$http',
  '$uibModal',
  'GoogleApiService',
  'FbService',
  '$timeout',
];

export default function StoryCtrl(
  $rootScope,
  $scope,
  $state,
  eventService,
  authService,
  storyService,
  notification,
  jwtHelper,
  $window,
  $http,
  $uibModal,
  googleApiService,
  fbService,
  $timeout,
) {
  $rootScope.fb = {};
  $rootScope.fb.like = 0;
  $rootScope.fb.comment = 0;
  $rootScope.fb.share = 0;
  $rootScope.fb.view = 0;
  $rootScope.yt = {};
  $rootScope.yt.like = 0;
  $rootScope.yt.comment = 0;
  $rootScope.yt.share = 0;
  $rootScope.yt.view = 0;
  $scope.ids = '';
  $scope.facebook_ids = '';
  eventService
    .event_analysis($state.params.id)
    .then(function(res) {
      $scope.ids = res.data.data.youtube_ids.toString();
      $scope.facebook_ids = res.data.data.facebook_ids;
      var twitter_ids = res.data.data.twitter_ids;
    })
    .catch(function(errors) {});

  $scope.refreshStats = () => {
    if ($scope.ids != '') {
      googleApiService.youtubeStats({ ids: $scope.ids }).then(res => {
        $rootScope.yt.comment = 0;
        $rootScope.yt.like = 0;
        $rootScope.yt.view = 0;
        res.items.forEach(function(itm) {
          $rootScope.yt.comment += parseInt(itm.statistics.commentCount);
          $rootScope.yt.like += parseInt(itm.statistics.likeCount);
          $rootScope.yt.view += parseInt(itm.statistics.viewCount);
        });
        $scope.circleFun();
      });
    }
  };
  $scope.refreshFbStats = () => {
    if ($scope.facebook_ids != '') {
      fbService.getToken().then(res => {
        var authToken = res.authresp.accessToken;
        var appsecret = res.appsecret;

        $scope.facebook_ids.forEach(function(item) {
          $rootScope.fb.comment = 0;
          $rootScope.fb.like = 0;
          $rootScope.fb.view = 0;
          fbService.fbLikeCount(item, authToken, appsecret).then(res => {
            $rootScope.fb.like += res.data.length;
            $scope.circleFun();
          });
          fbService.fbShareCount(item, authToken, appsecret).then(res => {
            $rootScope.fb.share += res.data.length;
            $scope.circleFun();
          });
          fbService.fbCommentCount(item, authToken, appsecret).then(res => {
            $rootScope.fb.comment += res.data.length;
            $scope.circleFun();
          });
        });
      });
    }
  };
  let getEvent = id => {
    eventService.getEvent(id).then(res => {
      $scope.event = res.data.data;
      if ($scope.event.event_picture != (null || undefined || '')) {
        $scope.event.event_picture = '/event_pictures/' + $scope.event.event_picture;
      }
      document.getElementsByClassName('story_banner')[0].style.background =
        "url('" + $scope.event.event_picture + "') center center / cover no-repeat";
    });
  };

  getEvent($state.params.id);

  $scope.storyForm = {
    story_title: '',
    story_details: '',
    story_picture: undefined,
    event_id: $state.params.id,
    id: undefined,
  };
  $scope.event = {
    id: $state.params.id,
  };

  var nextPageLoad;
  var lastPage;
  $(window).scroll(function() {
    if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
      if (nextPageLoad == false && lastPage != false && ($scope.search == undefined || $scope.search == '')) {
        $scope.nextPage();
      }
    }
  });

  $scope.storyOptions = {
    limit: 7,
    offset: 0,
    event_id: $state.params.id,
  };

  $scope.tab = {
    thumbnail: true,
    list: false,
  };

  $scope.getAllStroy = options => {
    nextPageLoad = true;
    storyService.getAllStroy(options).then(res => {
      if (res.data.data.stories) {
       
        $scope.stories = res.data.data.stories;
        $scope.StoriesView = 0;
        $scope.StoriesShare=0;
        $scope.StoriesComment=0;
        $scope.stories.forEach(function(story) {
       
          $scope.StoriesComment+=story.pundit_story.length;
          $scope.StoriesView += story.views;
          $scope.StoriesShare += story.shares;
        });
       
      }
      $scope.circleFun();
      nextPageLoad = false;
    });
  };

  $scope.nextPage = () => {
    nextPageLoad = true;
    $scope.nextPageloader = true;
    if ($state.current.name == 'master.stories') {
      $scope.storyOptions.offset += $scope.storyOptions.limit;
      $scope.storyOptions.limit = 8;
      storyService.getAllStroy($scope.storyOptions).then(res => {
        $scope.nextPageloader = false;
        if (res.status == 200) {
          nextPageLoad = false;
          if (res.data.data.stories.length == 0) {
            lastPage == false;
          }
          res.data.data.stories.forEach(function(item) {
            $scope.stories.push(item);
          });
        }
      });
    }
  };

  if ($state.current.name == 'master.stories') {
    $scope.getAllStroy($scope.storyOptions);
  }

  $scope.back = () => {
    $state.go('master.stories');
  };

  $scope.sorting = type => {
    if (type == 'Latest added') {
      $scope.sort_type = '-id';
    } else if (type == 'A to Z') {
      $scope.sort_type = 'story_title';
    } else if (type == 'Z to A') {
      $scope.sort_type = '-story_title';
    } else if (type == 'Maxmium Views') {
      $scope.sort_type = '';
    }
  };

  $scope.createStory = () => {
    $scope.requiredEmail = false;
    $scope.errors = undefined;
    $scope.err = undefined;
    $scope.loader = true;
    $scope.storyForm.event_id = $state.params['id'];
    // send request to forgot password
    storyService.createStory($scope.files, $scope.storyForm).then(res => {
      $scope.loader = false;
      if (res.status == 200) {
        notification.success({ message: res.data.message, positionY: 'bottom', positionX: 'right', delay: 3000 });
        $rootScope.newStory = res.data.data;
        $window.localStorage.newStory = JSON.stringify(res.data.data);
        $window.localStorage.twitter = [];
        $window.localStorage.gplus = [];
        $window.localStorage.gdrive = [];
        $window.localStorage.dropbox = [];
        $window.localStorage.facebook = [];
        $window.localStorage.instagram = [];
        $window.localStorage.temp = [];
        $rootScope.facebook = [];
        $rootScope.twitter = [];
        $rootScope.gdrive = [];
        $rootScope.gplus = [];
        $rootScope.dropbox = [];
        $rootScope.instagram = [];
        $state.go('master.curate');
      } else if (res.status == 400) {
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
      } else {
        notification.error({ message: 'Error', positionY: 'bottom', positionX: 'right', delay: 3000 });
      }
    });
  };

  $scope.switchTab = slug => {
    if (slug == 'thumbnail') {
      $scope.tab.thumbnail = true;
      $scope.tab.list = false;
    } else if (slug == 'list') {
      $scope.tab.thumbnail = false;
      $scope.tab.list = true;
    }
  };

  $scope.setClientData = data => {
    if (data.id != undefined) {
      $scope.stories = [];
      $scope.stories.push(data);
    } else {
      $scope.search.value = '';
    }
  };

  $scope.resetStories = value => {
    if (value == '') {
      $scope.storyOptions.limit = 7;
      $scope.storyOptions.offset = 0;
      $scope.getAllStroy($scope.storyOptions);
    }
  };

  $scope.deleteStory = id => {
    $rootScope.story_delect_id = id;
    var modalInstance = $uibModal.open({
      animation: true,
      ariaLabelledBy: 'modal-title',
      ariaDescribedBy: 'modal-body',
      size: 'lg',
      templateUrl: 'deleteStory.html',
      controller: [
        '$rootScope',
        '$scope',
        '$state',
        '$uibModalInstance',
        '$window',
        'StoryService',
        function($rootScope, $scope, $state, $uibModalInstance, $window, storyService) {
          $scope.close = function() {
            $uibModalInstance.dismiss('cancel');
          };
          storyService.getDeleteMsg().then(res => {
            if (res.status == 200) {
              $scope.deleteMsg = res.data.message;
            } else {
              $scope.deleteMsg = 'This Action Cannot Be Undo';
            }
          });
          $scope.ok = function() {
            $scope.loader = true;
            var id = $rootScope.story_delect_id;

            storyService.deleteStory(id).then(res => {
              $scope.loader = false;
              if (res.status == 200) {
                notification.success({
                  message: res.data.message,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
                $state.reload();
              } else {
                notification.error({ message: 'Error', positionY: 'bottom', positionX: 'right', delay: 3000 });
              }
              $uibModalInstance.dismiss('cancel');
            });
          };
        },
      ],
    });
  };

  $scope.select_image = function(image, data) {
    if (data == null) {
      notification.error({
        message: 'Image should be in jpeg,jpg,png format!',
        positionY: 'bottom',
        positionX: 'right',
        delay: 3000,
      });
    } else if (data.size > 4194304) {
      $scope.storyForm.story_picture = '';
      notification.error({
        message: 'Its size should not exceed 4MB!',
        positionY: 'bottom',
        positionX: 'right',
        delay: 3000,
      });
    }
  };

  $scope.remove_image = function(id) {
    $scope.storyForm.story_picture = '';
  };

  $scope.deleteEvent = id => {
    $rootScope.delect_id = id;
    var modalInstance = $uibModal.open({
      animation: true,
      ariaLabelledBy: 'modal-title',
      ariaDescribedBy: 'modal-body',
      size: 'lg',
      templateUrl: 'deleteEvent.html',
      controller: [
        '$rootScope',
        '$scope',
        '$state',
        '$uibModalInstance',
        '$window',
        'EventService',
        function($rootScope, $scope, $state, $uibModalInstance, $window, eventService) {
          eventService.getDeleteMsg().then(res => {
            if (res.status == 200) {
              $scope.deleteMsg = res.data.message;
            } else {
              $scope.deleteMsg = 'This Action Cannot Be Undo';
            }
          });

          $scope.close = function() {
            $uibModalInstance.dismiss('cancel');
          };

          $scope.ok = function() {
            $scope.loader = true;
            var id = $rootScope.delect_id;
            eventService.deleteEvent(id).then(res => {
              $scope.loader = false;
              if (res.status == 200) {
                notification.success({
                  message: res.data.message,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
                $uibModalInstance.dismiss('cancel');
                $state.go('master.event');
              }
            });
          };
        },
      ],
    });
  };

  if ($state.current.name == 'master.stories') {
    $('#dropImg').hover(
      function() {
        $(this).css('font-size', '25px');
        $('#dropImg').html($(this).attr('data-hover-title'));
      },
      function() {
        $(this).css('font-size', '14px');
        $('#dropImg').text($(this).attr('data-title'));
      },
    );
    $scope.circleFun = () => {
      // (function($) {
      // $timeout(function() {
      var c1 = $('.circle1');
      var c2 = $('.circle2');
      var c3 = $('.circle3');
      console.log('asdfasdf');
      console.log($scope.StoriesView)
      if ($scope.StoriesView > 0 && $scope.StoriesShare > 0) {
        var c1Radio = $scope.StoriesShare / $scope.StoriesView;
      } else {
        var c1Radio = 0;
      }
      var fbyt = $rootScope.yt.like + $rootScope.fb.like;
      if ($scope.StoriesView > 0 && fbyt > 0) {
        var c2Radio = fbyt / $scope.StoriesView;
      } else {
        var c2Radio = 0;
      }
      var fbcytc = $scope.StoriesComment;
      if ($scope.StoriesView > 0 && fbcytc > 0) {
        var c3Radio = fbcytc / $scope.StoriesView;
      } else {
        var c3Radio = 0;
      }
      c1.circleProgress({
        startAngle: (-Math.PI / 4) * 3,
        value: c1Radio,
        lineCap: 'round',
        fill: { gradient: ['#46CBA5', '#4199B3'] },
      });
      c2.circleProgress({
        startAngle: (-Math.PI / 4) * 3,
        value: c2Radio,
        lineCap: 'round',
        fill: { gradient: ['#46CBA5', '#4199B3'] },
      });
      c3.circleProgress({
        startAngle: (-Math.PI / 4) * 3,
        value: c3Radio,
        lineCap: 'round',
        fill: { gradient: ['#46CBA5', '#4199B3'] },
      });
      //}, 2500);
      // })(jQuery);
    };
    function DropDown(el) {
      this.dd = el;
      this.initEvents();
    }
    DropDown.prototype = {
      initEvents: function() {
        var obj = this;

        obj.dd.on('click', function(event) {
          $(this).toggleClass('active');
          event.stopPropagation();
        });
      },
    };

    $(function() {
      var dd = new DropDown($('#dd'));
      $(document).click(function() {
        // all dropdowns
        $('.wrapper-dropdown-2').removeClass('active');
      });
    });

    $(function() {
      var dd = new DropDown($('#dd2'));
      $(document).click(function() {
        // all dropdowns
        $('.wrapper-dropdown-3').removeClass('active');
      });
    });
  }
}
