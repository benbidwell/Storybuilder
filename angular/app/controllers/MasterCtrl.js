//MasterCtrl Ctrollers
MasterCtrl.$inject =  ['$rootScope' ,'$scope', '$state','AuthService','Notification','jwtHelper','$window','$http'];

export default function MasterCtrl($rootScope ,$scope, $state,authService,notification,jwtHelper,$window,$http){

	// call constructor
	if($rootScope.user==undefined){
		authService.getUserDetails()
		.then((res)=>{

			if(res.status==200){
				$rootScope.user=res.data.data;
				var decodeToken = jwtHelper.decodeToken($window.localStorage.ud);
				$rootScope.user.id=decodeToken.sub;
				if($rootScope.user.profile_picture!=null && $rootScope.user.profile_picture!=undefined && $rootScope.user.profile_picture!=""){
					$rootScope.user.profile_picture='/profile_pictures/'+$rootScope.user.profile_picture;
				}
				if($rootScope.user.profile_picture== ''){
					$rootScope.user.profile_picture=undefined;
				}
			}
			else if(res.status==400 || res.status== 401){
				$scope.logout();
			}
		})
	}

	$scope.logout=()=>{
		$window.localStorage.clear();
		$rootScope.user = null;
		$rootScope = undefined;
        $http.defaults.headers.common.Authorization=undefined;
        $state.go('public.login');
	};

}
