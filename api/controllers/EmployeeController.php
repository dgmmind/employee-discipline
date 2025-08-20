<?php
class EmployeeController {
  public static function handle($action, $method, $input) {
    switch ($action) {
      case 'employee_login': { // POST {employee_id, manager_id}
        if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
        $employee_id = $input['employee_id'] ?? null;
        $manager_id  = $input['manager_id']  ?? null;
        if (!$employee_id) respond(400, ['error' => 'employee_id requerido']);
        $_SESSION['employee_id'] = $employee_id;
        if ($manager_id) { $_SESSION['manager_id_for_employee'] = $manager_id; }
        respond(200, ['ok' => true, 'employee_id' => $employee_id, 'manager_id' => $manager_id]);
      }
      case 'employee_session_status': { // GET
        if ($method !== 'GET') respond(405, ['error' => 'Method not allowed']);
        $emp = $_SESSION['employee_id'] ?? null;
        $mgrForEmp = $_SESSION['manager_id_for_employee'] ?? null;
        respond(200, ['logged_in' => !!$emp, 'employee_id' => $emp, 'manager_id' => $mgrForEmp]);
      }
      case 'employee_logout': { // POST
        if ($method !== 'POST') respond(405, ['error' => 'Method not allowed']);
        unset($_SESSION['employee_id']);
        unset($_SESSION['manager_id_for_employee']);
        respond(200, ['ok' => true]);
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
      default:
        respond(404, ['error' => 'Ruta no encontrada en EmployeeController']);
    }
  }
}
