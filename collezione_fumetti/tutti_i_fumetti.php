<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$sql = 'SELECT * FROM fumetti';
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>I Miei Fumetti - Gnome Reads</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #5291ad;
            min-height: 100vh;
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

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .comics-table {
            width: 100%;
            background-color: #234248;
            border-radius: 10px;
            border-collapse: collapse;
            margin-top: 20px;
            color: white;
            overflow: hidden;
        }

        .comics-table th,
        .comics-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #12322b;
        }

        .comics-table th {
            background-color: #12322b;
            font-weight: bold;
        }

        .comics-table tr:hover {
            background-color: #2d535b;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
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
        <h1>La Mia Collezione di Fumetti</h1>
        <table class="comics-table">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Autore</th>
                    <th>Anno</th>
                    <th>Genere</th>
                    <th>Descrizione</th>
                    <th>Numero Volumi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($fumetto = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fumetto['titolo']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['autore']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['anno']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['genere']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['descrizione']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['numero_volumi']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center;'>Nessun fumetto trovato</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

