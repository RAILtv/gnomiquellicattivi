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

// Recupera i fumetti letti e i volumi per la sezione profilo
$sql = "SELECT f.titolo, c.quantita_letti, c.quantita FROM fumetti f INNER JOIN collezione c ON f.id = c.fumetto_id WHERE c.utente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$letture = $stmt->get_result();
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

        table {
            width: 100%;
            color: white;
            background: #234248;
            border-radius: 10px;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            color: #8ecae0;
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
            <div class="profile-info">
                <h3 style="color:#8ecae0;">I tuoi progressi di lettura:</h3>
                <table>
                    <tr><th>Fumetto</th><th>Pagina</th><th>Volume</th></tr>
                    <?php while($r = $letture->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['titolo']); ?></td>
                            <td><?php echo $r['quantita_letti']; ?></td>
                            <td><?php echo $r['quantita']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
