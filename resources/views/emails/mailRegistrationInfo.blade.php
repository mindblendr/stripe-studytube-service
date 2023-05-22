<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Info - {{$data['name']}}</title>
</head>
<body>
    <ul style="list-style: none;">
        <li>Name: {{$data['name']}}</li>
        <li>Email: {{$data['email']}}</li>
        <li>Product: {{$data['product']}}</li>
    </ul>
</body>
</html>