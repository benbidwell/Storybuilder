OAuthService.$inject = ['$http','$rootScope','$window','Upload',];

export default function OAuthService($http,$rootScope,$window,Upload) {

	this.tw_access_token=(httpMethod, baseUrl, reqParams)=>{

		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
	        keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
	        keys.accessToken         = '892095854194065409-qNH4NFKdXQul3QatLfFtVm7y0CFt0vf',
	        keys.accessTokenSecret   = '9el0sAgjLDJGTyNja5j94xkkp5vRnMGJ8k74vsStsOx4v';

		let Authorization=this.getAuthorization(httpMethod, baseUrl, reqParams,keys);

		return $http.post($rootScope.apiUrl+'api/gettoken',{Authorization: Authorization})
            .then(function successCallback(response){

                return response;
            }, function errorCallback(response){
                console.log('response');
                return response;
            })

	}

	this.accessToken=(accessToken,accessTokenSecret,oauth_verifier)=>{

		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
	        keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
	        keys.accessToken         = accessToken,
	        keys.accessTokenSecret   = accessTokenSecret;

		let Authorization=this.getAuthorization('POST', 'https://api.twitter.com/oauth/access_token','',keys);


		return $http.post($rootScope.apiUrl+'api/twitterImport',{ Authorization: Authorization,
			oauth_verifier:oauth_verifier
			})
            .then(function successCallback(response){
                return response;
            }, function errorCallback(response){
                console.log('response');
                return response;
            })
	}

	this.getdata=(twitter)=>{

		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
      keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
      keys.accessToken         = twitter['oauth_token'],
      keys.accessTokenSecret   = twitter['oauth_token_secret'];

		let Authorization=this.getAuthorization('GET', 'https://api.twitter.com/1.1/statuses/user_timeline.json',{'screen_name':twitter['screen_name'],'count':100},keys);

		return $http.post($rootScope.apiUrl+'api/twitterImport1',{
				Authorization: Authorization,
				oauth_verifier:twitter['oauth_verifier'],
				screen_name:twitter['screen_name']
			})
			.then(function successCallback(response){
          return response;
      }, function errorCallback(response){
          console.log('response');
          return response;
      })
	}

	this.keywordSearch=(keyword)=>{
		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
      keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
      keys.accessToken         = '892095854194065409-qNH4NFKdXQul3QatLfFtVm7y0CFt0vf',
      keys.accessTokenSecret   = '9el0sAgjLDJGTyNja5j94xkkp5vRnMGJ8k74vsStsOx4v';

		let Authorization=this.getAuthorization('GET', 'https://api.twitter.com/1.1/search/tweets.json',{'q': keyword+'%20filter%3Amedia','count':200,'tweet_mode':'extended','include_entities':true},keys);

		return $http.post($rootScope.apiUrl+'api/twitterKeyword',{
				Authorization: Authorization,
				keyword:keyword
			})
      .then(function successCallback(response){
          return response;
      }, function errorCallback(response){
          return response;
      })
	}

	this.keywordReSearch=(keyword,max_id)=>{
		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
      keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
      keys.accessToken         = '892095854194065409-qNH4NFKdXQul3QatLfFtVm7y0CFt0vf',
      keys.accessTokenSecret   = '9el0sAgjLDJGTyNja5j94xkkp5vRnMGJ8k74vsStsOx4v';

		let Authorization=this.getAuthorization('GET', 'https://api.twitter.com/1.1/search/tweets.json',{'q': keyword+'%20filter%3Amedia','count':100,'tweet_mode':'extended','include_entities':1,'result_type':'recent','max_id':max_id},keys);

		return $http.post($rootScope.apiUrl+'api/twitterKeyword1',{
				Authorization: Authorization,
				keyword:keyword,
				max_id:max_id
			})
			.then(function successCallback(response){
          return response;
      }, function errorCallback(response){
          return response;
      })
	}

	this.getAuthorization=(httpMethod, baseUrl, reqParams,keys)=> {
	    // Get acces keys
	    const consumerKey       = keys.consumerKey,
	        consumerSecret      = keys.consumerSecret,
	        accessToken         = keys.accessToken  ,
	        accessTokenSecret   = keys.accessTokenSecret ;
	    // timestamp as unix epoch
	    let timestamp  = Math.round(Date.now() / 1000);
	    // nonce as base64 encoded unique random string
	    let nonce      = btoa(consumerKey + ':' + timestamp);
	    // generate signature from base string & signing key
	    let baseString = this.oAuthBaseString(httpMethod, baseUrl, reqParams, consumerKey, accessToken, timestamp, nonce);
	    let signingKey = this.oAuthSigningKey(consumerSecret, accessTokenSecret);
	    let signature  = this.oAuthSignature(baseString, signingKey);

	    // return interpolated string
	    let auth='OAuth '                                         +
	        'oauth_consumer_key="'  + consumerKey       + '", ' +
	        'oauth_nonce="'         + nonce             + '", ' +
	        'oauth_signature="'     + signature         + '", ' +
	        'oauth_signature_method="HMAC-SHA1", '              +
	        'oauth_timestamp="'     + timestamp         + '", ' +
	        'oauth_token="'         + accessToken       + '", ' +
	        'oauth_version="1.0"'                               ;
	    return auth;
	}

	this.getAuthorization1=(httpMethod, baseUrl, reqParams,keys)=> {
		// Get acces keys
		
	    const consumerKey       = keys.consumerKey,
	        consumerSecret      = keys.consumerSecret,
	        accessToken         = keys.accessToken  ,
	        accessTokenSecret   = keys.accessTokenSecret ;
	    // timestamp as unix epoch
	    let timestamp  = Math.round(Date.now() / 1000);
	    // nonce as base64 encoded unique random string
	    let nonce      = btoa(consumerKey + ':' + timestamp);
	    // generate signature from base string & signing key
	    let baseString = this.oAuthBaseString(httpMethod, baseUrl, reqParams, consumerKey, accessToken, timestamp, nonce);
	
		let signingKey = this.oAuthSigningKey(consumerSecret, accessTokenSecret);
	    let signature  = this.oAuthSignature(baseString, signingKey);

	    // return interpolated string
	    let auth='OAuth '                                         +
	        'oauth_consumer_key="'  + consumerKey       + '", ' +
	        'oauth_nonce="'         + nonce             + '", ' +
	        'oauth_signature="'     + signature         + '", ' +
	        'oauth_signature_method="HMAC-SHA1", '              +
	        'oauth_timestamp="'     + timestamp         + '", ' +
	        'oauth_token="'         + accessToken       + '", ' +
	        'oauth_version="1.0"'                               ;
	    return auth;
	}

	this.oAuthBaseString=(method, url, params, key, token, timestamp, nonce)=> {

	    return method
	            + '&' + this.percentEncode(url)
	            + '&' + this.percentEncode(this.genSortedParamStr(params, key, token, timestamp, nonce));

	};

	this.oAuthSigningKey=(consumer_secret, token_secret)=>{
	    return consumer_secret + '&' + token_secret;
	};

	this.oAuthSignature=(base_string, signing_key)=>{
		var signature = this.hmac_sha1(base_string, signing_key);
		
	    return this.percentEncode(signature);
	};

	this.percentEncode=(str)=> {
	  return encodeURIComponent(str).replace(/[!*()']/g, (character) => {
	    return '%' + character.charCodeAt(0).toString(16);
	  });
	};

	// HMAC-SHA1 Encoding, uses jsSHA lib
	var jsSHA = require("jssha");

	this.hmac_sha1=(string, secret)=>{
	    let shaObj = new jsSHA("SHA-1", "TEXT");
	    shaObj.setHMACKey(secret, "TEXT");
	    shaObj.update(string);
	    let hmac = shaObj.getHMAC("B64");
	    return hmac;
	};
	// Merge two objects
	this.mergeObjs=(obj1, obj2) =>{
	    for (var attr in obj2) {
	        obj1[attr] = obj2[attr];
	    }
	    return obj1;
	};
	// Generate Sorted Parameter String for base string params
	this.genSortedParamStr=(params, key, token, timestamp, nonce) => {
	    	let paramObj;
		if(params!=''){
			// Merge oauth params & request params to single object
		    	paramObj = this.mergeObjs(
		        {
		            oauth_consumer_key : key,
		            oauth_nonce : nonce,
		            oauth_signature_method : 'HMAC-SHA1',
		            oauth_timestamp : timestamp,
		            oauth_token : token,
		            oauth_version : '1.0'
		        },
		        params
		    );
		} else {
			// Merge oauth params & request params to single object
		    	paramObj = this.mergeObjs(
		        {
		            oauth_consumer_key : key,
		            oauth_nonce : nonce,
		            oauth_signature_method : 'HMAC-SHA1',
		            oauth_timestamp : timestamp,
		            oauth_token : token,
		            oauth_version : '1.0'
		        }
		    );
		}


	    // Sort alphabetically
	    let paramObjKeys = Object.keys(paramObj);
	    let len = paramObjKeys.length;
	    paramObjKeys.sort();

	    // Interpolate to string with format as key1=val1&key2=val2&...
	    let paramStr = paramObjKeys[0] + '=' + paramObj[paramObjKeys[0]];
	    for (var i = 1; i < len; i++) {
			console.log(paramObjKeys[i]);
	        paramStr += '&' + paramObjKeys[i] + '=' + this.percentEncode(decodeURIComponent(paramObj[paramObjKeys[i]]));
	    }
	    return paramStr;
	};


	this.instagramLogin=(username)=>{
		// return $http.post($rootScope.apiUrl+'api/instagramToken',{ username:username})
  //       .then(function successCallback(response){
  //           return response;
  //       }, function errorCallback(response){
  //           console.log('response');
  //           return response;
  //       })
  		return $http.get('https://api.instagram.com/oauth/authorize/?client_id=bda0f70c8b834b3e9fc3d7230a459b94&redirect_uri=http://localhost&response_type=token&_method=GET')
        .then(function successCallback(response){
            return response;
        }, function errorCallback(response){
            console.log('response');
            return response;
        })
        //https://api.instagram.com/oauth/authorize/?client_id=&redirect_uri=http%3A%2F%2Flocalhost&response_type=code
	}


	/* POST A VIDEO  */
	this.postVideoInit=(data)=>{
		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
			keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
			keys.accessToken         = data.oauth_token;
			keys.accessTokenSecret   = data.oauth_secret;
	
		let Authorization=this.getAuthorization1('POST', 'https://upload.twitter.com/1.1/media/upload.json',{"command":'INIT','total_bytes':data.size,'media_type':'video/mp4'},keys);
		let url="https://upload.twitter.com/1.1/media/upload.json";
		//let Authorization='OAuth oauth_consumer_key="JhuPb38KHgYgUSbMXBB8rmpUD",oauth_token="892095854194065409-qNH4NFKdXQul3QatLfFtVm7y0CFt0vf",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1516396901",oauth_nonce="UgELwO",oauth_version="1.0",oauth_signature="0Ocz9ZpEebxqhoNI5Lk%2FLWG4Lf4%3D"';
		return $http.post($rootScope.apiUrl+'api/post_curl_response',{
			Authorization: Authorization,
			url:url,
			post_data:"command=INIT&total_bytes="+data.size+"&media_type=video%2Fmp4",
			command:"init"
		})
		.then(function successCallback(response){
		  		return response;
		  
		}, function errorCallback(response){
			
			return response;
		});

	}

	this.postVideoAppend=(data)=>{

		let keys={};
			keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
			keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
			keys.accessToken         = data.oauth_token;
			keys.accessTokenSecret   = data.oauth_secret;

		let url="https://upload.twitter.com/1.1/media/upload.json";
		//console.log(data.media);
		//let base64_chunk=data.media.split(',')[1];
		let test=encodeURI('PCFkb2N0eXBlIGh0bWw+CjxodG1s');
		test=test.replace(/\+/g, '%2B')
		let base64_chunk=$window.btoa(data.media);
		//console.log(data.media.length)
		//base64_chunk=base64_chunk.replace(/\+/g, '%2B');
		base64_chunk=this.percentEncode(base64_chunk);
		//console.log(base64_chunk.length);
		let Authorization=this.getAuthorization1('POST', 'https://upload.twitter.com/1.1/media/upload.json',{"command":'APPEND','media_id':data.media_id,'media_type':'video/mp4','media':base64_chunk,'segment_index':data.segment},keys);

		
		return $http.post($rootScope.apiUrl+'api/post_curl_response',{
			Authorization: Authorization,
			url:url,
			post_data:"command=APPEND&media_id="+data.media_id+"&media_type=video%2Fmp4&media="+base64_chunk+"&segment_index="+data.segment,
			command:"append"
		})
		.then(function successCallback(response){
			return response;
		}, function errorCallback(response){
			return response;
		})
	  
	}

	this.postVideoStatus=(data)=>{
		
		let keys={};
		keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
		keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
		keys.accessToken         = data.oauth_token;
		keys.accessTokenSecret   = data.oauth_secret;

		let url="https://upload.twitter.com/1.1/media/upload.json?command=STATUS&media_id="+data.media_id;

		let Authorization=this.getAuthorization('GET', 'https://upload.twitter.com/1.1/media/upload.json',{"command":'STATUS','media_id':data.media_id},keys);

		return $http.post($rootScope.apiUrl+'api/getCurlResponseTwit',{
			Authorization: Authorization,
			url:url
		})
		.then(function successCallback(response){
			return response;
		}, function errorCallback(response){
			return response;
		})
	}

	this.postVideoFinalize=(data)=>{
		let keys={};
		keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
		keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
		keys.accessToken         = data.oauth_token;
		keys.accessTokenSecret   = data.oauth_secret;

		let url="https://upload.twitter.com/1.1/media/upload.json";

		let Authorization=this.getAuthorization1('POST', 'https://upload.twitter.com/1.1/media/upload.json',{"command":'FINALIZE','media_id':data.media_id},keys);

		return $http.post($rootScope.apiUrl+'api/post_curl_response',{
			Authorization: Authorization,
			url:url,
			post_data:"command=FINALIZE&media_id="+data.media_id,
			command:"finailize"
		})
		.then(function successCallback(response) {
			return response;
		}, function errorCallback(response) {
			return response;
		});
	}

	this.createVideoPost=(data)=>{
		let keys={};
		keys.consumerKey       	 = 'JhuPb38KHgYgUSbMXBB8rmpUD',
		keys.consumerSecret      = 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU',
		keys.accessToken         = data.oauth_token;
		keys.accessTokenSecret   = data.oauth_secret;

		let url="https://api.twitter.com/1.1/statuses/update.json";
		var status=data.title;
		if(data.pageurl){
			status+='  '+data.pageurl;
		}
		let Authorization=this.getAuthorization1('POST', 'https://api.twitter.com/1.1/statuses/update.json',{"status":status,'media_ids':data.media_id},keys);
		
		return $http.post($rootScope.apiUrl+'api/post_curl_response',{
			Authorization: Authorization,
			url:url,
			post_data:"status="+encodeURIComponent(status)+"&media_ids="+data.media_id
		})
		.then(function successCallback(response) {
			return response;
		}, function errorCallback(response) {
			return response;
		});
	}
}
