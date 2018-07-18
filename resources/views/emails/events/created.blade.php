@component('mail::message')
<?php 
$name = $content['userdata']['first_name']." ".$content['userdata']['last_name'];
$search = array('##USERNAME##','##EVENTNAME##','##CREATED_DATE##');
$replace = array($name,$content['content']['event_title'],$content['content']['created_at']);
$emailBody = str_replace( $search, $replace, $content['body']['description']);
echo $emailBody;?>
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent