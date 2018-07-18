//App Ctrollers
SignupCtrl.$inject =  ['$rootScope' ,'$scope', '$state','AuthService','Notification','$window','$http'];

export default function SignupCtrl($rootScope ,$scope, $state,authService,notification,$window,$http){

	$scope.formdata={
		first_name:'',
		last_name:'',
		email:'',
		password:''
	}
	$scope.forgotPwdForm={
		email:''
	}

	$scope.resetForm={
		password:'',
		password_confirmation:''
	}

	$scope.registration=()=>{
		$scope.loader=true;
		authService.registration($scope.formdata)
		.then((res)=>{
			if(res.status==200){
				notification.success({message:'User Registered Successfully',positionY: 'bottom', positionX: 'right',delay: 3000});
				$scope.formdata={
					first_name:'',
					last_name:'',
					email:'',
					password:''
				}
				$window.localStorage.ud = res.data.token;
                $http.defaults.headers.common.Authorization = 'Bearer '+res.data.token;
                $state.go('master.event');
				

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
			$scope.loader=false;            
        })
	}


	// Forgot password function
	$scope.forgotpwd=()=>{
		$scope.requiredEmail=false;
		// regular  expression for test email address
		var regularExpForValidEmail = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
	
		$scope.loader=true;
		// send request to forgot password
		authService.forgotPassword($scope.forgotPwdForm)
		.then((res)=>{
			$scope.loader=false;
			if(res.status==200){
				notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				$state.go('public.login');
				
			} else if(res.status==400){

					const errors=res.data.data;
					$scope.err=[];
					$scope.errors=res.data.data;
					notification.error({message:res.data.message,templateUrl: "custom_template.html",scope: $scope,positionY: 'bottom', positionX: 'right',delay: 3000});
		    } else{
				notification.error({message:'Error',positionY: 'bottom', positionX: 'right',delay: 3000});
			}
		})
	}

	$scope.resetPwd=()=>{

		$scope.loader=true;
		$scope.resetForm.token=$state.params['id'];
		// send request to forgot password
		authService.resetPassword($scope.resetForm)
		.then((res)=>{
			$scope.loader=false;
			if(res.status==200){
				$window.localStorage.clear();
				$rootScope.user= null;
        		$rootScope= null;
        		$http.defaults.headers.common.Authorization=undefined; 
				notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				$state.go("public.login")
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
		})
	}
}