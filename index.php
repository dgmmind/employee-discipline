<?php
session_start();
// Redirecciones si ya hay sesión
if (!empty($_SESSION['manager_id'])) {
  header('Location: manager.php');
  exit;
}
if (!empty($_SESSION['employee_id'])) {
  header('Location: employee.php');
  exit;
}
$error = isset($_GET['error']) ? 'Credenciales inválidas' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Evaluación de Empleados</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{--primary:#2B6CB0;--text:#111}
    body{font-family:Arial,sans-serif;background:#f5f5f5;padding:20px}
    .container{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1);overflow:hidden}
    .login-screen{padding:40px;text-align:center}
    .login-form{max-width:320px;margin:0 auto;text-align:left}
    .form-group{margin-bottom:16px}
    label{display:block;margin-bottom:6px;font-weight:700}
    input{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px}
    .btn{background:var(--primary);color:#fff;padding:12px 16px;border:none;border-radius:6px;cursor:pointer;width:100%;font-weight:700}
    .error{color:#dc2626;margin-bottom:10px}
  </style>
</head>
<body>
  <div class="container">
    <div class="login-screen">
      <h2 style="margin-bottom:10px;">Sistema de Evaluación de Empleados</h2>
      <p style="color:#555;margin-bottom:20px;">Ingrese sus credenciales</p>
      <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form class="login-form" action="login.php" method="POST" autocomplete="on">
        <div class="form-group">
          <label for="username">Usuario</label>
          <input type="text" id="username" name="username" placeholder="ej. dmaldonado" required />
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required />
        </div>
        <button type="submit" class="btn">Ingresar</button>
      </form>
    </div>
  </div>
</body>
</html>
