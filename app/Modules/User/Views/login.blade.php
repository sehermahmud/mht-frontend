<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>MHT</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{asset('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{asset('plugins/iCheck/square/blue.css')}}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">

    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="{{ url('/') }}"><b> M</b>HT</a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Sign In</p>

                @if (count($errors) > 0)
                <div class="alert alert-danger alert-login">
                    <ul class="list-unstyled">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {!! Form::open(array('url' => 'user_login', 'id' => 'login_form')) !!}
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email" name="username" id="username">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div><br>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox"> Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                    <!-- /.col -->

                </div>
                {!! Form::close() !!}

            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->

        <!-- jQuery 2.2.3 -->
        <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>
        <!-- iCheck -->
        <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
        <script src="{{asset('plugins/validation/dist/jquery.validate.min.js')}}"></script>
        <script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>


        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });

            $(document).ready(function () {

                // initialize tooltipster on form input elements
                $('form input').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
                    trigger: 'custom', // default is 'hover' which is no good here
                    onlyOne: false, // allow multiple tips to be open at a time
                    position: 'right'  // display the tips to the right of the element
                });

                // initialize validate plugin on the form
                $('#login_form').validate({
                    errorPlacement: function (error, element) {

                        var lastError = $(element).data('lastError'),
                                newError = $(error).text();

                        $(element).data('lastError', newError);

                        if (newError !== '' && newError !== lastError) {
                            $(element).tooltipster('content', newError);
                            $(element).tooltipster('show');
                        }
                    },
                    success: function (label, element) {
                        $(element).tooltipster('hide');
                    },
                    rules: {
                        username: {
                            required: true, email: true
                        },
                        password: {
                            required: true, minlength: 6
                        }
                    },
                    messages: {
                        username: {
                            required: "Please provide username."
                        },
                        password: {
                            required: "Please provide password"
                        }
                    },
                });

            });
        </script>
    </body>
</html>
