<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'config.php';
$user_id = $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$fumetto_id = isset($_GET['fumetto_id']) ? intval($_GET['fumetto_id']) : 0;
if ($fumetto_id <= 0) {
    die('Fumetto non valido.');
}
// Recupera info fumetto
$stmt = $conn->prepare('SELECT * FROM fumetti WHERE id = ?');
$stmt->bind_param('i', $fumetto_id);
$stmt->execute();
$fumetto = $stmt->get_result()->fetch_assoc();
if (!$fumetto) die('Fumetto non trovato.');

// Gestione invio commento
if (isset($_POST['commento']) && trim($_POST['commento']) !== '') {
    $testo = trim($_POST['commento']);
    $sql = 'INSERT INTO commenti (utente_id, fumetto_id, testo) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $user_id, $fumetto_id, $testo);
    $stmt->execute();
}
// Recupera tutti i commenti
$stmt = $conn->prepare('SELECT c.testo, u.username, c.data_inserimento, c.utente_id FROM commenti c JOIN utenti u ON c.utente_id = u.id WHERE c.fumetto_id = ? ORDER BY c.data_inserimento ASC');
$stmt->bind_param('i', $fumetto_id);
$stmt->execute();
$commenti = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Commenti - <?php echo htmlspecialchars($fumetto['titolo']); ?></title>
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
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .comic-info-table {
            width: 100%;
            background-color: #18313a;
            border-radius: 10px 10px 0 0;
            color: white;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }
        .comic-info-table th, .comic-info-table td {
            padding: 10px 15px;
            text-align: left;
        }
        .chat-box {
            background: #10201b;
            min-height: 350px;
            max-height: 400px;
            overflow-y: auto;
            padding: 30px 40px 30px 40px;
            border-radius: 0 0 15px 15px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .msg-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 0;
        }
        .msg-row.right { flex-direction: row-reverse; }
        .msg-bubble {
            background: #eaf7e3;
            color: #234248;
            border-radius: 30px 30px 30px 10px;
            padding: 18px 30px;
            font-size: 1.1em;
            max-width: 60%;
            min-width: 120px;
            margin: 0 10px;
            position: relative;
        }
        .msg-row.right .msg-bubble {
            border-radius: 30px 30px 10px 30px;
        }
        .msg-user {
            font-size: 2em;
            color: #fff;
            margin: 0 10px;
        }
        .msg-user.right { margin-left: 0; margin-right: 10px; }
        .msg-user.left { margin-right: 0; margin-left: 10px; }
        .msg-date {
            color: #aaa;
            font-size: 0.85em;
            margin-top: 2px;
            margin-left: 8px;
        }
        .comment-form {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            justify-content: center;
        }
        .comment-form input[type=text] {
            flex: 1;
            border-radius: 20px;
            border: 1px solid #8ecae0;
            padding: 12px 20px;
            font-size: 1em;
        }
        .comment-form button {
            background-color: #b94a32;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 28px;
            font-size: 1em;
            cursor: pointer;
            font-weight: bold;
            letter-spacing: 1px;
            transition: background 0.3s, transform 0.3s;
        }
        .comment-form button:hover {
            background-color: #a13e29;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <div class="nav-left">
            <a href="homepage.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
            <a href="tutti_i_fumetti.php" class="nav-item active"><i class="fas fa-books"></i> Catalogo</a>
            <a href="la_mia_collezione.php" class="nav-item"><i class="fas fa-bookmark"></i> La Mia Collezione</a>
            <a href="preferiti.php" class="nav-item"><i class="fas fa-star"></i> Preferiti</a>
        </div>
        <div class="nav-right">
            <a href="account.php" class="nav-item"><i class="fas fa-user"></i> Account</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="container">
        <h1>Commenti</h1>
        <table class="comic-info-table">
            <tr>
                <th>Titolo</th><th>Autore</th><th>Anno</th><th>Genere</th><th>Descrizione</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($fumetto['titolo']); ?></td>
                <td><?php echo htmlspecialchars($fumetto['autore']); ?></td>
                <td><?php echo htmlspecialchars($fumetto['anno']); ?></td>
                <td><?php echo htmlspecialchars($fumetto['genere']); ?></td>
                <td><?php echo htmlspecialchars($fumetto['descrizione']); ?></td>
            </tr>
        </table>
        <div class="chat-box">
            <?php
            if ($commenti->num_rows > 0) {
                while($c = $commenti->fetch_assoc()) {
                    $isMe = ($c['username'] === $username);
                    $side = $isMe ? 'right' : 'left';
                    echo '<div class="msg-row ' . $side . '">';
                    echo '<span class="msg-user ' . $side . '"><i class="fas fa-user-circle"></i></span>';
                    echo '<div class="msg-bubble">' . htmlspecialchars($c['testo']) . '<div class="msg-date">' . htmlspecialchars($c['username']) . ' - ' . date('d/m/Y H:i', strtotime($c['data_inserimento'])) . '</div></div>';
                    echo '</div>';
                }
            } else {
                echo '<div style="color:#aaa; text-align:center;">Nessun commento ancora. Scrivi il primo!</div>';
            }
            ?>
        </div>
        <form method="post" class="comment-form" autocomplete="off">
            <input type="text" name="commento" placeholder="Scrivi un commento..." maxlength="500" required>
            <button type="submit">Invia</button>
        </form>
    </div>
</body>
</html>
