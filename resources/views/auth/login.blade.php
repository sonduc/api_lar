@extends('auth.app')
<?php
    use Illuminate\Foundation\Application;
    Session::set('backUrl', URL::previous());
?>
@section('content')
    <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
        <div class="auth-pages login-form">
            <!-- <div class="auth-header login-header">
                <a class="text-facebook" href="{{  route('login.facebook') }}" title="{{ isset($dataTranslates['login_facebook']) ? $dataTranslates['login_facebook'] : '' }}">
                    <i class="fa fa-facebook"></i>
                    <span>{{ trans('auth.login_facebook') }}</span>
                </a>
            </div> -->

            <div class="auth-form">
                <form  role="form" method="POST" action="{{ url((\App::getLocale() == Request::segment(1) ? Request::segment(1) : 'vi') .'/login') }}">
                    {{ csrf_field() }}
                    <div class="form-group {{-- {{ $errors->has('email') ? ' has-error' : '' }} --}}">
                        <label for="email">{{ trans('form.email') }}</label>
                        <input type="email" class="form-control" id="email" value="{{ old('email') }}" required  name="email" autofocus>
                       {{--  @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif --}}
                    </div>

                    <div class="form-group {{-- {{ $errors->has('password') ? ' has-error' : '' }} --}}">
                        <label for="password">{{ trans('form.password') }}</label>
                        <input type="password" class="form-control" id="password" required name="password">
                        {{-- @if ($errors->has('password'))
                            <span class="help-block">{{ $errors->first('password') }}</span>
                        @endif --}}
                    </div>
                    <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="checkbox">
                        <label>
                          <input type="checkbox" {{ old('remember') ? 'checked' : ''}}> {{ trans('form.remember_login') }}
                        </label>
                        <a href="{{ url((\App::getLocale() == Request::segment(1) ? Request::segment(1) : 'vi') . secure_url('/vi/password/reset')) }}" title="{{ trans('form.forgot_password') }}" class="reset">{{ trans('form.forgot_password') }}?</a>
                    </div>
                    <button class="btn btn-primary btn-block"  type="submit">{{ trans('general.login') }}</button>
                </form>

                <div class="signup">
                    <p>{{ trans('auth.account_yet') }}? <a href="{{ url((\App::getLocale() == Request::segment(1) ? Request::segment(1) : 'vi') . secure_url('/vi/register')) }}" title="{{ trans('auth.register_now') }}" class="highlighter">{{ trans('auth.register_now') }}.</a></p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function($) {
            $('#email').focus();
        });
    </script>
@stop
