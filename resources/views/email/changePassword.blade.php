<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>New password</title>
        <style>
            .button-reset{
                background-color: #e5125e;
                padding: 5px;
                color: white;
                text-align: center;
                border: none;
                border-radius: 3px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <p>Hi <b>{{ $user->username }}</b>,</p>
        <p>We received a request to reset your Doodfy password.</p>
        <a href="http://doodfy.ch/new-password/{{$user->verifyToken}}"><button type="button" class="button-reset">Reset Password</button></a>
    </body>
</html>
