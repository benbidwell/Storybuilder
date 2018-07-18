@component('mail::message')
<?php 
$smtpport = $content['content']['smtp_port'];
$smtphost = $content['content']['smtp_host'];
$smtp_username = $content['content']['smtp_username'];
$smtp_password = $content['content']['smtp_password'];
$google_analytics_code = $content['content']['google_analytics_code'];
$pundit_title_text = $content['content']['pundit_title_text'];
$facebook_pixel_code = $content['content']['facebook_pixel_code'];
$trasition_time = $content['content']['trasition_time'];

$search = array('##SMTPPORT##', '##SMTPHOST##', '##USERNAME##', '##PASSWORD##', '##GOOGLEANALYTIC##', '##PUNDITTITLE##', '##FACEBOOKPIXLE##', '##TRANSITIONTIME##');
$replace = array($smtpport,$smtphost,$smtp_username,$smtp_password,$google_analytics_code,$pundit_title_text,$facebook_pixel_code,$trasition_time);
$emailBody = str_replace( $search, $replace, $content['body']['description']);
echo $emailBody;?>
<br>Thanks,<br>
{{ config('app.name') }}
@endcomponent