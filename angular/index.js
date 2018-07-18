'use strict'
// //required files
import $ from 'jquery';
import jQuery from 'jquery';
require("./assets/js/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js");
import angular from 'angular';

//import "dependencies"
import uirouter from '@uirouter/angularjs';
import ngMessages from 'angular-messages';
import uiNotification from 'angular-ui-notification';
import jwt from 'angular-jwt';
import ngBootstrap from 'angular-ui-bootstrap';
import ngFileUpload from 'ng-file-upload';
import ngInfiniteScroll from 'ng-infinite-scroll';
import uiSelect from 'ui-select';

import ngSanitize from 'angular-sanitize';
require('../node_modules/angularjs-color-picker/dist/angularjs-color-picker.js')
require('../node_modules/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js')
require('../node_modules/angular-slimscroll/angular-slimScroll.min.js')
require('../node_modules/@uirouter/angularjs/release/stateEvents.min.js')
require('../node_modules/jssha/src/sha.js');
require('./assets/js/draganddrop.min.js');
require('./assets/js/angular-sortable-view.js');

// impoting css
import "./assets/css/bootstrap.min.css"
import "./assets/css/style.css"
import "./assets/css/font-awesome.min.css"
import "./assets/css/hover-min.css"
import "./assets/css/responsive.css"
import "./assets/css/noJS.css"
import "./assets/css/style_dropdown.css"
import "./assets/css/datepicker.css"
import "./assets/css/progress_bar.css"
import "./assets/css/component.css"
import "./assets/css/dropzone-custom-file.css"
import "./assets/css/ion.rangeSlider.css"
import "./assets/css/ion.rangeSlider.skinHTML5.css"
import "angular-ui-notification/dist/angular-ui-notification.min.css";
import "ng-sortable/dist/ng-sortable.style.min.css";
import "angularjs-color-picker/dist/angularjs-color-picker.min.css";
import "ui-select/dist/select.min.css";
// import routing app files
import routing from './app/routes/routes.js' // for routes

// import all controllers
import AuthCtrl from './app/controllers/AuthCtrl.js'
import SignupCtrl from './app/controllers/SignupCtrl'
import MasterCtrl from './app/controllers/MasterCtrl'
import EventCtrl from './app/controllers/EventCtrl'
import ProfileCtrl from './app/controllers/ProfileCtrl'
import StoryCtrl from './app/controllers/StoryCtrl'
import CurateCtrl from './app/controllers/CurateCtrl'
import CallbackCtrl from './app/controllers/CallbackCtrl'
import SelectMediaCtrl from './app/controllers/SelectMediaCtrl'
import StoryEditCtrl from './app/controllers/StoryEditCtrl'
import PunditCtrl from './app/controllers/PunditCtrl'
import SharingCtrl from './app/controllers/SharingCtrl'
import PunditActivationCtrl from './app/controllers/PunditActivationCtrl'

/** import all services*/
/**
 * Represents a book.
 * @constructor
 */
import AppService from './app/services/AppService'
import AuthService from './app/services/AuthService'
import OAuthService from './app/services/OAuthService'
import EventService from './app/services/EventService'
import StoryService from './app/services/StoryService'
import FbService from './app/services/FbService'
import InstaService from './app/services/InstagramService'
import PunditService from './app/services/PunditService'
import MediaService from './app/services/MediaService'
import GoogleApiService from './app/services/GoogleApiService'
import DropboxService from './app/services/DropboxService'
import LocalStorageService from './app/services/LocalStorageService'
import ShareService from './app/services/ShareService'

// Import all js depenency
import 'assets/js/slider/ion-rangeSlider.js';
import 'assets/js/slider/script-slider.js';
import 'assets/js/bootstrap.min.js';
import 'assets/js/circle-progress.min.js';
import 'assets/css/jquery.mCustomScrollbar.min.css';
import 'assets/js/whammy.js';
require("../node_modules/angular-slimscroll/angular-slimScroll.min.js")
require("../node_modules/ng-scrollbars/dist/scrollbars.min.js");
require("../node_modules/angular-filter/dist/angular-filter.min.js")
require("../node_modules/ng-sortable/dist/ng-sortable.min.js")

