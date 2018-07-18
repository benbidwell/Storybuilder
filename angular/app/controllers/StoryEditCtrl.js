//EffectsCtrl Ctrollers
StoryEditCtrl.$inject = [
  '$rootScope',
  '$scope',
  '$state',
  '$window',
  '$timeout',
  'Upload',
  'StoryService',
  'Notification',
  '$uibModal',
];
export default function StoryEditCtrl(
  $rootScope,
  $scope,
  $state,
  $window,
  $timeout,
  Upload,
  StoryService,
  notification,
  $uibModal,
) {
  // Javascript or jquery function
  {
    $timeout(function() {
      $('#range_27a').ionRangeSlider({
        type: 'double',
        min: 0,
        max: $scope.soundtrim,
        from: 0,
        to: $scope.soundtrim,
        onChange: function(data) {
          $rootScope.media.sound.start_time = data.from;
          $rootScope.media.sound.end_time = data.to;
        },
      });

      $('#range_28a').ionRangeSlider({
        min: 0,
        onChange: function(data) {
          $rootScope.media.sound.delay_sound = data.from;
        },
      });
      $scope.sliderDelay = $('#range_28a').data('ionRangeSlider');
      $('#range_29a').ionRangeSlider({
        min: 0.5,
        max: 10,
        from: 10,
        to: 10,
        step: 0.5,
        onChange: function(data) {
          $scope.selectedMedia.image_visibility = data.from;
          let index = $scope.media.map(el => el.media_url).indexOf($scope.selectedMedia.media_url);
          $rootScope.media[index].image_visibility = $scope.selectedMedia.image_visibility;
        },
      });
      $scope.slider = $('#range_27a').data('ionRangeSlider');

      $scope.sliderImageVisibility = $('#range_29a').data('ionRangeSlider');
      $('.test').mCustomScrollbar({
        axis: 'x',
        theme: 'dark',
        advanced: { autoExpandHorizontalScroll: true },
      });
      $('.testaudio').mCustomScrollbar({
        theme: 'minimal',
        axis: 'y',
        theme: 'dark',
      });
    }, 500);

    $scope.option = { filter: true, sound: false, transition: false };

    $scope.optionShow = value => {
      document.getElementById(value).classList.toggle('active');
      if (value == 'SOUNDTRACK') {
        document.getElementById('FILTERS').classList.remove('active');
        document.getElementById('TRANSITION').classList.remove('active');
        $scope.option = { filter: false, sound: !$scope.option.sound, transition: false };
      } else if (value == 'FILTERS') {
        document.getElementById('SOUNDTRACK').classList.remove('active');
        document.getElementById('TRANSITION').classList.remove('active');
        $scope.option = { filter: !$scope.option.filter, sound: false, transition: false };
      } else if (value == 'TRANSITION') {
        document.getElementById('SOUNDTRACK').classList.remove('active');
        document.getElementById('FILTERS').classList.remove('active');
        $scope.option = { filter: false, sound: false, transition: !$scope.option.transition };
      }
    };
    $scope.soundtrim = 0;
    $scope.sound_option = false;
    $scope.trim_sound = value => {
      // $scope.slider.reset();
      $rootScope.media.sound.audio_url = value.audio_url;

      $scope.sound_option = true;
      $scope.soundtrim = value.length;
      console.log($scope.sliderDelay);
      $timeout(function() {
        $scope.slider.update({
          min: 0,
          max: value.length,
          from: 0,
          to: value.length,
        });
        $scope.sliderDelay.reset();
      }, 500);
    };

    var acc = document.getElementsByClassName('accordion');
    var i;
  }

  // options - if a list is given then choose one of the items. The first item in the list will be the default
  $scope.options = {
    // color
    format: 'hex',
    alpha: true,
    round: false,
    hue: true,
    saturation: true,
    lightness: true, // Note: In the square mode this is HSV and in round mode this is HSL
    alpha: true,
    dynamicHue: true,
    dynamicSaturation: true,
    dynamicLightness: true,
    dynamicAlpha: true,
  };

  // Variable declaration & intialization
  $rootScope.newStory = JSON.parse($window.localStorage.newStory);
  $rootScope.media = JSON.parse($window.localStorage.media);
  $scope.selectedMedia = angular.copy($rootScope.media[0]);

  $scope.selectedMedia.text = {
    fontSize: 24,
    opacity: '100',
    fontFamily: 'sans-serif',
    color: '41A897',
    leading: 22,
    alignment: 'left',
    value: '',
  };

  $rootScope.media.sound = {
    start_time: 0,
    end_time: 0,
    delay_sound: 0,
    play_during_clips: true,
    play_full_track: true,
  };

  $scope.selectedMedia.filterOption = 'Normal';
  $scope.selectedMedia.transition = 'none';
  $scope.text_edit = true;

  $scope.newMedia = angular.copy($rootScope.media);

  function ApplyLineBreaks(strTextAreaId) {
    var oTextarea = document.getElementById(strTextAreaId);
    if (oTextarea.wrap) {
      oTextarea.setAttribute('wrap', 'off');
    } else {
      oTextarea.setAttribute('wrap', 'off');
      var newArea = oTextarea.cloneNode(true);
      newArea.value = oTextarea.value;
      oTextarea.parentNode.replaceChild(newArea, oTextarea);
      oTextarea = newArea;
    }

    var strRawValue = oTextarea.value;
    oTextarea.value = '';
    var nEmptyWidth = oTextarea.scrollWidth;
    var nLastWrappingIndex = -1;
    for (var i = 0; i < strRawValue.length; i++) {
      var curChar = strRawValue.charAt(i);
      if (curChar == ' ' || curChar == '-' || curChar == '+') nLastWrappingIndex = i;
      oTextarea.value += curChar;
      if (oTextarea.scrollWidth > nEmptyWidth) {
        var buffer = '';
        if (nLastWrappingIndex >= 0) {
          for (var j = nLastWrappingIndex + 1; j < i; j++) buffer += strRawValue.charAt(j);
          nLastWrappingIndex = -1;
        }
        buffer += curChar;
        oTextarea.value = oTextarea.value.substr(0, oTextarea.value.length - buffer.length);
        oTextarea.value += '\n' + buffer;
      }
    }
    oTextarea.setAttribute('wrap', '');
  }

  function cropper() {
    var org_img = document.getElementById('media_img');
    var icanvas = document.createElement('canvas');
    var ictx = icanvas.getContext('2d');

    // Set Width and Height of canvas
    icanvas.width = 400;
    icanvas.height = 300;

    // Set Black background
    ictx.beginPath();
    ictx.fillRect(0, 0, icanvas.width, icanvas.height);
    ictx.fillStyle = '#000';
    ictx.fill();

    org_img.onload = function() {
      var org_w = this.width;
      var org_h = this.height;

      if (org_w / org_h > 1.334) {
        var divison_factor = org_w / 400;
        var est_height = org_h / divison_factor;
        var est_width = 400;
        var ypos = (300 - est_height) / 2;

        ictx.drawImage(org_img, 0, ypos, est_width, est_height);
        $scope.canvas_image = icanvas.toDataURL();
      } else {
        var divison_factor = org_h / 300;

        var est_width = org_w / divison_factor;

        var est_height = 300;
        var xpos = (400 - est_width) / 2;

        ictx.drawImage(org_img, xpos, 0, est_width, est_height);

        $scope.canvas_image = icanvas.toDataURL();
      }

      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext('2d');
      canvas.width = 400;
      canvas.height = 300;
      // Filter Option 1
      $scope.filterimg1 = icanvas.toDataURL();

      // Filter option  2
      ctx.filter = 'grayscale(100%)';
      ctx.drawImage(icanvas, 0, 0);
      $scope.filterimg2 = canvas.toDataURL();

      // Filter option 3
      ctx.filter = 'hue-rotate(30deg) opacity(95%)';
      ctx.drawImage(icanvas, 0, 0);
      $scope.filterimg3 = canvas.toDataURL();

      // Filter Option 4
      ctx.filter = 'blur(1px) contrast(90%)';
      ctx.drawImage(icanvas, 0, 0);
      $scope.filterimg4 = canvas.toDataURL();

      //Filter Option 5
      ctx.filter = 'hue-rotate(90deg) grayscale(50%)';
      ctx.drawImage(icanvas, 0, 0);
      $scope.filterimg5 = canvas.toDataURL();

      $scope.$apply();
    };
  }

  if ($scope.selectedMedia.media_type == 'photo') {
    cropper();
  } else {
    $scope.canvas_image = $scope.selectedMedia.media_url;
  }

  $scope.downloadimag = url => {
    var settings = {
      async: true,
      crossDomain: true,
      url: url,
      method: 'GET',
      headers: {
        'cache-control': 'no-cache',
        'postman-token': '42fd4698-bf8b-2851-3cd9-1d28a226ac00',
        'Access-Control-Allow-Origin': '*',
        'access-control-allow-methods': 'GET,OPTIONS',
      },
    };

    $.ajax(settings).done(function(response) {
      console.log(response);
    });
  };

  $scope.show_trim_sound = () => {
    if ($rootScope.media.sound.play_full_track) {
      $scope.slider.update({
        disable: true,
      });
    } else {
      $scope.slider.update({
        disable: false,
      });
    }
  };
  // Select Media
  $scope.selectMedia = item => {
    let index = $scope.media.map(el => el.media_url).indexOf(item.media_url);

    $scope.selectedMedia = angular.copy(item);

    //$scope.selectedMedia.media_url = $scope.media[index].media_url;
    if ($scope.selectedMedia.text == undefined) {
      $scope.textEffect.defaultTextValue();
    }
    if ($scope.selectedMedia.filterOption == undefined) {
      $scope.selectedMedia.filterOption = 'Normal';
    }
    if ($scope.selectedMedia.transition == undefined) {
      $scope.selectedMedia.transition = 'none';
    }
    if ($rootScope.media[index].text && $rootScope.media[index].text.left != undefined) {
      document.getElementById('addtextbox').style.left = $rootScope.media[index].text.left;
    }
    if ($rootScope.media[index].text && $rootScope.media[index].text.top != undefined) {
      document.getElementById('addtextbox').style.left = $rootScope.media[index].text.top;
    }
    //$scope.canvas_image = item.media_url;
    document.getElementById('addtextbox').style.display = 'none';

    $scope.sliderImageVisibility.update({
      from: $scope.selectedMedia.image_visibility ? $scope.selectedMedia.image_visibility : 10,
    });
    document.getElementById('media_img').setAttribute('src', item.media_url);
    if ($scope.selectedMedia.media_type != 'video') {
      cropper();
    } else {
      $scope.canvas_image = $scope.selectedMedia.media_url;
    }
  };

  // Image Filter
  $scope.ImageFilter = filterOption => {
    if ($scope.selectedMedia.media_type == 'video') {
      return;
    }
    $scope.selectedMedia.filterOption = filterOption;

    var image2 = document.getElementById('selectedMedia');
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    canvas.width = 400;
    canvas.height = 300;
    switch (filterOption) {
      case 'Normal': {
        ctx.drawImage(image2, 0, 0);
        break;
      }
      case 'Moon': {
        ctx.filter = 'grayscale(100%)';
        break;
      }
      case 'Hudson': {
        ctx.filter = 'hue-rotate(30deg) opacity(95%)';
        break;
      }
      case 'Gingham': {
        ctx.filter = 'blur(1px) contrast(90%)';
        break;
      }
      case 'Retro': {
        ctx.filter = 'hue-rotate(90deg) grayscale(50%)';
        break;
      }
    }
    ctx.drawImage(image2, 0, 0);
    $scope.canvas_image = canvas.toDataURL();

    let index = $rootScope.media.map(el => el.media_url).indexOf($scope.selectedMedia.media_url);
    $rootScope.media[index] = $scope.selectedMedia;
    $rootScope.media[index].media_url = $scope.canvas_image;
    $scope.selectMedia($rootScope.media[index]);
  };

  // Text Effect
  $scope.textEffect = {
    // open text effect box
    textEdit: () => {
      if ($scope.text_edit == true) {
        $scope.text_edit = false;
      } else {
        $scope.text_edit = true;
      }
    },
    // Save test in image
    saveText: () => {
      if (document.getElementById('addtextbox').style.display == 'block') {
        ApplyLineBreaks('addtextarea');
        var textArray = document.getElementById('addtextarea').value.split('\n');
        $scope.selectedMedia.text.value = document.getElementById('addtextarea').value;
        let textDivWidth = document.getElementById('addtextarea').offsetWidth;
        let left = document.getElementById('addtextbox').style.left.replace('px', '');
        let top = document.getElementById('addtextbox').style.top.replace('px', '');
        let image = document.getElementById('selectedMedia');
        let image2 = document.getElementById('media_img');

        // Temp Image Canvas
        let canvasImg = document.createElement('canvas');
        let ctxImg = canvasImg.getContext('2d');
        canvasImg.width = 400;
        canvasImg.height = 300;
        ctxImg.drawImage(image, 0, 0);

        // Temp TExt Canvas
        let canvas = document.createElement('canvas');
        let ctx = canvas.getContext('2d');
        canvas.width = 400;
        canvas.height = 300;
        ctx.font =
          $scope.selectedMedia.text.fontSize * (canvas.width / 450) + 2 + 'px ' + $scope.selectedMedia.text.fontFamily;
        ctx.fillStyle = '#' + $scope.selectedMedia.text.color;
        let additionalValue = 0;
        if ($scope.selectedMedia.text.alignment == 'center') {
          additionalValue = (textDivWidth / 2) * (canvas.width / 450);
        } else if ($scope.selectedMedia.text.alignment == 'right') {
          additionalValue = textDivWidth * (canvas.width / 450);
        }
        ctx.textAlign = $scope.selectedMedia.text.alignment;

        for (var i in textArray) {
          if (textArray.hasOwnProperty(i)) {
            console.log(textDivWidth);
            var dummyFontSize = $scope.selectedMedia.text.fontSize * (canvas.width / 450);
            console.log(dummyFontSize * textArray[i].length);
            console.log(textArray[i].length);
            let leadingValue = $scope.selectedMedia.text.leading * (canvas.width / 450) * i;
            ctx.fillText(
              textArray[i],
              parseInt(left) + parseInt(additionalValue),
              top * (canvas.height / 300) + 48 + leadingValue,
            );
          }
        }
        ctx.globalAlpha = 100 / parseInt($scope.selectedMedia.text.opacity);

        // Final real canvas by combing image & text canvas
        let canvasFinal = document.createElement('canvas');
        let ctxFinal = canvasFinal.getContext('2d');
        canvasFinal.width = 400;
        canvasFinal.height = 300;
        ctxFinal.drawImage(canvasImg, 0, 0);
        ctxFinal.drawImage(canvas, 0, 0);

        $scope.canvas_image = canvasFinal.toDataURL();

        let index = $scope.media.map(el => el.media_url).indexOf($scope.selectedMedia.media_url);
        $rootScope.media[index].media_url = $scope.canvas_image;
        $scope.selectedMedia.media_url = $scope.canvas_image;
        document.getElementById('addtextbox').style.display = 'none';
        notification.success({
          message: 'Setting Saved',
          templateUrl: 'custom_template.html',
          scope: $scope,
          positionY: 'bottom',
          positionX: 'right',
          delay: 3000,
        });
      }
    },
    resetImage: () => {
      let index = $scope.media.map(el => el.media_url).indexOf($scope.selectedMedia.media_url);
      $scope.selectedMedia.media_url = $scope.newMedia[index].media_url;
      $scope.media[index].media_url = $scope.newMedia[index].media_url;
      $scope.selectedMedia.transition = 'none';
      $scope.selectedMedia.filterOption = 'Normal';
      $scope.textEffect.defaultTextValue();
      $scope.selectMedia($scope.selectedMedia);
    },
    // set default value
    defaultTextValue: () => {
      $scope.selectedMedia.text = {
        fontSize: 24,
        opacity: '100',
        fontFamily: 'sans-serif',
        color: '41A897',
        leading: 22,
        alignment: 'left',
        value: '',
      };
      var style =
        'background:transparent;resize: both;' +
        'font-size:' +
        $scope.selectedMedia.text.fontSize +
        'px' +
        ';opacity:' +
        parseInt($scope.selectedMedia.text.opacity) / 100 +
        ';font-family:' +
        $scope.selectedMedia.text.fontFamily +
        ';color: #' +
        $scope.selectedMedia.text.color +
        ';text-align:' +
        $scope.selectedMedia.text.alignment +
        ';border: 2px dashed #189b85;';

      document.getElementById('addtextarea').setAttribute('style', style);
      document.getElementById('addtextbox').style.left = '0px';
      document.getElementById('addtextbox').style.top = '0px';
    },
    // Add text box on image or video
    addTextBox: () => {
      if ($scope.selectedMedia.media_type != 'video') {
        document.getElementById('addtextbox').style.display = 'block';
      }
    },
    // Increase & Decrease Font Size
    fontSizeInc: () => {
      if ($scope.selectedMedia.text.fontSize == undefined) {
        $scope.selectedMedia.text.fontSize = 0;
      }
      $scope.selectedMedia.text.fontSize += 1;
    },
    fontSizeDec: () => {
      if ($scope.selectedMedia.text.fontSize == undefined) {
        $scope.selectedMedia.text.fontSize = 0;
      } else if ($scope.selectedMedia.text.fontSize > 0) {
        $scope.selectedMedia.text.fontSize -= 1;
      }
    },
    // Increase or  Descrease leading
    leadingInc: () => {
      if ($scope.selectedMedia.text.leading == undefined) {
        $scope.selectedMedia.text.leading = 0;
      }
      $scope.selectedMedia.text.leading = parseInt($scope.selectedMedia.text.leading) + 1;
    },
    leadingDec: () => {
      if ($scope.selectedMedia.text.leading == undefined) {
        $scope.selectedMedia.text.leading = 0;
      } else if ($scope.selectedMedia.text.leading > 0) {
        $scope.selectedMedia.text.leading -= 1;
      }
    },
  };
  $scope.textEffect.defaultTextValue();
  // Transition Effects
  $scope.addTransition = () => {
    let index = $scope.media.map(el => el.media_url).indexOf($scope.selectedMedia.media_url);
    $rootScope.media[index].transition = $scope.selectedMedia.transition;
  };

  $scope.upload = { audio: '' };
  $scope.allSound = [
    {
      story_id: 0,
      audio_url: 'anewbeginning.mp3',
      length: 155,
    },
    {
      story_id: 0,
      audio_url: 'buddy.mp3',
      length: 123,
    },
    {
      story_id: 0,
      audio_url: 'clearday.mp3',
      length: 90,
    },
    {
      story_id: 0,
      audio_url: 'happyrock.mp3',
      length: 106,
    },
    {
      story_id: 0,
      audio_url: 'ukulele.mp3',
      length: 146,
    },
  ];
  StoryService.getAudioList($rootScope.newStory.id).then(res => {
    if (res.status == 200) {
      res.data.data.story_audio.forEach(function(item) {
        $scope.allSound.push(item);
      });
    }

    $scope.soundtrim = $scope.allSound.length ? $scope.allSound[0]['length'] : 0;

    $timeout(function() {
      $scope.slider.update({
        min: 0,
        max: $scope.soundtrim,
        from: 0,
        to: $scope.soundtrim,
      });
      $scope.sliderDelay.reset();
    }, 500);
  });

  // upload single audio
  $scope.upload = data => {
    var length = data.$ngfDuration ? data.$ngfDuration : 0;
    var formdata = { audio: data, story_id: $rootScope.newStory.id, length: length };

    Upload.upload({
      url: $rootScope.apiUrl + 'api/story_audio',
      data: formdata,
    }).then(
      function(resp) {
        $scope.audioProcess = false;
        if (resp.status == 200) {
          //$state.reload()
          StoryService.getAudioList($rootScope.newStory.id).then(res => {
            if (res.status == 200) {
              $scope.allSound = [
                {
                  story_id: 0,
                  audio_url: 'anewbeginning.mp3',
                  length: 155,
                },
                {
                  story_id: 0,
                  audio_url: 'buddy.mp3',
                  length: 123,
                },
                {
                  story_id: 0,
                  audio_url: 'clearday.mp3',
                  length: 90,
                },
                {
                  story_id: 0,
                  audio_url: 'happyrock.mp3',
                  length: 106,
                },
                {
                  story_id: 0,
                  audio_url: 'ukulele.mp3',
                  length: 146,
                },
              ];
              res.data.data.story_audio.forEach(function(item) {
                $scope.allSound.push(item);
              });
              $scope.soundtrim = $scope.allSound[0]['length'];
              $timeout(function() {
                $scope.slider.update({
                  min: 0,
                  max: $scope.soundtrim,
                  from: 0,
                  to: $scope.soundtrim,
                });
                $scope.sliderDelay.reset();
              }, 500);
            }
          });
          notification.success({
            message: 'Soundtrack successfully uploaded',
            positionY: 'bottom',
            positionX: 'right',
            delay: 3000,
          });
        } else {
          notification.error({
            message: 'Error in uploaded media',
            positionY: 'bottom',
            positionX: 'right',
            delay: 3000,
          });
        }
      },
      function(resp) {
        $scope.audioProcess = false;
      },
      evt => {
        $scope.audioProcess = true;
        $scope.progressPercentage = parseInt((100.0 * evt.loaded) / evt.total);
      },
    );
  };

  $scope.resetSoundData = () => {
    $rootScope.media.sound.audio_url = undefined;
  };

  $scope.story_video_preview = () => {
    // $rootScope.videoWhammyPreview=undefined;
    // var video = new Whammy.Video(0.1);
    // var i = 0;
    // function nextFrame(){
    // 	if(i==$rootScope.media.length){
    //  			finalizeVideo()
    //  	} else {
    //  		var canvas=document.getElementById('canvas');
    // 		var ctx = canvas.getContext('2d');
    //  		if($rootScope.media[i].media_type=='photo'){
    //  			video.duration=1000/1;

    // 			var img = new Image;
    // 			img.crossOrigin = "anonymous";
    // 			img.src = $rootScope.media[i].media_url;
    // 			img.onload = function(){
    // 				canvas.height=600;
    // 				canvas.width=800;
    // 			  	ctx.drawImage(img,0,0,800,600);

    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			  	video.add(canvas);
    // 			 	i++;
    // 	 			nextFrame();
    // 			}
    //  		}
    //  		else if($rootScope.media[i].media_type=='video') {

    // 				video.duration=45;
    // 				var dummyVideo = document.getElementById('dummyVideo');
    // 				dummyVideo.crossOrigin = "anonymous";
    // 				dummyVideo.src= $rootScope.media[i].media_url;
    // 				canvas.height=600;
    // 				canvas.width=800;
    // 				dummyVideo.addEventListener('play', function(){
    // 					drawVideo(this,ctx,video,canvas);
    // 		    },false);
    // 		    dummyVideo.play();
    //  		}
    //  	}
    //  	function drawVideo(v,c,video,canvas) {
    // 	    if(v.paused || v.ended) {
    // 	    	i++;
    // 	    	nextFrame();
    // 	    }
    // 	    if(v.videoWidth!=0)
    // 	    	canvas.width=v.videoWidth;
    // 	    if(v.videoHeight!=0)
    // 	    	canvas.height=v.videoHeight;
    // 	    c.drawImage(v,0,0,800,600);
    // 	    video.add(canvas);
    // 	    setTimeout(drawVideo,45,v,c,video,canvas);
    // 	}

    // 	function fadeIn(img,ctx,encoder,canvas){

    // 		encoder.add(canvas);
    // 	}
    // }

    // nextFrame();
    // function finalizeVideo(){

    // 	video.compile(false, function(output){

    // 		StoryService.addAudioToVideo({video:output,sound:$rootScope.media.sound,story_id:$rootScope.newStory.id})
    //         .then((res)=>{

    //      			if(res.data){
    //      				$rootScope.videoFileName=res.data.data["new_video"];
    //      				$rootScope.videoWhammyPreview ='./story_videos/story_'+$rootScope.newStory.id+'/'+res.data.data["new_video"];
    //      				document.getElementById('awesome').src = $rootScope.videoWhammyPreview;
    //      			}

    //         },(err)=>{

    //         })

    // 		 //toString converts it to a URL via Object URLs, falling back to DataURL
    // 	});
    // }

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
        'StoryService',
        function($rootScope, $scope, $uibModalInstance, $window, punditService, StoryService) {
          StoryService.createPreview($rootScope.media, $rootScope.newStory.id, $rootScope.media.sound).then(
            res => {
              $scope.loading = false;
              $scope.videoWhammyPreview =
                '/story_media/story_' + $rootScope.newStory.id + '/' + res.data.data.new_video;

              notification.success({
                message: res.data.message,
                positionY: 'bottom',
                positionX: 'right',
                delay: 3000,
              });
            },
            err => {
              $scope.loading = false;
            },
          );

          $scope.close = function() {
            $uibModalInstance.dismiss('cancel');
          };

          $scope.publishVideo = function() {
            $scope.loading = true;
            var url = './story_videos/story_' + $rootScope.newStory.id + '/' + $rootScope.videoFileName;

            StoryService.publishVideo({ story_id: $rootScope.newStory.id }).then(
              res => {
                $scope.loading = false;
                notification.success({
                  message: res.data.message,
                  positionY: 'bottom',
                  positionX: 'right',
                  delay: 3000,
                });
                $state.go('ShareVideo', { storyId: $rootScope.newStory.id });
              },
              err => {
                $scope.loading = false;
              },
            );
          };
        },
      ],
    });
  };

  // Watch Selected Media variable changes
  $scope.$watch(
    function(scope) {
      return scope.selectedMedia;
    },
    function(newValue, oldValue) {
      if (angular.equals(newValue, oldValue) == false) {
        let index = $scope.newMedia.map(el => el.media_url).indexOf($scope.selectedMedia.media_url);

        if (angular.equals(newValue.text, oldValue.text) == false) {
          var style =
            'background:transparent;' +
            'font-size:' +
            newValue.text.fontSize +
            'px' +
            ';opacity:' +
            parseInt(newValue.text.opacity) / 100 +
            ';font-family:' +
            newValue.text.fontFamily +
            ';color: #' +
            newValue.text.color +
            ';text-align:' +
            newValue.text.alignment +
            ';line-height:' +
            newValue.text.leading +
            'px' +
            ';resize: both; border: 2px dashed rgb(24, 155, 133)';

          if (document.getElementById('addtextarea').style.width) {
            style += ';width:' + document.getElementById('addtextarea').style.width;
          }

          if (document.getElementById('addtextarea').style.height) {
            style += ';height:' + document.getElementById('addtextarea').style.height;
          }
          document.getElementById('addtextarea').setAttribute('style', style);

          $rootScope.media[index].text = newValue.text;
        }
        if (angular.equals(newValue.media_url, oldValue.media_url) == false) {
        }
      }
    },
    true,
  );
}
