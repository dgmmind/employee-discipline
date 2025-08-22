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
  <link rel="stylesheet" href="assets/employee.css">
</head>
<body>

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

<?php include 'includes/header.php'; ?>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <div class="main-content-inner">
                <header class="main-header">
                    <button class="main-menu-btn" id="mobileMenuBtn">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="main-title">Dashboard</h1>
                </header>
  <!-- eliminar container -->

  <div class="main-body">
            <div class="content-card">
                        
    <div class="topbar">
      <div class="brand"><i data-feather="user"></i><span>Mis evaluaciones</span></div>
      <div class="search-area">
        <span id="employeeHeader" class="manager-name"></span>
        <button class="nav-btn" onclick="logoutApp()">Cerrar sesión</button>
      </div>
    </div>

    <div id="myEvaluationsSection">
      <div>
        <div id="myWeekHeader" class="week-header">Semana:</div>
        <select id="myWeekSelect" onchange="onMyWeekChange()"></select>
      </div>
      <table class="evaluation-table" id="myEvaluationsTable">
        <thead id="myEvaluationsHeader"></thead>
        <tbody id="myEvaluationsBody"></tbody>
      </table>
    </div>
  </div>
            </div>

            <div class="content-grid">
                        <div class="content-card">
                            <h3 class="content-subtitle">Card 1</h3>
                            <p class="content-text">Sample content for demonstration.</p>
                        </div>
                        <div class="content-card">
                            <h3 class="content-subtitle">Card 2</h3>
                            <p class="content-text">More sample content here.</p>
                        </div>
                        <div class="content-card">
                            <h3 class="content-subtitle">Card 3</h3>
                            <p class="content-text">Additional content example.</p>
                          </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    
<?php include 'includes/footer.php'; ?>