// angular module
export default angular.module('app', [
  uirouter,
  ngBootstrap,
  ngMessages,
  uiNotification,
  jwt,
  ngFileUpload,
  "ngScrollbars",
  'angular.filter',
  'ui.sortable',
  'as.sortable',
  'ngDragDrop',
  'angular-sortable-view',
  'ngSlimScroll',
  'ui.router.state.events',
  'infinite-scroll',
  ngSanitize,
  uiSelect,
  'color.picker'
])

  .config(routing)
  .run(['$http', '$rootScope', 'jwtHelper', '$window', '$state', 'EventService', '$uibModalStack',
    function ($http, $rootScope, jwtHelper, $window, $state, EventService, $uibModalStack) {


      $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
        if (fromState.name == "master.curate") {
          if (toState.name == '' || toState.name == "master.selectMedia") {

          } else {
            $window.localStorage.dropbox = '';
            $window.localStorage.facebook = '';
            $window.localStorage.gdrive = '';
            $window.localStorage.gplus = '';
            $window.localStorage.instagram = '';
            $window.localStorage.temp = '';
            $window.localStorage.newStory = '';
          }
        }
        $uibModalStack.dismissAll();
      });

      $rootScope.apiUrl = '/';

      var token = $window.localStorage.ud;
      if (token != undefined && token != 'null') {
        $http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        $http.defaults.headers.common.Authorization = 'Bearer ' + token;
      }


      window.fbAsyncInit = function () {
        FB.init({
          appId: '909742709184337',
          autoLogAppEvents: true,
          xfbml: true,
          cookie: true, 
          version: 'v2.9'
        });
        FB.AppEvents.logPageView();
      };

      (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) { return; }
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));

      var Dropbox = require('dropbox');
      $rootScope.dbx = new Dropbox({ clientId: 'or8norwuzgjv957', accessToken: 'bd4Znbn13YAAAAAAAAAAEPbgarkJrH0IsJuHPsoQrgwlzSmbQWrjo1Rzo9JlxhAj' });
    }])
  //assign
  .controller('AuthCtrl', AuthCtrl)
  .controller('CallbackCtrl', CallbackCtrl)
  .controller('CurateCtrl', CurateCtrl)
  .controller('EventCtrl', EventCtrl)
  .controller('MasterCtrl', MasterCtrl)
  .controller('ProfileCtrl', ProfileCtrl)
  .controller('PunditActivationCtrl', PunditActivationCtrl)
  .controller('PunditCtrl', PunditCtrl)
  .controller('SelectMediaCtrl', SelectMediaCtrl)
  .controller('SignupCtrl', SignupCtrl)
  .controller('SharingCtrl', SharingCtrl)
  .controller('StoryCtrl', StoryCtrl)
  .controller('StoryEditCtrl', StoryEditCtrl)
  // services
  .service('AppService', AppService)
  .service('AuthService', AuthService)
  .service('DropboxService', DropboxService)
  .service('EventService', EventService)
  .service('FbService', FbService)
  .service('GoogleApiService', GoogleApiService)
  .service('InstaService', InstaService)
  .service('LocalStorageService', LocalStorageService)
  .service('MediaService', MediaService)
  .service('OAuthService', OAuthService)
  .service('PunditService', PunditService)
  .service('ShareService', ShareService)
  .service('StoryService', StoryService)
  .directive('uidraggable', function ($rootScope) {
    return {
      // A = attribute, E = Element, C = Class and M = HTML Comment
      restrict: 'A',
      //The link function is responsible for registering DOM listeners as well as updating the DOM.
      link: function (scope, element, attrs) {
        element.draggable({
          containment: "#dragContainer",
          drag: function (event, ui) {
            let left = ui.position.left;
            let top = ui.position.top;
            let index = scope.media.map((el) => el.media_url).indexOf(scope.selectedMedia.media_url);
            if ($rootScope.media[index].text == undefined) {
              $rootScope.media[index].text = {
                left: left,
                top: top
              }
            } else {
              $rootScope.media[index].text.left = left;
              $rootScope.media[index].text.top = top;
            }
          }
        });
      }
    };
  })
  .filter('searchFilter', function ($filter) {

    return function (items, searchfilter) {
      if (searchfilter.length && items != null) {
        let list = [];
        items.forEach(function (el) {
          if (searchfilter.indexOf(el['id']) !== -1) {
            list.push(el);
          }
        })

        return list;
      }
      else {
        return [];
      }
    }
  })
  .filter('searchFilter2', function ($filter) {
    return function (items, searchfilter) {
      let list = [];

      items.forEach(function (el) {
        if (el.type == 'facebook' && searchfilter['Facebook'] == true) {
          list.push(el);
        } else if (el.type == 'gplus' && searchfilter['Google+'] == true) {
          list.push(el);
        } else if (el.type == 'instagram' && searchfilter['Instagram'] == true) {
          list.push(el);
        } else if (el.type == 'twitter' && searchfilter['Twitter'] == true) {
          list.push(el);
        } else if ((el.type == 'gdrive' || el.type == 'dropbox') && searchfilter['Cloud Storage'] == true) {
          list.push(el);
        } else if (el.type == 'uploaded' && searchfilter['Uploaded Image'] == true && el.media_type == 'photo') {
          list.push(el);
        } else if (el.type == 'uploaded' && searchfilter['Uploaded Video'] == true && el.media_type == 'video') {
          list.push(el);
        }
      })

      return list;
    }
  })
  .filter('timeFilter', function ($filter) {
    return function (items, timeValue) {

      let list = [];
      items.forEach(function (el) {
        if (timeValue) {
          if (el.created_at) {
            if (el.type == "instagram") {
              var dt = parseInt(el.created_at) * 1000;
              var d = new Date(dt);
            } else {
              var msec = Date.parse(el.created_at);
              var d = new Date(msec);
            }
            var date = new Date();

            var checkDate;
            if (timeValue == 'Last 30 mins') {
              checkDate = new Date(date.getTime() - 30 * 60000);
            } else if (timeValue == 'Last hour') {
              checkDate = new Date(date.getTime() - 60 * 60000);
            } else if (timeValue == 'Last 5 hours') {
              checkDate = new Date(date.getTime() - 5 * 60 * 60000);
            } else if (timeValue == 'Last 24 hours') {
              checkDate = new Date(date.getTime() - 24 * 60 * 60000);
            } else if (timeValue == 'This week') {
              var day = date.getDay();
              checkDate = new Date(date.getTime() - day * 24 * 60 * 60000);
            } else {


              timeValue = timeValue.replace(/['"]+/g, '');
              var checkDate = new Date(timeValue);

            }

            if (checkDate.getDate() + '-' + checkDate.getMonth() + '-' + checkDate.getFullYear() == d.getDate() + '-' + d.getMonth() + '-' + d.getFullYear()) {
              list.push(el);
            }
          }
        } else {
          list = items;
        }
      })
      return list;
    }
  })
  .filter('locationFilter', function ($filter) {
    return function (items, location) {
      let list = [];

      if (location != '' && location != undefined) {

        items.forEach(function (el) {
          if (el.type == 'gplus' || el.type == 'twitter' || el.type == 'facebook' || el.type == 'instagram') {
            if (el.location && el.location.toLowerCase().indexOf(location.toLowerCase()) != -1) {
              list.push(el)
            }
          } else {
            // list.push(el);
          }
        })
      } else {
        list = items
      }
      return list;
    }
  })
  .directive("whenScrolled", function () {
    return {

      restrict: 'A',
      link: function (scope, elem, attrs) {

        // we get a list of elements of size 1 and need the first element
        var raw = elem[0];

        // we load more elements when scrolled past a limit
        elem.bind("scroll", function () {
          if (raw.scrollTop + raw.offsetHeight + 5 >= raw.scrollHeight) {
            scope.loading = true;
            // we can give any function which loads more elements into the list
            scope.$apply(attrs.whenScrolled);
          }
        });
      }
    }
  })
  .directive('clientAutoComplete', function ($filter) {
    return {
      restrict: 'A',
      link: function (scope, elem, attrs) {
        elem.autocomplete({
          source: function (request, response) {

            //term has the data typed by the user
            var params = request.term;

            var data = scope.dataSource;
            if (data) {

              var result1 = $filter('filter')(data, { event_title: params });
              var result2 = $filter('filter')(data, { event_details: params });
              var result = result1.concat(result2.filter(function (item) {
                return result1.indexOf(item) < 0;
              }));


              angular.forEach(result, function (item) {

                item['value'] = item['event_title'];
              });

            }

            if (result.length == 0) {
              result = [{ value: "We couldn't find any matching Events or Stories", id: undefined }]
            }
            response(result);

          },
          minLength: 1,
          select: function (event, ui) {
            //force a digest cycle to update the views
            scope.$apply(function () {
              scope.setClientData(ui.item);
            });
          },

        });
      }

    };

  })
  .directive('storyAutoComplete', function ($filter, StoryService) {
    return {
      restrict: 'A',
      link: function (scope, elem, attrs) {
        elem.autocomplete({
          source: function (request, response) {
            //term has the data typed by the user
            var params = request.term;

            if (params != '') {
              StoryService.searchStory(params, scope.event.id)
                .then((res) => {
                  var result;
                  if (res.data.data && res.status == 200) {
                    result = res.data.data.stories;
                    angular.forEach(result, function (item) {

                      item['value'] = item['story_title'];
                    });
                  } else {
                    result = [{ value: res.data.message || "We couldn't find any matching Stories", id: undefined }]
                  }
                  response(result);
                })
            }
          },
          minLength: 1,
          select: function (event, ui) {
            //force a digest cycle to update the views
            scope.$apply(function () {
              scope.setClientData(ui.item);
            });
          }
        });
      }
    }
  })
  .directive('onErrorSrc', function () {
    return {
      link: function (scope, element, attrs) {
        element.bind('error', function () {
          if (attrs.src != attrs.onErrorSrc) {
            attrs.$set('src', attrs.onErrorSrc);
          }
        });
      }
    }
  })
  .directive('onErrorSrcHide', function () {
    return {
      link: function (scope, element, attrs) {
        function isHidden (array) {
          for (var i = 0; i < array.length - 1; i++) {

            if (array[i + 1].style.display != "none") {
              return false;
            };
          };
          return true;
        };
        element.bind('error', function () {
          this.parentNode.parentNode.style.display = 'none';
          var children = this.parentNode.parentNode.parentNode.getElementsByTagName("li");
          if (isHidden(children)) {
            this.parentNode.parentNode.parentNode.innerHTML = "No image found";
          }
        })
      }
    }
  })
  .name
