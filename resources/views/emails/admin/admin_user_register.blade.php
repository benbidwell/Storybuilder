@component('mail::message')
<?php 
$name = $content['content']['first_name']." ".$content['content']['last_name'];
$search = array('##UserName##','##UserEmail##','##UserPassword##');
$replace = array($name,$content['content']['email'],$content['password']);
$emailBody = str_replace( $search, $replace, $content['body']['description']);
echo $emailBody;?>
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent