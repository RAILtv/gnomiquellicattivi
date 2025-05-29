<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT f.*, c.quantita, c.quantita_letti, c.quantita_nonletti 
        FROM fumetti f 
        INNER JOIN collezione c ON f.id = c.fumetto_id 
        WHERE c.utente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>La Mia Collezione - Gnome Reads</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-left, .nav-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-item i {
            font-size: 16px;
        }

        .nav-item:hover {
            background-color: #12322b;
            transform: translateY(-2px);
        }

        .nav-item.active {
            background-color: #12322b;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logout-btn {
            background-color: #12322b;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background-color: #0a1e1a;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }

        .collection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .comic-card {
            background-color: #234248;
            border-radius: 15px;
            padding: 20px;
            color: white;
            transition: transform 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .comic-card:hover {
            transform: translateY(-5px);
        }

        .comic-title {
            font-size: 1.2em;
            color: #8ecae0;
            margin-bottom: 10px;
        }

        .comic-info {
            margin: 5px 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .progress-bar {
            width: 100%;
            background-color: #12322b;
            height: 10px;
            border-radius: 5px;
            margin: 10px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #8ecae0;
            transition: width 0.3s;
        }

        .stats {
            color: #8ecae0;
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <div class="nav-left">
            <a href="homepage.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
            <a href="tutti_i_fumetti.php" class="nav-item"><i class="fas fa-books"></i> Catalogo</a>
            <a href="la_mia_collezione.php" class="nav-item active"><i class="fas fa-bookmark"></i> La Mia Collezione</a>
            <a href="preferiti.php" class="nav-item"><i class="fas fa-star"></i> Preferiti</a>
        </div>
        <div class="nav-right">
            <a href="account.php" class="nav-item"><i class="fas fa-user"></i> Account</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>La Mia Collezione</h1>
        
        <div class="collection-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $progress = ($row['quantita_letti'] / $row['numero_volumi']) * 100;
                    echo '<div class="comic-card">';
                    echo '<div class="comic-title">' . htmlspecialchars($row['titolo']) . '</div>';
                    echo '<div class="comic-info">Autore: ' . htmlspecialchars($row['autore']) . '</div>';
                    echo '<div class="comic-info">Genere: ' . htmlspecialchars($row['genere']) . '</div>';
                    echo '<div class="progress-bar">';
                    echo '<div class="progress-fill" style="width: ' . $progress . '%;"></div>';
                    echo '</div>';
                    echo '<div class="stats">';
                    echo 'Letti: ' . $row['quantita_letti'] . ' / ' . $row['numero_volumi'];
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align: center; color: white; grid-column: 1/-1;">Non hai ancora aggiunto fumetti alla tua collezione.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
