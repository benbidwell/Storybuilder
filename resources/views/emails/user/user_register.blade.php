@component('mail::message')
<?php 
$search = array('##UserName##');
$replace = array($content['username']);
$emailBody = str_replace( $search, $replace, $content['userEmailTemplate']);
?>
{!!$emailBody!!}
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent
