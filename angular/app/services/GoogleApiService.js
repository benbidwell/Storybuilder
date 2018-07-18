GoogleApiService.$inject = ['$http', '$rootScope', '$window', 'Upload', 'Notification', '$q'];

export default function GoogleApiService($http, $rootScope, $window, Upload, notification, $q) {
  // Login with google drive
  var developerKey = 'AIzaSyBq3owHHRZ3Hv1roRByqj1wrsLVaQ6xd8Y';
  var clientId = '1012815045794-009s10adj6q7hqvvotr5lritek4vt2ha.apps.googleusercontent.com';
  var appId = '1012815045794';
  var scope = [
    'https://www.googleapis.com/auth/plus.me',
    'https://www.googleapis.com/auth/drive',
    'https://www.googleapis.com/auth/plus.media.upload',
    'https://www.googleapis.com/auth/plus.stream.read',
    'https://www.googleapis.com/auth/plus.stream.write',
  ];
  var scope_gPlus = [
    'https://www.googleapis.com/auth/plus.me',
    'https://www.googleapis.com/auth/plus.stream.read',
    'https://www.googleapis.com/auth/plus.media.upload',
    'https://www.googleapis.com/auth/plus.stream.write',
    'https://www.googleapis.com/auth/youtube.force-ssl',
    'https://www.googleapis.com/auth/youtubepartner',
  ];
  var pickerApiLoaded = false;
  var oauthToken;
  this.newAccount;

  this.gdriveLogin = username => {
    this.newAccount = username;
    gapi.load('auth', { callback: this.onAuthApiLoad });
    gapi.load('picker', { callback: this.onPickerApiLoad });
  };

  this.gPlusLogin = username => {
    if ($window.localStorage.gUser) {
      // alert('User is already authorised.');
      gapi.load('auth', { callback: this.onAuthGPlusApiLoad });
    } else {
      $rootScope.newAccount = username;
      gapi.load('auth', { callback: this.onAuthGPlusApiLoad });
    }
  };

  this.gPlusKeyword = data => {
    return $http.post($rootScope.apiUrl + 'api/googleKeywordSerach', data).then(
      function successCallback(response) {
        return response;
      },
      function errorCallback(response) {
        return response;
      },
    );
  };

  this.onAuthApiLoad = () => {
    window.gapi.auth.authorize(
      {
        client_id: clientId,
        scope: scope,
        prompt: 'consent',
        display: 'popup',
        login_hint: this.newAccount,
      },
      this.handleAuthResult,
    );
  };

  this.onAuthGPlusApiLoad = () => {
    window.gapi.auth.authorize(
      {
        client_id: clientId,
        scope: scope_gPlus,
        prompt: 'consent',
        display: 'popup',
        login_hint: this.newAccount,
      },
      this.handleGPlusAuthResult,
    );
  };

  this.onPickerApiLoad = () => {
    pickerApiLoaded = true;
    //this.createPicker();
  };

  this.handleAuthResult = authResult => {
    // this.newAccount
    var that = this;
    if (authResult && !authResult.error) {
      oauthToken = authResult.access_token;
      console.log(oauthToken);
      $rootScope.google_aacess_t = oauthToken;
      $rootScope.$digest();
      console.log($rootScope.google_aacess_t);
      gapi.client.load('plus', 'v1').then(function() {
        gapi.client.plus.people
          .get({
            userId: 'me',
          })
          .execute(function(resp) {
            that.newAccount = resp.displayName;
            that.createPicker();
            // that.gdriveUserDetails();
          });
      });
    }
  };

  this.gdriveUserDetails = () => {
    var that = this;
    gapi.client.load('drive', 'v3').then(function() {
      gapi.client.drive.files
        .list({
          orderBy: 'createdTime',
          pageSize: 10,
          q: 'mimeType="image/jpeg"',
          fields: 'files',
          corpus: 'user',
        })
        .execute(that.gdriveCallback);
    });
  };
  this.gdriveCallback = resp => {
    var Gdrive_data = [];
    var that = this;

    angular.forEach(resp.files, function(value, key) {
      if (Gdrive_data.length < 101) {
        if (value.webContentLink != '') {
          if (value.mimeType == 'image/jpeg' || value.mimeType == 'image/jpg' || value.mimeType == 'image/png') {
            Gdrive_data.push({
              type: 'gdrive',
              media_type: 'photo',
              media_url: value.webContentLink,
              id: 'GDrive' + that.newAccount,
              created_at: value.createdTime,
            });
          } else if (value.mimeType == 'video/mp4') {
            Gdrive_data.push({
              type: 'gdrive',
              media_type: 'video',
              media_url: value.webContentLink,
              id: 'GDrive' + that.newAccount,
              created_at: value.createdTime,
            });
          }
        }
      }
    });

    var gdrive = [];
    if ($window.localStorage.gdrive == undefined || $window.localStorage.gdrive == '') {
      gdrive.push({ user_id: 'GDrive' + this.newAccount, screen_name: this.newAccount });
    } else {
      gdrive = JSON.parse($window.localStorage.gdrive);
      gdrive.push({ user_id: 'GDrive' + this.newAccount, screen_name: this.newAccount });
    }

    $window.localStorage.gdrive = JSON.stringify(gdrive);
    $rootScope.gdrive = gdrive;
    $window.localStorage.temp = JSON.stringify(Gdrive_data);

    $rootScope.$digest();
    $window.location.reload();
  };

  this.handleGPlusAuthResult = authResult => {
    if (authResult && !authResult.error) {
      oauthToken = authResult.access_token;
      var request = '';
      var that = this;
      gapi.client.load('plus', 'v1').then(function() {
        gapi.client.plus.people
          .get({
            userId: 'me',
          })
          .execute(function(resp) {
            $window.localStorage.gUser = JSON.stringify(resp);
            $window.localStorage.gToken = authResult.access_token;
            //this.newAccount=resp;
            that.gplushUserDetails();
          });
      });
    }
  };

  this.gplushUserDetails = () => {
    gapi.client.plus.activities
      .list({
        userId: 'me',
        collection: 'public',
        maxResults: 100,
      })
      .execute(this.gplusCallback);
  };

  this.gplusKeywordCallback = resp => {
    var GPlusData = [];
    var GPlus_data = [];

    angular.forEach(resp.items, function(value, key) {
      if (GPlusData.length < 101) {
        if (value.object.attachments != '') {
          var location;
          if (value.location != undefined) location = value.location.address.formatted;
          var updated = value.updated;
          angular.forEach(value.object.attachments, function(data, key) {
            if (data.objectType == 'photo') {
              GPlus_data.push({
                type: 'gplus',
                media_type: data.objectType,
                media_url: data.fullImage.url,
                id: 'GPlus' + $rootScope.newAccount,
                created_at: updated,
                location: location,
              });
            } else if (data.objectType == 'article' && data.fullImage != undefined) {
              GPlus_data.push({
                type: 'gplus',
                media_type: data.objectType,
                media_url: data.fullImage.url,
                id: 'GPlus' + $rootScope.newAccount,
                created_at: updated,
                location: location,
              });
            }
          });
        }
      }
    });

    var gplus = [];
    var channel_type;
    if ($rootScope.newAccount.indexOf('#') != -1) {
      channel_type = 'Hashtag';
    } else {
      channel_type = 'Keyword';
    }
    if ($window.localStorage.gplus == undefined || $window.localStorage.gplus == '') {
      gplus.push({
        user_id: 'GPlus' + $rootScope.newAccount,
        channel_type: channel_type,
        screen_name: $rootScope.newAccount,
      });
    } else {
      gplus = JSON.parse($window.localStorage.gplus);
      gplus.push({
        user_id: 'GPlus' + $rootScope.newAccount,
        channel_type: channel_type,
        screen_name: $rootScope.newAccount,
      });
    }
    $window.localStorage.gplus = JSON.stringify(gplus);
    $rootScope.gplus = gplus;
    $window.localStorage.temp = JSON.stringify(GPlus_data);
    $rootScope.$digest();
    $window.location.reload();
  };

  this.gplusCallback = resp => {
    debugger;
    var GPlusData = [];
    var GPlus_data = [];

    angular.forEach(resp.items, function(value, key) {
      if (GPlusData.length < 101) {
        if (value.object.attachments != '') {
          var location;
          if (value.location != undefined) location = value.location.address.formatted;

          var updated = value.updated;
          $rootScope.newAccount = resp.items[0].actor.displayName;
          angular.forEach(value.object.attachments, function(data, key) {
            if (data.objectType == 'video') {
              var request = gapi.client.plus.activities.get({ activityId: value.id });

              request.execute(function(resp) {});
            }
            if (data.objectType == 'photo') {
              GPlus_data.push({
                type: 'gplus',
                media_type: data.objectType,
                media_url: data.fullImage.url,
                id: 'GPlus' + $rootScope.newAccount,
                created_at: updated,
                location: location,
              });
            } else if (data.objectType == 'article' && data.fullImage != undefined) {
              GPlus_data.push({
                type: 'gplus',
                media_type: data.objectType,
                media_url: data.fullImage.url,
                id: 'GPlus' + $rootScope.newAccount,
                created_at: updated,
                location: location,
              });
            } else if (data.objectType == 'video') {
              GPlus_data.push({
                type: 'gplus',
                media_type: data.objectType,
                media_url: '',
                indirect_link: data.url,
                id: 'GPlus' + $rootScope.newAccount,
                created_at: updated,
                location: location,
              });
            }
          });
        }
      }
    });

    var video_data = GPlus_data.filter(el => {
      if (el.media_type == 'video') return el;
    });

    if (video_data.length) {
      var that = this;
      var temmp = GPlus_data;

      $http.post('api/story_media_google_plus', { media: video_data }).then(
        function successCallback(response) {
          for (var i = 0; i < temmp.length; i++) {
            response.data.data.map(el => {
              if (el.id == temmp[i].indirect_link) {
                temmp[i].media_url = el.link;
              }
            });
          }
          that.outputResponse(temmp);
        },
        function errorCallback(response) {
          that.outputResponse(GPlus_data);
        },
      );
    } else {
      this.outputResponse(GPlus_data);
    }

    $window.location.reload();
  };

  this.outputResponse = GPlus_data => {
    $window.localStorage.temp = JSON.stringify(GPlus_data);

    var gplus = [];
    if ($window.localStorage.gplus == undefined || $window.localStorage.gplus == '') {
      gplus.push({
        user_id: 'GPlus' + $rootScope.newAccount,
        channel_type: 'Account',
        screen_name: $rootScope.newAccount,
      });
    } else {
      gplus = JSON.parse($window.localStorage.gplus);
      gplus.push({
        user_id: 'GPlus' + $rootScope.newAccount,
        channel_type: 'Account',
        screen_name: $rootScope.newAccount,
      });
    }
    $window.localStorage.gplus = JSON.stringify(gplus);
    $rootScope.gplus = gplus;
    $rootScope.newStory = $rootScope.newStory;
    $window.location.reload();
  };

  // Create and render a Picker object for searching images.
  this.createPicker = () => {
    if (pickerApiLoaded && oauthToken) {
      var view = new google.picker.View(google.picker.ViewId.DOCS);
      view.setMimeTypes('image/png,image/jpeg,image/jpg,video/mp4');
      var picker = new google.picker.PickerBuilder()
        .enableFeature(google.picker.Feature.NAV_HIDDEN)
        .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
        .setAppId(appId)
        .setOAuthToken(oauthToken)
        .addView(view)
        .addView(new google.picker.DocsUploadView())
        .setDeveloperKey(developerKey)
        .setCallback(this.pickerCallback)
        .build();
      picker.setVisible(true);
    }
  };

  // A simple callback implementation.
  this.pickerCallback = data => {
    if (data.action == google.picker.Action.PICKED) {
      var newAccount = this.newAccount;
      var supertemp = [];

      gapi.client.load('drive', 'v2', function() {
        data.docs.forEach(item => {
          var request = gapi.client.drive.files.get({
            fileId: item.id,
          });
          request.execute(function(resp) {
            $rootScope.newStory = JSON.parse($window.localStorage.newStory);
            var data = {
              url: resp.webContentLink,
              story_id: $rootScope.newStory.id,
              access_token: $rootScope.google_aacess_t,
            };
            $http.post($rootScope.apiUrl + 'api/story_gmedia_upload', data).then(
              function successCallback(response) {
                supertemp.push({
                  type: 'gdrive',
                  media_type: item.type,
                  media_url: response.data,
                  drive_id: item.id,
                  id: 'GDrive' + newAccount,
                  access_token: $rootScope.google_aacess_t,
                  created_at: resp.createdDate,
                });
                if (supertemp.length > 0) {
                  $rootScope.gdrive_temp = supertemp;
                  $rootScope.newStory = JSON.parse($window.localStorage.newStory);
                  if ($rootScope.newStory.media == undefined) {
                    $rootScope.newStory.media = [];
                  }
                  $rootScope.newStory.media = $rootScope.newStory.media.concat(supertemp);
                  $window.localStorage.newStory = JSON.stringify($rootScope.newStory);
                  $window.location.reload();
                }
              },
              function errorCallback(response) {
                return response;
              },
            );
          });
        });
      });

      if ($window.localStorage.gdrive == undefined || $window.localStorage.gdrive == '') {
        var gdrive = [];
        gdrive.push({ user_id: 'GDrive' + this.newAccount, screen_name: this.newAccount });
      } else {
        var gdrive = JSON.parse($window.localStorage.gdrive);
        gdrive.push({ user_id: 'GDrive' + this.newAccount, screen_name: this.newAccount });
      }
      $window.localStorage.gdrive = JSON.stringify(gdrive);
      $rootScope.gdrive = gdrive;
    }
  };

  this.gpluspostmedia = data => {
    if (!$window.localStorage.gUser || !$window.localStorage.gToken) {
      this.gPlusLogin();
    } else {
      var UserResp = JSON.parse($window.localStorage.gUser);
      var access_token = $window.localStorage.gToken;
      var data = data.media;
      var headers = {
        Authorization: 'OAuth ' + access_token,
        'Content-Type': 'video/mp4',
      };
      $http
        .post('https://www.googleapis.com/upload/plusDomains/v1/people/' + UserResp.id + '/media/cloud', data, {
          headers: headers,
        })
        .then(
          function successCallback(response) {
            // var userID = response.data.id;
            // return $http.post($rootScope.apiUrl+'api/post_curl_response_fb',{
            //     url:"https://graph-video.facebook.com/v2.3/"+userID+"/videos",
            //     post_data:"upload_phase=start&file_size="+data.size+"&access_token="+UserResp.token,
            //     command:"init"
            // })
            // .then(function successCallback(response){
            //     return response;
            // }, function errorCallback(response){
            //     return response;
            // });
          },
          function errorCallback(response) {
            return response;
          },
        );
    }
  };

  this.youtubepostmedia = formdata => {
    if (!$window.localStorage.gUser || !$window.localStorage.gToken) {
      var googleLogin = username => {
        gapi.load('auth', { callback: onAuthGoogleApiLoad });
      };
      var onAuthGoogleApiLoad = () => {
        window.gapi.auth.authorize(
          {
            client_id: clientId,
            scope: scope_gPlus,
            prompt: 'consent',
            display: 'popup',
          },
          handleGoogleAuthResult,
        );
      };
      var handleGoogleAuthResult = authResult => {
        var that = this;
        if (authResult && !authResult.error) {
          // oauthToken = authResult.access_token;
          $window.localStorage.gToken = authResult.access_token;
          do_upload();
        }
      };
      googleLogin();
    } else {
      do_upload();
    }
    function do_upload() {
      function createResource(properties) {
        var resource = {};
        var normalizedProps = properties;
        for (var p in properties) {
          var value = properties[p];
          if (p && p.substr(-2, 2) == '[]') {
            var adjustedName = p.replace('[]', '');
            if (value) {
              normalizedProps[adjustedName] = value.split(',');
            }
            delete normalizedProps[p];
          }
        }
        for (var p in normalizedProps) {
          // Leave properties that don't have values out of inserted resource.
          if (normalizedProps.hasOwnProperty(p) && normalizedProps[p]) {
            var propArray = p.split('.');
            var ref = resource;
            for (var pa = 0; pa < propArray.length; pa++) {
              var key = propArray[pa];
              if (pa == propArray.length - 1) {
                ref[key] = normalizedProps[p];
              } else {
                ref = ref[key] = ref[key] || {};
              }
            }
          }
        }
        return resource;
      }
      var metadata = createResource({
        'snippet.categoryId': '22',
        'snippet.defaultLanguage': '',
        'snippet.description': formdata.data.story_des + ' ' + formdata.data.pageurl,
        'snippet.tags[]': '',
        'snippet.title': formdata.data.title,
        'status.embeddable': '',
        'status.license': '',
        'status.privacyStatus': 'private',
        'status.publicStatsViewable': '',
      });
      var token = $window.localStorage.gToken;
      if (!token) {
        alert('You need to authorize the request to proceed.');
        return;
      }

      if (!formdata.media) {
        alert('You need to select a file to proceed.');
        return;
      }
      var params = { part: 'snippet,status' };
      function removeEmptyParams(params) {
        for (var p in params) {
          if (!params[p] || params[p] == 'undefined') {
            delete params[p];
          }
        }
        return params;
      }

      function executeRequest(request) {
        request.execute(function(response) {
          console.log(response);
        });
      }

      function buildApiRequest(requestMethod, path, params, properties) {
        params = removeEmptyParams(params);
        var request;
        if (properties) {
          var resource = createResource(properties);
          request = gapi.client.request({
            body: resource,
            method: requestMethod,
            path: path,
            params: params,
          });
        } else {
          request = gapi.client.request({
            method: requestMethod,
            path: path,
            params: params,
          });
        }
        executeRequest(request);
      }

      /**
       * Retrieve the access token for the currently authorized user.
       */
      function getAccessToken(event) {
        return GoogleAuth.currentUser.get().getAuthResponse(true).access_token;
      }

      /**
       * Helper for implementing retries with backoff. Initial retry
       * delay is 1 second, increasing by 2x (+jitter) for subsequent retries
       *
       * @constructor
       */
      var RetryHandler = function() {
        this.interval = 1000; // Start at one second
        this.maxInterval = 60 * 1000; // Don't wait longer than a minute
      };

      /**
       * Invoke the function after waiting
       *
       * @param {function} fn Function to invoke
       */
      RetryHandler.prototype.retry = function(fn) {
        setTimeout(fn, this.interval);
        this.interval = this.nextInterval_();
      };

      /**
       * Reset the counter (e.g. after successful request.)
       */
      RetryHandler.prototype.reset = function() {
        this.interval = 1000;
      };

      /**
       * Calculate the next wait time.
       * @return {number} Next wait interval, in milliseconds
       *
       * @private
       */
      RetryHandler.prototype.nextInterval_ = function() {
        var interval = this.interval * 2 + this.getRandomInt_(0, 1000);
        return Math.min(interval, this.maxInterval);
      };

      /**
       * Get a random int in the range of min to max. Used to add jitter to wait times.
       *
       * @param {number} min Lower bounds
       * @param {number} max Upper bounds
       * @private
       */
      RetryHandler.prototype.getRandomInt_ = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
      };
      var MediaUploader = function(options) {
        var noop = function() {};
        this.file = options.file;
        this.contentType = options.contentType || this.file.type || 'application/octet-stream';
        this.metadata = options.metadata || {
          title: this.file.name,
          mimeType: this.contentType,
        };
        this.token = options.token;
        this.onComplete = options.onComplete || noop;
        this.onProgress = options.onProgress || noop;
        this.onError = options.onError || noop;
        this.offset = options.offset || 0;
        this.chunkSize = options.chunkSize || 0;
        this.retryHandler = new RetryHandler();

        this.url = options.url;
        if (!this.url) {
          var params = options.params || {};
          params.uploadType = 'resumable';
          this.url = this.buildUrl_(options.fileId, params, options.baseUrl);
        }
        this.httpMethod = options.fileId ? 'PUT' : 'POST';
      };

      /**
       * Initiate the upload.
       */
      MediaUploader.prototype.upload = function() {
        var self = this;
        var xhr = new XMLHttpRequest();

        xhr.open(this.httpMethod, this.url, true);
        xhr.setRequestHeader('Authorization', 'Bearer ' + this.token);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-Upload-Content-Length', this.file.size);
        xhr.setRequestHeader('X-Upload-Content-Type', this.contentType);

        xhr.onload = function(e) {
          if (e.target.status < 400) {
            var location = e.target.getResponseHeader('Location');
            this.url = location;
            this.sendFile_();
          } else {
            this.onUploadError_(e);
          }
        }.bind(this);
        xhr.onerror = this.onUploadError_.bind(this);
        xhr.send(JSON.stringify(this.metadata));
      };

      /**
       * Send the actual file content.
       *
       * @private
       */
      MediaUploader.prototype.sendFile_ = function() {
        var content = this.file;
        var end = this.file.size;

        if (this.offset || this.chunkSize) {
          // Only slice the file if we're either resuming or uploading in chunks
          if (this.chunkSize) {
            end = Math.min(this.offset + this.chunkSize, this.file.size);
          }
          content = content.slice(this.offset, end);
        }

        var xhr = new XMLHttpRequest();
        xhr.open('PUT', this.url, true);
        xhr.setRequestHeader('Content-Type', this.contentType);
        xhr.setRequestHeader('Content-Range', 'bytes ' + this.offset + '-' + (end - 1) + '/' + this.file.size);
        xhr.setRequestHeader('X-Upload-Content-Type', this.file.type);
        if (xhr.upload) {
          xhr.upload.addEventListener('progress', this.onProgress);
        }
        xhr.onload = this.onContentUploadSuccess_.bind(this);
        xhr.onerror = this.onContentUploadError_.bind(this);
        xhr.send(content);
      };

      /**
       * Query for the state of the file for resumption.
       *
       * @private
       */
      MediaUploader.prototype.resume_ = function() {
        var xhr = new XMLHttpRequest();
        xhr.open('PUT', this.url, true);
        xhr.setRequestHeader('Content-Range', 'bytes */' + this.file.size);
        xhr.setRequestHeader('X-Upload-Content-Type', this.file.type);
        if (xhr.upload) {
          xhr.upload.addEventListener('progress', this.onProgress);
        }
        xhr.onload = this.onContentUploadSuccess_.bind(this);
        xhr.onerror = this.onContentUploadError_.bind(this);
        xhr.send();
      };

      /**
       * Extract the last saved range if available in the request.
       *
       * @param {XMLHttpRequest} xhr Request object
       */
      MediaUploader.prototype.extractRange_ = function(xhr) {
        var range = xhr.getResponseHeader('Range');
        if (range) {
          this.offset = parseInt(range.match(/\d+/g).pop(), 10) + 1;
        }
      };

      /**
       * Handle successful responses for uploads. Depending on the context,
       * may continue with uploading the next chunk of the file or, if complete,
       * invokes the caller's callback.
       *
       * @private
       * @param {object} e XHR event
       */
      MediaUploader.prototype.onContentUploadSuccess_ = function(e) {
        if (e.target.status == 200 || e.target.status == 201) {
          this.onComplete(e.target.response);
        } else if (e.target.status == 308) {
          this.extractRange_(e.target);
          this.retryHandler.reset();
          this.sendFile_();
        }
      };

      /**
       * Handles errors for uploads. Either retries or aborts depending
       * on the error.
       *
       * @private
       * @param {object} e XHR event
       */
      MediaUploader.prototype.onContentUploadError_ = function(e) {
        if (e.target.status && e.target.status < 500) {
          this.onError(e.target.response);
        } else {
          this.retryHandler.retry(this.resume_.bind(this));
        }
      };

      /**
       * Handles errors for the initial request.
       *
       * @private
       * @param {object} e XHR event
       */
      MediaUploader.prototype.onUploadError_ = function(e) {
        this.onError(e.target.response); // TODO - Retries for initial upload
      };

      /**
       * Construct a query string from a hash/object
       *
       * @private
       * @param {object} [params] Key/value pairs for query string
       * @return {string} query string
       */
      MediaUploader.prototype.buildQuery_ = function(params) {
        params = params || {};
        return Object.keys(params)
          .map(function(key) {
            return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
          })
          .join('&');
      };

      /**
       * Build the upload URL
       *
       * @private
       * @param {string} [id] File ID if replacing
       * @param {object} [params] Query parameters
       * @return {string} URL
       */
      MediaUploader.prototype.buildUrl_ = function(id, params, baseUrl) {
        var url = baseUrl;
        if (id) {
          url += id;
        }
        var query = this.buildQuery_(params);
        if (query) {
          url += '?' + query;
        }
        return url;
      };

      var uploader = new MediaUploader({
        baseUrl: 'https://www.googleapis.com/upload/youtube/v3/videos',
        file: formdata.media,
        token: token,
        metadata: metadata,
        params: params,
        onError: function(data) {
          var message = data;
          try {
            var errorResponse = JSON.parse(data);
            message = errorResponse.error.message;
          } finally {
            alert(message);
          }
        }.bind(this),
        onProgress: function(data) {
          var currentTime = Date.now();
          console.log('Progress: ' + data.loaded + ' bytes loaded out of ' + data.total);
          var totalBytes = data.total;
        }.bind(this),
        onComplete: function(data) {
          var uploadResponse = JSON.parse(data);

          if (formdata.data.id) {
            $http
              .post($rootScope.apiUrl + 'api/stories2/' + formdata.data.id, {
                id: formdata.data.id,
                youtube: uploadResponse.id,
              })
              .then(
                function successCallback(response) {
                  notification.success({
                    message: 'video posted succesfully',
                    positionY: 'bottom',
                    positionX: 'right',
                    delay: 3000,
                  });
                },
                function errorCallback(response) {
                  return response;
                },
              );
          } else {
            notification.success({
              message: 'Video posted succesfully',
              positionY: 'bottom',
              positionX: 'right',
              delay: 3000,
            });
          }
        }.bind(this),
      });

      uploader.upload();
    }
  };

  this.youtubeStats = data => {
    var deferred = $q.defer();
    if (!$window.localStorage.gUser || !$window.localStorage.gToken) {
      var googleLogin = username => {
        gapi.load('auth', { callback: onAuthGoogleApiLoad });
      };
      var onAuthGoogleApiLoad = () => {
        window.gapi.auth.authorize(
          {
            client_id: clientId,
            scope: scope_gPlus,
            prompt: 'consent',
            display: 'popup',
          },
          handleGoogleAuthResult,
        );
      };
      var handleGoogleAuthResult = authResult => {
        var that = this;
        if (authResult && !authResult.error) {
          // oauthToken = authResult.access_token;
          $window.localStorage.gToken = authResult.access_token;
          defineRequest();
        }
      };
      googleLogin();
    } else {
      defineRequest();
    }
    function createResource(properties) {
      var resource = {};
      var normalizedProps = properties;
      for (var p in properties) {
        var value = properties[p];
        if (p && p.substr(-2, 2) == '[]') {
          var adjustedName = p.replace('[]', '');
          if (value) {
            normalizedProps[adjustedName] = value.split(',');
          }
          delete normalizedProps[p];
        }
      }
      for (var p in normalizedProps) {
        // Leave properties that don't have values out of inserted resource.
        if (normalizedProps.hasOwnProperty(p) && normalizedProps[p]) {
          var propArray = p.split('.');
          var ref = resource;
          for (var pa = 0; pa < propArray.length; pa++) {
            var key = propArray[pa];
            if (pa == propArray.length - 1) {
              ref[key] = normalizedProps[p];
            } else {
              ref = ref[key] = ref[key] || {};
            }
          }
        }
      }
      return resource;
    }

    function removeEmptyParams(params) {
      for (var p in params) {
        if (!params[p] || params[p] == 'undefined') {
          delete params[p];
        }
      }
      return params;
    }

    function buildApiRequest(requestMethod, path, params, properties) {
      params = removeEmptyParams(params);
      var request;
      if (properties) {
        var resource = createResource(properties);
        request = gapi.client.request({
          body: resource,
          method: requestMethod,
          path: path,
          params: params,
        });
      } else {
        gapi.load('client', {
          callback: function() {
            request = gapi.client.request({
              method: requestMethod,
              path: path,
              params: params,
            });
            request.execute(function(response) {
              console.log(response);
              deferred.resolve(response);
            });
          },
        });
      }
    }

    /***** END BOILERPLATE CODE *****/

    function defineRequest() {
      buildApiRequest('GET', '/youtube/v3/videos', {
        id: data.ids,
        part: 'statistics',
      });
    }
    return deferred.promise;
  };
}
