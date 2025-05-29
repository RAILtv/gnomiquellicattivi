<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Get all comics
$sql = 'SELECT * FROM fumetti';
$comics_result = $conn->query($sql);

// Get distinct genres for filter
$genres_sql = "SELECT DISTINCT genere FROM fumetti ORDER BY genere";
$genres_result = $conn->query($genres_sql);
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

        .comics-table th,
        .comics-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(18, 50, 43, 0.5);
        }

        .comics-table td {
            transition: background-color 0.3s;
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

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .search-input {
            padding: 12px 20px;
            border-radius: 25px;
            background-color: #234248;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.1);
            width: 300px;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #8ecae0;
            box-shadow: 0 0 0 2px rgba(142, 202, 224, 0.2);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .genre-select {
            padding: 12px 20px;
            border-radius: 25px;
            background-color: #234248;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s;
            min-width: 200px;
        }

        .genre-select:focus {
            outline: none;
            border-color: #8ecae0;
        }

        .genre-select option {
            background-color: #234248;
            color: white;
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

    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <div class="container">
        <h1>La Mia Collezione di Fumetti</h1>
        
        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="searchComics()" placeholder="Cerca per titolo o autore..." class="search-input">
            
            <select id="genereFilter" onchange="filterByGenre(this.value)" class="genre-select">
                <option value="">Tutti i Generi</option>
                <?php
                while($row = $genres_result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['genere']) . '">' . htmlspecialchars($row['genere']) . '</option>';
                }
                ?>
            </select>
        </div>
        
        <table class="comics-table">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Autore</th>
                    <th>Anno</th>
                    <th>Genere</th>
                    <th>Descrizione</th>
                    <th>Numero Volumi</th>
                    <th>Valutazione</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($comics_result && $comics_result->num_rows > 0) {
                    while($fumetto = $comics_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fumetto['titolo']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['autore']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['anno']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['genere']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['descrizione']) . "</td>";
                        echo "<td>" . htmlspecialchars($fumetto['numero_volumi']) . "</td>";
                        echo "<td style='color: #ffd700;'>★ " . number_format($fumetto['rating'], 1) . "</td>";
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

    <script>
    function filterByGenre(genre) {
        filterComics();
    }

    function searchComics() {
        filterComics();
    }

    function filterComics() {
        const rows = document.querySelectorAll('.comics-table tbody tr');
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
    </script>
</body>
</html>

