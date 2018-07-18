<?php
//
namespace App\Http\Controllers\Frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendController;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Response;
use App\Models\StoryMedias;
use DOMDocument;
use DomXPath;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Storage;

class StoryMediasController extends FrontendController
{

    // for storing media of stories from different sources
    public function saveMedia(Request $request)
    {

    	try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
            	$medias = json_decode($request->media);

            	if(count($medias) >0 ){
            		foreach($medias as $media){
            			$fileInfo = array_change_key_case(get_headers($media->media_url, TRUE));
			            $filesize = $fileInfo['content-length'];
			            $fileTypeArray = explode('/',$fileInfo['content-type']);
      						if($media->media == 'image'){
      							$maxSize = 2;
      						}else{
      							$maxSize = 10;
      						}

      						if($filesize / (1048576)> $maxSize){
      							//Nothing to be done for this media
      						}else{
      							//Store in the filesystem.
      							$folderName = public_path().'/story_media/story_'.$request->story_id;
      							//echo $folderName;die;

      							if (!file_exists($folderName)) {
      								mkdir($folderName, 0777, true);
      							}
      							$content = file_get_contents($media->media_url);
      							$fileName = time().'.'.$fileTypeArray[1];
      							//Store in the filesystem.
      							$fp = fopen("$folderName/".$fileName, "w");
      							fwrite($fp, $content);
      							fclose($fp);

      							StoryMedias::create(['story_id' => $request->story_id,'media_type' => $media->media,'media_source' => $media->media_source,'media_url' => $fileName,'status' => 1 ]);

      						}
            		}

            		return $this->setResponseFormat(200, $this->notificationMessage('Story_media_saved_successfully','success'));

            	}else{
            		 return $this->setResponseFormat(400, $this->notificationMessage('story_media_not_exists','error'));
            	}
            }
        }
        catch (TokenExpiredException $e) {
            return $this->setResponseFormat(400, 'Token has been expired');
        }
        catch (TokenInvalidException $e) {
            return $this->setResponseFormat(400, 'Invalid token sent');
        }
        catch (JWTException $e) {
            return $this->setResponseFormat(400, 'Token is missing, please send token');
        }
    }
    public function gettoken(Request $request)
    {

        $curl="curl -X POST \
          https://api.twitter.com/oauth/request_token \
          -H 'authorization: ".$request->Authorization."' \
          -H 'cache-control: no-cache'";

        return exec($curl);

    }
    public function twitterImport(Request $request){

        $curl="curl -X POST \
          https://api.twitter.com/oauth/access_token \
          -H 'authorization: ".$request->Authorization."' \
          -H 'content-type: application/x-www-form-urlencoded' \
          -H 'oauth_verifier: ".$request->oauth_verifier."' \
          -d 'oauth_verifier=".$request->oauth_verifier."'";

        return exec($curl);

    }
    public function twitterImport1(Request $request){

        $curl="curl -X GET \
          'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$request->screen_name."&count=100' \
          -H 'authorization: ".$request->Authorization."'";

        return exec($curl);

    }
    public function instagramToken(Request $request){
        $curl="curl -X GET \
  'https://api.instagram.com/oauth/authorize/?client_id=bda0f70c8b834b3e9fc3d7230a459b94&redirect_uri=http%3A%2F%2Flocalhost&response_type=code'";
        return exec($curl);
    }

    public function twitterKeyword(Request $request){
        $curl="curl -X GET \
              'https://api.twitter.com/1.1/search/tweets.json?q=".$request->keyword."%20filter%3Amedia&count=200&tweet_mode=extended&include_entities=true' \
              -H 'authorization: ".$request->Authorization."'";

        return exec($curl);
    }
    public function twitterKeyword1(Request $request){
        $curl="curl -X GET \
              'https://api.twitter.com/1.1/search/tweets.json?q=".$request->keyword."%20filter%3Amedia&count=100&tweet_mode=extended&include_entities=1&result_type=recent&max_id=".$request->max_id."' \
              -H 'authorization: ".$request->Authorization."'";

        return exec($curl);
    }

    public function googleKeywordSerach(Request $request){

        try{
            $arr=array();
            $goutteClient = new Client();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 60,
            ));
            $goutteClient->setClient($guzzleClient);
            if($request->type=='Hashtag'){
                $keyword='%23'.$request->keyword;
            } else {
                $keyword=$request->keyword;
            }
            $crawler = $goutteClient->request('GET', 'https://plus.google.com/s/'.$keyword.'/posts');

            $links=$crawler->filter('.E68jgf > img')->each(
                function($node){
                    return $node->attr('src');
                }
            );
            return $this->setResponseFormat(200, '',$links);
        }
        catch(Exception $e) {
          return $this->setResponseFormat(400, 'Error ');
        }
    }

    public function instaHashtagSerach(Request $request){

        try{
            $arr=array();
            $goutteClient = new Client();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 60,
            ));
            $goutteClient->setClient($guzzleClient);

            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/instagram/0/');

            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');

            $links=array();
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            //2nd Call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/instagram/1/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            //3rd Call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/instagram/2/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            //4th Call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/instagram/3/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            //5th Call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/instagram/4/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            return $this->setResponseFormat(200, '',$links);
        }
        catch(Exception $e) {
          return $this->setResponseFormat(400, 'Error ');
        }
    }
    public function facebookHashtagSerach(Request $request){

        try{
            $arr=array();
            $goutteClient = new Client();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 60,
            ));
            $goutteClient->setClient($guzzleClient);

            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/facebook/0/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');

            $links=array();
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            //2nd call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/facebook/1/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            // 3rd call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/facebook/2/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            // 4th call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/facebook/3/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            // 5th call
            $crawler = $goutteClient->request('GET', 'http://www.hashatit.com/load_more_new/'.$request->keyword.'/facebook/4/');
            $html=stripslashes($crawler->html());
            libxml_use_internal_errors(true);
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $xpath = new DOMXpath($dom);
            $imagelink=$xpath->query('//img[contains(@class, "image")]');
            $count=0;
            foreach ($imagelink as $node) {
              if($count!=0){
                array_push($links,$node->getAttribute('src'));
              }
              $count=1;
            }
            return $this->setResponseFormat(200, '',$links);
        }
        catch(Exception $e) {
          return $this->setResponseFormat(400, 'Error ');
        }
    }

    public function saveMediaUploaded(Request $request){

      try {
        
        	if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

               if(count($request->media) >0 ){
                    
                    $folderName = '/story_media/story_'.$request->story_id;

                    $folderPath = public_path().$folderName;


                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }

                    foreach($request->media as $media){

                        $fileTypeArray = explode('/',$media->getClientMimeType());

                        $newFileName = time().'-'.str_replace(' ','-',$media->getClientOriginalName());

                        $media->move(public_path($folderName), $newFileName);

                        StoryMedias::create(['story_id' => $request->story_id,'media_type' => $fileTypeArray[0],'media_source' => 'uploaded','media_url' => $newFileName,'status' => 1 ]);

                    }

                    return $this->setResponseFormat(200, $this->notificationMessage('Story_media_saved_successfully','success'));

                }else{
                         return $this->setResponseFormat(400, $this->notificationMessage('story_media_not_exists','error'));
                }
            }
        }
        catch (TokenExpiredException $e) {
            return $this->setResponseFormat(400, 'Token has been expired');
        }
        catch (TokenInvalidException $e) {
            return $this->setResponseFormat(400, 'Invalid token sent');
        }
        catch (JWTException $e) {
            return $this->setResponseFormat(400, 'Token is missing, please send token');
        }
    }


    public function savegMediaUploaded(Request $request){
               if($request->url && $request->story_id){
                      $folderName = '/story_media/story_'.$request->story_id;
                      $folderPath = public_path().$folderName;
                      $filename  =  time().'-gdrive'.'.png';
                      if (!file_exists($folderPath)) {
                          mkdir($folderPath, 0777, true);
                      }
                      $client = new \Google_Client();
                      $client->setAccessToken($request->access_token);
                      $driveService = new \Google_Service_Drive($client);
                      $parts = parse_url($request->url);
                      parse_str($parts['query'], $query);
                      $fileId = $query['id'];
                      $response = $driveService->files->get($fileId, array(
                  'alt' => 'media'));
                      $content = $response->getBody()->getContents();
                      file_put_contents($folderPath.'/'.$filename, $content);
                      StoryMedias::create(['story_id' => $request->story_id,'media_type' => 'image','media_source' => 'googledrive','media_url' => $filename,'status' => 1 ]);
                      return $folderName.'/'.$filename;
                  }else{
                       return $this->setResponseFormat(400, $this->notificationMessage('story_media_not_exists','error'));
                  }
           
    }
    public function allmedia($story_id,$limit = NULL,$offset= NULL){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                if(isset($story_id)){

                    $story_medias = StoryMedias::orderBy('id', 'DESC')->where(['status'=>1,'media_source'=>'uploaded'])->where('story_id',$story_id)->limit(@$limit)->offset(@$offset)->get();

                    $story_media_count = StoryMedias::where('story_id',$story_id)->where(['status'=>1,'media_source'=>'uploaded'])->get()->count();

                    // $story_medias['total_count'] = $story_media_count;

                    return $this->setResponseFormat(200, 'All Story Media',array('story_media' => $story_medias), NULL, NULL, 0);
                }else{
                    return $this->setResponseFormat(400, 'Please select any story to get its media');
                }
            }
        }
        catch (TokenExpiredException $e) {
            return $this->setResponseFormat(400, 'Token has been expired');
        }
        catch (TokenInvalidException $e) {
            return $this->setResponseFormat(400, 'Invalid token sent');
        }
        catch (JWTException $e) {
            return $this->setResponseFormat(400, 'Token is missing, please send token');
        }
    }

    public function saveMediaGooglePlus(Request $request){

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $list = $request->media;
                $video=array();
                foreach ($list as $key => $media) {
                    if(isset($media['indirect_link']) && $media['indirect_link'] != ''){

                        $str = file_get_contents($media['indirect_link']);

                        libxml_use_internal_errors(true);

                        $doc = new DOMDocument();

                        $doc->loadHTML($str);

                        $result = $doc->getElementsByTagName('img');

                        $video_url = '';

                        foreach($result as $node) {
                            $video_url = $node->getAttribute('src');
                            $video_url = explode('=',$video_url);
                            $new_video_url = $video_url[0].'=m18';
                        }
                        array_push($video,['id'=>$media['indirect_link'],'link'=>$new_video_url]);

                    }else{
                         return $this->setResponseFormat(400, $this->notificationMessage('story_media_not_exists','error'));
                    }


                }

                return $this->setResponseFormat(200, 'sdfsaf',$video);

                // if(isset($media) && $media != ''){

                //     $str = file_get_contents($media);

                //     libxml_use_internal_errors(true);

                //     $doc = new DOMDocument();

                //     $doc->loadHTML($str);

                //     $result = $doc->getElementsByTagName('img');

                //     $video_url = '';

                //     foreach($result as $node) {
                //         $video_url = $node->getAttribute('src');
                //     }

                //     // if($video_url != ''){

                //     //     $video_url = explode('=',$video_url);

                //     //     $new_video_url = $video_url[0].'=m18';

                //     //     $fileInfo = array_change_key_case(get_headers($new_video_url, TRUE));

                //     //     $fileTypeArray = explode('/',$fileInfo['content-type'][1]);

                //     //     //Store in the filesystem.
                //     //     $folderName = public_path().'/story_media/story_'.$request->story_id;

                //     //     if (!file_exists($folderName)) {
                //     //         mkdir($folderName, 0777, true);
                //     //     }

                //     //     $content = file_get_contents($new_video_url);

                //     //     $fileName = time().'.'.$fileTypeArray[1];

                //     //     //Store in the filesystem.
                //     //     $fp = fopen("$folderName/".$fileName, "w");
                //     //     fwrite($fp, $content);
                //     //     fclose($fp);

                //     //     StoryMedias::create(['story_id' => $request->story_id,'media_type' => 'video','media_source' => 'googleplus','media_url' => $fileName,'status' => 1 ]);

                //     //     return $this->setResponseFormat(200, $this->notificationMessage('Story_media_saved_successfully','success'));

                //     // }

                // }else{
                //      return $this->setResponseFormat(400, $this->notificationMessage('story_media_not_exists','error'));
                // }
            }
        }
        catch (TokenExpiredException $e) {
            return $this->setResponseFormat(400, 'Token has been expired');
        }
        catch (TokenInvalidException $e) {
            return $this->setResponseFormat(400, 'Invalid token sent');
        }
        catch (JWTException $e) {
            return $this->setResponseFormat(400, 'Token is missing, please send token');
        }
    }

    public function addPictureEffect(Request $request){
      try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $folderName = '/story_media/story_'.$request->story_id;

                $folderPath = public_path().$folderName;

                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }

                $media = $request->media;

                $newFileName = time().'-'.str_replace(' ','-',$media->getClientOriginalName());

                $media->move(public_path($folderName), $newFileName);

                $newMedia = StoryMedias::create(['story_id' => $request->story_id,'media_type' => 'image','media_source' => $request->media_source,'media_url' => $newFileName,'status' => 1 ]);

                $data = array('media_id'=>$newMedia->id,'media_url'=>$newMedia->media_url);

                return $this->setResponseFormat(200, $this->notificationMessage('Story_media_saved_successfully','success'),$data);
            }
        }
        catch (TokenExpiredException $e) {
            return $this->setResponseFormat(400, 'Token has been expired');
        }
        catch (TokenInvalidException $e) {
            return $this->setResponseFormat(400, 'Invalid token sent');
        }
        catch (JWTException $e) {
            return $this->setResponseFormat(400, 'Token is missing, please send token');
        }
    }
}
