@extends('layouts.app')
@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <form class="forget-form" role="form" method="POST" action="{{ url('admin/password/email') }}">
        {{ csrf_field() }}
        <h3 class="font-green">Forget Password ?</h3>
        <p> Enter your e-mail address below to reset your password. </p>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email" name="email" value="{{ old('email') }}" required/>  
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
        </div>
        <div class="form-actions">
            <a href="{{ url('admin/login') }}"><button type="button" id="back-btn" class="btn green btn-outline">Back</button></a>
            <button type="submit" class="btn btn-success uppercase pull-right">Submit</button>
        </div>
    </form>         
@endsection
