'use strict';
//CurateCtrl Ctrollers
SelectMediaCtrl.$inject =  ['$rootScope' ,'$scope', '$state','Notification','$window','$uibModal','$timeout'];
export default function SelectMediaCtrl($rootScope ,$scope, $state,notification,$window,$uibModal,$timeout){

    $timeout(function() {
        $(".test").mCustomScrollbar({
            axis:"x",
            theme:"dark",
            advanced: { autoExpandHorizontalScroll:true }

        });
        $(".test1").mCustomScrollbar({
            theme:"minimal",
        });
    }, 500);

    $scope.active = {
        sources: true,
        location: false,
        publish: false
    };

    $scope.open = function (size,item,$event) {
        if (item.media_type=="video") {
            $scope.dimension = {
                newWidth: 600,
                newHeight: 400
            };
        } else {
            var newImg = new Image();
            newImg.src = $event.target.getAttribute('src');
            newImg.onload = function() {}
            $scope.dimension = {
                newWidth: newImg.width+30,
                newHeight: newImg.height+50
            };
        }

        var modalInstance = $uibModal.open({
          animation: true,
          ariaLabelledBy: 'modal-title',
          ariaDescribedBy: 'modal-body',
          size: 'md',
          templateUrl: 'myModalContent.html',
          controller: ['$rootScope','$scope', '$uibModalInstance','$window',
            function($rootScope,$scope, $uibModalInstance,$window) {
                $scope.item = item;
                $scope.formdata = {};
                $scope.close = function () {
                  $uibModalInstance.dismiss('cancel');
                };
            }]
        });
    };

    $scope.filter={
        checkbox:{
            'Facebook':false,
            'Instagram':false,
            'Google+':false,
            'Twitter':false,
            'Cloud Storage':false,
            'Uploaded Image':false,
            'Uploaded Video':false
        },
        value:[],
        time_publish:'',
        location:'',
        upload_type:'',
        type:''
    };

    var init=()=>{
        // Initailze rootscope.newStory variable;
        $rootScope.newStory=JSON.parse($window.localStorage.newStory);
        $rootScope.newStory.select_media = JSON.parse($window.localStorage.select_media);
        $scope.story_id = $rootScope.newStory.id;
        $scope.location_listing=[];
        $rootScope.newStory.select_media.map((el)=>{
            
            if(el.type=='facebook')
                $scope.filter.checkbox['Facebook']=true;
            else if(el.type=="uploaded" && el.media_type=='photo')
                $scope.filter.checkbox['Uploaded Image']=true;
            else if(el.type=="uploaded" && el.media_type=='video')
                $scope.filter.checkbox['Uploaded Video']=true;
            else if(el.type=="instagram")
                $scope.filter.checkbox['Instagram']=true;
            else if(el.type=="gplus")
                $scope.filter.checkbox['Google+']=true;
            else if(el.type=="gdrive")
                $scope.filter.checkbox['Cloud Storage']=true;
            else if(el.type=="dropbox")
                $scope.filter.checkbox['Cloud Storage']=true;
            else if(el.type=="twitter")
                $scope.filter.checkbox['Twitter']=true;

            if(el.location){
                if(!$scope.location_listing.includes(el.location))
                $scope.location_listing.push(el.location);
            }
        })
    }
    init();

    $scope.clear = function() {
        $scope.filter.dt = null;
    };

    $scope.inlineOptions = {
        showWeeks: true
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        maxDate: new Date(),
        startingDay: 1
    };

    $scope.open1 = function () {
        $scope.popup1.opened = true;
    };

    $scope.popup1 = {
        opened: false
    };

    $scope.format ='dd-MM-yyyy';

    $scope.items1 = $rootScope.newStory.media;
    $scope.items2=[];

    $scope.dropSuccessHandler = function ($event,index,array) {
        array.splice(index,1);
    };

    $scope.onDrop = function ($event,$data,array) {
        array.push($data);
    };

    $scope.removeDragValue = function (item) {
        $rootScope.newStory.select_media.push(item);
        var index=$scope.items2.map((el)=>el.media_url).indexOf(item.media_url);
        $scope.items2.splice(index,1);
    };

    $scope.toggleNav = (value) => {
        if (value == "SOURCES") {
        $scope.active.sources = !$scope.active.sources;
        } else if (value == "PUBLISHED"){
          $scope.active.publish = !$scope.active.publish
        } else if (value == "LOCATION") {
          $scope.active.location = !$scope.active.location
        }
    };

    $scope.continue= () => {
        if($scope.items2.length!=0){
           $window.localStorage.media=JSON.stringify($scope.items2);
            $rootScope.media=$scope.items2;
            $state.go('master.storyEdit');
        }
    }
}
