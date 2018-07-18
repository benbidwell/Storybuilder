"use strict";

routing.$inject = ['$stateProvider', '$urlRouterProvider','$locationProvider',"ScrollBarsProvider"];

export default function routing($stateProvider, $urlRouterProvider,$locationProvider,ScrollBarsProvider) {

    ScrollBarsProvider.defaults = {
  		scrollButtons: {
		    scrollAmount: 'auto', // scroll amount when button pressed
  			enable: true // enable scrolling buttons by default
  		},
  		axis: 'yx' // enable 2 axis scrollbars by default
    };

    $locationProvider.html5Mode(true);

    $stateProvider
// APP Routes
        .state('index', {
            url : '/',
            templateUrl : require('extract-loader!html-loader!../views/index.html'),

        })
	      .state('public', {
            url : '',
            templateUrl : require('extract-loader!html-loader!../views/layout/public.html'),

        })
        .state('public.login', {
            url : '/login?return_url',
            params: {
                return_url: null
            },
            templateUrl : require('extract-loader!html-loader!../views/login.html'),
            controller : 'AuthCtrl'

        })
        .state('public.signup', {
            url : '/sign-up',
            templateUrl : require('extract-loader!html-loader!../views/sign-up.html'),
            controller : 'SignupCtrl'
        })
        .state('public.forgotpwd', {
            url : '/forgot-password',
            templateUrl : require('extract-loader!html-loader!../views/forgot-password.html'),
            controller : 'SignupCtrl'
        })
        .state('public.reset_password',{
            url:'/reset_password/:id',
            templateUrl : require('extract-loader!html-loader!../views/reset-password.html'),
            controller : 'SignupCtrl'
        })


        .state('master', {
            url : '',
            templateUrl : require('extract-loader!html-loader!../views/layout/master.html'),
            controller:'MasterCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth()){
                            q.resolve("ok")
                        }
                        else{
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('master.edit-profile', {
            url : '/profile/edit',
            templateUrl : require('extract-loader!html-loader!../views/edit-profile.html'),
            controller:'ProfileCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth())
                        {
                            q.resolve("ok")
                        }
                        else
                        {
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('master.change-password', {
            url : '/change-password',
            templateUrl : require('extract-loader!html-loader!../views/edit-profile.html'),
            controller:'ProfileCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth())
                        {
                            q.resolve("ok")
                        }
                        else
                        {
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('master.stories', {
            url : '/event/:id/stories',
            templateUrl : require('extract-loader!html-loader!../views/stories.html'),
            controller:'StoryCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth())
                        {
                            q.resolve("ok")
                        }
                        else
                        {
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('master.add-story', {
            url : '/event/:id/story/new',
            templateUrl : require('extract-loader!html-loader!../views/story/add-story.html'),
            controller:'StoryCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth())
                        {
                            q.resolve("ok")
                        }
                        else
                        {
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('master.edit-story', {
            url : '/event/:id/story/:story_id/edit',
            templateUrl : require('extract-loader!html-loader!../views/story/edit-story.html'),
            controller:'StoryCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth())
                        {
                            q.resolve("ok")
                        }
                        else
                        {
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('master.event', {
            url : '/events',
            templateUrl : require('extract-loader!html-loader!../views/event.html'),
            controller:'EventCtrl'

        })
        .state('master.create-new', {
            url : '/events/new',
            templateUrl : require('extract-loader!html-loader!../views/create-new.html'),
            controller:'EventCtrl'
        })
        .state('master.edit-event', {
            url : '/event/:id/edit',
            templateUrl : require('extract-loader!html-loader!../views/edit-event.html'),
            controller:'EventCtrl'
        })
        .state('master.view-event', {
            url : '/event/:id',
            templateUrl : require('extract-loader!html-loader!../views/edit-event.html'),
            controller:'EventCtrl'
        })
        .state('master.curate', {
            url : '/curate',
            templateUrl : require('extract-loader!html-loader!../views/curate.html'),
            controller:'CurateCtrl'
        })
        .state('master.selectMedia',{
            url : '/story/select-media',
            templateUrl : require('extract-loader!html-loader!../views/select-media.html'),
            controller:'SelectMediaCtrl'
        })
        .state('master.storyEdit', {
            url : '/story/edit',
            templateUrl : require('extract-loader!html-loader!../views/story-effects.html'),
            controller:'StoryEditCtrl'
        })
        .state('recording', {
            url : '/recording',
            templateUrl : require('extract-loader!html-loader!../views/pundit.html'),
            controller:'PunditCtrl'
        })
        .state('UserStory',{
            url : '/pundit-story/:storyId',
            templateUrl : require('extract-loader!html-loader!../views/pundit.html'),
            controller:'PunditCtrl'
        })
        .state('punditStory',{
            url : '/pundit-story/:storyId/:punditId',
            templateUrl : require('extract-loader!html-loader!../views/pundit.html'),
            controller:'PunditCtrl'
        })
        .state('ShareVideo',{
            url : '/share-video/:storyId',
            templateUrl : require('extract-loader!html-loader!../views/sharing.html'),
            controller:'SharingCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth()){
                            q.resolve("ok")
                        }
                        else{
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('SharePunditVideo',{
            url : '/share-video/:storyId/:punditId',
            templateUrl : require('extract-loader!html-loader!../views/sharing.html'),
            controller:'SharingCtrl',
            resolve :{
                data : function($q,AuthService){
                     var q = $q.defer();
                        if (AuthService.auth()){
                            q.resolve("ok")
                        }
                        else{
                            q.reject("not authorized");
                        }
                        return q.promise;
                }
            }
        })
        .state('punditActivation',{
            url : '/pundit-activate-account/:userId/:email/:punditId',
            controller:'PunditActivationCtrl'
        })
        .state('twitter-callback',{
            url : '/twitter-callback',
            controller:'CallbackCtrl'
        })
        .state('twitter-callback1',{
            url : '/twitter-callback1',
            controller:'CallbackCtrl'
        })
        .state('instagram-callback',{
            url : '/instagram-callback/:token',
            controller:'CallbackCtrl'
        })
        .state('insta-tag-callback',{
            url : '/insta-tag-callback/:token',
            controller:'CallbackCtrl'
        })
        .state('privacy-policy',{
            url : '/privacy-policy',
            templateUrl : require('extract-loader!html-loader!../views/privacy.html'),

        })
        .state('term-condition',{
            url : '/term-condition',
            templateUrl : require('extract-loader!html-loader!../views/termCondition.html'),

        })
        $urlRouterProvider.otherwise('/login');
}
