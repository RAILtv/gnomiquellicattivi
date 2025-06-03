<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';
$user_id = $_SESSION['user_id'];

// Aggiorna pagina letta
if (isset($_POST['update_page'])) {
    $fumetto_id = intval($_POST['fumetto_id']);
    $pagina = intval($_POST['pagina']);
    // Aggiorna quantita_letti
    $sql = "UPDATE collezione SET quantita_letti = ? WHERE utente_id = ? AND fumetto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $pagina, $user_id, $fumetto_id);
    $stmt->execute();
}
// Aggiorna volume
if (isset($_POST['update_volume'])) {
    $fumetto_id = intval($_POST['fumetto_id']);
    $volume = intval($_POST['volume']);
    $sql = "UPDATE collezione SET quantita = ? WHERE utente_id = ? AND fumetto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $volume, $user_id, $fumetto_id);
    $stmt->execute();
}

// Aggiungi fumetto alla collezione
if (isset($_POST['add_fumetto'])) {
    $fumetto_id = intval($_POST['fumetto_id']);
    // Prendi numero volumi
    $sql = "SELECT numero_volumi FROM fumetti WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fumetto_id);
    $stmt->execute();
    $stmt->bind_result($num_volumi);
    $stmt->fetch();
    $stmt->close();
    // Inserisci nella collezione
    $sql = "INSERT INTO collezione (utente_id, fumetto_id, quantita, quantita_letti, quantita_nonletti) VALUES (?, ?, 1, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $fumetto_id, $num_volumi);
    $stmt->execute();
}

// Cancella fumetto dalla collezione
if (isset($_POST['delete_fumetto'])) {
    $fumetto_id = intval($_POST['fumetto_id']);
    $sql = "DELETE FROM collezione WHERE utente_id = ? AND fumetto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $fumetto_id);
    $stmt->execute();
}

// Gestione inserimento commento
if (isset($_POST['add_comment'])) {
    $fumetto_id = intval($_POST['fumetto_id']);
    $commento = trim($_POST['commento']);
    if ($commento !== '') {
        $sql = "INSERT INTO commenti (utente_id, fumetto_id, testo) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $fumetto_id, $commento);
        $stmt->execute();
    }
}

