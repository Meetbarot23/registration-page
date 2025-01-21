<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            padding: 20px;
            background-color: #e0f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 400px;
            width: 100%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #00796b;
        }
        .button {
            display: block;
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #00796b;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
        }
        .button:hover {
            background-color: #004d40;
            transform: translateY(-2px);
        }
        .section {
            margin-bottom: 20px;
        }
        .input-field {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .send-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .send-button:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Request</h2>
        <div class="section">
            <a href="taily.php" class="button">Taily</a>
        </div>

        <div class="section">
            <a href="email.php" class="button">Email</a>
        </div>

        <div class="section">
            <a href="usb_full.php" class="button">USB Full </a>
        </div>

        <div class="section">
            <a href="usb_read.php" class="button">USB Read</a>
        </div>
    </div>
</body>
</html> 