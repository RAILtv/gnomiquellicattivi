<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check_sql = "SELECT id FROM utenti WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $error = "Email già registrata";
    } else {
        $sql = "INSERT INTO utenti (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            header('Location: homepage.php');
            exit();
        } else {
            $error = "Errore durante la registrazione";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Registrazione - Gnome Reads</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #5291ad;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .form-container {
      background-color: #234248;
      padding: 40px;
      border-radius: 30px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      width: 400px;
    }

    .form-container input,
    .form-container button {
      background-color: #0c1b16;
      color: white;
      border: none;
      border-radius: 30px;
      padding: 20px;
      font-size: 16px;
      text-align: center;
      outline: none;
    }

    .form-container input::placeholder {
      color: white;
    }

    .form-container button:hover {
      background-color: #12322b;
      cursor: pointer;
    }
    
    .error {
      color: #ff4444;
      text-align: center;
    }
    
    .login-link {
      color: white;
      text-align: center;
      text-decoration: none;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <form class="form-container" method="POST" action="register.php">
    <?php if (isset($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <input type="text" name="username" placeholder="USERNAME" required>
    <input type="email" name="email" placeholder="E-MAIL" required>
    <input type="password" name="password" placeholder="PASSWORD" required>
    <button type="submit">REGISTRATI</button>
    <a href="login.php" class="login-link">Hai già un account? Accedi</a>
  </form>
</body>
</html>
