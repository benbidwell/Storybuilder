//PunditActivationCtrl
PunditActivationCtrl.$inject = ['$rootScope', '$scope', '$state', '$window', '$timeout', '$uibModal', 'PunditService', 'AuthService', 'jwtHelper', '$http', 'Notification'];
export default function PunditActivationCtrl($rootScope, $scope, $state, $window, $timeout, $uibModal, punditService, authService, jwtHelper, $http, notification) {

    var data = {
        user_id: parseInt(atob($state.params.userId)),
        pundit_id: parseInt(atob($state.params.punditId))
    };

    var host;
    if(window.location.port==80 || window.location.port==''){

      host=window.location.protocol+'//'+window.location.hostname
    } else{
      host=window.location.protocol+'//'+window.location.hostname+':'+window.location.port
    }
    punditService.punditActivation(data)
    .then((res)=>{
        if(res.status==200){
            var story_id=res.data.data.story_id;
            notification.success({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
            $state.go('public.login',{return_url:encodeURI(host+'/share-video/'+story_id+'/'+data.pundit_id)});
        } else{
            notification.error({message:res.data.message,positionY: 'bottom', positionX: 'right',delay: 3000});
            $state.go('public.login');
        }
    })

}
