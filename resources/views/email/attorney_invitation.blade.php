<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attorney Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #007BFF;
            color: #ffffff;
            padding: 10px 0;
            text-align: center;
        }

        .content {
            padding: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Attorney Invitation</h1>
        </div>
        <div class="content">
            <p>Dear {{ $attorneyName }},</p>
            <p>This is to inform you that you have been added as an Attorney in <strong> {{ $companyName }} </strong>.</p>
            <p>Click the button below to log in:</p>
            <p><a class="button" href="{{ $loginLink }}">Log In</a></p>
            <div class="credentials">
                <p>Your login credentials:</p>
                <ul>
                    <li><strong>Username:</strong> {{ $username }}</li>
                    <li><strong>Password:</strong> {{ $password }}</li>
                </ul>
            </div>
            <p>In case of any ambiguities, please contact <strong>{{ $companyName }} </strong></p>
            <p>Email: <strong> {{ $companyEmail }} </strong></p>
        </div>
        <div class="footer">
            <p>Warm Regards,<br>{{ $companyName }}</p>
            <p>Powered by {{ env('APP_NAME') }}</p>
        </div>
    </div>
</body>

</html>
