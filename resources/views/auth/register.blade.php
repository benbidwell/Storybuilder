@extends('layouts.app')

@section('content')
    <form class="" role="form" method="POST" action="{{ route('admin/register') }}">
        {{ csrf_field() }}
        <h3 class="font-green">Sign Up</h3>
        <p class="hint"> Enter your personal details below: </p>
        
        <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Full Name</label>
                    <input class="form-control placeholder-no-fix" type="text" placeholder="Full Name" name="fullname" /> </div>


        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Full Name</label>
                <input id="name" type="text" class="form-control placeholder-no-fix" name="name" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
        </div>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Email</label>
            <input id="email" type="email" class="form-control placeholder-no-fix"  placeholder="Email" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
                <input id="password" type="password" class="form-control placeholder-no-fix" name="password" placeholder="Password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Confirm Password</label>
            <input id="password-confirm" type="password" class="form-control placeholder-no-fix" name="password_confirmation" placeholder="Confirm Password" required>
        </div>

        <div class="form-actions">
            <a href="{{ route('login') }}"><button type="button" id="register-back-btn" class="btn green btn-outline">Back</button></a>
            <button type="submit" id="register-submit-btn" class="btn btn-success uppercase pull-right">Submit</button>
        </div>
    </form>
@endsection
