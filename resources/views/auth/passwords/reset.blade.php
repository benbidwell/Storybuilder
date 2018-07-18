@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="forget-form" role="form" method="POST" action="{{ url('admin/password/reset') }}">
        <h3 class="font-green">Reset Password ?</h3>
        {{ csrf_field() }}
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input class="form-control placeholder-no-fix" id="email" type="email" autocomplete="off" placeholder="Email" name="email" value="{{ $email or old('email') }}" required autofocus/>  
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password"  autocomplete="off" placeholder="Password" required>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <input id="password-confirm" autocomplete="off" placeholder="Confirm Password"  type="password" class="form-control" name="password_confirmation" required>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success uppercase pull-right">Reset Password
            </button>
        </div>
    </form>
@endsection