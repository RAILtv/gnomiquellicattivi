<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Account - Gnome Reads</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #5291ad;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-card {
            background-color: #234248;
            padding: 40px;
            border-radius: 30px;
            color: white;
            margin-top: 20px;
        }

        .nav-bar {
            background-color: #234248;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-bar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .nav-bar a:hover {
            background-color: #12322b;
        }

        .logout-btn {
            background-color: #12322b;
        }

        h1 {
            color: white;
            margin-bottom: 30px;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 18px;
        }

        .label {
            font-weight: bold;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <div>
            <a href="homepage.php">Home</a>
            <a href="tutti_i_fumetti.php">I miei fumetti</a>
            <a href="account.php">Account</a>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <h1>Il tuo profilo</h1>
        <div class="profile-card">
            <div class="profile-info">
                <p><span class="label">Username:</span> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><span class="label">Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
