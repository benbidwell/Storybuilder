@extends('layouts.app')
@section('content')
    <form  class="login-form" role="form" method="POST" action="{{ url('admin/login') }}">
        {{ csrf_field() }}
        <h3 class="form-title font-green">Sign In</h3>
         @if ($errors->has('email'))
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button>
                <span> {{ $errors->first('email') }} </span>
            </div>
        @endif
        @if ($errors->has('password'))
             <div class="alert alert-danger">
                <button class="close" data-close="alert"></button>
                <span> {{ $errors->first('password') }} </span>
            </div>
        @endif
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9 ">E-Mail Address</label>
            <input id="email" type="email" class="form-control form-control-solid placeholder-no-fix" name="email" value="{{ old('email') }}" placeholder="E-Mail Address">
        </div>

        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <input id="password" type="password" class="form-control form-control-solid placeholder-no-fix" name="password" placeholder="Password">
        </div>
        <div class="form-actions">
            <button type="submit" id="login" class="btn green uppercase">Login</button>
            <label class="rememberme check mt-checkbox mt-checkbox-outline">
                <input type="checkbox" name="remember" {{old('remember') ? 'checked' : ''}} />Remember
                    <span></span>
            </label>
           <!--  <a href="{{ url('password/request') }}" id="forget-password" class="forget-password">Forgot Password?</a> -->
            <a href="{{ url('admin/password/reset') }}" id="forget-password" class="forget-password">Forgot Password?</a>
        </div>
    </form>
  <!--   <div class="create-account">
        <p>
            <a href="{{ url('admin/register') }}" id="register-btn" class="uppercase">Create an account</a>
        </p>
    </div> -->
@endsection
@section('scripts')
<script type="text/javascript">
jQuery('#login').click(function() {
    var email = jQuery('#email').val();
    var password =jQuery('#password').val();
    var url = _baseUrl+'/api/login';
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: 'email='+email+'&password='+password,
        success: function (response) 
        {
           var token = response.token;
           // window.localStorage.ud = response.token;
            window.localStorage.setItem('ud', token);
            var accessed  = window.localStorage.getItem('ud');
          //  window.location.href = _baseUrl;
        },
        error: function(data, errorThrown)
          {
              alert('request failed :'+errorThrown);
          }
    });
});
</script>
@stop