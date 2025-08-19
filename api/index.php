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

switch ($action) {
  case 'login': { // POST {manager_id}
    if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    $manager_id = $input['manager_id'] ?? null;
    if (!$manager_id) respond(400, ['error' => 'manager_id requerido']);
    $_SESSION['manager_id'] = $manager_id;
    respond(200, ['ok' => true, 'manager_id' => $manager_id]);
  }
  case 'employee_login': { // POST {employee_id, manager_id}
    if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    $employee_id = $input['employee_id'] ?? null;
    $manager_id  = $input['manager_id']  ?? null;
    if (!$employee_id) respond(400, ['error' => 'employee_id requerido']);
    $_SESSION['employee_id'] = $employee_id;
    if ($manager_id) { $_SESSION['manager_id_for_employee'] = $manager_id; }
    respond(200, ['ok' => true, 'employee_id' => $employee_id, 'manager_id' => $manager_id]);
  }
  case 'session_status': { // GET
    if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
    $mgr = $_SESSION['manager_id'] ?? null;
    respond(200, ['logged_in' => !!$mgr, 'manager_id' => $mgr]);
  }
  case 'employee_session_status': { // GET
    if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
    $emp = $_SESSION['employee_id'] ?? null;
    $mgrForEmp = $_SESSION['manager_id_for_employee'] ?? null;
    respond(200, ['logged_in' => !!$emp, 'employee_id' => $emp, 'manager_id' => $mgrForEmp]);
  }
  case 'logout': { // POST
    if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    respond(200, ['ok' => true]);
  }
  case 'employee_logout': { // POST
    if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    // Limpiar solo claves de empleado
    unset($_SESSION['employee_id']);
    unset($_SESSION['manager_id_for_employee']);
    respond(200, ['ok' => true]);
  }
  case 'weeks_current': { // GET ?manager_id=...
    if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
    $manager_id = $_GET['manager_id'] ?? ($_SESSION['manager_id'] ?? null);
    if (!$manager_id) respond(400, ['error' => 'manager_id requerido']);
    $today = new DateTime('now', new DateTimeZone('UTC'));
    $todayStr = $today->format('Y-m-d');
    // Buscar semana que contenga hoy
    $query = [
      'manager_id' => 'eq.' . $manager_id,
      'start_date' => 'lte.' . $todayStr,
      'end_date'   => 'gte.' . $todayStr,
      'order' => 'start_date.desc',
      'limit' => 1,
    ];
    $res = supabase_request('GET', '/weeks', $query);
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    $current = $res['data'];
    if (!empty($current)) {
      respond(200, ['week' => $current[0]]);
    }
    // Si no hay semana actual, devolver la más reciente (opcional)
    $res2 = supabase_request('GET', '/weeks', [
      'manager_id' => 'eq.' . $manager_id,
      'order' => 'start_date.desc',
      'limit' => 1,
    ]);
    if ($res2['error']) respond($res2['status'], ['error' => $res2['error']]);
    respond(200, ['week' => $res2['data'][0] ?? null]);
  }
  case 'weeks_create': { // POST {manager_id, start_date, end_date}
    if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    $manager_id = $input['manager_id'] ?? null;
    $start_date = $input['start_date'] ?? null;
    $end_date   = $input['end_date'] ?? null;
    if (!$manager_id || !$start_date || !$end_date) respond(400, ['error' => 'manager_id, start_date, end_date requeridos']);

    $res = supabase_request('POST', '/weeks', [], [[
      'manager_id' => $manager_id,
      'start_date' => $start_date,
      'end_date'   => $end_date,
    ]], 'representation');
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    // devuelve la fila creada (incluye id)
    respond(200, ['week' => $res['data'][0] ?? null]);
  }
  case 'weeks_list': { // GET ?manager_id=...
    if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
    $manager_id = $_GET['manager_id'] ?? null;
    if (!$manager_id) respond(400, ['error' => 'manager_id requerido']);

    $query = [
      'manager_id' => 'eq.' . $manager_id,
      'order' => 'start_date.desc',
    ];
    $res = supabase_request('GET', '/weeks', $query);
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    respond(200, ['weeks' => $res['data']]);
  }
  case 'evaluations_by_week': { // GET ?week_id=...
    if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
    $week_id = $_GET['week_id'] ?? null;
    if (!$week_id) respond(400, ['error' => 'week_id requerido']);

    $query = [
      'week_id' => 'eq.' . $week_id,
      'select' => 'employee_id,day_index,category,checked,item',
    ];
    $res = supabase_request('GET', '/evaluations', $query);
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    respond(200, ['evaluations' => $res['data']]);
  }
  case 'evaluations_by_week_employee': { // GET ?week_id=...&employee_id=...
    if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
    $week_id = $_GET['week_id'] ?? null;
    $employee_id = $_GET['employee_id'] ?? null;
    if (!$week_id || !$employee_id) respond(400, ['error' => 'week_id y employee_id requeridos']);

    $query = [
      'week_id' => 'eq.' . $week_id,
      'employee_id' => 'eq.' . $employee_id,
      'select' => 'employee_id,day_index,category,checked,item',
    ];
    $res = supabase_request('GET', '/evaluations', $query);
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    respond(200, ['evaluations' => $res['data']]);
  }
  case 'evaluations_bulk_insert': { // POST {rows: [...]}
    if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    $rows = $input['rows'] ?? null;
    if (!$rows || !is_array($rows) || count($rows) === 0) respond(400, ['error' => 'rows requerido']);

    $res = supabase_request('POST', '/evaluations', [], $rows, 'minimal');
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    respond(200, ['ok' => true, 'inserted' => count($rows)]);
  }
  case 'evaluation_update': { // PATCH {week_id, employee_id, day_index, category, item, checked}
    if ($method !== 'PATCH' && $method !== 'POST') respond(405, ['error' => 'Method not allowed']);
    $week_id = $input['week_id'] ?? null;
    $employee_id = $input['employee_id'] ?? null;
    $day_index = $input['day_index'] ?? null;
    $category = $input['category'] ?? null;
    $item = $input['item'] ?? null;
    $checked = $input['checked'] ?? null;

    if ($week_id === null || $employee_id === null || $day_index === null || $category === null || $item === null || $checked === null) {
      respond(400, ['error' => 'Campos requeridos: week_id, employee_id, day_index, category, item, checked']);
    }

    $query = [
      'week_id' => 'eq.' . $week_id,
      'employee_id' => 'eq.' . $employee_id,
      'day_index' => 'eq.' . $day_index,
      'category' => 'eq.' . $category,
    ];
    $body = [ 'item' => $item, 'checked' => (bool)$checked ];
    $res = supabase_request('PATCH', '/evaluations', $query, $body, 'minimal');
    if ($res['error']) respond($res['status'], ['error' => $res['error']]);
    respond(200, ['ok' => true]);
  }
  default:
    respond(404, ['error' => 'Ruta no encontrada', 'hint' => 'Usa action=weeks_create|weeks_list|weeks_current|evaluations_by_week|evaluations_by_week_employee|evaluations_bulk_insert|evaluation_update|login|session_status|logout|employee_login|employee_session_status|employee_logout']);
}
