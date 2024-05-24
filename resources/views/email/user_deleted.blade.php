<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Deleted Notification</title>
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
            background-color: #FF5733;
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

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 5px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>User Account Deleted</h1>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>We regret to inform you that your user account has been deleted from <strong> {{ $companyName }} </strong>.</p>
            <p>If you have any concerns or need further assistance, please feel free to reach out to us:</p>
            <p>Email: {{ $companyEmail }}</p>
        </div>
        <div class="footer">
            <p>Warm Regards,<br>{{ $companyName }}</p>
            <p>Powered by {{ env('APP_NAME') }}</p>
        </div>
    </div>
</body>

</html>
