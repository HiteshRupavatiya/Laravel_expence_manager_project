<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Email</title>
</head>
<body>
    
<h1>Hi {{$user->first_name}} {{$user->last_name}},</h1>

<p>We just need to verify your email address before you can access our site.<br><br>

Verify your email address <a href="{{URL('api/varifyAccount/'.$user->verification_token)}}">Verify Email</a>
</p>

Thanks!
</body>
</html>
