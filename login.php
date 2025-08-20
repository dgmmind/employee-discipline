<?php
session_start();
function redirect($path){ header('Location: ' . $path); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
$username = strtolower(trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';
if ($username === '' || $password === '') redirect('index.php?error=1');
$managersPath = __DIR__ . '/json/managers.json';
if (!file_exists($managersPath)) redirect('index.php?error=1');
$data = json_decode(file_get_contents($managersPath), true);
if (!is_array($data)) redirect('index.php?error=1');
// Buscar manager por username
foreach ($data as $managerId => $m) {
  if (isset($m['username']) && strtolower($m['username']) === $username) {
    $expected = $m['password'] ?? null;
    if (!is_string($expected) || $expected !== $password) redirect('index.php?error=1');
    $_SESSION['manager_id'] = $managerId;
    redirect('manager.php');
  }
}
// Buscar empleado por username
foreach ($data as $managerId => $m) {
  foreach (($m['employees'] ?? []) as $emp) {
    if (isset($emp['username']) && strtolower($emp['username']) === $username) {
      $expected = $emp['password'] ?? null;
      if (!is_string($expected) || $expected !== $password) redirect('index.php?error=1');
      $_SESSION['employee_id'] = $emp['id'];
      $_SESSION['manager_id_for_employee'] = $managerId;
      redirect('employee.php');
    }
  }
}
redirect('index.php?error=1');
