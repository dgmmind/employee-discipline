<?php
class ManagerController {
  public static function handle($action, $method, $input) {
    switch ($action) {
      case 'login': { // POST {manager_id}
        if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
        $manager_id = $input['manager_id'] ?? null;
        if (!$manager_id) respond(400, ['error' => 'manager_id requerido']);
        $_SESSION['manager_id'] = $manager_id;
        respond(200, ['ok' => true, 'manager_id' => $manager_id]);
      }
      case 'session_status': { // GET
        if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
        $mgr = $_SESSION['manager_id'] ?? null;
        respond(200, ['logged_in' => !!$mgr, 'manager_id' => $mgr]);
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
      case 'weeks_current': { // GET ?manager_id=...
        if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
        $manager_id = $_GET['manager_id'] ?? ($_SESSION['manager_id'] ?? null);
        if (!$manager_id) respond(400, ['error' => 'manager_id requerido']);
        $today = new DateTime('now', new DateTimeZone('UTC'));
        $todayStr = $today->format('Y-m-d');
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
        $res = supabase_request('PATCH', '/evaluations', $query, $body, 'representation');
        if ($res['error']) respond($res['status'], ['error' => $res['error']]);
        if (empty($res['data'])) {
          respond(404, ['error' => 'No se encontró registro a actualizar']);
        }
        respond(200, ['ok' => true]);
      }
      default:
        respond(404, ['error' => 'Ruta no encontrada en ManagerController']);
    }
  }
}
