@component('mail::message')
<?php 
$search = array('##USERNAME##','##VIDEONAME##','##CREATED_DATE##','
##VIDEOLINK##');
$replace = array($content['username'],$content['videoname'],$content['created_date'],$content['videolink']);
$emailBody = str_replace( $search, $replace, $content['userEmailTemplate']);
?>
{!!$emailBody!!}
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent