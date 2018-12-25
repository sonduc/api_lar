@extends('auth.app')

@section('content')
    <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
        <div class="auth-pages login-form">
            <div class="auth-header login-header">
                <div class="logo">
                    @if($metadata->getLogo())
                        <a id="logo" class="navbar-brand" href="{{ secure_url('/') }}" style="background: url('{{ LOGO_SETTING.$metadata->getLogo() }}') center center no-repeat; background-size: contain; width: 160px; " title="Westay"></a>
                    @else
                        <a id="logo" class="navbar-brand" href="{{ secure_url('/') }}" style="background: url('/images/Logo-westay.png') center center no-repeat; background-size: contain; width: 160px; " title="Westay"></a>
                    @endif
                </div>
                <h3 class="headding_title">{{ trans('auth.password_recovery') }}</h3>
            </div>

            <div class="auth-form">
                {{-- @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif --}}

                <form class="form-horizontal" role="form" method="POST" action="{{ secure_url('/' . (\App::getLocale() == Request::segment(1) ? Request::segment(1) : 'vi') . '/password/email') }}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="control-label">{{ trans('form.email') }}</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="{{ isset($dataTranslates['placeholder_txt7']) ? $dataTranslates['placeholder_txt7'] : '' }}">

                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('auth.send_reset_link') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
