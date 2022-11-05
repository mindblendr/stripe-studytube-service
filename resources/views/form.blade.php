<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
</head>

<body>
    @if($errors->any())
    <h4>{{$errors->first()}}</h4>
    @endif
    <center>
        <form action="{{ route('process.register') }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="apiToken" value="{{env('SERVICE_TOKEN')}}">
            <!-- <input type="hidden" name="team_id" value="10235"> -->
            <input type="hidden" name="team_id" value="10217"> 
            <input type="text" name="first_name" placeholder="firstname" value="Aljon"> <br>
            <input type="text" name="last_name" placeholder="lastname" value="Cruz"> <br>
            <input type="text" name="email" placeholder="email" value="cruz.aljon1990@gmail.com"> <br>
            <input type="text" name="coupon" placeholder="code" value=""> <br>
            <input type="submit" value="Submit">
        </form>
    </center>
</body>

</html>