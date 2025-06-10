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

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $_SESSION['order_id'] = $order_id;
} elseif (isset($_SESSION['order_id'])) {
    $order_id = $_SESSION['order_id'];
} else {
    die("Order ID is missing");
}

$products = [];
$result = $conn->query("SELECT * FROM products");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $total = $price * $quantity;

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $total);
    $stmt->execute();
    $stmt->close();
}

$cart_items = [];
$stmt = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    header("Location: create_order.php");
    exit;
}

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM order_items WHERE id = $delete_id");

    header("Location: add_items.php?order_id=$order_id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $delete_id = intval($_POST['finalize_order']); // ✅ Use POST, not GET

    $conn->query("DELETE FROM order_items WHERE order_id = $delete_id");

    $conn->query("DELETE FROM orders WHERE id = $delete_id");

    echo "<script>window.location.href = 'create_order.php';</script>";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Items to Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }

        .wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .products, .cart {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .product, .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        form.inline {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
        }

        button {
            padding: 6px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .total {
            font-weight: bold;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<h2>Add Items to Order #<?= htmlspecialchars($order_id) ?></h2>

<div class="wrapper">
    <div class="products">
        <h3>Available Products</h3>
        <?php foreach ($products as $product): ?>
            <div class="product">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">

                    <div style="flex: 1;">
                        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                        <span style="color: #555;">Price: <?= number_format($product['price'], 2) ?> EGP</span>

                        <form class="inline" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" required>
                            <button type="submit">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="cart">
        <form method="POST">
            <input type="hidden" name="finalize_order" value="<?= $order_id ?>">
            <button style="margin-left:30px; background-color: red;" type="submit" name="cancel_order" class="finish-btn">cancel Order</button>
        </form>
        <h3>Current Order</h3>

        <?php if (count($cart_items) == 0): ?>
            <p>No items yet.</p>
        <?php else: ?>
            <?php $grand_total = 0; ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                    <a href="add_items.php?order_id=<?= $order_id ?>&delete=<?= $item['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    <br>
                    <small><?= number_format($item['price_at_order'], 2) ?> EGP</small>
                </div>
                <?php $grand_total += $item['price_at_order']; ?>
            <?php endforeach; ?>
            <div class="total">Total: <?= number_format($grand_total, 2) ?> EGP</div>
            <br>
            <form method="POST" >
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <button type="submit" style="background:#28a745;">Save order</button>
            </form>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