// Fumetti nella collezione
$sql = "SELECT f.*, c.quantita, c.quantita_letti, c.quantita_nonletti FROM fumetti f INNER JOIN collezione c ON f.id = c.fumetto_id WHERE c.utente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fumetti non ancora nella collezione
$sql = "SELECT * FROM fumetti WHERE id NOT IN (SELECT fumetto_id FROM collezione WHERE utente_id = ?)";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$not_in_collection = $stmt2->get_result();
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

        .comics-table {
            width: 100%;
            background-color: #234248;
            border-radius: 10px;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            color: white;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .comics-table th, .comics-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(18, 50, 43, 0.5);
        }

        .comics-table th {
            background-color: #12322b;
            font-weight: bold;
        }

        .comics-table tr:hover {
            background-color: #2d535b;
        }

        .add-btn, .update-btn, .important-btn {
            background-color: #b94a32;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        .add-btn:hover, .update-btn:hover, .important-btn:hover {
            background-color: #a13e29;
            transform: scale(1.05);
        }

        .plus-btn {
            background-color: #b94a32;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1.2em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s, transform 0.3s;
        }

        .plus-btn:hover {
            background-color: #a13e29;
            transform: scale(1.1);
        }

        .add-section {
            margin: 30px 0 10px 0;
            text-align: center;
        }

        .add-form {
            display: inline-block;
            margin-top: 10px;
        }

        .page-input {
            width: 60px;
            border-radius: 10px;
            border: 1px solid #8ecae0;
            padding: 5px 10px;
            font-size: 1em;
            text-align: center;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .search-input, .genre-select {
            padding: 12px 20px;
            border-radius: 25px;
            background-color: #234248;
            color: white;
            border: 2px solid rgba(255,255,255,0.1);
            font-size: 1em;
        }

        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .genre-select {
            min-width: 220px;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <div class="nav-left">
            <img src="logo_folletto.png" alt="Logo Folletto" style="height:38px; margin-right:18px; vertical-align:middle; border-radius:50%; box-shadow:0 2px 8px #12322b; background:#fff;">
            <a href="homepage.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
            <a href="tutti_i_fumetti.php" class="nav-item"><i class="fas fa-books"></i> Catalogo</a>
            <a href="la_mia_collezione.php" class="nav-item active"><i class="fas fa-bookmark"></i> La Mia Collezione</a>
            <a href="preferiti.php" class="nav-item"><i class="fas fa-star"></i> Preferiti</a>
        </div>
        <div class="nav-right">
            <img src="logo_folletto.png" alt="Logo Folletto" style="height:38px; margin-right:18px; vertical-align:middle; border-radius:50%; box-shadow:0 2px 8px #12322b; background:#fff;">
            <a href="account.php" class="nav-item"><i class="fas fa-user"></i> Account</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="container">
        <h1>La Mia Collezione</h1>
        <div class="search-container" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px;">
            <input type="text" id="searchInput" onkeyup="searchComics()" placeholder="Cerca per titolo o autore..." class="search-input" style="padding: 12px 20px; border-radius: 25px; background-color: #234248; color: white; border: 2px solid rgba(255,255,255,0.1); width: 400px; font-size: 1em;">
            <select id="genereFilter" onchange="filterByGenre(this.value)" class="genre-select" style="padding: 12px 20px; border-radius: 25px; background-color: #234248; color: white; border: 2px solid rgba(255,255,255,0.1); min-width: 220px; font-size: 1em;">
                <option value="">Tutti i Generi</option>
                <?php
                $genres = $conn->query("SELECT DISTINCT genere FROM fumetti ORDER BY genere");
                while($g = $genres->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($g['genere']) . '">' . htmlspecialchars($g['genere']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="add-section">
            <form method="post" class="add-form" style="display: flex; justify-content: center; align-items: center; gap: 15px;">
                <input type="text" id="addSearchInput" onkeyup="filterAddOptions()" placeholder="Cerca fumetto da aggiungere..." class="search-input" style="width: 250px;">
                <select name="fumetto_id" id="addSelect" required class="page-input" style="min-width: 220px; font-size: 1em;">
                    <option value="">Seleziona fumetto da aggiungere...</option>
                    <?php $not_in_collection->data_seek(0); while($f = $not_in_collection->fetch_assoc()): ?>
                        <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['titolo']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="add_fumetto" class="add-btn" style="font-weight:bold; letter-spacing:1px;">AGGIUNGI</button>
            </form>
        </div>
        <table class="comics-table" id="comicsTable">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Autore</th>
                    <th>Anno</th>
                    <th>Genere</th>
                    <th>Descrizione</th>
                    <th>Numero Volumi</th>
                    <th>Pagina</th>
                    <th></th>
                    <th>Volume</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['titolo']); ?></td>
                            <td><?php echo htmlspecialchars($row['autore']); ?></td>
                            <td><?php echo htmlspecialchars($row['anno']); ?></td>
                            <td><?php echo htmlspecialchars($row['genere']); ?></td>
                            <td><?php echo htmlspecialchars($row['descrizione']); ?></td>
                            <td><?php echo $row['numero_volumi']; ?></td>
                            <td>
                                <form method="post" style="display:inline-flex; align-items:center; gap:5px;">
                                    <input type="hidden" name="fumetto_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="pagina" min="0" max="<?php echo $row['numero_volumi']; ?>" value="<?php echo $row['quantita_letti']; ?>" class="page-input">
                                    <button type="submit" name="update_page" class="plus-btn"><i class="fas fa-plus"></i></button>
                                </form>
                            </td>
                            <td></td>
                            <td>
                                <form method="post" style="display:inline-flex; align-items:center; gap:5px;">
                                    <input type="hidden" name="fumetto_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="volume" min="0" max="<?php echo $row['numero_volumi']; ?>" value="<?php echo $row['quantita']; ?>" class="page-input">
                                    <button type="submit" name="update_volume" class="plus-btn"><i class="fas fa-plus"></i></button>
                                </form>
                            </td>
                            <td>
                                <form method="post" onsubmit="return confirm('Sei sicuro di voler rimuovere questo fumetto dalla tua collezione?');" style="display:inline;">
                                    <input type="hidden" name="fumetto_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_fumetto" class="add-btn" style="background-color:#b94a32; font-weight:bold;">RIMUOVI</button>
                                </form>
                                <a href="commenti.php?fumetto_id=<?php echo $row['id']; ?>" class="comment-btn" title="Commenti" style="background:#18313a; color:#b94a32; border-radius:50%; width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; font-size:1.3em; margin-left:8px; text-decoration:none;"><span aria-label="Commenta">&#128172;</span></a>
                            </td>
                        </tr>
                    <?php endwhile;
                else:
                    echo '<tr><td colspan="10" style="text-align: center; color: white;">Non hai ancora aggiunto fumetti alla tua collezione.</td></tr>';
                endif; ?>
            </tbody>
        </table>
    </div>
    <script>
    function filterByGenre() { filterComics(); }
    function searchComics() { filterComics(); }
    function filterComics() {
        const rows = document.querySelectorAll('#comicsTable tbody tr');
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const selectedGenre = document.getElementById('genereFilter').value;
        rows.forEach(row => {
            const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const author = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const genre = row.querySelector('td:nth-child(4)').textContent;
            const matchesSearch = title.includes(searchText) || author.includes(searchText);
            const matchesGenre = !selectedGenre || genre === selectedGenre;
            row.style.display = (matchesSearch && matchesGenre) ? '' : 'none';
        });
    }

    function filterAddOptions() {
        var input = document.getElementById('addSearchInput').value.toLowerCase();
        var select = document.getElementById('addSelect');
        for (var i = 0; i < select.options.length; i++) {
            var txt = select.options[i].text.toLowerCase();
            select.options[i].style.display = txt.includes(input) ? '' : 'none';
        }
    }
    </script>
</body>
</html>
