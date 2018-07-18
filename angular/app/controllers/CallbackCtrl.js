//CallbackCtrl Ctrollers
CallbackCtrl.$inject =  ['$rootScope' ,'$scope', '$state','AppService','OAuthService','$window','InstaService'];

export default function CallbackCtrl($rootScope ,$scope, $state,appService,oAuthService,$window,InstaService){

	switch($state.current.name) {
	    case 'twitter-callback':{

	    	var assoc  = {};
		  	var decode = function (s) { return decodeURIComponent(s.replace(/\+/g, " ")); };
		  	var queryString=$window.location.search.substring(1);
		  	var keyValues = queryString.split('&');

		  	for(var i in keyValues) {
			    var key = keyValues[i].split('=');
			    if (key.length > 1) {
			      assoc[decode(key[0])] = decode(key[1]);
			    }
		  	}

		  	if(assoc['denied']!=undefined){
			  	$window.opener.location.reload();
		      	$window.close();
		  		return ;
		  	}
		  	assoc['channel_type']='Account';
			if(assoc['oauth_token'] && assoc['oauth_verifier']){


			oAuthService.accessToken(assoc['oauth_token'],$rootScope.accessTokenSecret,assoc['oauth_verifier']).
			then(res=>{
				
			  var keyValues = res.data.split('&');
			   for(var i in keyValues) {
			      var key = keyValues[i].split('=');
			      if (key.length > 1) {
			        assoc[decode(key[0])] = decode(key[1]);
			      }
				}
				
				$window.localStorage.twit_token=assoc['oauth_token'];
				$window.localStorage.twit_token_s=assoc['oauth_token_secret'];
			  if($window.localStorage.twitter==undefined || $window.localStorage.twitter==''){
			  	var twitter=[];
			  	twitter.push(assoc);
			  } else {

			  	var twitter=JSON.parse($window.localStorage.twitter);

			  	if(twitter.map(function(el){return el['user_id'];}).indexOf(assoc['user_id'])!=-1){
			  		$window.opener.location.reload();
			      	$window.close();
			  		return ;
			  	}else{

			  		twitter.push(assoc);
			  	}
			  }
			  $window.localStorage.twit_test=1;
			  $window.localStorage.twitter=JSON.stringify(twitter);
			  var supertemp=[];

		  		oAuthService.getdata(assoc)
			      .then(res=>{
			        res.data.forEach((item)=>{
			        	var location;
			        	if(item.place!=null)
			        	location=item.place.full_name;
			          if(item.extended_entities){
			          item.extended_entities.media.forEach((item2)=>{
			            if(supertemp.length < 100){
			              if(item2.type=="photo"){
			                supertemp.push({
			                  type:'twitter',
			                  media_type:'photo',
			                  media_url:item2.media_url,
			                  created_at:item.created_at,
			                  id:assoc['user_id'],
			                  location:location
			                });
			              }
			              else if (item2.type=="video"){
			                supertemp.push({
			                  type:'twitter',
			                  media_type:'video',
			                  media_url:item2.video_info.variants[1].url,
			                  created_at:item.created_at,
												thumb_url:item2.media_url,
			                  id:assoc['user_id'],
			                  location:location
			                });
			              }
			            }
			          })
			      }
			        })

					$window.localStorage.temp=JSON.stringify(supertemp);
					$window.opener.location.reload();
		      		$window.close();
			      	});
				})
	  		}
	        break;
	    }
	    case 'instagram-callback':{

	    	var array = $.map($state.params, function(value, index) {
			    return [value];
			});

			var temp=array[0];
			var access_token = temp.replace('access_token=','');
			var url='https://api.instagram.com/v1/users/self/';

            InstaService.instagramRequest(url,access_token).then(res=> {
          		var instagram=[];
          		if($window.localStorage.instagram==undefined || $window.localStorage.instagram==''){
		          	instagram.push({
		          		user_id:'instagram' + res.data.data.username,
		          		channel_type:'Account',
		          		screen_name:res.data.data.username,
		          		access_token:access_token
		          	});
		        } else {
	          		instagram = JSON.parse($window.localStorage.instagram);
		          	instagram.push({
		          		user_id:'instagram' + res.data.data.username,
		          		channel_type:'Account',
		          		screen_name: res.data.data.username,
		          		access_token:access_token
		          	});
		        }

		        var instagram_media=[];
          		InstaService.instagramRequest('https://api.instagram.com/v1/users/self/media/recent/',access_token+'&count=100').then(resp=> {
								var allmedia=resp.data.data;

								if(resp.data.pagination!=undefined && resp.data.pagination.next_url!=undefined)
								{
									InstaService.instagramRequest2(resp.data.pagination.next_url).then(resp2=> {
										allmedia = allmedia.concat(resp2.data.data);
										if(resp2.data.pagination!=undefined && resp2.data.pagination.next_url!=undefined)
										{
											InstaService.instagramRequest2(resp2.data.pagination.next_url).then(resp3=> {
												allmedia= allmedia.concat(resp3.data.data)

											});

											startExecution();
										} else {

											startExecution();
										}
									});

								} else {

									startExecution();
								}

								var startExecution=function(){
									allmedia.forEach(function(item){
		          			if(instagram_media.length < 100){
			          			if(item.type=='image'){
			          				instagram_media.push({
				                      type: 'instagram',
				                      media_type: 'photo',
				                      media_url: item.images.standard_resolution.url,
				                      id:'instagram' + res.data.data.username,
				                      created_at: item.created_time,
				                      location:(item.location?item.location.name:item.location)
				                  	});
			          			} else if(item.type=='video'){
			          				instagram_media.push({
				                      type: 'instagram',
				                      media_type: 'video',
				                      media_url: item.videos.standard_resolution.url,
				                      id:'instagram' + res.data.data.username,
				                      created_at: item.created_time,
															thumb_url: item.images.low_resolution.url,
				                      location:(item.location?item.location.name:item.location)
				                  	});
			          			}
			          			else if(item.type=='carousel'){
			          				if(item.images){
			          					instagram_media.push({
					                      type: 'instagram',
					                      media_type: 'photo',
					                      media_url: item.images.standard_resolution.url,
					                      id:'instagram' + res.data.data.username,
					                      created_at: item.created_time,
					                      location:(item.location?item.location.name:item.location)
					                  	});
			          				} else if(item.videos){
			          					instagram_media.push({
					                      type: 'instagram',
					                      media_type: 'video',
					                      media_url: item.videos.standard_resolution.url,
					                      id:'instagram' + res.data.data.username,
					                      created_at: item.created_time,
																thumb_url: item.images.low_resolution.url,
					                      location:(item.location?item.location.name:item.location)
					                  	});
			          				}
			          			}
			          		}

		          		})
									$window.localStorage.temp=JSON.stringify(instagram_media);
		           		$window.localStorage.instagram = JSON.stringify(instagram);

				      		//$window.location.href="https://instagram.com/accounts/logout/";
				      		setTimeout(function(){
				      			$window.opener.location.reload();
				      			$window.close();
				      		},100)
								}




	            })
            })
            break;
	    }
	    case 'insta-tag-callback':{
	    	var array = $.map($state.params, function(value, index) {
			    return [value];
			});

			var temp=array[0];

			var access_token = temp.replace('access_token=','');

			var keyword=$window.localStorage.keyword;
			$window.localStorage.keyword=undefined;
			var url='https://api.instagram.com/v1/tags/'+keyword+'/media/recent';
	    	var instagram=[];

      		if($window.localStorage.instagram==undefined || $window.localStorage.instagram==''){
	          	instagram.push({
	          		user_id:'instagram' + keyword,
	          		channel_type:'Hashtag',
	          		screen_name:keyword,
	          		access_token:access_token
	          	});
	        } else {
          		instagram = JSON.parse($window.localStorage.instagram);
	          	instagram.push({
	          		user_id:'instagram' + keyword,
	          		channel_type:'Hashtag',
	          		screen_name: keyword,
	          		access_token:access_token
	          	});
	        }

	        var instagram_media=[];
      		InstaService.instagramRequest(url,access_token).then(resp=> {
          		resp.data.data.forEach(function(item){
          			if(instagram_media.length<10){
	          			if(item.type=='image'){
	          				instagram_media.push({
		                      type: 'instagram',
		                      media_type: 'photo',
		                      media_url: item.images.standard_resolution.url,
		                      id:'instagram' + keyword,
		                      created_at: item.created_time,
		                      location:item.location
		                  	});
	          			} else if(item.type=='video'){
	          				instagram_media.push({
		                      type: 'instagram',
		                      media_type: 'video',
		                      media_url: item.videos.standard_resolution.url,
		                      id:'instagram' + keyword,
		                      created_at: item.created_time,
		                      location:item.location
		                  	});
	          			}
	          		}

          		})

          		$window.localStorage.temp=JSON.stringify(instagram_media);
           		$window.localStorage.instagram = JSON.stringify(instagram);

	      		$window.location.href="https://instagram.com/accounts/logout/";
	      		setTimeout(function(){
	      			$window.opener.location.reload();
	      			$window.close();
	      		},100)


            })
            break;
	    }
	}
}
