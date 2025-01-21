<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taily</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 600px;
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
        .input-field {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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
            transition: background-color 0.3s, transform 0.2s;
        }
        .send-button:hover {
            background-color: #004d40;
            transform: translateY(-2px);
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #00796b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn:hover {
            background-color: #004d40;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Taily Page</h2>
        <form id="tailyForm" method="POST" action="submit_to_it.php">
    <div>
        <label>
            <input type="radio" name="employee_type" value="new" required> New Employee
        </label>
        <label>
            <input type="radio" name="employee_type" value="old" required> Old Employee
        </label>
    </div>
    <div>
        <input type="text" name="name" placeholder="Name" class="input-field" required>
    </div>
    <div>
        <input type="text" name="employee_id" placeholder="Employee ID" class="input-field" required>
    </div>
    <div>
        <input type="text" name="additional_info" placeholder="Additional Info" class="input-field">
    </div>
    <div>
        <button type="submit" class="send-button">Send Request</button>
    </div>
    <div>
        <a href="new_request.php" class="btn">back to home</a>
    </div>
</form>
       
    </div>
    <script>
        document.getElementById('tailyForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const employeeType = document.querySelector('input[name="employee_type"]:checked').value;
            const formAction = employeeType === 'new' ? 'submit_to_hr.php' : 'submit_to_it.php';

            this.action = formAction;
            this.submit();
        });
    </script>
</body>
</html> 