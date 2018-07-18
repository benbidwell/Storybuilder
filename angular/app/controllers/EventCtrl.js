//Event Ctrollers
EventCtrl.$inject =  ['$rootScope' ,'$scope', '$state','EventService','Notification','jwtHelper','$window','$http','$uibModal'];

export default function EventCtrl($rootScope ,$scope, $state,eventService,notification,jwtHelper,$window,$http,$uibModal){

	$("#dropImg").hover(function(){
	    $(this).css("font-size",'25px');
	    $('#dropImg').html($(this).attr("data-hover-title") );

	}, function() {
		$(this).css("font-size",'14px');
	    $('#dropImg').text($(this).attr("data-title"));
	});

	$scope.eventForm={
		event_title:'',
		event_details:'',
		event_picture:undefined,
		status:0,
		id:undefined
	}

	var nextPageLoad;
	var lastPage;
	if($state.current.name=='master.event'){
	    $(window).scroll(function () {
		   	if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
		     	if(nextPageLoad==false && lastPage!=false){
		     		$scope.nextPage();
		     	}
		   	}
		});
	}

	var eventOptions={
		limit:7,
		offset:0
	}

	$scope.tab={
		thumbnail:true,
		list:false
	}

	var getAllEvent=(options)=>{
		nextPageLoad=true;
		eventService.getAllEvent(options)
		.then((res)=>{
			if(res.data.data){
				$rootScope.events=res.data.data.events;
			}
			nextPageLoad=false;
		})
	}
	var ctrl = this;
	ctrl.search ={name:'', id:''};
	eventService.getserachEvent()
    .then((res)=>{
        if(res.data.data){
          $rootScope.event_dataSource = res.data.data.events;
          $scope.dataSource = $rootScope.event_dataSource;
        }
    })

	$scope.setClientData = function(item){
		lastPage=false;
		$rootScope.events=[];
		$rootScope.events.push(item);

        if (item){
          ctrl.search = item;
        }
        else {
        	item.name = "No result found"
        	item.id = 0
        	ctrl.search = item;

        }
    };

    $scope.searchEvent=(value)=>{
	  	if(value==''){
	  		$rootScope.events=$rootScope.event_dataSource;
	    }
	};

	$scope.nextPage=()=>{
		nextPageLoad=true;
		$scope.nextPageloader=true;
		eventOptions.offset+=eventOptions.limit;
		eventOptions.limit=8;
		if($state.current.name=='master.event'){
			eventService.getAllEvent(eventOptions)
			.then((res)=>{
				$scope.nextPageloader=false;
				nextPageLoad=false;
				if(res.data.data.events.length==0){
					lastPage=false;
				}
				res.data.data.events.forEach(function(item){
					$rootScope.events.push(item);
				})
			})
		}
	};

	if($state.current.name=='master.event'){
		getAllEvent(eventOptions);
	}

	$scope.back = () => {
		$state.go("master.event")
	}

	$scope.createEvent=()=>{

		$scope.requiredEmail=false;
		$scope.errors=undefined;
		$scope.err=undefined;
		$scope.loader=true;
		$scope.eventForm.user_id=$rootScope.user.id;
		// send request to create event

		eventService.createEvent($scope.files,$scope.eventForm)
		.then((res)=>{
			$scope.loader=false;
			if(res.status==200){
				notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				$scope.eventForm={
					event_title:'',
					event_details:'',
					event_picture:''
				}
				$rootScope.events.push(res.data.event);
			
				$state.go('master.stories',{id: res.data.data.event.id});
			} else if(res.status==400){
				if(res.data.data){
					const errors=res.data.data;
					$scope.err=[];
					Object.keys(errors).forEach(function(key,index) {
					    $scope.err[key]=true;
					});
					$scope.errors=res.data.data;
				}
				const msg=res.data.message?res.data.message:'Error';
				notification.error({message:msg,templateUrl: "custom_template.html",scope: $scope,positionY: 'bottom', positionX: 'right',delay: 3000});

			} else{
				notification.error({message:'Error',positionY: 'bottom', positionX: 'right',delay: 3000});
			}
		})
	}

	$scope.editEvent=()=>{

		$scope.requiredEmail=false;
		$scope.errors=undefined;
		$scope.err=undefined;
		$scope.loader=true;
		$scope.eventForm.user_id=$rootScope.user.id;

		$scope.eventForm.id=$state.params.id;

		$scope.eventForm._method='PUT';

		if ($scope.eventForm.event_picture == ""){
			eventService.remove_image($scope.eventForm.id)
			.then((res)=>{})
		}

		if((typeof $scope.eventForm.event_picture)=='string'){
		    $scope.eventForm.event_picture=undefined;
		}
		if ($scope.eventForm.event_details == ""){
			$scope.eventForm.event_details=undefined;
		}

		// send request to update event
		eventService.updateEvent($scope.files,$scope.eventForm)
		.then((res)=>{
			$scope.loader=false;
			if(res.status==200){
				notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				$scope.eventForm={
					event_title:'',
					event_details:'',
					event_picture:''
				}
				$state.go('master.event');
			} else if(res.status==400){
				if(res.data.data){
					const errors=res.data.data;
					$scope.err=[];
					Object.keys(errors).forEach(function(key,index) {
					    $scope.err[key]=true;
					});
					$scope.errors=res.data.data;
				}
				const msg=res.data.message?res.data.message:'Error';
				notification.error({message:msg,templateUrl: "custom_template.html",scope: $scope,positionY: 'bottom', positionX: 'right',delay: 3000});

			} else{
				notification.error({message:'Error',positionY: 'bottom', positionX: 'right',delay: 3000});
			}
		},(err) => {

		})
	}

	$scope.remove_image = function(id) {
		$scope.eventForm.event_picture="";
	}

	$scope.deleteEvent =  (id)=> {
		$rootScope.delect_id = id;
	    var modalInstance = $uibModal.open({
	      animation: true,
	      ariaLabelledBy: 'modal-title',
	      ariaDescribedBy: 'modal-body',
	      size: 'lg',
	      templateUrl: 'deleteEvent.html',
	      controller: ['$rootScope','$scope', '$state','$uibModalInstance','$window','EventService',
	      	function($rootScope,$scope,$state, $uibModalInstance,$window,eventService) {

	           	eventService.getDeleteMsg()
	           	.then((res)=>{
	           		if(res.status==200){

	           			$scope.deleteMsg=res.data.message;
	           		} else {
	           			$scope.deleteMsg='This Action Cannot Be Undo';
	           		}
	           	})

	          	$scope.close = function () {
	            	$uibModalInstance.dismiss('cancel');
	          	}

	          	$scope.ok = function (){
	          		$scope.loader=true;
		          	var id = $rootScope.delect_id
		            eventService.deleteEvent(id)
					.then((res)=>{
						$scope.loader=false;
						if(res.status==200){
							notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
							$state.reload();
							$uibModalInstance.dismiss('cancel');
					   }
					})
	          	}
	        }]

	    })
  	};

	let getEvent=(id)=>{
		eventService.getEvent(id)
		.then((res)=>{
			$scope.eventForm=res.data.data;
			if($scope.eventForm.event_picture!=(null || undefined || '')){
				$scope.eventForm.event_picture='/event_pictures/'+$scope.eventForm.event_picture;
			}
		})
	};

	if($state.current.name=='master.edit-event'){
		getEvent($state.params.id);
	}

	$scope.switchTab=(slug)=>{
		if(slug=='thumbnail'){
			$scope.tab.thumbnail=true;
			$scope.tab.list=false;
		}
		else if(slug=='list'){
			$scope.tab.thumbnail=false;
			$scope.tab.list=true;
		}
	}
	

}
