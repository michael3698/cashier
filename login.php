<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "1234567";
    $dbname = "cashier";


    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } 
    else {

        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $error = "No user found with this email address.";
        } else {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
            // if ($password = $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type']  = $user['type'];


                if($user['type'] == "cashier"){
                    echo " cashier";
                    header("Location: create_order.php");
                }
                else{
                    echo " admin";
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error = "Incorrect password.";
            }
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f4f8;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .error-message {
            color: red;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .login-container {
            background-color: rgb(189, 219, 237);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            font-size: 1em;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #ff8c00;
            background-color: #fff;
        }

        button.login-btn {
            width: 100%;
            padding: 14px;
            background-color:rgb(0, 162, 255);
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button.login-btn:hover {
            background-color:rgb(0, 123, 195);
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 25px;
            }

            h2 {
                font-size: 1.6em;
            }

            button.login-btn {
                font-size: 1em;
            }

            input[type="email"],
            input[type="password"] {
                width: 95%;
            }
        }

        @media ( max-width: 480px){

            .login-wrapper {
                width: 80%;
            }

            input[type="email"],
            input[type="password"] {
                width: 92%;
            }
                
        }
    </style>
</head>
<body>
    
    <div class="login-wrapper">
        <div class="login-container">
            <h3>Welcome to cashier website</h3>
            <h2>Login page</h2>

            <?php if (isset($error)) { ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php } ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="input-group">
                    <label for="password">Password : </label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="login-btn">Log In</button>
            </form>
        </div>
    </div>

</body>
</html>