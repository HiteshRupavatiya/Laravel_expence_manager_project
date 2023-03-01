<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
    <h2>
        <pre>
            Hi {{$user['first_name']}},
            Forgot Password Code :
                {{$user['verification_token']}}
        </pre>
    </h2>
</body>
</html>