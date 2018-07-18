@component('mail::message')
<?php 
$search = array('##UserName##','##AdminURL##');
$replace = array($content['username'],$content['adminUrl']);
$emailBody = str_replace( $search, $replace, $content['userEmailTemplate']);
?>
{!!$emailBody!!}
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent
