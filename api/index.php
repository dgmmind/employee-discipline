<?php
// Habilitar sesiones
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/ManagerController.php';
require_once __DIR__ . '/controllers/EmployeeController.php';

if (!defined('SUPABASE_URL') || !defined('SUPABASE_SERVICE_ROLE') || SUPABASE_SERVICE_ROLE === 'REEMPLAZA_CON_TU_SERVICE_ROLE_KEY') {
  http_response_code(500);
  echo json_encode(['error' => 'Configura SUPABASE_URL y SUPABASE_SERVICE_ROLE en api/config.php']);
  exit;
}

function respond($status, $payload) {
  http_response_code($status);
  echo json_encode($payload);
  exit;
}

function supabase_request($method, $path, $query = [], $body = null, $preferReturn = 'representation') {
  $url = SUPABASE_REST_URL . $path;
  if (!empty($query)) {
    $url .= '?' . http_build_query($query);
  }
  $ch = curl_init($url);
  $headers = [
    'apikey: ' . SUPABASE_SERVICE_ROLE,
    'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE,
    'Content-Type: application/json',
  ];
  if ($method === 'POST' || $method === 'PATCH') {
    $headers[] = 'Prefer: return=' . $preferReturn; // representation|minimal
  }
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  if ($body !== null) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
  }
  $resp = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($resp === false) {
    $err = curl_error($ch);
    curl_close($ch);
    return ['status' => 500, 'data' => null, 'error' => $err];
  }
  curl_close($ch);
  $data = json_decode($resp, true);
  $error = null;
  if ($status >= 400) {
    $error = $data ?: ['message' => 'HTTP ' . $status];
  }
  return ['status' => $status, 'data' => $data, 'error' => $error];
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Tabla de ruteo por controlador
$managerActions = [
  'login','session_status','logout',
  'weeks_current','weeks_create','weeks_list',
  'evaluations_by_week','evaluations_bulk_insert','evaluation_update',
];
$employeeActions = [
  'employee_login','employee_session_status','employee_logout',
  'evaluations_by_week_employee',
];

if (in_array($action, $managerActions, true)) {
  ManagerController::handle($action, $method, $input);
} elseif (in_array($action, $employeeActions, true)) {
  EmployeeController::handle($action, $method, $input);
} else {
  respond(404, ['error' => 'Ruta no encontrada', 'hint' => 'Usa action=weeks_create|weeks_list|weeks_current|evaluations_by_week|evaluations_by_week_employee|evaluations_bulk_insert|evaluation_update|login|session_status|logout|employee_login|employee_session_status|employee_logout']);
}
