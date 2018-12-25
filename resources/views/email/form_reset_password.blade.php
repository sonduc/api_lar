
<!doctype html>
<html>
<head>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .container {
            display:flex;
            overflow: hidden;
            clear: both;
            padding-left: 15px;
            padding-right: 15px;
            height: 100vh;
            background-color: #dff0d8;
            margin-left: auto;
            margin-right: auto;
        }

        form {
            padding: 30px;
            border: 1px solid #e6e6e6;
            border-radius: 3px;
            background-color: #fff;
            box-shadow: none;
        }

        .login-form {
            margin-top: 80px;
            margin-bottom: 50px;
            color: #8C8C8C;
        }
        .login-form .form-group label {
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 400;
            padding-top: 0;
            display: block;
            text-align: left
        }

        .login-form button {
            width: 100%;
            height: 40px;
            border: 1px solid ;
            font-weight: 400;
            margin-top: 5px
        }
        .auth-form {
            margin: 0 auto;
        }
    </style>
</head>
<!------ Include the above  your HEAD tag ---------->
<body class="">
<div class="container">
    <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
        <div class="auth-pages login-form">
            <div class="auth-header login-header">
                <div class="logo">
                    <a id="logo" class="navbar-brand" href="" style="" title="Westay"></a>
                </div>

                <h3 class="headding_title"></h3>
            </div>

            <div class="auth-form">

                <form class="form-horizontal" role="form" method="POST" action="">

                    <div class="form-group">
                        <label for="password" class="control-label">Mật khẩu mới</label>
                        <input id="password" type="password" class="form-control" name="password" required>

                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="control-label">Nhập lại mật khẩu</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Thiết lập mật khẩu.
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
