/*
	DropboxService
*/
DropboxService.$inject = ['$http','$rootScope','$window','Upload',];
export default function DropboxService($http,$rootScope,$window,Upload) {

	// Login to DropBox
	this.dropboxLogin=(newAccount)=>{
		var options = {

		    // Required. Called when a user selects an item in the Chooser.
		    success: function(files) {
		    	$rootScope.dbx.usersGetCurrentAccount()
			    .then(function(response) {

						var supertemp=[];
		    		files.forEach((item)=>{
	                supertemp.push({
	                  	type:'dropbox',
	                  	media_url:item.link,
	                  	media_type:(item.name.search(/.mp4/)==-1)?'photo':'video',
	                  	id:'Dropbox'+newAccount
	                });
        	 	})
	      	 	$rootScope.newStory=JSON.parse($window.localStorage.newStory);
	        	if($rootScope.newStory.media==undefined){
							$rootScope.newStory.media=[];
						}
	        	$rootScope.newStory.media=$rootScope.newStory.media.concat(supertemp);
		     		$window.localStorage.newStory=JSON.stringify($rootScope.newStory);

			    	if($window.localStorage.dropbox==undefined || $window.localStorage.dropbox==''){
					  	var dropbox=[];
					  	dropbox.push({user_id:'Dropbox'+newAccount,screen_name:newAccount});
				  	} else {
					  	var dropbox=JSON.parse($window.localStorage.dropbox);
					  	dropbox.push({user_id:'Dropbox'+newAccount,screen_name:newAccount});
				  	}
				  	$window.localStorage.dropbox=JSON.stringify(dropbox);
				  	$rootScope.dropbox=JSON.stringify(dropbox);
				  	$window.location.reload();
			    })
			    .catch(function(error) {
			      console.log(error);
			    });
		    },

		    // Optional. Called when the user closes the dialog without selecting a file
		    // and does not include any parameters.
		    cancel: function() {

		    },

		    linkType: "direct", // or "preview"

		    // Optional. A value of false (default) limits selection to a single file, while
		    // true enables multiple file selection.
		    multiselect: true, // or false

		    // Optional. This is a list of file extensions. If specified, the user will
		    // only be able to select files with these extensions. You may also specify
		    // file types, such as "video" or "images" in the list. For more information,
		    // see File types below. By default, all extensions are allowed.
		    extensions: ['.jpg', '.png', '.jpeg','.mp4'],
		};
		Dropbox.choose(options);
	}

}
