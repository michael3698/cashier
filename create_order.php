<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "1234567";
$dbname = "cashier";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name =trim( $_POST['username']);
    $phone =trim( $_POST['user_phone']);
    $location =trim( $_POST['location']);

    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO orders (username, user_phone, location, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $phone, $location, $created_by);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        header("Location: add_items.php?order_id=$order_id");
        exit;
    } else {
        echo "Error while saving order. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create New Order</title>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Order</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s;
        }

        input:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .logout-btn{
            display: block;
            text-decoration: none;
            margin-top : 20px;
            margin-left: 15px;
            width: 5%;
            padding: 12px;
            background-color:rgb(208, 60, 60);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color:rgb(161, 30, 30);
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

</head>
<body>
    <a class="logout-btn" href="logout.php">log out</a>
    <div class="container">
        <h2>Create New Order</h2>

        <form method="POST">
            <label>Customer Name :</label>
            <input type="text" name="username" value="customer">

            <label>Customer Phone :</label>
            <input type="text" name="user_phone">

            <label>Customer Location :</label>
            <textarea name="location" rows="3"></textarea>

            <button type="submit">Start Order</button>
        </form>
    </div>
</body>

</html>
