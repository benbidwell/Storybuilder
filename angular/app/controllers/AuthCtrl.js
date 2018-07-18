//AuthCtrl Ctrollers
AuthCtrl.$inject =  ['$rootScope' ,'$scope', '$state','AuthService','Notification','jwtHelper','$window','$http'];

export default function AuthCtrl($rootScope ,$scope, $state,authService,notification,jwtHelper,$window,$http){

    $scope.loginForm={
        password:'',
        email:'',
        rememberMe:''
    }

    if ($scope.loginForm.password!='' && $scope.loginForm.email!='') {
        if($window.localStorage.ud){
            notification.success({message:"Already Loged in!",positionY: 'bottom', positionX: 'right',delay: 3000});
            $state.go("master.event");
        }
    }
    else if($window.localStorage.ud){
        $state.go("master.event");
    }

    // login functions
    $scope.login=()=>{
        $scope.errors=[];
        $scope.error=[];
        var regularExpForValidEmail = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
        // Requeired validation
        if($scope.loginForm.password=='' || $scope.loginForm.password==undefined){
            $scope.errors.push('Password field is required');
            $scope.error['password']=true;
        }
        if( $scope.loginForm.email=='' || $scope.loginForm.email==undefined){
            $scope.errors.push('Email field is required');
            $scope.error['email']=true;
        }
        else if (!regularExpForValidEmail.test($scope.loginForm.email)){
            // Email Validation
            $scope.error['email']=true;
            $scope.errors.push('Please enter valid email address');
        }

        if($scope.errors.length>0){
            notification.error({message:'Validation Error',templateUrl: "custom_template.html",scope: $scope,positionY: 'bottom', positionX: 'right',delay: 3000});
        }
        else{
            $scope.loader=true;
            authService.login($scope.loginForm)
            .then((res)=>{
				$scope.loader=false;
				if(res.status==200){
                    //$rootScope.user = jwtHelper.decodeToken(res.data.token);
                    $window.localStorage.ud = res.data.token;
                    $http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                    $http.defaults.headers.common.Authorization = 'Bearer '+res.data.token;
					notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
					$scope.loginForm=undefined;
                    if($state.params.return_url){
                        window.location.href=$state.params.return_url;
                    } else {
                         $state.go('master.event');
                    }
				} else if(res.status==400){
					notification.error({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
				} else{
					notification.error({message:'Error',positionY: 'bottom', positionX: 'right',delay: 3000});
				}
			})
        }
    }



};
