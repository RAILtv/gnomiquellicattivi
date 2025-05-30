<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Homepage - Gnome Reads</title>
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
            text-align: center;
        }

        .welcome-section {
            background-color: #234248;
            padding: 40px;
            border-radius: 30px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .feature-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .view-comics-btn {
            display: inline-block;
            background-color: #12322b;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1.2em;
            margin: 20px 0;
            transition: background-color 0.3s, transform 0.3s;
        }

        .view-comics-btn:hover {
            background-color: #1a4d42;
            transform: scale(1.05);
        }

        .card {
            background-color: #234248;
            padding: 30px;
            border-radius: 20px;
            color: white;
            transition: transform 0.3s;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            background-color: #2d535b;
        }

        .card h3 {
            margin-top: 0;
            color: #8ecae0;
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .card p {
            margin-bottom: 0;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .comics-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
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
            font-size: 0.9em;
            color: rgba(255, 255, 255, 0.8);
        }

        .rating {
            color: #ffd700;
            margin-top: 10px;
        }

        h1 {
            color: #8ecae0;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .welcome-text {
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #8ecae0;
        }

        .stat-label {
            font-size: 0.9em;
            color: #ffffff;
        }

        .logo {
            width: 150px;
            margin-bottom: 20px;
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
        <div class="welcome-section">
            <img src="logo_folletto.png" alt="Logo Folletto" class="logo" style="width: 200px; height: auto; display: block; margin: 0 auto 20px;">
            <h1>Benvenuto in Gnome Reads</h1>
            <p class="welcome-text">
                La tua biblioteca digitale di fumetti fantasy. Esplora mondi magici, 
                incontra creature straordinarie e vivi avventure uniche attraverso 
                la nostra collezione di fumetti.
            </p>
            
            <?php
            // Get some statistics
            $sql = "SELECT COUNT(*) as total FROM fumetti";
            $result = $conn->query($sql);
            $total_comics = $result->fetch_assoc()['total'];
            
            $sql = "SELECT COUNT(DISTINCT genere) as genres FROM fumetti";
            $result = $conn->query($sql);
            $total_genres = $result->fetch_assoc()['genres'];
            ?>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_comics; ?></div>
                    <div class="stat-label">Fumetti Totali</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_genres; ?></div>
                    <div class="stat-label">Generi Diversi</div>
                </div>
            </div>

            <a href="tutti_i_fumetti.php" class="view-comics-btn">
                Visualizza Tutti i Fumetti
            </a>
        </div>

        <div class="feature-cards">
            <div class="card" onclick="window.location.href='tutti_i_fumetti.php'">
                <h3>Esplora la Collezione</h3>
                <p>Sfoglia la nostra vasta collezione di fumetti fantasy e trova la tua prossima avventura.</p>
            </div>
            <div class="card" onclick="window.location.href='account.php'">
                <h3>Il Tuo Profilo</h3>
                <p>Gestisci il tuo account e personalizza la tua esperienza di lettura.</p>
            </div>
            <div class="card">
                <h3>Novità in Arrivo</h3>
                <p>Resta aggiornato sulle ultime uscite e le prossime aggiunte alla collezione.</p>
            </div>
        </div>

        <h2 style="color: white; text-align: center; margin-top: 40px;">Fumetti in Evidenza</h2>
        <div class="comics-preview">
            <?php
            $sql = "SELECT * FROM fumetti ORDER BY rating DESC LIMIT 4";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while($fumetto = $result->fetch_assoc()) {
                    echo '<div class="comic-card">';
                    echo '<div class="comic-title">' . htmlspecialchars($fumetto['titolo']) . '</div>';
                    echo '<div class="comic-info">';
                    echo '<div>Autore: ' . htmlspecialchars($fumetto['autore']) . '</div>';
                    echo '<div>Genere: ' . htmlspecialchars($fumetto['genere']) . '</div>';
                    echo '<div>Anno: ' . htmlspecialchars($fumetto['anno']) . '</div>';
                    echo '</div>';
                    echo '<div class="rating">★ ' . number_format($fumetto['rating'], 1) . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
