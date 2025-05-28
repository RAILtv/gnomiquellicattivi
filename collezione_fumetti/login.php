<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM utenti WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: homepage.php');
            exit();
        }
    }
    $error = "Email o password non validi";
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Accesso - Gnome Reads</title>
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
    
    .register-link {
      color: white;
      text-align: center;
      text-decoration: none;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <form class="form-container" method="POST" action="login.php">
    <?php if (isset($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <input type="email" name="email" placeholder="E-MAIL" required>
    <input type="password" name="password" placeholder="PASSWORD" required>
    <button type="submit">ACCEDI</button>
    <a href="register.php" class="register-link">Non hai un account? Registrati</a>
  </form>
</body>
</html>
