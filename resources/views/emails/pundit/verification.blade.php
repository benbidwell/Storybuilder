@component('mail::message')
<?php 
$search = array('##USERNAME##','##USER_ID##','##PASSWORD##','##VERIFICATION_LINK##');
$replace = array($content['username'],$content['user_id'],$content['password'],$content['verification_link']);
$emailBody = str_replace( $search, $replace, $content['userEmailTemplate']);
?>
{!!$emailBody!!}
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent