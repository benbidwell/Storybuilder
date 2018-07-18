@component('mail::message')
<?php 
$search = array('##UserName##','##ResetPasswordURL##');
$replace = array($content['username'],$content['resetUrl']);
$emailBody = str_replace( $search, $replace, $content['userEmailTemplate']);
?>
{!!$emailBody!!}
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent