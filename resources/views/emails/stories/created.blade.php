@component('mail::message')
<?php 
$search = array('##USERNAME##','##STORYNAME##','##CREATED_DATE##');
$replace = array($content['username'],$content['storyname'],$content['created_date']);
$emailBody = str_replace( $search, $replace, $content['userEmailTemplate']);
?>
{!!$emailBody!!}
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent