<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Cancelled</title>
    <style>
        body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
        }

        h1 {
            color: #c10000;
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
            color: #c10000;
            font-size: 100px;
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
</head>

<body>
    <div class="card">
        <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
            <i class="top-symbol">✘</i>
        </div>
        <img src="https://academyofbrain.com/wp-content/uploads/2020/03/181217_AOB-logo_RGB_72dpi-1024x159-1.png" alt="" srcset="">
        <h1>Cancelled</h1>
        <p>Registration has been cancelled. Thank you for your consideration!</p>
        <br><br>
        @if($errors->any())
        <script>
            console.log('{{$errors->first()}}');
        </script>
        <p><small><i>Something went wrong. Please <a href="https://academyofbrain.com">contact us</a>.</i></small></p>
        @else
        <p><small><i>Please <a href="https://academyofbrain.com">contact us</a> if something went wrong.</i></small></p>
        @endif

    </div>
</body>

</html>