<?php
session_start();
if (empty($_SESSION['employee_id']) || empty($_SESSION['manager_id_for_employee'])) {
  header('Location: index.php');
  exit;
}
$employeeId = $_SESSION['employee_id'];
$managerIdForEmployee = $_SESSION['manager_id_for_employee'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Evaluaciones - Empleado</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    :root{ --primary:#2B6CB0; }
    body { font-family: Arial, sans-serif; background:#f5f5f5; padding:20px; }
    .container { max-width:1100px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); overflow:hidden; }
    .topbar{ position:sticky; top:0; z-index:10; display:flex; align-items:center; gap:12px; padding:10px 16px; background:#ffffff; border-bottom:1px solid #ddd; }
    .topbar .brand{ display:flex; align-items:center; gap:8px; font-weight:700; color:#111827; }
    .topbar .nav-actions{ display:flex; align-items:center; gap:8px; margin-left:8px; }
    .topbar .nav-btn{ background:#f5f7fb; border:1px solid #e2e8f0; color:#334155; padding:8px 12px; border-radius:8px; cursor:pointer; font-weight:600; font-size:13px; }
    .topbar .nav-btn:hover{ background:#e2e8f0; color:#111827; }
    .topbar .search-area{ margin-left:auto; display:flex; align-items:center; gap:10px; }
    .topbar .manager-name{ color:#475569; font-weight:700; }
    .week-header{ background:#A5D6A7; color:#2E7D32; text-align:center; padding:10px; font-weight:bold; }
    .evaluation-table{ width:100%; border-collapse:collapse; font-size:12px; }
    .evaluation-table th{ background:#E8F5E8; padding:8px 4px; text-align:center; font-weight:bold; border:1px solid #ddd; font-size:11px; }
    .evaluation-table td{ padding:6px 4px; border:1px solid #ddd; text-align:center; vertical-align:middle; }
    .employee-name{ text-align:left !important; font-weight:bold; background:#f9f9f9; padding-left:10px !important; min-width:200px; font-size:11px; }
    .hidden{ display:none; }
    .btn { background-color: var(--primary); color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div class="brand"><i data-feather="user"></i><span>Mis evaluaciones</span></div>
      <div class="search-area">
        <span id="employeeHeader" class="manager-name"></span>
        <button class="nav-btn" onclick="logoutApp()">Cerrar sesión</button>
      </div>
    </div>

    <div id="myEvaluationsSection" style="padding:20px;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
        <div id="myWeekHeader" class="week-header" style="margin:0;">Semana:</div>
        <select id="myWeekSelect" onchange="onMyWeekChange()" style="padding:6px 8px; border-radius:6px; border:1px solid #e5e7eb;"></select>
      </div>
      <table class="evaluation-table" id="myEvaluationsTable">
        <thead id="myEvaluationsHeader"></thead>
        <tbody id="myEvaluationsBody"></tbody>
      </table>
    </div>
  </div>

  <script>
    window.PHP_SESSION = {
      employee_id: <?php echo json_encode($employeeId); ?>,
      manager_id_for_employee: <?php echo json_encode($managerIdForEmployee); ?>
    };
  </script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script>
    class EmployeeEvaluationSystem {
      constructor() {
        this.currentManager = null;
        this.currentEmployee = null;
        this.employeeManagerId = null;
        this.currentWeek = null;
        this.currentWeekId = null;
        this.managers = {};
        this.evaluationItems = {};
        this.categories = ['PUNTUALIDAD', 'PRESENTACION', 'ORDEN', 'COMUNICACION', 'EQUIPO', 'CONDUCTA', 'ACTITUD', 'PRODUCTIVIDAD', 'COLABORACION', 'NORMAS', 'RESPONSABILIDAD', 'ATENCION_AL_CLIENTE'];
        this.dataLoaded = false;
        this.dataLoadPromise = null;
        this.weekIdByKey = {};
        this.weekKeyById = {};
      }

      async loadStaticData() {
        if (this.dataLoadPromise) {
          await this.dataLoadPromise;
          return;
        }
        this.dataLoadPromise = (async () => {
          const [managersRes, itemsRes] = await Promise.all([
            fetch('json/managers.json'),
            fetch('json/evaluation_items.json')
          ]);
          this.managers = await managersRes.json();
          this.evaluationItems = await itemsRes.json();
        })();
        await this.dataLoadPromise;
        this.dataLoaded = true;
      }

      getEmployeeName(empId) {
        for (const mid of Object.keys(this.managers)) {
          const emp = (this.managers[mid].employees || []).find(e => e.id === empId);
          if (emp) return emp.name;
        }
        return empId;
      }

      async loadMyEvaluationsCurrentWeek() {
        try {
          const res = await fetch(`api/index.php?action=weeks_current&manager_id=${encodeURIComponent(this.employeeManagerId)}`);
          const json = await res.json();
          const w = json.week;
          if (!w) {
            document.getElementById('myWeekHeader').textContent = 'No hay semanas disponibles';
            return;
          }
          const pad = n => String(n).padStart(2, '0');
          const parseLocalDate = (s) => {
            const [y, m, d] = String(s).split('-').map(Number);
            return new Date(y, (m || 1) - 1, d || 1);
          };
          const s = parseLocalDate(w.start_date);
          const e = parseLocalDate(w.end_date);
          const weekKey = `${pad(s.getDate())}/${pad(s.getMonth() + 1)}/${s.getFullYear()} - ${pad(e.getDate())}/${pad(e.getMonth() + 1)}/${e.getFullYear()}`;
          this.currentWeek = weekKey;
          this.currentWeekId = w.id;
          document.getElementById('myWeekHeader').textContent = `Semana: ${weekKey}`;
          const evRes = await fetch(`api/index.php?action=evaluations_by_week_employee&week_id=${encodeURIComponent(w.id)}&employee_id=${encodeURIComponent(this.currentEmployee)}`);
          const evJson = await evRes.json();
          this.renderMyEvaluationsTable(evJson.evaluations || []);
        } catch (e) {
          console.error('loadMyEvaluationsCurrentWeek error', e);
        }
      }

      async loadEmployeeWeeks() {
        try {
          const sel = document.getElementById('myWeekSelect');
          sel.innerHTML = '';
          const res = await fetch(`api/index.php?action=weeks_list&manager_id=${encodeURIComponent(this.employeeManagerId)}`);
          if (!res.ok) return;
          const js = await res.json();
          const weeks = js.weeks || [];
          const pad = n => String(n).padStart(2, '0');
          const parseLocalDate = (s) => {
            const [y, m, d] = String(s).split('-').map(Number);
            return new Date(y, (m || 1) - 1, d || 1);
          };
          this.weekIdByKey = {};
          this.weekKeyById = {};
          weeks.forEach(w => {
            const s = parseLocalDate(w.start_date);
            const e = parseLocalDate(w.end_date);
            const key = `${pad(s.getDate())}/${pad(s.getMonth() + 1)}/${s.getFullYear()} - ${pad(e.getDate())}/${pad(e.getMonth() + 1)}/${e.getFullYear()}`;
            this.weekIdByKey[key] = w.id;
            this.weekKeyById[w.id] = key;
          });
          weeks.forEach(w => {
            const key = this.weekKeyById[w.id];
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = key;
            sel.appendChild(opt);
          });
          if (this.currentWeek && this.weekIdByKey[this.currentWeek]) {
            sel.value = this.currentWeek;
          } else if (sel.options.length > 0) {
            sel.selectedIndex = 0;
          }
        } catch (e) {
          console.error('loadEmployeeWeeks error', e);
        }
      }

      async loadMyEvaluationsByWeekKey(weekKey) {
        try {
          const weekId = this.weekIdByKey[weekKey];
          if (!weekId) return;
          this.currentWeek = weekKey;
          this.currentWeekId = weekId;
          document.getElementById('myWeekHeader').textContent = `Semana: ${weekKey}`;
          const evRes = await fetch(`api/index.php?action=evaluations_by_week_employee&week_id=${encodeURIComponent(weekId)}&employee_id=${encodeURIComponent(this.currentEmployee)}`);
          const evJson = await evRes.json();
          this.renderMyEvaluationsTable(evJson.evaluations || []);
        } catch (e) {
          console.error('loadMyEvaluationsByWeekKey error', e);
        }
      }

      renderMyEvaluationsTable(rows) {
        const head = document.getElementById('myEvaluationsHeader');
        const body = document.getElementById('myEvaluationsBody');
        if (!head || !body) return;
        const days = ['LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES'];
        const cats = this.categories;
        head.innerHTML = '';
        body.innerHTML = '';
        const trH = document.createElement('tr');
        trH.innerHTML = `<th class="category-header">Categoría</th>${days.map(d => `<th class="day-header">${d}</th>`).join('')}`;
        head.appendChild(trH);
        const map = {};
        rows.forEach(r => {
          if (!map[r.day_index]) map[r.day_index] = {};
          map[r.day_index][r.category] = { item: r.item, checked: !!r.checked };
        });
        cats.forEach(cat => {
          const tr = document.createElement('tr');
          tr.innerHTML = `<td class="employee-name">${cat}</td>` + days.map((_, idx) => {
            const d = map[idx]?.[cat];
            const item = d ? d.item : 'PERFECTO';
            const ok = d ? d.checked : true;
            const badge = ok ? '<span style="color:#2e7d32;">✔</span>' : '<span style="color:#dc2626;">✖</span>';
            return `<td>${badge} <small>${item}</small></td>`;
          }).join('');
          body.appendChild(tr);
        });
      }
    }

    let evaluationSystem;

    function onMyWeekChange() {
      const sel = document.getElementById('myWeekSelect');
      const key = sel.value;
      if (key) evaluationSystem.loadMyEvaluationsByWeekKey(key);
    }

    async function logoutApp() {
      try {
        await fetch('api/index.php?action=employee_logout', { method: 'POST' });
      } catch (e) {}
      window.location.href = 'index.php';
    }

    async function initEmployee() {
      try {
        evaluationSystem = new EmployeeEvaluationSystem();
        await evaluationSystem.loadStaticData();
        // Use injected PHP session instead of fetching session status
        const sess = window.PHP_SESSION || {};
        if (!(sess.employee_id && sess.manager_id_for_employee)) {
          window.location.href = 'index.php';
          return;
        }
        evaluationSystem.currentEmployee = sess.employee_id;
        evaluationSystem.employeeManagerId = sess.manager_id_for_employee;
        const empName = evaluationSystem.getEmployeeName(sess.employee_id);
        document.getElementById('employeeHeader').textContent = `EMPLEADO ${empName}`;
        if (window.feather && typeof window.feather.replace === 'function') window.feather.replace();
        await evaluationSystem.loadMyEvaluationsCurrentWeek();
        await evaluationSystem.loadEmployeeWeeks();
      } catch (e) {
        console.error(e);
        window.location.href = 'index.php';
      }
    }

    document.addEventListener('DOMContentLoaded', initEmployee);
  </script>
</body>
</html>
