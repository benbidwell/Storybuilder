<!doctype html>
<html lang="{{ config('app.locale') }}" data-ng-app="app">
    <head>
        <base href="/">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="manifest" href="/manifest.json">
        <meta name="google-signin-client_id" content="175801496366-dsbhan14indaorjmb0bc77e778fqmgo0.apps.googleusercontent.com"></meta>
        <title>Clipcrowd</title>

        <!-- Angular JS -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />

        <link href="{{asset('app.css')}}" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

        <script src="{{asset('vendor.bundle.js')}}" ></script>
        <script src="{{asset('app.bundle.js')}}" ></script>


        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>


        <script type="text/javascript" src="https://tympanus.net/Tutorials/CustomDropDownListStyling/js/modernizr.custom.79639.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-sortable/0.15.0/sortable.min.js"></script>
        <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="or8norwuzgjv957"></script>
        <script type="text/javascript" src="https://apis.google.com/js/api.js?onload=loadPicker"></script>
        <script src="https://apis.google.com/js/client:platform.js"></script>
        <script type="text/javascript" src="/voice-recording/recorder.js"></script>
        <script type="text/javascript" src="/voice-recording/Fr.voice.js"></script>

    </head>

    <body>
        <ui-view autoscroll="true"></ui-view>
        <script>
        // if ('serviceWorker' in navigator) {
        //   navigator.serviceWorker.register('/sw.js').then(function() {
        //     console.log("Service Worker Registered");
        //   });
        // }
        </script>
        @if (env('GOOGLE_TRACKING_ID')!='')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{env('GOOGLE_TRACKING_ID')}}"></script>
        <script>
         window.dataLayer = window.dataLayer || [];
         function gtag(){dataLayer.push(arguments)};
         gtag('js', new Date());

         gtag('config', "{{env('GOOGLE_TRACKING_ID')}}");
        </script>
        @endif
        
        <!-- {!! @$setting !!} -->
        <?php
         if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
         $ip=$_SERVER['HTTP_CLIENT_IP'];}
         elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];} else {
         $ip=$_SERVER['REMOTE_ADDR'];}
        ?>


        <script type='text/javascript'>
        gapi.push(['_setCustomVar', 1, 'IP', '<?=$ip;?>', 1]);
        </script>
    </body>
</html>
