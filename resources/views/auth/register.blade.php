@extends('auth.app')
@section('styles')
    <link href="/css/bootstrap-fileupload.css" rel="stylesheet">
    <style type="text/css">
        .fileupload-new .btn-file input {
            transform: none;
        }
        .group-birthday {
            padding: 0 5px;
        }
        .padding-10 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .group-birthday select {
            -moz-appearance: none;
            -webkit-appearance: none;
            appearance: none;
        }
        .group-birthday i {
            position: absolute;
            top: calc(50% - 7px);
            right: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
            <div class="auth-pages login-form">
                <!-- @if ( Request::get('type') != 1)
                    <div class="auth-header login-header">
                        <a class="text-facebook" href="{{  route('login.facebook') }}" title="{{ trans('auth.signup_facebook') }}">
                            <i class="fa fa-facebook"></i>
                            <span>{{ trans('auth.signup_facebook') }}</span>
                        </a>

                        <div class="signup-or-separator">
                            <span class="h6 signup-or-separator--text">{{ trans('auth.or_txt') }}</span>
                            <hr>
                        </div>
                    </div>
                @endif -->

                @if(Request::get('type') == 1 || Request::has('system') && Request::get('system') == 1)
                    <div class="auth-form">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url((\App::getLocale() == Request::segment(1) ? Request::segment(1) : 'vi') .'/register') }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}
                                
                            @if(Request::has('ref'))
                            <input type="hidden" name="reference" value="{{ Request::get('ref') }}">
                            @endif

                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="control-label">{{ trans('auth.customer_name') }}<b class="text-danger">*</b></label>
                                <input placeholder="{{ trans('auth.placeholder_txt1') }}" id="name" type="text" class="form-control" name="name" value="{{ Request::old('name') }}" required autofocus>
                                {{-- <input type="hidden" name="type" value="{{ Request::get('type', 0) }}"> --}}
                                @if ($errors->has('name'))
                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="control-label">{{ trans('form.email') }}<b class="text-danger">*</b></label>
                                <input id="email" type="email" class="form-control" name="email" value="{{  Request::old('email') }}" placeholder="{{ trans('auth.placeholder_txt2') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="name" class="control-label">{{ trans('form.phone') }}<b class="text-danger">*</b></label>
                                <input placeholder="{{ trans('auth.placeholder_txt3') }}" id="phone" type="text" class="form-control" name="phone" value="{{  Request::old('phone') }}" required>
                                @if ($errors->has('phone'))
                                    <span class="help-block">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="control-label">{{ trans('form.password') }}<b class="text-danger">*</b></label>
                                <input id="password" type="password" class="form-control" name="password" placeholder="{{ trans('auth.placeholder_txt4') }}" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="date">Ngày sinh:<span class="text-danger"> *</span></label>
                                <div class="row">
                                    <div class="col-md-12 group-birthday">
                                        <div class="col-md-3 padding-10 {{ $errors->has('date') ? 'has-error' : '' }}">
                                            <select name="date" class="form-control">
                                                <option value="" >Ngày</option>
                                                @for ($i = 1; $i <=31; $i++)
                                                   <option value="{{$i}}" {{ Request::old('date') == $i ? 'selected' : '' }}>{{$i}}</option>
                                                @endfor
                                            </select>
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                        </div>

                                        <div class="col-md-5 padding-10 {{ $errors->has('month') ? 'has-error' : '' }}">
                                            <select name="month" class="form-control">
                                                <option value="" >Tháng</option>
                                                @foreach ( getTextMonth() as $key => $month)
                                                    <option value="{{ $key }}" {{ Request::old('month') == $key ? 'selected' : '' }}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-md-4 padding-10 {{ $errors->has('year') ? 'has-error' : '' }}">
                                            <select name="year" class="form-control">
                                                <option value="" >Năm</option>
                                                @for ($i = date('Y'); $i >= 1900; $i--)
                                                   <option value="{{$i}}" {{ Request::old('year') == $i ? 'selected' : '' }}>{{$i}}</option>
                                                @endfor
                                            </select>
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-12 {{ $errors->has('date') || $errors->has('month') || $errors->has('year') ? 'has-error' : ''}}">
                                        @if (!empty($errors->first('date')) && !empty($errors->first('month')) && !empty($errors->first('year')))
                                            <span class="help-block text-danger">{{ $errors->first('year') }}</span>
                                        @elseif (!empty($errors->first('date')))
                                            <span class="help-block text-danger">{{ $errors->first('date') }}</span>
                                        @elseif (!empty($errors->first('month')))
                                            <span class="help-block text-danger">{{ $errors->first('month') }}</span>
                                        @elseif (!empty($errors->first('year')))
                                            <span class="help-block text-danger">{{ $errors->first('year') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('id_card1') || $errors->has('id_card2')  ? ' has-error' : '' }}">
                                <label for="photos" class="control-label">{{ trans('auth.idcard_passport') }} {{-- <b class="text-danger">*</b> --}}</label>
                                <div class="photos_upload">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <div class="fileupload-new thumbnail" style=" background: rgba(0,0,0,.5);">
                                            <img class="img-reponsive" src="{{ Request::old('id_card1', '/images/md_default.png') }}" alt="" />
                                        </div>
                                        <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                        <div>
                                            <span class="btn btn-white btn-file">
                                                <span class="fileupload-new fix-new"><i class="fa fa-paperclip hidden-xs"></i> {{ trans('auth.identity_card_frontside') }}</span>
                                                <span class="fileupload-exists"><i class="fa fa-undo"></i> {{ trans('auth.change_image') }}</span>
                                                <input type="file" name="id_card1" id="card1" class="default" value="{{ Request::old('id_card1','')}}" />
                                            </span>
                                        </div>
                                        @if ($errors->has('id_card1'))
                                            <span class="help-block">{{ $errors->first('id_card1') }}</span>
                                        @endif
                                    </div>

                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <div class="fileupload-new thumbnail" style=" background: rgba(0,0,0,.5);">
                                            <img class="img-reponsive" src="/images/md_default.png" alt="" />
                                        </div>
                                        <div class="fileupload-preview fileupload-exists thumbnail" >

                                        </div>
                                        <div>
                                            <span class="btn btn-white btn-file">
                                                <span class="fileupload-new fix-new"><i class="fa fa-paperclip hidden-xs"></i> {{ trans('auth.identity_card_backside') }}</span>
                                                <span class="fileupload-exists"><i class="fa fa-undo"></i> {{ trans('auth.change_image') }}</span>
                                                <input type="file" name="id_card2" class="default" id="card2" {{ Request::old('id_card2','')}}/>
                                            </span>
                                        </div>
                                        @if ($errors->has('id_card2'))
                                            <span class="help-block">{{ $errors->first('id_card2') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('is_confirm') ? 'has-error' : '' }}" >
                                <div id="check_confirm">
                                    <input type="checkbox" value="1" name="is_confirm" class="confrim_input">
                                    <p>
                                        {{ trans('auth.i_agree_with') }}
                                        <a target="blank" href="/vi/b-20-quy-che-hoat-dong">Quy chế hoạt động</a>,
                                        <a target="blank" href="/vi/b-21-chinh-sach-bao-mat">Chính sách bảo mật</a>, và
                                        <a target="blank" href="/vi/b-22-quy-trinh-giai-quyet-tranh-chap-khieu-nai">Quy trình giải quyết tranh chấp, khiếu nại.</a>
                                    </p>
                                </div>
                                <div class="te-center">{!! $errors->first('is_confirm', '<span class="help-inline text-danger">:message</span>') !!}</div>
                            </div>

                            <div class="form-group checkbox hide">
                                <label class="control-label">
                                  <input type="checkbox" {{ Request::get('type') == 1 ||  Request::old('type') ? 'checked' : ''}} value="1" name="type"> {{ trans('auth.signup_host') }}
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('general.register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <a class="btn-register-email" href="{{ url()->current() }}?system=1" title="{{ isset($dataTranslates['signup_with_email']) ? $dataTranslates['signup_with_email'] : '' }}">
                        <i class="fa fa-envelope-o"></i>
                        <span>{{ trans('auth.signup_with_email') }}</span>
                    </a>
                @endif

                <div class="signup">
                    <p>{{ trans('auth.already_accounts') }}? <a class="highlighter" href="{{ url((\App::getLocale() == Request::segment(1) ? Request::segment(1) : 'vi') . '/login') }}" title="">{{ trans('auth.login_now') }}.</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="/js/bootstrap-fileupload.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('#name').focus();
        });
    </script>
@stop
