<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('title')
    <style>
        body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
        }

        h1 {
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-weight: 900;
            font-size: 40px;
            margin-bottom: 10px;
        }

        p {
            color: #404F5E;
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-size: 20px;
            margin: 0;
        }

        i.top-symbol {
            font-size: 180px;
            line-height: 200px;
            margin-left: -15px;
        }

        .card {
            background: white;
            padding: 60px;
            border-radius: 4px;
            box-shadow: 0 2px 3px #C8D0D8;
            display: inline-block;
            margin: 0 auto;
        }

        a:link,
        a:visited {
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            text-decoration: none;
            color: #ffcc00;
        }
    </style>
    @yield('color')
</head>

<body>
    <div class="card">
        <div style="border-radius:200px; height:200px; width:200px; margin:0 auto;">
            @yield('top-symbol')
        </div>
        @if (env('APP_ENV') != 'local')
        <img src="{{secure_asset('images/aob_logo.png')}}" alt="aob_logo" srcset="">
        @else
        <img src="{{asset('images/aob_logo.png')}}" alt="aob_logo" srcset="">
        @endif
        @yield('content')
    </div>
</body>

</html>