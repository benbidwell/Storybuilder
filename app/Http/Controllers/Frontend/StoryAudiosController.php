<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendController;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use App\Models\StoryAudios;
class StoryAudiosController extends FrontendController
{
   // for storing audio of stories from different sources
    public function SaveAudio(Request $request)
    {
       	try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

      			if(isset($request->audio)){

      				$audio = $request->audio;

                    $folderName = '/story_audio/story_'.$request->story_id;

                    $folderPath = public_path().$folderName;

                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }

                    $fileTypeArray = explode('/',$audio->getClientMimeType());

                    $newFileName = time().'-.'.$audio->getClientOriginalName();

                    $audio->move(public_path($folderName), $newFileName);

                    $storyAudio = new StoryAudios();

                    $storyAudio->story_id = $request->story_id;

                    $storyAudio->audio_url = $newFileName;

                    $storyAudio->original_name=$audio->getClientOriginalName().'.'.$audio->getClientOriginalExtension();

                    $storyAudio->length=$request->length;
                    $storyAudio->status = 1;

                    $storyAudio->save();

                    return $this->setResponseFormat(200, $this->notificationMessage('story_audio_saved_successfully','success'));

                }else{
                    return $this->setResponseFormat(400, $this->notificationMessage('story_audio_not_exists','error'));
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

    // for storing audio of stories from different sources
    public function getAllAudio($id)
    {
       	try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
            	$storyAudios = StoryAudios::orderBy('id', 'DESC')->where('status',1)->where('story_id',$id)->get();

            	return $this->setResponseFormat(200, 'All Story Audios',array('story_audio' => $storyAudios), NULL, NULL, 0);
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
