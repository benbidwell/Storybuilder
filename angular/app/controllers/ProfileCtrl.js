//Profile Controllers
ProfileCtrl.$inject =  ['$rootScope' ,'$scope', '$state','AuthService','Notification','jwtHelper','$window','$http'];

export default function ProfileCtrl($rootScope ,$scope, $state,authService,notification,jwtHelper,$window,$http){

	authService.getUserDetails()
	.then((res)=>{
		$scope.editForm=res.data.data;
		if($scope.editForm.profile_picture!=null && $scope.editForm.profile_picture!=undefined && $scope.editForm.profile_picture!=''){
			$scope.editForm.profile_picture='/profile_pictures/'+$scope.editForm.profile_picture;
		}
		if($scope.editForm.profile_picture== '' || $scope.editForm.profile_picture==null){
			$scope.editForm.profile_picture=undefined;
		}
	})

	$scope.editProfile=()=>{
		$scope.loader=true;
		$scope.country_id=0;

		var data = Object.assign({}, $scope.editForm);
		var image = $scope.editForm.profile_picture

		if((typeof data.profile_picture)==='string'){
			  data.profile_picture = undefined;
		}

		authService.editProfile(data)
		.then((res)=>{
			$scope.loader=false;
			if(res.status==200){
				notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				authService.getUserDetails()
				.then((res)=>{
					$scope.err=[];
					$scope.editForm = res.data.data;
					 $rootScope.user = res.data.data;
					if($scope.editForm.profile_picture!=(null && undefined && '')){
						$scope.editForm.profile_picture='/profile_pictures/'+$scope.editForm.profile_picture;
					    $rootScope.user.profile_picture = $scope.editForm.profile_picture;
					}
					$state.reload();
				})
			} else if(res.status==400){

				const errors=res.data.data;
				$scope.err=[];
				Object.keys(errors).forEach(function(key,index) {
				    $scope.err[key]=true;
				});
				$scope.errors=res.data.data;
				notification.error({message:res.data.message,templateUrl: "custom_template.html",scope: $scope,positionY: 'bottom', positionX: 'right',delay: 3000});
			} else{

				notification.error({message:'Error',positionY: 'bottom', positionX: 'right',delay: 3000});
			}
            // $state.go('master.event');

        })
	}

    $scope.select_image = function (image,data) {
        if (data == null) {
        	 $scope.editForm.profile_picture = $rootScope.user.profile_picture
        }
    	else if (data.size > 1048576) {
    	   $scope.editForm.profile_picture = $rootScope.user.profile_picture
    	   notification.error({message:'Its size should not exceed 1MB!',positionY: 'bottom', positionX: 'right',delay: 3000});
         }
    }

	$("#changePass").click(function(){
        $(".popupBigPublish").toggleClass('popPubl');
    });

	$scope.clospublish = function () {
	 	$scope.changePwdForm = { old_password: null,
				  password: null,
				  password_confirmation:null }
	 	$(".popupBigPublish").toggleClass('popPubl');
	}

    $scope.changePassword=()=>{
    	$scope.loader1=true;
		authService.changePassword($scope.changePwdForm)
		.then((res)=>{
			$scope.loader1=false;
			if(res.status==200){
				$scope.changePwdForm.old_password = '';
				$scope.changePwdForm.password = '';
				$scope.changePwdForm.password_confirmation = ''
				$(".popupBigPublish").toggleClass('popPubl');
				notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
			} else if(res.status==400){
				if(res.data.data==null){
					notification.error({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				} else {
					const errors=res.data.data;
					$scope.err=[];
					Object.keys(errors).forEach(function(key,index) {
					    $scope.err[key]=true;
					});
					$scope.errors=res.data.data;
					notification.error({message:res.data.message,templateUrl: "custom_template.html",scope: $scope,positionY: 'bottom', positionX: 'right',delay: 3000});

				}
			} else{
				notification.error({message:'Error',positionY: 'bottom', positionX: 'right',delay: 3000});
			}
            //$state.go('master.event');
        })
    };


}
