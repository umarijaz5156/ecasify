<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companyName }} Login Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #007BFF;
            color: #FFFFFF;
            text-align: center;
            padding: 10px;
        }

        .content {
            padding: 20px;
            border: 1px solid #E0E0E0;
            border-radius: 5px;
            margin-top: 20px;
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
            <h1>{{ $companyName }} Login Notification</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>This is to inform you that {{ $username }} has logged in:</p>
            <ul>
                <li>IP Address: {{ $ipAddress }}</li>
                <li>Device: {{ $device }}</li>
                <li>Browser: {{ $browser }}</li>
                <li>Location: {{ $location }}</li>
            </ul>
            <p>If you didn't perform this action, please contact us immediately.</p>
            <p>In case of any ambiguities, please contact {{ $companyName }}:</p>
            <p>Email: {{ $companyEmail }}</p>
        </div>
        <div class="footer">
            <p>Warm Regards,<br>{{ $companyName }}</p>
            <p>Powered by {{ env('APP_NAME') }}</p>
        </div>
    </div>
</body>

</html>
