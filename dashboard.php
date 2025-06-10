<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page = $_GET['page'] ?? 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f2f2f2;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background-color: #1e1e2f;
            color: #fff;
            padding: 20px;
            flex-shrink: 0;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 24px;
            color: #fff;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: #ccc;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px 15px;
            border-radius: 6px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #333;
            color: #fff;
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        .content h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }

        .content p {
            font-size: 18px;
            color: #555;
        }

        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
                text-align: center;
            }

            .sidebar ul li {
                display: inline-block;
                margin: 5px;
            }

            .sidebar ul li a {
                padding: 8px 12px;
            }
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f2f4f8;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        form input,
        form select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 180px;
        }

        form button {
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        form button:hover {
            background-color: #219150;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background-color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        a {
            text-decoration: none;
            color: #e74c3c;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }


    </style>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php?page=cashiers">Cashiers</a></li>
            <li><a href="dashboard.php?page=products">Products</a></li>
            <li><a href="dashboard.php?page=orders">Orders</a></li>
            <li><a href="dashboard.php?page=sales">Sales per Month</a></li>
            <li><a href="dashboard.php?page=create_order">Create Order</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </aside>

    <main class="content">
        <?php
        switch ($page) {
            case 'cashiers':

                
                $servername = "localhost";
                $username = "root";
                $password = "1234567";
                $dbname = "cashier";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cashier'])) {
                    $name = $_POST['name'];
                    $phone = $_POST['phone'];
                    $email = $_POST['email'];
                    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $role = $_POST['type']; 

                    if (!empty($name) && !empty($phone) && !empty($email) && !empty($password) && !empty($role)) {
                        $stmt = $conn->prepare("INSERT INTO users (name, phone, email , password , type) VALUES (?, ?, ? , ? , ?)");
                        $stmt->bind_param("sssss", $name, $phone, $email, $password, $role);
                        $stmt->execute();
                    }
                }

                // حذف كاشير
                if (isset($_GET['delete'])) {
                    $delete_id = intval($_GET['delete']);
                    $conn->query("DELETE FROM users WHERE id = $delete_id");
                }

                $sql = "SELECT c.*, COUNT(o.id) AS orders_count
                        FROM users c
                        LEFT JOIN orders o ON c.id = o.created_by
                        GROUP BY c.id";
                $result = $conn->query($sql);
                ?>

                <h2>Cashiers</h2>

                <form method="POST" style="margin-bottom: 20px;">
                    <input type="text" name="name" placeholder="Cashier Name" required>
                    <input type="text" name="phone" placeholder="Phone" required>
                    <input type="email" name="email" placeholder="email" required>
                    <input type="password" name="password" placeholder="password" required>
                    <select name="type" id="type" required>
                            <option value="cashier">cashier</option>
                            <option value="admin">Admin</option>
                        </select>
                    <button type="submit" name="add_cashier">Add Cashier</button>
                </form>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>email</th>
                        <th>type</th>
                        <th>Orders Count</th>
                        <th>Action</th>
                    </tr>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= $row['orders_count'] ?></td>
                        <td><a href="dashboard.php?page=cashiers&delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <?php

                break;
            case 'products':

                    $conn = new mysqli("localhost", "root", "1234567", "cashier");

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
                        $name = $_POST['name'];
                        $price = $_POST['price'];

                        $image_name = '';
                        if (!empty($_FILES['image']['name'])) {
                            $image_name = time() . '_' . $_FILES['image']['name'];
                            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_name);
                        }

                        if (!empty($name) && !empty($price)) {
                            $stmt = $conn->prepare("INSERT INTO products (name, image, price) VALUES (?, ?, ?)");
                            $stmt->bind_param("ssd", $name, $image_name, $price);
                            $stmt->execute();
                        }
                    }

                    if (isset($_GET['delete'])) {
                        $delete_id = intval($_GET['delete']);
                        $conn->query("DELETE FROM products WHERE id = $delete_id");
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_update'])) {
                    $id = intval($_POST['update_id']);
                    $name = $_POST['name'];
                    $price = floatval($_POST['price']);

                    if (!empty($_FILES['image']['name'])) {
                        $image = $_FILES['image']['name'];
                        $tmp = $_FILES['image']['tmp_name'];
                        move_uploaded_file($tmp, "uploads/$image");

                        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ? WHERE id = ?");
                        $stmt->bind_param("sdsi", $name, $price, $image, $id);
                    } else {
                        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
                        $stmt->bind_param("sdi", $name, $price, $id);
                    }

                    $stmt->execute();
                    echo "<script>location.href='dashboard.php?page=products';</script>";
                    exit;
                }


                    $result = $conn->query("SELECT * FROM products");
                ?>
                <h2>Products</h2>

                <form method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
                    <input type="text" name="name" placeholder="Product Name" required>
                    <input type="number" step="0.01" name="price" placeholder="Price" required>
                    <input type="file" name="image" required>
                    <button type="submit" name="add_product">Add Product</button>
                </form>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><img src="uploads/<?= $row['image'] ?>" width="50"></td>
                        <td><?= number_format($row['price'], 2) ?> EGP</td>
                        <td>
                            <a href="dashboard.php?page=products&delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete </a>   -  
                            <a href="#" style="color: rgb(0, 166, 255);" onclick="openModal('<?= $row['id'] ?>', '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', '<?= $row['price'] ?>')">Update</a>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                
                <div id="updateModal" style="display:none; position:fixed; top:20%; left:35%; width:30%; background:#fff; padding:20px; border:1px solid #ccc; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2); z-index:1000;">
                    <h3>Update Product</h3>
                    <form id="updateForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_id" id="update_id">
                        <input type="text" name="name" id="update_name" required><br><br>
                        <input type="number" name="price" id="update_price" step="0.01" required><br><br>
                        <input type="file" name="image"><br><br>
                        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                            <button type="submit" name="save_update" style="padding: 5px 10px;">Save</button>
                            <button type="button" onclick="closeModal()" style="padding: 5px 10px;">Cancel</button>
                        </div>
                    </form>
                </div>
                <?php

                break;
            case 'orders':

                    $conn = new mysqli("localhost", "root", "1234567", "cashier");

                    $orders = $conn->query("SELECT o.*, u.name as cashier_name 
                                            FROM orders o 
                                            LEFT JOIN users u ON o.created_by = u.id 
                                            ORDER BY o.created_at DESC");

                    $total_orders = $orders->num_rows;

                    if (isset($_GET['delete'])) {
                        $delete_id = intval($_GET['delete']);
                        $conn->query("DELETE FROM orders WHERE id = $delete_id");
                        echo "<script>window.location.href = 'dashboard.php?page=orders';</script>";
                        exit;
                        
                    }

                    $result = $conn->query("SELECT * FROM users");
                ?>

                <h2 style="color: green;">Orders (Total: <?= $total_orders ?>)</h2><br>

                <?php while ($order = $orders->fetch_assoc()): ?>
                    <div style="background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
                        <h3>Order # <?= $order['id'] ?> - <?= htmlspecialchars($order['username']) ?> ( <?= $order['user_phone'] ?>  ) <a style=" margin-left: 90%;" href="dashboard.php?page=orders&delete=<?= $order['id'] ?>" onclick="return confirm('Delete this order?')">Delete</a></h3><br>
                        <p><strong>Created by:</strong> <?= htmlspecialchars($order['cashier_name']) ?>  | <strong>Date:</strong> <?= $order['created_at'] ?></p>

                        <table border="1" cellpadding="10" cellspacing="0" width="100%">
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                            <?php
                                $order_id = $order['id'];
                                $items = $conn->query("SELECT oi.*, p.name, p.price 
                                                    FROM order_items oi
                                                    LEFT JOIN products p ON oi.product_id = p.id 
                                                    WHERE oi.order_id = $order_id");

                                $total = 0;
                                while ($item = $items->fetch_assoc() ):
                                    $line_total = $item['price'] * $item['quantity'];
                                    $total += $line_total;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['price'], 2) ?> EGP</td>
                                <td><?= number_format($line_total, 2) ?> EGP</td>
                            </tr>
                            <?php endwhile; ?>
                            <tr style="background: #f7f7f7;">
                                <td colspan="3" align="right"><strong>Total:</strong></td>
                                <td><strong><?= number_format($total, 2) ?> EGP</strong></td>
                            </tr>
                        </table>
                    </div>
                <?php endwhile; ?>

                <?php

                break;
            case 'sales':

                $conn = new mysqli("localhost", "root", "1234567", "cashier");

                $daily_sales = $conn->query("
                    SELECT DATE(o.created_at) as sale_day, SUM(oi.price_at_order) as total 
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    GROUP BY sale_day 
                    ORDER BY sale_day DESC
                ");

                $monthly_sales = $conn->query("
                    SELECT DATE_FORMAT(o.created_at, '%Y-%m') as sale_month, SUM(oi.price_at_order) as total 
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    GROUP BY sale_month 
                    ORDER BY sale_month DESC
                ");
            


                ?>

                <div style="display: flex; justify-content: space-between; gap: 20px;">
                    
                    <div style="width: 48%;">
                        <h3>Daily Sales</h3>
                        <table border="1" cellpadding="10" cellspacing="0" width="100%">
                            <tr>
                                <th>Date</th>
                                <th>Total Sales (EGP)</th>
                            </tr>
                            <?php while($row = $daily_sales->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['sale_day'] ?></td>
                                    <td><?= number_format($row['total'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>

                    <div style="width: 48%;">
                        <h3>Monthly Sales</h3>
                        <table border="1" cellpadding="10" cellspacing="0" width="100%">
                            <tr>
                                <th>Month</th>
                                <th>Total Sales (EGP)</th>
                            </tr>
                            <?php while($row = $monthly_sales->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['sale_month'] ?></td>
                                    <td><?= number_format($row['total'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>


                <?php
                break;
            case 'create_order':

                $conn = new mysqli("localhost", "root", "1234567", "cashier");

                $order_id = null;

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
                    $username = $_POST['username'];
                    $user_phone = $_POST['user_phone'];
                    $location = $_POST['location'];
                    $created_by = $_SESSION['user_id'];

                    if (!empty($username)) {
                        $stmt = $conn->prepare("INSERT INTO orders (username, user_phone, location, created_by) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("sssi", $username, $user_phone, $location, $created_by);
                        $stmt->execute();
                        $order_id = $stmt->insert_id;
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
                    $order_id = intval($_POST['order_id']);
                    $product_id = intval($_POST['product_id']);
                    $quantity = intval($_POST['quantity']);

                    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $stmt->bind_result($price);
                    $stmt->fetch();
                    $stmt->close();

                    $total = $price * $quantity;
                    
                    if ($order_id && $product_id && $quantity > 0) {
                        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?,? , ?, ?)");
                        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $total);
                        $stmt->execute();
                    }

                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finish_order'])) {
                    $order_id = intval($_POST['finalize_order']);

                    echo "<script>alert('Order Saved!'); window.location.href='dashboard.php?page=create_order';</script>";
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
                    $delete_id = intval($_POST['finalize_order']); // ✅ Use POST, not GET

                    $conn->query("DELETE FROM order_items WHERE order_id = $delete_id");

                    $conn->query("DELETE FROM orders WHERE id = $delete_id");

                    echo "<script>window.location.href = 'dashboard.php?page=create_order';</script>";
                    exit;
                }


                $products = $conn->query("SELECT * FROM products");

                if ($order_id) {
                    $cart_items = $conn->query("
                        SELECT oi.*, p.name, p.price 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = $order_id
                    ");
                }


            ?>

            <h2>Create New Order</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Customer Name" value="customer" required><br><br>
                <input type="text" name="user_phone" placeholder="Customer Phone"><br><br>
                <textarea name="location" placeholder="Customer Location"></textarea><br><br>
                <button type="submit" name="create_order">Start Order</button>
            </form>

            <?php if ($order_id): ?>
            <hr>
            <h3>Add Items to Order #<?= $order_id ?></h3>

            <div class="order-wrapper" style="display: flex; gap: 20px;">

                <div class="cart" style="width: 45%;">
                    <h4>Cart Items</h4><br>
                    <table border="1" width="100%">
                        <tr><th>Name</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                        <?php 
                        $grand_total = 0;
                        while($item = $cart_items->fetch_assoc()):
                            $total = $item['quantity'] * $item['price'];
                            $grand_total += $total;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= $item['price'] ?></td>
                            <td><?= $total ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td><strong><?= $grand_total ?></strong></td>
                        </tr>
                    </table>

                    <form method="POST" style="margin-top: 20px; display: flex; gap: 20px;">
                        <input type="hidden" name="finalize_order" value="<?= $order_id ?>">
                        <button style="background-color: green; color: white; padding: 10px 15px; border: none; cursor: pointer;" type="submit" name="finish_order">Save Order</button>
                        <button style="background-color: red; color: white; padding: 10px 15px; border: none; cursor: pointer;" type="submit" name="cancel_order">Cancel Order</button>
                    </form>
                </div>

                <div class="products" style="width: 55%;">
                    <h4>Products</h4>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                        <?php while($product = $products->fetch_assoc()): ?>
                        <div class="product-card" style="border: 1px solid #ccc; padding: 10px; text-align: center;">
                            <img src="uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; height: 100px; object-fit: cover;"><br>
                            <p><strong><?= htmlspecialchars($product['name']) ?></strong></p>
                            <br>
                            <p>Price: <?= number_format($product['price'], 2) ?> EGP</p>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="number" name="quantity" class="quantity" value="1" min="1" style="width: 95%;">
                                <br><br>
                                <button name="add_item" style="padding: 5px 10px;">Add to Order</button>
                            </form>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            </div>

            <?php endif; ?>
<style>
.order-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}
.cart {
    flex: 1;
    background: #f0f4f8;
    padding: 15px;
    border-radius: 8px;
}
.products {
    flex: 2;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}
.product-card {
    width: 180px;
    padding: 10px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    text-align: center;
}
.product-card img {
    width: 100%;
    height: 100px;
    object-fit: contain;
}
.quantity{
    width: 100%;
}
button {
    background-color: #2563eb;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    margin-top: 5px;
}
</style>

            <?php
            break;

            default:
                echo "<h1>Welcome to the Supermarket Dashboard</h1>";
                echo "<p>Select an item from the sidebar to manage the system.</p>";
                break;
        }
        ?>
    </main>
</div>

<script>
function openModal(id, name, price) {
    document.getElementById('update_id').value = id;
    document.getElementById('update_name').value = name;
    document.getElementById('update_price').value = price;
    document.getElementById('updateModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('updateModal').style.display = 'none';
}
</script>


</body>
</html>
