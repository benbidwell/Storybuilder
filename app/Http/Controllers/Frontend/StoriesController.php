<?php
namespace App\Http\Controllers\Frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontendController;
use JWTAuth;
use App\Models\Story;
use App\Models\User;
use App\Models\StoryPunditVideo;
use App\Models\AdminSetting;
use Response;
use Validator;
use Mail;
use Carbon\Carbon;
use App\Mail\StoryCreated;
use App\Mail\StoryPublished;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class StoriesController extends FrontendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Incremnt story view
     * @param Intergar $story_id
     * @return \Illuminate\Http\Response
     */
     public function increment_view($story_id, $pundit_id='')
     {
         if($pundit_id){
           
            $pundit_story=StoryPunditVideo::find($pundit_id);
            $pundit_story->views+=1;
            $pundit_story->save();
         } else {
    
            $story=Story::find($story_id);
            $story->views+=1;
            $story->save();
         }

        return $this->setResponseFormat(200, 'Success!');
     }
     /**
     * Incremnt story share
     * @param Intergar $story_id
     * @return \Illuminate\Http\Response
     */
    public function increment_share($story_id, $pundit_id='')
    {
        if($pundit_id){
          
           $pundit_story=StoryPunditVideo::find($pundit_id);
           $pundit_story->shares+=1;
           $pundit_story->save();
        } else {
   
           $story=Story::find($story_id);
           $story->shares+=1;
           $story->save();
        }

       return $this->setResponseFormat(200, 'Success!');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                //if(!isset($request->story_details) || empty($request->story_details) || $request->story_details==''){
                    $rules = [
                        'story_title'   => 'required',
                        'story_picture' => 'mimes:jpeg,jpg,png|max:4096',
                        'event_id' =>'required',
                        ];

                    $messages = [
                        'story_title.required'      => $this->notificationMessage('create_story_story_title_required','error'),
                        'story_details.required'    => $this->notificationMessage('create_story_story_details_required','error'),
                        'story_picture.mimes'       => $this->notificationMessage('create_story_story_pictures_mimes','error'),
                        'story_picture.max'         => $this->notificationMessage('create_story_story_pictures_max','error'),
                        'event_id.required' => $this->notificationMessage('eventid_required','error'),
                    ];
                /*}else{
                    $rules = [
                    'story_title'           => 'required|min:15',
                    'story_picture'         => 'mimes:jpeg,jpg,png|max:4096',
                      'event_id' =>'required',  ];

                    $messages = [
                        'story_title.required'      => $this->notificationMessage('create_story_story_title_required','error'),
                        'story_title.min'           => $this->notificationMessage('create_story_story_title_min','error'),
                        'story_details.required'    => $this->notificationMessage('create_story_story_details_required','error'),
                        'story_picture.mimes'       => $this->notificationMessage('create_story_story_pictures_mimes','error'),
                        'story_picture.max'         => $this->notificationMessage('create_story_story_pictures_max','error'),
                        'event_id.required' => $this->notificationMessage('eventid_required','error'),
                    ];
                }*/

                $validator = Validator::make($request->all(), $rules,$messages);

                if(!$validator->fails()){
                    
                    $req_data = $request->all();
                    $req_data['video_size'] = 0;
                    
                    $story = Story::create($req_data);

                    if($request->story_picture){

                        $ext = $request->file('story_picture')->getClientOriginalExtension();

                        $storyPictureName = 'story-pic'.'-'.time().'.'.$ext;

                        $request->file('story_picture')->move(public_path("/story_pictures/"), $storyPictureName);

                    }else{
                        $storyPictureName = '';
                    }

                    $story->story_picture = $storyPictureName;

                    $story->save();

                    $dt = Carbon::now();

                    //email template for front end user
                    $userEmailTemplate = $this->EmailTemplate('story_created_user');

                    $email = $user->email;

                    //setting email content
                    $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'storyname' => $story->story_title,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                    //sending email to user
                    if($userEmailTemplate){
                     Mail::to($email)->send(new StoryCreated($emailContent,$userEmailTemplate->subject));
                    }
                    //email template for back end user
                    $userEmailTemplate = $this->EmailTemplate('story_created_admin');

                    $email = env('ADMIN_EMAIL_ID');

                    //setting email content
                    $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'storyname' => $story->story_title,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                    //sending email to admin
                    Mail::to($email)->send(new StoryCreated($emailContent,$userEmailTemplate->subject));

                    return $this->setResponseFormat(200, $this->notificationMessage('story_created_successfully','success'),$story);
                }else{
                    // on error return error messages array
                    return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
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

    public function getStoryDetails($id){
         try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                 return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
             }else{
                 $story = Story::find($id);
                 return $this->setResponseFormat(200, 'Story Details',array('story_details' => $story), NULL, NULL, 0);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        // try {
        //     if (! $user = JWTAuth::parseToken()->authenticate()) {
        //          return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
        //      }else{
                $story = Story::with('event')->find($id);
                if(!empty($story)){
                    //$numberofviews = $story->number_of_views;
                    //$number_of_views = $numberofviews+1;
                    //$story->number_of_views = $number_of_views;
                   // $story->save();
                    $returnFormat = $this->setResponseFormat(200, $this->notificationMessage('single_story','success'), $story);
                    return $returnFormat;
                }
                else{
                    $returnFormat = $this->setResponseFormat(400, $this->notificationMessage('story_not_found','error'),NULL,NULL,0);
                    return $returnFormat;
                }
        //     }
        // }
        // catch (TokenExpiredException $e) {
        //    return $this->setResponseFormat(400, 'Token has been expired');
        // }
        // catch (TokenInvalidException $e) {
        //     return $this->setResponseFormat(400, 'Invalid token sent');
        // }
        // catch (JWTException $e) {
        //     return $this->setResponseFormat(400, 'Token is missing, please send token');
        // }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }
    public function updatemystory(Request $request, $id){
        $story = Story::find($id);
        if($request->has('twitter'))
        $story->twitter=$request->twitter;
        if($request->has('youtube'))
        $story->youtube=$request->youtube;
        $story->save();
        return 1;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                 return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                $story = Story::find($id);
                if(!empty($story)){
                    $story->status = 1;
                    $story->save();

                    $returnFormat = $this->setResponseFormat(200,$this->notificationMessage('story_deleted_successfully','success'));
                    return $returnFormat;
                }else{
                    $returnFormat = $this->setResponseFormat(404, $this->notificationMessage('story_deletion_failed','error'), NULL, NULL, 0);
                    return $returnFormat;
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


    public function serachStories(Request $request,$event_id,$search = NULL){

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()){
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $id = $user->id;
                $stories = Story::whereHas('event', function($q) use($id,$event_id)
                {
                    $q->where('status',0)->where('user_id',$id)->where('event_id',$event_id);
                })->where('status',0)->where('publish_status',1)->where(function($query) use ($search)
                    {
                        $query->orWhere('story_title', 'LIKE', '%'.$search.'%')
                            ->orWhere('story_details', 'LIKE', '%'.$search.'%');
                    });
                $count = $stories->count();
                $stories =  $stories->get();
                if($count != 0){
                   return $this->setResponseFormat(200, 'Your Search Results',array('stories' =>$stories), NULL, NULL, 0);
                }
                else{
                    return $this->setResponseFormat(404, $this->notificationMessage('story_search_notfound','error'),NULL, NULL, 0);

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

    public function makeVideo(Request $request){

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $j = 0;
                $video_temp = array();
                $video_durations=array();
                $count=0;
                $all_duration=array();
                // Create Transition video of each slide
                foreach($request->video_array as $arr){

                    $prev=$count;

                    if($arr['media_type']=='photo'){
                        $video_temp[$j] = $this->_mkVideo($arr,$j,$request->story_id);
                    } else {
                        $video_temp[$j] = $this->_mkVideo($arr,$j,$request->story_id);
                    }

                    if($arr['media_type']=='photo'){
                        if(@$arr['image_visibility']){
                            $data=AdminSetting::first();
                            $trans=$data->trasition_time;
                            if(@$arr['transition']!='none' && @$arr['transition']){
                               
                                $count +=(int)@$arr['image_visibility']+($data->trasition_time || 2);
                               
                            } else {
                                $count +=(int)@$arr['image_visibility'];
                            }
                        } else {
                            $data=AdminSetting::first();
                            if(@$arr['transition']!='none' && @$arr['transition']){
                                $count +=(int)10+($data->trasition_time || 2);
                            } else{
                                $count +=10;
                            }        
                        }
                    } else {
                        $file = public_path().'/story_media/story_'.$request->story_id.'/'.$video_temp[$j];
                   
                        $file=str_replace(".ts",".mp4", $file);
                        $file=str_replace("video-ts-","video-trans-", $file);
                        $result = shell_exec('ffmpeg -i ' . escapeshellcmd($file) . ' 2>&1');
                        preg_match('/(?<=Duration: )(\d{2}:\d{2}:\d{2})\.\d{2}/', $result, $match);
                        //print_r($match); 
                        $time = explode(':', @$match[1]) + array(00,00,00);
                      // echo $count.'-';
                        $count+=@$time[1]*60+@$time[2];
                        //echo $count;
                    }
                    
                    
                    if($arr['media_type']=='photo'){
                        array_push($all_duration,$prev.'-'.$count);
                    } else {
                        array_push($all_duration,$prev.'-'.$count);
                        array_push($video_durations,$prev.'-'.$count);
                    }
                    $j++;
                }
              

               // die;
                // Create intermediate TEXT file 
                if(sizeof($video_temp) > 0 ){

                    $folderName = '/story_media/story_'.$request->story_id;

                    $folderPath = public_path().$folderName;

                    $filePath = $folderPath.'/'.time().'.txt';

                    $fp = fopen($filePath,"w+");

                    $fileString = '';

                    foreach($video_temp as $vtemp){
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            //echo 'This is a server using Windows!';
                            $folderPathUpdated = str_replace("\\","\\\\",$folderPath);
                            $folderPathUpdated = str_replace('/','\\\\',$folderPathUpdated);

                            $vtemp = str_replace('/',"\\\\",$vtemp);
                            $fileString .= 'file '. $folderPathUpdated.'\\\\'.$vtemp;
                          //  $fileString .= '\r';
                        } else {
                            $fileString.= 'file '. $folderPath.'/'.$vtemp;

                        }
                        $fileString .= "\r\n";
                    }
                    fwrite($fp,$fileString);
                    fclose($fp);
                }

                // Concat All video using intermediate text file
                $newFileName = '';

                if(isset($filePath)){
                    $newFileName = time().'.mp4';
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $newFolderPath = str_replace('/','\\',$folderPath);
                        $filePath = str_replace('/','\\',$filePath);
                        $newFolderPath = $newFolderPath.'\\';
                    }else{
                        $newFolderPath = $folderPath.'/';
                    }
                    $ffmpeg_command = 'ffmpeg -f concat -safe 0 -i "'.$filePath.'"  -c copy -bsf:a aac_adtstoasc  '.$newFolderPath.$newFileName;
                    //echo $ffmpeg_command; die;
                    shell_exec($ffmpeg_command);
                    $output=time().'1.mp4';
                    $ffmpeg_command="ffmpeg -i ".$newFolderPath.$newFileName." -strict -2 -c:v copy ".$newFolderPath.$output;
            
                    shell_exec($ffmpeg_command);
                }
              //  echo $newFileName;die;
                // unlink($filePath);
                // foreach($video_temp as $vtemp){
                //     unlink($folderPath.'/'.$vtemp);
                // }
                  
                // Add Audio to Video
                $data=$this->_addAudioToVideo($request->all(),$output,$video_durations, $all_duration);
                 
                return $this->setResponseFormat(200, $this->notificationMessage('story_video_created_successfully','success'),$data);
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
    
    public function _addAudioToVideo($data,$newFileName,$video_durations, $all_duration){
        
        $story_id=$data['story_id'];

        // Case when no audio is selected 
        if(!array_key_exists("audio_url",$data['sound'])){
        
            $story = Story::find( $story_id);
        
            $story->story_video_url = $newFileName;

            $story->story_published_video_url = $newFileName;

            $story->save();

            $data = array('new_video'=>$newFileName);

            return $data;
        }

        // Declaring  audio & video variable
        $audiArray=[];
        $videoArr=[];
        $firstAudioUrl='';
        if(@$data['sound']['audio_url']=='anewbeginning.mp3' || @$data['sound']['audio_url']=='buddy.mp3' || @$data['sound']['audio_url']=='clearday.mp3' || @$data['sound']['audio_url']=='happyrock.mp3' || @$data['sound']['audio_url']=='ukulele.mp3'){
            $audiArray['audio_url']=public_path().'/story_audio/';
        } else {
            $audiArray['audio_url']=public_path().'/story_audio/story_'.$story_id.'/';
        }
        
       // $audiArray['audio_url']=public_path().'/story_audio/story_'.$story_id.'/';
        $audiArray['audio_file']=$data['sound']['audio_url'];
        $videoArr['video_url'] = public_path().'/story_media/story_'.$story_id.'/';
        $videoArr['video_file'] = $newFileName;
        
       
        // case when audio is selected and play full track is disabled
        if($data['sound']['play_full_track'] != 1)  {    

            // Trimmed Audio file
            if($data['sound']['start_time']!=0 || $data['sound']['end_time']!=0){
                
                $trimmedAudio = time().'-audio1.mp3';
               
                $ffmpeg_command="ffmpeg -i '".$audiArray['audio_url'].$audiArray['audio_file']."' -acodec copy -ss 00:".((int)($data['sound']['start_time']/60)).":".($data['sound']['start_time']%60)." -t 00:".((int)(($data['sound']['end_time']-$data['sound']['start_time'])/60)).":".(($data['sound']['end_time']-$data['sound']['start_time'])%60)." ".$audiArray['audio_url'].$trimmedAudio;
             
                
                shell_exec($ffmpeg_command);
                $audiArray['audio_file']=$trimmedAudio;

            }
        } 
        
        // Adding Delay
        if($data['sound']['delay_sound'] > 0){
            $audiArray['audio_file'] = $this->_make_aud_with_delay( $audiArray['audio_url'],$audiArray['audio_file'],$data['sound']['delay_sound']);
        }
          
        $newAudioFile = '';
        // case when play during clip is enabled
        if($data['sound']['play_during_clips'] == 'true'){
            // clearing the previous voice on video
            $newVideoWithoutSound = time().'-w.mp4';
            
            $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -qscale 0 -an ".$videoArr['video_url'].$newVideoWithoutSound;

            shell_exec($ffmpeg_command);
            
        } else {
           
            if(count($video_durations)!=0){
                // Extract Audio 
                $exAudiofile='audio-'.time().'.mp3';
                $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -vn -acodec libmp3lame -ab 128k ".$audiArray['audio_url'].$exAudiofile;
                shell_exec($ffmpeg_command);
                //echo $ffmpeg_command;die;
                $extracted_audios=array();
                $current_position=0;
                
                foreach($all_duration as $vid_dur){
                    $dur=explode('-',$vid_dur);
                    // Trimmed Audio file
                    
                    if(!in_array($vid_dur, $video_durations)){
                        
                        $trimmedAudio = time().'-extendedAudio'.$dur[0].'.mp3';

                        $ffmpeg_command="ffmpeg -i '".$audiArray['audio_url'].$audiArray['audio_file']."' -acodec copy -ss 00:".((int)($current_position/60)).":".($current_position%60)." -t 00:".((int)(($dur[1]-$current_position)/60)).":".(($dur[1]-$current_position)%60)." ".$audiArray['audio_url'].$trimmedAudio;

                        shell_exec($ffmpeg_command);

                        
                        array_push($extracted_audios,$trimmedAudio);
                        
                    }  else {
                       
                        $trimmedAudio = time().'-extendedAudio'.$dur[0].'.mp3';

                        $ffmpeg_command="ffmpeg -i '".$audiArray['audio_url'].$exAudiofile."' -acodec copy -ss 00:".((int)($dur[0]/60)).":".($dur[0]%60)." -t 00:".((int)(($dur[1]-$dur[0])/60)).":".(($dur[1]-$dur[0])%60)." ".$audiArray['audio_url'].$trimmedAudio;

                        shell_exec($ffmpeg_command);

                      //  echo $ffmpeg_command;
                        array_push($extracted_audios,$trimmedAudio);
                    }
                
                    
                    $current_position=$dur[1];
                }
                
               
                //print_r($extracted_audios); die;
                // Create intermediate TEXT file 
                if(sizeof($extracted_audios) > 0 ){

                    $filePath = $audiArray['audio_url'].time().'.txt';

                    $fp = fopen($filePath,"w+");

                    $fileString = '';
                    
                    foreach($extracted_audios as $vtemp){
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            //echo 'This is a server using Windows!';
                            $folderPathUpdated = str_replace("\\","\\\\",$audiArray['audio_url']);
                            $folderPathUpdated = str_replace('/','\\\\',$folderPathUpdated);

                            $vtemp = str_replace('/',"\\\\",$vtemp);
                            $fileString .= 'file '. $folderPathUpdated.'\\\\'.$vtemp;
                          //  $fileString .= '\r';
                        } else {
                            $fileString.= 'file '. $audiArray['audio_url'].'/'.$vtemp;

                        }
                        $fileString .= "\r\n";
                    }
                    fwrite($fp,$fileString);
                    fclose($fp);
                }

                // Concat All audio using intermediate text file
                

                if(isset($audiArray['audio_url'])){
                    $newAudioFile = time().'.mp3';
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $newFolderPath = str_replace('/','\\',$audiArray['audio_url']);
                        $filePath = str_replace('/','\\',$audiArray['audio_url']);
                        $newFolderPath = $newFolderPath.'\\';
                    }else{
                        $newFolderPath = $audiArray['audio_url'].'/';
                    }
                    
                    $ffmpeg_command = 'ffmpeg -f concat -safe 0 -i "'.$filePath.'"  -c copy   '.$newFolderPath.$newAudioFile;
                   // echo $ffmpeg_command; die;
                    shell_exec($ffmpeg_command);
                  
                }
                
                $audiArray['audio_file']=$newAudioFile;
            }
            //$newAudioFile='newAudio-'.time().'.mp3';
            // echo $ffmpeg_command;
            // die();    

            // $ffmpeg_command = "ffmpeg -i ".$audiArray['audio_url'].$exAudiofile." -i '".$audiArray['audio_url'].$audiArray['audio_file']."' -filter_complex amerge -ac 2 -c:a libmp3lame ".$audiArray['audio_url'].$newAudioFile;

            // shell_exec($ffmpeg_command);
           
            
            
            $newVideoWithoutSound = time().'-w.mp4';

            $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -qscale 0 -an ".$videoArr['video_url'].$newVideoWithoutSound;
            shell_exec($ffmpeg_command);

            
        }
        
        
        // Embedded audio to video file
        $newVideo = time().'-video.mp4';
        $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$newVideoWithoutSound." -i " .$audiArray['audio_url'].$audiArray['audio_file'] . ' -c:v copy -af apad -shortest -strict -2 '.$videoArr['video_url'].$newVideo;

        shell_exec($ffmpeg_command);
     
       
         
        // Unlink all unneccessary file
        //unlink($videoArr['video_url'].$newVideoWithoutSound);

        if($newVideoWithoutSound!=$videoArr['video_file']){
           // unlink($videoArr['video_url'].$videoArr['video_file']);
        }

        if(@$trimmedAudio){
           // unlink($audiArray['audio_url'].$trimmedAudio);
        }

        // Save entry to story table
        $story = Story::find($story_id);
        $story->story_video_url = $videoArr['video_url'].$videoArr['video_file'];
        $story->story_audio_url = $audiArray['audio_url'].$data['sound']['audio_url'];
        $story->story_published_video_url = $newVideo;
        $story->save();

        $data = array('new_video'=>$newVideo);
        
        return $data;
    }

    public function _mkVideo($images,$j,$story_id){

        $folderName = '/story_media/story_'.$story_id.'/temp'.$j.'/';
        $folderPath = public_path().$folderName;
        $oldFolderPath = public_path().'/story_media/story_'.$story_id.'/';
        
        if(@$images['image_visibility']){
            $data=AdminSetting::first();
            $trans=$data->trasition_time;
            if(@$images['transition']!='none' && @$images['transition']){
       
                    $image_visibility =(float)@$images['image_visibility']+($data->trasition_time || 2);
                
            } else { 
                $image_visibility =(float)@$images['image_visibility'];
            }
        } else {
            $data=AdminSetting::first();
            if(@$images['transition']!='none' && @$images['transition']){
           
                    $image_visibility =(float)10+($data->trasition_time || 2);
                
                
            } else{

                $image_visibility =10;
            }        
        }

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
            chmod($folderPath, 0777);
        } else {
            chmod($folderPath, 0777);
        }
       
        if($images['media_type']=='video'){

            
            if(strpos($images['media_url'], 'http') !== false) {
               

                $test=file_put_contents($folderPath.'video1.mp4', file_get_contents( $images['media_url']));
            } else {
                $test=file_put_contents($folderPath.'video1.mp4', file_get_contents( public_path().$images['media_url']));
            }
            $video_name=time().'video.mp4';
            
            $ffmpeg_command='ffmpeg  -y -i '.$folderPath.'video1.mp4 -vf scale=360:240 -strict -2 '.$folderPath.$video_name;
            
            // Execute Command
            shell_exec($ffmpeg_command);
        
        } else if($images['media_type']=='photo'){

            if (strpos($images['media_url'], 'data:image') !== false) {

                list($type, $data) = explode(';', $images['media_url']);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                file_put_contents( $folderPath.'image-1.jpg', $data);

            }  else if(strpos($images['media_url'], 'http') !== false) {
               

                file_put_contents($folderPath.'image-1.jpg', file_get_contents($images['media_url']));
            } else {
                file_put_contents($folderPath.'image-1.jpg', file_get_contents( public_path().$images['media_url']));
            }

            $vid_dur=10;
            $video_name = 'video-'.time().'.mp4';
 
            $ffmpeg_command='ffmpeg -loop 1 -i '.$folderPath.'image-1.jpg -i '.public_path().'/story_media/silent.mp3 -c:v libx264 -c:a aac  -t '.$image_visibility.' -pix_fmt yuv420p -strict -2 -vf scale=360:240  '.$folderPath.$video_name;
           
            // Execute Command
            shell_exec($ffmpeg_command);
            
        }
      
        $trans_video_name = 'video-trans-'.time().'.mp4';

        switch(@$images['transition']){
            case 'fade':{
                $ffmpeg_command='ffmpeg -i '.$folderPath.$video_name.' -filter:v "fade=in:0:30, fade=out:200:30" -filter:a "afade=in:0:44100, afade=out:2601900:44100" -c:v libx264 -c:a aac  -strict -2 '.$folderPath.$trans_video_name; 
                
                // Execute Command
                shell_exec($ffmpeg_command);
                break;
            }
            case 'fade up':{
                $black='black'.time().'.mp4';
                $time=$image_visibility/10;
                //echo $image_visibility;
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/black.mp4 -filter:v "setpts='.$time.'*PTS" '.public_path().'/story_media/'.$black;
                shell_exec($ffmpeg_command);

                $temp="[0:v][1:v]overlay=y='if(lte(h-(t)*100,1),1,h-(t)*100)':x=0[out]";
                $temp2="'[out]'";
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/'.$black.' -i  '.$folderPath.$video_name.'   -filter_complex "'.$temp.'" -map '.$temp2.' -y -shortest '.$folderPath.$trans_video_name;
                shell_exec($ffmpeg_command);
               
                break;
            }
            case 'fade down':{
                $temp="[0:v][1:v]overlay=y='if(lte(-h+(t)*100,1),-h+(t)*100,1)':x=0[out]";
                $temp2="'[out]'";
            
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/black.mp4 -i  '.$folderPath.$video_name.'   -filter_complex "'.$temp.'" -map '.$temp2.' -y -shortest '.$folderPath.$trans_video_name;
                shell_exec($ffmpeg_command);
                break;
            }
            case 'fade left':{
                $temp="[0:v][1:v]overlay=x='if(lte(w-(t)*100,1),1,w-(t)*100)':y=0[out]";
                $temp2="'[out]'";
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/black.mp4  -i '.$folderPath.$video_name.' -filter_complex "'.$temp.'" -map '.$temp2.' -y '.$folderPath.$trans_video_name;
                shell_exec($ffmpeg_command);
                break;
            }
            case 'fade right':{
                $temp="[0:v][1:v]overlay=x='if(lte(-w+(t)*100,1),-w+(t)*100,1)':y=0 [out] ";
                $temp2="'[out]'";
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/black.mp4 -i '.$folderPath.$video_name.' -filter_complex "'.$temp.'" -map '.$temp2.' -y '.$folderPath.$trans_video_name;
                shell_exec($ffmpeg_command);
                break;
            }
            case 'rotate':{           

                // $ffmpeg_command='ffmpeg -i '.$folderPath.$video_name.' -vf vflip '.$folderPath.'temp.mp4';
                // shell_exec($ffmpeg_command);

                $temp="rotate='if(lt(t,PI),2*t,2*PI)'";
                $ffmpeg_command='ffmpeg -i '.$folderPath.$video_name.'  -vf "'.$temp.'" -strict -2  '.$folderPath.$trans_video_name;

                shell_exec($ffmpeg_command);
                // $ffmpeg_command='ffmpeg -i '.$folderPath.'temp.mp4 -c copy -metadata:s:v:0 rotate=180 '.$folderPath.$trans_video_name;

                // shell_exec($ffmpeg_command);
                //unlink($folderPath.'temp.mp4');
                break;
            }
            case 'flip x':{

                $black='black'.time().'.mp4';
                $time=$image_visibility/10;
                //echo $image_visibility;
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/black.mp4 -filter:v "setpts='.$time.'*PTS" '.public_path().'/story_media/'.$black;
                shell_exec($ffmpeg_command);

                $temp="[0]setsar=3/2[a];[1]setsar=3/2[b];[a][b]blend=all_expr='if(lte((W/2-sqrt((X-W/2)*(X-W/2)))+N*SW,W/2),A,B)'";
                $ffmpeg_command='ffmpeg -i  '.public_path().'/story_media/'.$black.' -i '.$folderPath.$video_name.' -lavfi "'.$temp.'"  -strict -2 '.$folderPath.$trans_video_name;
                shell_exec($ffmpeg_command);
                break;
            }
            case 'flip y':{

                $black='black'.time().'.mp4';
                $time=$image_visibility/10;
                //echo $image_visibility;
                $ffmpeg_command='ffmpeg -i '.public_path().'/story_media/black.mp4 -filter:v "setpts='.$time.'*PTS" '.public_path().'/story_media/'.$black;
                shell_exec($ffmpeg_command);

                $temp="[0]setsar=3/2[a];[1]setsar=3/2[b];[a][b]blend=all_expr='if(lte((W/2-sqrt((X-W/2)*(X-W/2)))+N*SW,W/2),A,B)'";
                $ffmpeg_command='ffmpeg -i  '.public_path().'/story_media/'.$black.' -i '.$folderPath.$video_name.' -lavfi "'.$temp.'"  -strict -2 '.$folderPath.$trans_video_name;
                shell_exec($ffmpeg_command);
               
                break;
            }
            case 'zoom':{
                    $temp="zoompan=z='zoom+0.002':d=25*4:s=320x240";
                    $ffmpeg_command='ffmpeg -i '.$folderPath.'image-1.jpg -i '.public_path().'/story_media/silent.mp3 -c:v libx264 -c:a aac  -t '.$image_visibility.' -filter_complex "'.$temp.'" -pix_fmt yuv420p  -strict -2 '.$folderPath.$trans_video_name;

                    // $temp="zoompan=z='1+(1.4*in/300)':x='70*in/300':y='190*in/300':d=1";
                    // $ffmpeg_command='ffmpeg -i '.$folderPath.$video_name.' -vf "'.$temp.'" -strict -2 '.$folderPath.$trans_video_name;

                    shell_exec($ffmpeg_command);
                    //echo $ffmpeg_command;die;
                break;
            }
            case 'overlay option':{
                $ffmpeg_command='ffmpeg -i '.$folderPath.$video_name.'  '.$folderPath.$trans_video_name; 
                shell_exec($ffmpeg_command);
                break;
            }
            default:{
                $ffmpeg_command='ffmpeg -i '.$folderPath.$video_name.' -vcodec copy -acodec copy  '.$folderPath.$trans_video_name; 
                shell_exec($ffmpeg_command);
                break;
            }
        }
     
        $ts_video_name = 'video-ts-'.time().'.ts';

        $ffmpeg_command="ffmpeg -y -i ".$folderPath.$trans_video_name." -c copy  -bsf:v h264_mp4toannexb -bsf:a aac_adtstoasc -f mpegts ".$folderPath.$ts_video_name;
        shell_exec($ffmpeg_command);
        
        //unlink($folderPath.$video_name);

        //unlink($folderPath.$trans_video_name);
     
        return 'temp'.$j.'/'.$ts_video_name;

        
    }
    /* public function addAudioToVideo(Request $request){
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                if(!array_key_exists("audio_url",$request->sound)){
                    $videoArr = $this->_upload_video($request->video, $request->story_id);

                    $newVideo = time().'-video.mp4';
                    $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -qscale 0 ".$videoArr['video_url'].$newVideo;

                    shell_exec($ffmpeg_command);

                    $story = Story::find($request->story_id);

                    $story->story_video_url = @$videoUrl;

                    $story->story_audio_url = @$audioFile;

                    $story->story_published_video_url = $newVideo;

                    $story->save();

                    $data = array('new_video'=>$newVideo);

                    return $this->setResponseFormat(200, $this->notificationMessage('story_audio_added_successfully','success'),$data);
                    exit;
                }
                if($request->sound['play_full_track'] == 'true'){

                    $audiArray=[];
                    $videoArr = $this->_upload_video($request->video, $request->story_id);
                    $audiArray['audio_url']=public_path().'/story_audio/story_'.$request->story_id.'/';
                    $audiArray['audio_file']=$request->sound['audio_url'];

                    if($request->sound['delay_sound'] > 0){
                       $audiArray['audio_file'] = $this->_make_aud_with_delay($audiArray['audio_url'],$audiArray['audio_file'],$request->sound['delay_sound']);
                    }

                    // clearing the previous voice on video
                    $newVideoWithoutSound = time().'-w.mp4';

                    //$ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -c copy -an ".$videoArr['video_url'].$newVideoWithoutSound;
                    $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -qscale 0 -an ".$videoArr['video_url'].$newVideoWithoutSound;

                    shell_exec($ffmpeg_command);

                    $newVideo = time().'-video.mp4';

                    $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$newVideoWithoutSound." -i " .$audiArray['audio_url'].$audiArray['audio_file'] . ' -c:v copy -af apad -shortest -strict -2 '.$videoArr['video_url'].$newVideo;

                    shell_exec($ffmpeg_command);

                    unlink($videoArr['video_url'].$newVideoWithoutSound);
                    unlink($videoArr['video_url'].$videoArr['video_file']);

                    $story = Story::find($request->story_id);

                    $story->story_video_url = $videoArr['video_url'].$videoArr['video_file'];

                    $story->story_audio_url = $audiArray['audio_url'].$request->sound['audio_url'];

                    $story->story_published_video_url = $newVideo;

                    $story->save();

                    $data = array('new_video'=>$newVideo);

                    return $this->setResponseFormat(200, $this->notificationMessage('story_audio_added_successfully','success'),$data);

                }else{

                    if($request->sound['play_during_clips'] == 'true'){
                         //case when play during clips in enabled
                        $audiArray=[];
                        $videoArr = $this->_upload_video($request->video, $request->story_id);
                        $audiArray['audio_url']=public_path().'/story_audio/story_'.$request->story_id."/";
                        $audiArray['audio_file']=$request->sound['audio_url'];

                        if($request->sound['start_time']!=0 || $request->sound['end_time']!=0){
                            // Trim audio
                            $trimmedAudio = time().'-audio1.mp3';

                            $ffmpeg_command="ffmpeg -i ".$audiArray['audio_url'].$audiArray['audio_file']." -acodec copy -ss 00:".((int)($request->sound['start_time']/60)).":".($request->sound['start_time']%60)." -t 00:".((int)(($request->sound['end_time']-$request->sound['start_time'])/60)).":".(($request->sound['end_time']-$request->sound['start_time'])%60)." ".$audiArray['audio_url'].$trimmedAudio;

                            shell_exec($ffmpeg_command);
                            $audiArray['audio_file']=$trimmedAudio;

                        }

                        if($request->sound['delay_sound'] > 0){

                           $audiArray['audio_file'] = $this->_make_aud_with_delay($audiArray['audio_url'],$audiArray['audio_file'],$request->sound['delay_sound']);
                        }



                        // clearing the previous voice on video
                        $newVideoWithoutSound = time().'-w.mp4';

                        $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$videoArr['video_file']." -qscale 0 -an ".$videoArr['video_url'].$newVideoWithoutSound;

                        shell_exec($ffmpeg_command);

                        $newVideo = time().'-video.mp4';

                        $ffmpeg_command = "ffmpeg -i ".$videoArr['video_url'].$newVideoWithoutSound." -i " .$audiArray['audio_url'].$audiArray['audio_file'] . ' -c:v copy -af apad -shortest -strict -2 '.$videoArr['video_url'].$newVideo;

                        shell_exec($ffmpeg_command);
                        if($trimmedAudio){
                            unlink($audiArray['audio_url'].$trimmedAudio);
                        }

                        $story = Story::find($request->story_id);

                        $story->story_video_url = $videoArr['video_url'].$videoArr['video_file'];

                        $story->story_audio_url = $audiArray['audio_url'].$request->sound['audio_url'];

                        $story->story_published_video_url = $newVideo;

                        $story->save();

                        $data =  array('new_video'=>$newVideo);

                        return $this->setResponseFormat(200, $this->notificationMessage('story_audio_added_successfully','success'),$data);

                    }else{
                        //case when play during clips is disabled


                    }
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

    public function _upload_audio($audioArray, $story_id){
         // file upload of audio on to server
        if(isset($audioArray)){

            $audio = $audioArray;

            $folderName = '/story_audio/story_'.$story_id;

            $audioPath = public_path().$folderName;

            if (!file_exists($audioPath)) {
                mkdir($audioPath, 0777, true);
            }

            $newFileName = time().'-'.$audio->getClientOriginalName();

            $audio->move(public_path($folderName), $newFileName);

            return array('audio_url'=>$audioPath,'audio_file'=>$newFileName);
        }
    }
    public function _upload_video($videoBlob, $story_id){
         // file upload of audio on to server
        if(isset($videoBlob)){

            $video = $videoBlob;

            $folderName = '/story_videos/story_'.$story_id."/";

            $videoPath = public_path().$folderName;

            if (!file_exists($videoPath)) {
                mkdir($videoPath, 0777, true);
            }

            $newFileName = time().'-'.$video->getClientOriginalName();

            $video->move(public_path($folderName), $newFileName);

            return array('video_url'=>$videoPath,'video_file'=>$newFileName);
        }
    }*/

    public function _make_aud_with_delay($audioPath, $newFileName,$delay_time ){

        $silenceAudioName = time().'-silence.mp3';

        // making silent audio
        $ffmpeg_command = "ffmpeg -f lavfi -i aevalsrc=0:0::duration=".$delay_time." -ab 320k ".$audioPath.$silenceAudioName;

        shell_exec($ffmpeg_command);

        $audioFile = time().'-audio.mp3';

            // contactinating silent and uploaded audio together
        $ffmpeg_command = 'ffmpeg -i concat:"'.$audioPath.$silenceAudioName.'|'.$audioPath.$newFileName.'" -codec copy '.$audioPath.$audioFile;

        shell_exec($ffmpeg_command);

        //unlink($audioPath.'/'.$silenceAudioName);

        //unlink($audioPath.'/'.$newFileName);

        return $audioFile;
    }


    public function addWaterMark(Request $request){
       
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $story = Story::find($request->story_id);
                $video = $story->story_published_video_url;
                $setting=AdminSetting::first();
               
                if($setting->is_watermark){
                    $folderPath = public_path().'/';
                   
                    $waterMarkImage = $folderPath."logo-watermark.png";
                    
                    $videoFolderPath = $folderPath."story_videos/story_".$request->story_id.'/';
                    if (!file_exists($videoFolderPath)) {
                        mkdir($videoFolderPath, 0777, true);
                    }
                    $newVideoName = time().'-video.mp4';

                    $newVideoPath = $videoFolderPath.$newVideoName;

                    $oldVideoPath = $folderPath."story_media/story_".$request->story_id.'/'.$video;

                    $ffmpeg_command = 'ffmpeg -i '.$oldVideoPath.' -i '. $waterMarkImage.' -filter_complex "overlay=5:5" -strict -2 '.$newVideoPath;

                    shell_exec($ffmpeg_command);

                    $story->story_published_video_url = $newVideoName;

                    $data = array('new_video_url' => $newVideoName);
                }
                else{
                    $folderPath = public_path().'/';
                   
                    $waterMarkImage = $folderPath."logo-watermark.png";
                    
                    $videoFolderPath = $folderPath."story_videos/story_".$request->story_id.'/';
                    if (!file_exists($videoFolderPath)) {
                        mkdir($videoFolderPath, 0777, true);
                    }
                    $newVideoName = time().'-video.mp4';

                    $newVideoPath = $videoFolderPath.$newVideoName;

                    $oldVideoPath = $folderPath."story_media/story_".$request->story_id.'/'.$video;

                    $ffmpeg_command = 'ffmpeg -i '.$oldVideoPath.' -strict -2 '.$newVideoPath;

                    shell_exec($ffmpeg_command);

                    $story->story_published_video_url = $newVideoName;

                    $data = array('new_video_url' => $newVideoName);
                }
               

                

                $story->publish_status = 1;

                $story->publish_date = date('Y-m-d H:i:s');
                $story->video_size= filesize($newVideoPath);
                $story->save();

                $dt = Carbon::now();

                // Email to user for publishing the story
                $userEmailTemplate = $this->EmailTemplate('story_published_user');

                $email = $user->email;

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'storyname' => $story->story_title,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                //sending email to user
                if($userEmailTemplate){
                Mail::to($email)->send(new StoryPublished($emailContent,$userEmailTemplate->subject));
                }
                //Email to Admin for publishing the story
                $userEmailTemplate = $this->EmailTemplate('story_published_admin');

                $email = env('ADMIN_EMAIL_ID');

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'storyname' => $story->story_title,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                //sending email to admin
                 if($userEmailTemplate){
                Mail::to($email)->send(new StoryPublished($emailContent,$userEmailTemplate->subject));
                }
                return $this->setResponseFormat(200, $this->notificationMessage('story_published_successfully','success'),$data);

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

/*     public function addTextEffectOnVideo(Request $request){
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $cordinates = $this->_x_y_cordinates($request->x_position,$request->y_position);

                $x = $cordinates['x_cord'];

                $y = $cordinates['y_cord'];

                $opacity = ( $request->opacity / 100);

                $opacity = number_format($opacity, 1);

                $folderPath = public_path().'/';

                if($request->font_family == 'Arial'){
                    $font =  $folderPath.'fonts/arial.ttf';
                }else if($request->font_family == 'Tahoma' ){
                    $font =  $folderPath.'fonts/tahoma.ttf';
                }else if($request->font_family == 'Serif' ){
                    $font =  $folderPath.'fonts/FreeSerif.ttf';
                }else{
                    $font =  $folderPath.'fonts/OpenSans-Regular.ttf';
                }

                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $fontArray = explode('\\',$font);
                    $i = 0;
                    $f = '';
                    foreach($fontArray as $newFont){
                        if($i == 0){
                            $newFont_1 = explode(':',$newFont);
                            $f = $newFont_1[0] .'\:';
                        }else{
                            $f.='\\\\'.$newFont;
                        }
                        $i++;
                    }
                    $font = str_replace('/','\\\\',$f);
                }

                $story = Story::find($request->story_id);

                $video = $story->story_video_url;

                $videoFolderPath = $folderPath."story_media/story_".$request->story_id.'/';

                $newVideoName = time().'-video.mp4';

                $newVideoPath = $videoFolderPath.$newVideoName;

                $oldVideoPath = $videoFolderPath.$video;

                $ffmpeg_command = 'ffmpeg -i '.$oldVideoPath.' -vf drawtext="fontfile=\''. $font.'\': text=\''. $request->draw_text . '\': fontcolor=\''.$request->color.'\'@'.$opacity.':fontsize='.$request->font_size.':x='.$x.':y='.$y.'" -codec:a copy '.$newVideoPath;

                shell_exec($ffmpeg_command);

                $data = array('new_video_url' => $newVideoName);

                return $this->setResponseFormat(200, $this->notificationMessage('story_video_text_effect','success'),$data);
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

    public function _x_y_cordinates($x_position,$y_position){

        $total_x = env('VIDEO_X_SIZE');

        $total_y = env('VIDEO_Y_SIZE');

        $x_perscentage = $x_position / $total_x * 100;

        $y_perscentage = $y_position / $total_y * 100;

        $xnew_position = round($x_perscentage / 100 * env('NEW_VIDEO_X_SIZE'));

        $ynew_position = round($y_perscentage / 100 * env('NEW_VIDEO_Y_SIZE'));

        $data = array('x_cord' => $xnew_position, 'y_cord' => $ynew_position );

        return $data;
    }


    public function storyPublishVideo(Request $request){

        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                $story = Story::find($request->story_id);

               // dd($story);

                $story->publish_status = 1;

                $story->publish_date = date('Y-m-d H:i:s');

                $story->save();

                $dt = Carbon::now();

                // Email to user for publishing the story
                $userEmailTemplate = $this->EmailTemplate('story_published_user');

                $email = $user->email;

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'storyname' => $story->story_title,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                //sending email to user
                Mail::to($email)->send(new StoryPublished($emailContent,$userEmailTemplate->subject));

                //Email to Admin for publishing the story
                $userEmailTemplate = $this->EmailTemplate('story_published_admin');

                $email = env('ADMIN_EMAIL_ID');

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'storyname' => $story->story_title,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                //sending email to admin
                Mail::to($email)->send(new StoryPublished($emailContent,$userEmailTemplate->subject));

                return $this->setResponseFormat(200, $this->notificationMessage('story_published_successfully','success'));

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
    } */


     public function allPublishedVideos(){

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                 return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
             }else{
                $id = $user->id;
                $stories = Story::whereHas('event', function($q) use($id)
                {
                    $q->where('status',0)->where('user_id',$id);
                })->where('status',0)->where('publish_status',1)->orderBy('id', 'DESC')->get();

                 return $this->setResponseFormat(200, 'All Published Stories',array('stories' => $stories), NULL, NULL, 0);

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

    //Remove Story Picture
    public function removeStoryPicture(Request $request, $id){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                    $updateStory = Story::find($id);
                    if($updateStory){
                    if(file_exists(public_path().'/story_pictures/'.$updateStory->story_picture)){
                        @unlink(public_path().'/story_pictures/'.$updateStory->story_picture);
                    }
                        $updateStory->story_picture = '';
                        $updateStory->save();
                        return $this->setResponseFormat(200, $this->notificationMessage('story_pic_removed','success'));
                    }else{
                        return $this->setResponseFormat(200, $this->notificationMessage('story_pic_notremoved','error'));
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

    //story Searching api for Autocomplete
    //search event function
    public function AutoCompleteSearching()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()){
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                $id = $user->id;
                $stories = Story::whereHas('event', function($q) use($id)
                {
                    $q->where('status',0)->where('user_id',$id);
                })->where('status',0);
                $count = $stories->count();
                $stories =  $stories->get(['story_title  AS name','story_details  AS description', 'id']);
                if($count != 0){
                   return $this->setResponseFormat(200, 'Your Search Results',array('stories' =>$stories), NULL, NULL, 0);
                }
                else{
                    return $this->setResponseFormat(404, $this->notificationMessage('story_search_notfound','error'),NULL, NULL, 0);

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

    //stories confirmation message
    public function delete_story_confirmation(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                  return $this->setResponseFormat(200, $this->notificationMessage('delete_story_confirmation','confirmation'));
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

    public function save_recording_confirmation(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                  return $this->setResponseFormat(200, $this->notificationMessage('save_recording','confirmation'));
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
    public function allstories(Request $request, $event_id){
       try {
           if (! $user = JWTAuth::parseToken()->authenticate()) {
               return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
           }else{

               $limit = $request['limit'];
               $offset = $request['offset'];
               $event_id = $request->event_id;
               if($limit == ''){
                   $limit = 8;
               }
               $id = $user->id;
               $stories = Story::whereHas('event', function($q) use($id,$event_id)
               {
                   $q->where('status',0)->where('user_id',$id)->where('event_id',$event_id);
               })->with('pundit_story')->where('status',0)->where('publish_status',1)->where('event_id',$event_id)->limit($limit)->offset($offset);

               //$stories = Story:: orderBy('id', 'DESC')->where('status',0)->where('event_id',$event_id)->limit($limit)->offset($offset);
               $count = $stories->count();
               $stories = $stories->get();
               if(count($stories) != 0){
               $returnFormat = $this->setResponseFormat(200, 'All Stories',array('stories' =>$stories), NULL, NULL, 0);
                   return $returnFormat;
               }else{
                   $returnFormat = $this->setResponseFormat(400, 'Records not found.', NULL, NULL, 0);
                   return $returnFormat;
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
    // public function allstories(Request $request, $event_id){
    //     try {
    //         if (! $user = JWTAuth::parseToken()->authenticate()) {
    //             return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
    //         }else{

    //             $limit = $request['limit'];
    //             $offset = $request['offset'];
    //             $event_id = $request->event_id;
    //             if($limit == ''){
    //                 $limit = 8;
    //             }
    //             $id = $user->id;
    //             $stories = Story::whereHas('event', function($q) use($id,$event_id)
    //             {
    //                 $q->where('status',0)->where('user_id',$id)->where('event_id',$event_id);
    //             })->where('status',0)->where('event_id',$event_id)->limit($limit)->offset($offset);

    //             //$stories = Story:: orderBy('id', 'DESC')->where('status',0)->where('event_id',$event_id)->limit($limit)->offset($offset);
    //             $count = $stories->count();
    //             $stories = $stories->get();
    //             if($count != 0){
    //             $returnFormat = $this->setResponseFormat(200, 'All Stories',array('stories' =>$stories), NULL, NULL, 0);
    //                 return $returnFormat;
    //             }else{
    //                 $returnFormat = $this->setResponseFormat(400, 'Records not found.', NULL, NULL, 0);
    //                 return $returnFormat;
    //             }
    //         }
    //     }
    //     catch (TokenExpiredException $e) {
    //         return $this->setResponseFormat(400, 'Token has been expired');
    //     }
    //     catch (TokenInvalidException $e) {
    //         return $this->setResponseFormat(400, 'Invalid token sent');
    //     }
    //     catch (JWTException $e) {
    //         return $this->setResponseFormat(400, 'Token is missing, please send token');
    //     }
    // }
}
