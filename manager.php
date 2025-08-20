<?php
session_start();
if (empty($_SESSION['manager_id'])) {
  header('Location: index.php');
  exit;
}
$managerId = $_SESSION['manager_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Evaluaciones - Manager</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    :root{ --bg:#0B0E14; --surface:#0F1115; --surface-2:#151924; --text:#E7EAF0; --text-2:#A7AEC0; --primary:#2B6CB0; --success:#16A34A; --warning:#F59E0B; --danger:#DC2626; --border:#1C1F26; --shadow:0 6px 20px rgba(0,0,0,.35); --radius:8px; --space:16px; --sidebar-w:280px; }
    body { font-family: Arial, sans-serif; background-color:#f5f5f5; padding:20px; }
    .container { max-width:1400px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); overflow:hidden; }
    .btn { background-color: var(--primary); color: var(--text); padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin: 5px; }
    .btn-secondary { background-color: #2196F3; }
    .topbar{ position:sticky; top:0; z-index:10; display:flex; align-items:center; gap:12px; padding:10px 16px; background:#ffffff; border-bottom:1px solid #ddd; }
    .topbar .brand{ display:flex; align-items:center; gap:8px; font-weight:700; color:#111827; }
    .topbar .nav-actions{ display:flex; align-items:center; gap:8px; margin-left:8px; }
    .topbar .nav-btn{ background:#f5f7fb; border:1px solid #e2e8f0; color:#334155; padding:8px 12px; border-radius:8px; cursor:pointer; font-weight:600; font-size:13px; }
    .topbar .nav-btn:hover{ background:#e2e8f0; color:#111827; }
    .topbar .search-area{ margin-left:auto; display:flex; align-items:center; gap:10px; }
    .topbar #globalSearch{ padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; min-width:220px; }
    .topbar .manager-name{ color:#475569; font-weight:700; }
    .week-header{ background:#A5D6A7; color:#2E7D32; text-align:center; padding:10px; font-weight:bold; }
    .evaluation-table{ width:100%; border-collapse:collapse; font-size:12px; }
    .evaluation-table th{ background:#E8F5E8; padding:8px 4px; text-align:center; font-weight:bold; border:1px solid #ddd; font-size:11px; }
    .evaluation-table td{ padding:6px 4px; border:1px solid #ddd; text-align:center; vertical-align:middle; }
    .employee-name{ text-align:left !important; font-weight:bold; background:#f9f9f9; padding-left:10px !important; min-width:200px; font-size:11px; }
    .category-header{ background:#C8E6C9; font-weight:bold; color:#2E7D32; }
    .day-header{ background:#FFE082; font-weight:bold; color:#F57C00; }
    .checkbox-container{ display:flex; align-items:center; justify-content:center; gap:2px; }
    .evaluation-checkbox{ width:16px; height:16px; cursor:pointer; }
    .evaluation-checkbox:disabled { opacity:.6; cursor:not-allowed; }
    .item-select{ padding:2px; border:1px solid #ddd; border-radius:3px; font-size:10px; min-width:90px; max-width:100px; }
    .item-select.has-issue{ background:#ffebee; border-color:#f44336; }
    .week-management{ padding:20px; background:#f8f9fa; border-bottom:1px solid #ddd; }
    .week-controls{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; }
    .day-navigation{ padding:15px 20px; background:#e8f5e8; border-bottom:1px solid #ddd; display:none; }
    .day-tabs{ display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
    .day-tab{ padding:8px 16px; background:#fff; border:2px solid #4CAF50; border-radius:5px; cursor:pointer; font-weight:bold; transition:all .3s; }
    .day-tab.active{ background:#4CAF50; color:#fff; }
    .reports-module{ padding:20px; background:#f0f8ff; border-bottom:1px solid #ddd; display:none; }
    .reports-controls{ display:flex; gap:15px; align-items:center; flex-wrap:wrap; }
    .stats{ margin-top:20px; padding:15px; background:#e3f2fd; border-radius:4px; }
    .stats-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); gap:10px; margin-top:10px; }
    .stat-item{ background:#fff; padding:8px; border-radius:4px; text-align:center; }
    .stat-number{ font-size:20px; font-weight:bold; color:#1976d2; }
    .hidden{ display:none; }
  </style>
</head>
<body>
  <div class="container">
    <div id="mainScreen">
      <div class="topbar">
        <div class="brand"><i data-feather="grid"></i><span>Evaluaciones</span></div>
        <div class="nav-actions">
          <button class="nav-btn" id="btnCreateWeek" onclick="showCreateWeek()">Crear semana</button>
          <button class="nav-btn" id="btnWeeklyView" onclick="showWeekView()">Vista semanal</button>
          <button class="nav-btn" id="btnDailyView" onclick="showDayView()">Vista por día</button>
          <button class="nav-btn" id="btnReports" onclick="showReports()">Reportes</button>
        </div>
        <div class="search-area">
          <input id="globalSearch" type="search" placeholder="Buscar empleado..." />
          <span id="managerHeader" class="manager-name"></span>
          <button class="nav-btn" id="btnLogout" onclick="logoutApp()">Cerrar sesión</button>
        </div>
      </div>

      <div id="createWeekView" class="hidden">
        <div class="week-management">
          <div class="week-controls">
            <div>
              <input type="date" id="startDate" style="margin-right:10px; padding:8px;">
              <input type="date" id="endDate" style="margin-right:10px; padding:8px;">
              <button class="btn" onclick="createNewWeek()">Crear Nueva Semana</button>
              <button class="btn btn-secondary" onclick="loadWeek()">Cargar Semana</button>
            </div>
            <div>
              <select id="weekSelect"><option value="">Seleccionar semana...</option></select>
            </div>
          </div>
        </div>
      </div>

      <div id="dayNavigation" class="day-navigation">
        <div class="day-tabs">
          <div class="day-tab" onclick="selectDay('LUNES')">LUNES</div>
          <div class="day-tab" onclick="selectDay('MARTES')">MARTES</div>
          <div class="day-tab" onclick="selectDay('MIÉRCOLES')">MIÉRCOLES</div>
          <div class="day-tab" onclick="selectDay('JUEVES')">JUEVES</div>
          <div class="day-tab" onclick="selectDay('VIERNES')">VIERNES</div>
        </div>
      </div>

      <div id="reportsModule" class="reports-module">
        <h3>Módulo de Reportes</h3>
        <div class="reports-controls">
          <label>Alcance:</label>
          <select id="reportScope">
            <option value="current_week">Semana actual</option>
            <option value="selected_week">Semana específica</option>
            <option value="all_weeks">Todas las semanas</option>
          </select>
          <label id="reportWeekLabel" style="display:none;">Semana:</label>
          <select id="reportWeek" style="display:none;"></select>
          <label>Vista:</label>
          <select id="reportView">
            <option value="by_employee">Por empleado</option>
            <option value="by_week">Por semana</option>
          </select>
          <label>Categoría:</label>
          <select id="reportCategory"></select>
          <label>Empleado (opcional):</label>
          <select id="reportEmployee"><option value="ALL">Todos</option></select>
          <button class="btn" onclick="renderReport()">Visualizar</button>
          <button class="btn" onclick="generatePDFReport()">Generar PDF</button>
          <button class="btn" onclick="generateCSVReport()">Generar CSV</button>
        </div>
        <div id="reportsResults" style="margin-top:12px"></div>
      </div>

      <div id="evaluationSection" class="hidden">
        <div id="weekHeader" class="week-header"></div>
        <table class="evaluation-table" id="weeklyTable">
          <thead id="weeklyTableHeader"></thead>
          <tbody id="evaluationTableBody"></tbody>
        </table>
        <table class="evaluation-table single-day-table" id="mondayTable"><thead id="mondayTableHeader"></thead><tbody id="mondayTableBody"></tbody></table>
        <table class="evaluation-table single-day-table" id="tuesdayTable"><thead id="tuesdayTableHeader"></thead><tbody id="tuesdayTableBody"></tbody></table>
        <table class="evaluation-table single-day-table" id="wednesdayTable"><thead id="wednesdayTableHeader"></thead><tbody id="wednesdayTableBody"></tbody></table>
        <table class="evaluation-table single-day-table" id="thursdayTable"><thead id="thursdayTableHeader"></thead><tbody id="thursdayTableBody"></tbody></table>
        <table class="evaluation-table single-day-table" id="fridayTable"><thead id="fridayTableHeader"></thead><tbody id="fridayTableBody"></tbody></table>
        <div class="stats"><h3>Estadísticas de la Semana</h3><div class="stats-grid" id="statsGrid"></div></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/feather-icons"></script>
  <script>
    window.PHP_SESSION = { manager_id: <?php echo json_encode($managerId); ?> };
  </script>
  <script>
  // Copia adaptada de manager.html, usando PHP_SESSION en lugar de fetch session_status
  class EmployeeEvaluationSystem {
    constructor() {
      this.currentManager = null;
      this.currentEmployee = null;
      this.employeeManagerId = null;
      this.currentWeek = null;
      this.currentWeekId = null;
      this.evaluationData = {};
      this.weekIdByKey = {};
      this.weekKeyById = {};
      this.managers = {};
      this.evaluationItems = {};
      this.managerUsernames = {};
      this.managerPasswords = {};
      this.employeeUsernames = {};
      this.employeePasswords = {};
      this.categories = ['ASISTENCIA', 'PLÁTICAS', 'PRODUCTIVIDAD', 'ACTITUD'];
      this.categoryMapping = { 'ASISTENCIA': 'asistencia', 'PLÁTICAS': 'platicas', 'PRODUCTIVIDAD': 'productividad', 'ACTITUD': 'actitud' };
      this.days = ['LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES'];
      this.dataLoaded = false;
      this.dataLoadPromise = null;
    }

    async loadStaticData() {
      if (this.dataLoadPromise) { await this.dataLoadPromise; return; }
      this.dataLoadPromise = (async () => {
        const [managersRes, itemsRes] = await Promise.all([
          fetch('json/managers.json'),
          fetch('json/evaluation_items.json')
        ]);
        this.managers = await managersRes.json();
        this.evaluationItems = await itemsRes.json();
      })();
      await this.dataLoadPromise;
      this.managerUsernames = {};
      this.managerPasswords = {};
      Object.keys(this.managers).forEach(mid => {
        const m = this.managers[mid];
        if (m && m.username) this.managerUsernames[String(m.username).toLowerCase()] = mid;
        if (m && m.password) this.managerPasswords[mid] = m.password;
      });
      this.employeeUsernames = {};
      this.employeePasswords = {};
      Object.keys(this.managers).forEach(mid => {
        (this.managers[mid].employees || []).forEach(emp => {
          if (emp.username) this.employeeUsernames[String(emp.username).toLowerCase()] = { managerId: mid, employeeId: emp.id };
          if (emp.password) this.employeePasswords[emp.id] = emp.password;
        });
      });
      this.dataLoaded = true;
    }

    renderWeeklyHeader() {
      const thead = document.getElementById('weeklyTableHeader');
      if (!thead) return;
      let firstRow = '<tr><th rowspan="2">EMPLEADO</th>';
      this.days.forEach(day => {
        firstRow += `<th colspan="${this.categories.length * 2}" class="day-header">${day}</th>`;
      });
      firstRow += '</tr>';
      let secondRow = '<tr>';
      this.days.forEach(() => {
        this.categories.forEach(cat => {
          secondRow += '<th class="category-header">✓</th>';
          secondRow += `<th class="category-header">${cat}</th>`;
        });
      });
      secondRow += '</tr>';
      thead.innerHTML = firstRow + secondRow;
    }

    renderWeeklyEvaluationTable() {
      const tableBody = document.getElementById('evaluationTableBody');
      tableBody.innerHTML = '';
      if (!this.currentWeek || !this.evaluationData[this.currentManager] || !this.evaluationData[this.currentManager][this.currentWeek]) return;
      const weekData = this.evaluationData[this.currentManager][this.currentWeek];
      this.renderWeeklyHeader();
      this.managers[this.currentManager].employees.forEach(employee => {
        const row = document.createElement('tr');
        row.innerHTML = `<td class="employee-name">${employee.name}</td>`;
        for (let dayIndex = 0; dayIndex < 5; dayIndex++) {
          const dayData = weekData[employee.id][dayIndex];
          this.categories.forEach(category => {
            const isChecked = dayData[category].checked ? 'checked' : '';
            const selectClass = dayData[category].checked ? '' : 'has-issue';
            const categoryKey = this.categoryMapping[category];
            row.innerHTML += `<td><div class="checkbox-container"><input type="checkbox" class="evaluation-checkbox" ${isChecked} disabled></div></td><td><select class="item-select ${selectClass}" onchange="evaluationSystem.updateEvaluation('${employee.id}', ${dayIndex}, '${category}', 'item', this.value)">${this.evaluationItems[categoryKey].map(item => `<option value="${item}" ${item === dayData[category].item ? 'selected' : ''}>${item}</option>`).join('')}</select></td>`;
          });
        }
        document.getElementById('evaluationTableBody').appendChild(row);
      });
      this.updateStats();
    }

    renderSingleDayHeader(day) {
      const ids = { 'LUNES': 'mondayTableHeader', 'MARTES': 'tuesdayTableHeader', 'MIÉRCOLES': 'wednesdayTableHeader', 'JUEVES': 'thursdayTableHeader', 'VIERNES': 'fridayTableHeader' };
      const thead = document.getElementById(ids[day]);
      if (!thead) return;
      let html = '<tr><th rowspan="2">EMPLEADO</th>' + `<th colspan="${this.categories.length * 2}" class="day-header">${day}</th>` + '</tr><tr>';
      this.categories.forEach(cat => {
        html += '<th class="category-header">✓</th>';
        html += `<th class="category-header">${cat}</th>`;
      });
      html += '</tr>';
      thead.innerHTML = html;
    }

    renderDayTable(day) {
      const dayIndex = this.days.indexOf(day);
      if (dayIndex === -1) return;
      const map = { 'LUNES': 'mondayTableBody', 'MARTES': 'tuesdayTableBody', 'MIÉRCOLES': 'wednesdayTableBody', 'JUEVES': 'thursdayTableBody', 'VIERNES': 'fridayTableBody' };
      const tbody = document.getElementById(map[day]);
      tbody.innerHTML = '';
      if (!this.currentWeek || !this.evaluationData[this.currentManager] || !this.evaluationData[this.currentManager][this.currentWeek]) return;
      const weekData = this.evaluationData[this.currentManager][this.currentWeek];
      this.managers[this.currentManager].employees.forEach(employee => {
        const row = document.createElement('tr');
        row.innerHTML = `<td class="employee-name">${employee.name}</td>`;
        const dayData = weekData[employee.id][dayIndex];
        this.categories.forEach(category => {
          const isChecked = dayData[category].checked ? 'checked' : '';
          const selectClass = dayData[category].checked ? '' : 'has-issue';
          const categoryKey = this.categoryMapping[category];
          row.innerHTML += `<td><div class="checkbox-container"><input type="checkbox" class="evaluation-checkbox" ${isChecked} disabled></div></td><td><select class="item-select ${selectClass}" onchange="evaluationSystem.updateEvaluation('${employee.id}', ${dayIndex}, '${category}', 'item', this.value)">${this.evaluationItems[categoryKey].map(item => `<option value="${item}" ${item === dayData[category].item ? 'selected' : ''}>${item}</option>`).join('')}</select></td>`;
        });
        tbody.appendChild(row);
      });
    }

    async updateEvaluation(employeeId, dayIndex, category, field, value) {
      if (!this.currentWeek || !this.evaluationData[this.currentManager] || !this.evaluationData[this.currentManager][this.currentWeek]) return;
      const weekData = this.evaluationData[this.currentManager][this.currentWeek];
      if (!weekData[employeeId] || !weekData[employeeId][dayIndex]) return;
      const categoryKey = this.categoryMapping[category];
      if (!categoryKey || !this.evaluationItems[categoryKey]) return;
      const prevItem = weekData[employeeId][dayIndex][category]['item'];
      const prevChecked = weekData[employeeId][dayIndex][category]['checked'];
      if (field === 'item') {
        weekData[employeeId][dayIndex][category]['item'] = value;
        weekData[employeeId][dayIndex][category]['checked'] = (value === 'Perfecto');
      }
      try {
        const resp = await fetch('api/index.php?action=evaluation_update', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            week_id: this.currentWeekId,
            employee_id: employeeId,
            day_index: dayIndex,
            category: category,
            checked: weekData[employeeId][dayIndex][category].checked,
            item: weekData[employeeId][dayIndex][category].item
          })
        });
        const json = await resp.json();
        if (!resp.ok) {
          // Revert UI/state when backend rejects (e.g., no row updated)
          weekData[employeeId][dayIndex][category]['item'] = prevItem;
          weekData[employeeId][dayIndex][category]['checked'] = prevChecked;
          console.warn('Actualización rechazada por el servidor:', json && (json.error || json));
        }
      } catch (e) {
        console.error('Error actualizando evaluación:', e);
      }
      this.renderWeeklyEvaluationTable();
      if (currentView === 'day') {
        this.renderDayTable(currentDay || 'LUNES');
      }
    }

    updateStats() {
      if (!this.currentWeek || !this.evaluationData[this.currentManager] || !this.evaluationData[this.currentManager][this.currentWeek]) return;
      const statsGrid = document.getElementById('statsGrid');
      statsGrid.innerHTML = '';
      const weekData = this.evaluationData[this.currentManager][this.currentWeek];
      const employees = this.managers[this.currentManager].employees;
      for (let dayIndex = 0; dayIndex < 5; dayIndex++) {
        const dayName = this.days[dayIndex];
        let totalEmployees = employees.length;
        let perfectEmployees = 0;
        employees.forEach(employee => {
          const employeeData = weekData[employee.id][dayIndex];
          if (employeeData) {
            let isPerfect = true;
            this.categories.forEach(category => {
              if (!(employeeData[category] && employeeData[category].checked)) isPerfect = false;
            });
            if (isPerfect) perfectEmployees++;
          }
        });
        statsGrid.innerHTML += `<div class="stat-item"><div class="stat-number">${perfectEmployees}/${totalEmployees}</div><div>Perfectos ${dayName}</div></div>`;
      }
    }

    async loadWeekOptions() {
      const weekSelect = document.getElementById('weekSelect');
      if (!weekSelect) return;
      weekSelect.innerHTML = '<option value="">Seleccionar semana...</option>';
      try {
        const res = await fetch(`api/index.php?action=weeks_list&manager_id=${encodeURIComponent(this.currentManager)}`);
        const json = await res.json();
        this.weekIdByKey = {};
        this.weekKeyById = {};
        const pad = n => String(n).padStart(2, '0');
        const parseLocalDate = (s) => {
          const [y, m, d] = String(s).split('-').map(Number);
          return new Date(y, (m || 1) - 1, d || 1);
        };
        (json.weeks || []).forEach(week => {
          const start = parseLocalDate(week.start_date);
          const end = parseLocalDate(week.end_date);
          const weekKey = `${pad(start.getDate())}/${pad(start.getMonth() + 1)}/${start.getFullYear()} - ${pad(end.getDate())}/${pad(end.getMonth() + 1)}/${end.getFullYear()}`;
          this.weekIdByKey[weekKey] = week.id;
          this.weekKeyById[week.id] = weekKey;
          const option = document.createElement('option');
          option.value = weekKey;
          option.textContent = weekKey;
          weekSelect.appendChild(option);
        });
      } catch (e) {
        console.error('Error cargando semanas:', e);
      }
    }

    async createNewWeek() {
      const toDMY = (ymd) => {
        if (/^\d{4}-\d{2}-\d{2}$/.test(ymd)) {
          const [y, m, d] = ymd.split('-');
          return `${d}/${m}/${y}`;
        }
        return ymd;
      };
      const startDateInput = toDMY(document.getElementById('startDate').value);
      const endDateInput = toDMY(document.getElementById('endDate').value);
      if (!this.validateWeekDates(startDateInput, endDateInput)) return;
      const weekKey = `${startDateInput} - ${endDateInput}`;
      this.currentWeek = weekKey;
      const toISO = (dmy) => {
        const [d, m, y] = dmy.split('/');
        return `${y}-${m}-${d}`;
      };
      try {
        const resp = await fetch('api/index.php?action=weeks_create', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            manager_id: this.currentManager,
            start_date: toISO(startDateInput),
            end_date: toISO(endDateInput)
          })
        });
        const json = await resp.json();
        const weekId = json.week.id;
        this.currentWeekId = weekId;
        this.weekIdByKey[weekKey] = weekId;
        this.weekKeyById[weekId] = weekKey;
        this.initializeWeekData(weekKey);
        const rows = [];
        const employees = this.managers[this.currentManager].employees;
        for (const employee of employees) {
          for (let i = 0; i < 5; i++) {
            this.categories.forEach(category => {
              rows.push({
                week_id: weekId,
                employee_id: employee.id,
                day_index: i,
                category: category,
                checked: true,
                item: 'Perfecto'
              });
            });
          }
        }
        if (rows.length) {
          await fetch('api/index.php?action=evaluations_bulk_insert', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rows })
          });
        }
        this.renderWeeklyEvaluationTable();
        this.loadWeekOptions();
      } catch (e) {
        console.error('Error al crear semana:', e);
        alert('No se pudo crear la semana en el servidor');
        return;
      }
      document.getElementById('evaluationSection').classList.remove('hidden');
      document.getElementById('weekHeader').textContent = weekKey;
      document.getElementById('weekSelect').value = weekKey;
    }

    validateWeekDates(startDateInput, endDateInput) {
      const dateRegex = /(\d{2})\/(\d{2})\/(\d{4})/;
      if (!dateRegex.test(startDateInput) || !dateRegex.test(endDateInput)) {
        alert('Formato de fecha incorrecto. Use DD/MM/YYYY');
        return false;
      }
      const [sd, sm, sy] = startDateInput.split('/');
      const [ed, em, ey] = endDateInput.split('/');
      const startDate = new Date(sy, sm - 1, sd);
      const endDate = new Date(ey, em - 1, ed);
      if (startDate.getDay() !== 1) {
        alert('La fecha de inicio debe ser un LUNES');
        return false;
      }
      if (endDate.getDay() !== 5) {
        alert('La fecha de fin debe ser un VIERNES');
        return false;
      }
      const diffDays = Math.ceil(Math.abs(endDate - startDate) / (1000 * 60 * 60 * 24));
      if (diffDays !== 4) {
        alert('Debe haber exactamente 4 días entre lunes y viernes');
        return false;
      }
      return true;
    }

    initializeWeekData(weekKey) {
      if (!this.evaluationData[this.currentManager]) this.evaluationData[this.currentManager] = {};
      if (!this.evaluationData[this.currentManager][weekKey]) {
        this.evaluationData[this.currentManager][weekKey] = {};
        this.managers[this.currentManager].employees.forEach(employee => {
          this.evaluationData[this.currentManager][weekKey][employee.id] = [];
          for (let i = 0; i < 5; i++) {
            const dayObj = {};
            this.categories.forEach(cat => {
              dayObj[cat] = { checked: true, item: 'Perfecto' };
            });
            this.evaluationData[this.currentManager][weekKey][employee.id][i] = dayObj;
          }
        });
      }
    }

    async loadWeek() {
      const weekKey = document.getElementById('weekSelect').value;
      if (!weekKey) {
        alert('Por favor selecciona una semana');
        return;
      }
      this.currentWeek = weekKey;
      this.currentWeekId = this.weekIdByKey[weekKey] || null;
      if (this.currentWeekId) {
        try {
          const res = await fetch(`api/index.php?action=evaluations_by_week&week_id=${encodeURIComponent(this.currentWeekId)}`);
          const json = await res.json();
          if (!this.evaluationData[this.currentManager]) this.evaluationData[this.currentManager] = {};
          this.evaluationData[this.currentManager][weekKey] = {};
          this.managers[this.currentManager].employees.forEach(employee => {
            this.evaluationData[this.currentManager][weekKey][employee.id] = [];
            for (let i = 0; i < 5; i++) {
              const dayObj = {};
              this.categories.forEach(cat => {
                dayObj[cat] = { checked: true, item: 'Perfecto' };
              });
              this.evaluationData[this.currentManager][weekKey][employee.id][i] = dayObj;
            }
          });
          (json.evaluations || []).forEach(row => {
            const emp = row.employee_id;
            const d = row.day_index;
            const cat = row.category;
            if (this.evaluationData[this.currentManager][weekKey][emp] && this.evaluationData[this.currentManager][weekKey][emp][d]) {
              this.evaluationData[this.currentManager][weekKey][emp][d][cat] = { checked: !!row.checked, item: row.item };
            }
          });
        } catch (e) {
          console.error('Error cargando evaluaciones:', e);
        }
      }
      this.renderWeeklyEvaluationTable();
      document.getElementById('evaluationSection').classList.remove('hidden');
      document.getElementById('weekHeader').textContent = weekKey;
      this.renderWeeklyHeader();
    }

    async autoLoadCurrentWeek() {
      try {
        const res = await fetch(`api/index.php?action=weeks_current&manager_id=${encodeURIComponent(this.currentManager)}`);
        const json = await res.json();
        const w = json.week;
        if (!w) {
          showCreateWeek?.();
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
        this.weekIdByKey[weekKey] = w.id;
        this.weekKeyById[w.id] = weekKey;
        if (!this.evaluationData[this.currentManager] || !this.evaluationData[this.currentManager][weekKey]) this.initializeWeekData(weekKey);
        const evRes = await fetch(`api/index.php?action=evaluations_by_week&week_id=${encodeURIComponent(w.id)}`);
        const evJson = await evRes.json();
        if (evRes.ok) {
          const mgrEmps = this.managers[this.currentManager].employees;
          mgrEmps.forEach(emp => {
            for (let i = 0; i < 5; i++) {
              this.categories.forEach(category => {
                this.evaluationData[this.currentManager][weekKey][emp.id][i][category] = { checked: true, item: 'Perfecto' };
              });
            }
          });
          (evJson.evaluations || []).forEach(row => {
            const d = row.day_index, emp = row.employee_id, cat = row.category;
            if (this.evaluationData[this.currentManager][weekKey][emp] && this.evaluationData[this.currentManager][weekKey][emp][d]) {
              this.evaluationData[this.currentManager][weekKey][emp][d][cat] = { checked: !!row.checked, item: row.item };
            }
          });
        }
        document.getElementById('evaluationSection').classList.remove('hidden');
        document.getElementById('weekHeader').textContent = weekKey;
        this.renderWeeklyEvaluationTable?.();
        showWeekView?.();
        const wkSel = document.getElementById('weekSelect');
        if (wkSel) wkSel.value = weekKey;
      } catch (e) {
        console.error('autoLoadCurrentWeek error', e);
      }
    }
  }

  let evaluationSystem;
  let currentView = 'week';
  let currentDay = 'LUNES';

  function createNewWeek() { evaluationSystem.createNewWeek(); }
  function loadWeek() { evaluationSystem.loadWeek(); }
  function showCreateWeek() { currentView='create'; document.getElementById('createWeekView').classList.remove('hidden'); document.getElementById('weeklyTable').style.display='none'; document.getElementById('dayNavigation').style.display='none'; document.getElementById('reportsModule').style.display='none'; document.querySelectorAll('.single-day-table').forEach(t => t.style.display='none'); }
  function showWeekView() { currentView='week'; document.getElementById('createWeekView').classList.add('hidden'); document.getElementById('weeklyTable').style.display='table'; document.getElementById('dayNavigation').style.display='none'; document.getElementById('reportsModule').style.display='none'; document.querySelectorAll('.single-day-table').forEach(t => t.style.display='none'); }
  function showDayView() { if (!evaluationSystem.currentWeek) { alert('Por favor selecciona o crea una semana primero'); return; } currentView='day'; document.getElementById('createWeekView').classList.add('hidden'); document.getElementById('weeklyTable').style.display='none'; document.getElementById('dayNavigation').style.display='block'; document.getElementById('reportsModule').style.display='none'; selectDay(currentDay); }
  async function showReports() {
    currentView = 'reports';
    document.getElementById('createWeekView').classList.add('hidden');
    document.getElementById('weeklyTable').style.display = 'none';
    document.getElementById('dayNavigation').style.display = 'none';
    document.getElementById('reportsModule').style.display = 'block';
    const sel = document.getElementById('reportEmployee');
    sel.innerHTML = '<option value="ALL">Todos</option>';
    evaluationSystem.managers[evaluationSystem.currentManager].employees.forEach(emp => {
      const opt = document.createElement('option');
      opt.value = emp.id;
      opt.textContent = emp.name;
      sel.appendChild(opt);
    });
    const catSel = document.getElementById('reportCategory');
    catSel.innerHTML = '';
    const optAll = document.createElement('option');
    optAll.value = 'ALL';
    optAll.textContent = 'Todas';
    catSel.appendChild(optAll);
    evaluationSystem.categories.forEach(cat => {
      const opt = document.createElement('option');
      opt.value = cat;
      opt.textContent = cat;
      catSel.appendChild(opt);
    });
    const weekLabel = document.getElementById('reportWeekLabel');
    const weekSelect = document.getElementById('reportWeek');
    const scopeSel = document.getElementById('reportScope');
    const pad = n => String(n).padStart(2, '0');
    const parseLocalDate = (s) => {
      const [y, m, d] = String(s).split('-').map(Number);
      return new Date(y, (m || 1) - 1, (d || 1));
    };
    const populateWeeks = async () => {
      weekSelect.innerHTML = '';
      let keys = [];
      if (evaluationSystem.weekIdByKey && Object.keys(evaluationSystem.weekIdByKey).length) {
        keys = Object.keys(evaluationSystem.weekIdByKey);
      } else {
        try {
          const weeksRes = await fetch(`api/index.php?action=weeks_list&manager_id=${encodeURIComponent(evaluationSystem.currentManager)}`);
          const weeksJson = await weeksRes.json();
          const tmpMap = {};
          (weeksJson.weeks || []).forEach(w => {
            const s = parseLocalDate(w.start_date);
            const e = parseLocalDate(w.end_date);
            const key = `${pad(s.getDate())}/${pad(s.getMonth() + 1)}/${s.getFullYear()} - ${pad(e.getDate())}/${pad(e.getMonth() + 1)}/${e.getFullYear()}`;
            tmpMap[key] = w.id;
          });
          evaluationSystem.weekIdByKey = { ...(evaluationSystem.weekIdByKey || {}), ...tmpMap };
          keys = Object.keys(tmpMap);
        } catch (e) {
          console.warn('No se pudieron cargar semanas', e);
        }
      }
      keys.sort();
      keys.forEach(k => {
        const opt = document.createElement('option');
        opt.value = k;
        opt.textContent = k;
        weekSelect.appendChild(opt);
      });
      if (evaluationSystem.currentWeek && keys.includes(evaluationSystem.currentWeek)) weekSelect.value = evaluationSystem.currentWeek;
    };
    const toggleWeekSelector = async () => {
      const show = scopeSel.value === 'selected_week';
      weekLabel.style.display = show ? 'inline-block' : 'none';
      weekSelect.style.display = show ? 'inline-block' : 'none';
      if (show) await populateWeeks();
    };
    if (!scopeSel._boundChange) {
      scopeSel.addEventListener('change', toggleWeekSelector);
      scopeSel._boundChange = true;
    }
    await toggleWeekSelector();
  }
  function selectDay(day){ currentDay=day; document.querySelectorAll('.day-tab').forEach(t=>t.classList.remove('active')); document.querySelectorAll('.day-tab').forEach(tab=>{ if (tab.textContent===day) tab.classList.add('active'); }); document.querySelectorAll('.single-day-table').forEach(t=>t.style.display='none'); const map={ 'LUNES':'mondayTable','MARTES':'tuesdayTable','MIÉRCOLES':'wednesdayTable','JUEVES':'thursdayTable','VIERNES':'fridayTable' }; document.getElementById(map[day]).style.display='table'; evaluationSystem.renderSingleDayHeader(day); evaluationSystem.renderDayTable(day); }

  function generatePDFReport() {
    const resultsDiv = document.getElementById('reportsResults');
    const table = resultsDiv ? resultsDiv.querySelector('table') : null;
    if (!table) { alert('Primero visualiza un reporte'); return; }
    const managerName = evaluationSystem.managers[evaluationSystem.currentManager]?.name || '';
    const html = `<!doctype html><html><head><meta charset="utf-8" /><title>Reporte</title><style>body{font-family:Arial;padding:20px;}h1{margin:0 0 6px 0}.meta{color:#555;margin-bottom:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:6px;text-align:center}th{background:#4CAF50;color:#fff}.employee-name{text-align:left}</style></head><body><h1>Reporte de Evaluaciones</h1><div class="meta">Manager: ${managerName}</div>${table.outerHTML}</body></html>`;
    const win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
    win.focus();
    win.print();
  }

  function generateCSVReport() {
    const resultsDiv = document.getElementById('reportsResults');
    const table = resultsDiv ? resultsDiv.querySelector('table') : null;
    if (!table) { alert('Primero visualiza un reporte'); return; }
    let csv = '';
    const rows = table.querySelectorAll('tr');
    rows.forEach((tr, idx) => {
      const cells = tr.querySelectorAll(idx === 0 ? 'th' : 'td');
      const values = Array.from(cells).map(td => '"' + td.textContent.replace(/"/g, '""') + '"');
      csv += values.join(',') + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    const ts = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
    link.download = `reporte_${ts}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  async function renderReport() {
    const scope = document.getElementById('reportScope')?.value || 'current_week';
    const view = document.getElementById('reportView')?.value || 'by_employee';
    const category = document.getElementById('reportCategory')?.value || 'ALL';
    const employeeId = document.getElementById('reportEmployee')?.value || 'ALL';
    const selectedWeekKey = document.getElementById('reportWeek')?.value || '';
    const resultsDiv = document.getElementById('reportsResults');
    if (!resultsDiv) return;
    resultsDiv.innerHTML = 'Cargando...';
    let dataset = {};
    try {
      if (scope === 'current_week') {
        if (!evaluationSystem.currentWeek) { resultsDiv.innerHTML = 'Selecciona o crea una semana primero'; return; }
        const wk = evaluationSystem.currentWeek;
        dataset[wk] = evaluationSystem.evaluationData[evaluationSystem.currentManager][wk];
      } else if (scope === 'selected_week') {
        const wk = selectedWeekKey || evaluationSystem.currentWeek;
        if (!wk) { resultsDiv.innerHTML = 'Selecciona una semana específica'; return; }
        if (wk === evaluationSystem.currentWeek && evaluationSystem.evaluationData[evaluationSystem.currentManager][wk]) {
          dataset[wk] = evaluationSystem.evaluationData[evaluationSystem.currentManager][wk];
        } else {
          if (!evaluationSystem.weekIdByKey[wk]) { await evaluationSystem.loadWeekOptions(); }
          const weekId = evaluationSystem.weekIdByKey[wk];
          if (!weekId) { resultsDiv.innerHTML = 'No se encontró la semana seleccionada'; return; }
          dataset[wk] = {};
          evaluationSystem.managers[evaluationSystem.currentManager].employees.forEach(emp => {
            dataset[wk][emp.id] = [];
            for (let i = 0; i < 5; i++) {
              const dayObj = {};
              evaluationSystem.categories.forEach(cat => { dayObj[cat] = { checked: true, item: 'Perfecto' }; });
              dataset[wk][emp.id][i] = dayObj;
            }
          });
          const res = await fetch(`api/index.php?action=evaluations_by_week&week_id=${encodeURIComponent(weekId)}`);
          const json = await res.json();
          if (res.ok) {
            (json.evaluations || []).forEach(row => {
              const emp = row.employee_id, d = row.day_index, cat = row.category;
              if (dataset[wk][emp] && dataset[wk][emp][d]) {
                dataset[wk][emp][d][cat] = { checked: !!row.checked, item: row.item };
              }
            });
          }
        }
      } else {
        const weeksRes = await fetch(`api/index.php?action=weeks_list&manager_id=${encodeURIComponent(evaluationSystem.currentManager)}`);
        const weeksJson = await weeksRes.json();
        if (!weeksRes.ok) throw new Error(JSON.stringify(weeksJson.error || weeksJson));
        const pad = n => String(n).padStart(2, '0');
        const parseLocalDate = (s) => { const [y, m, d] = String(s).split('-').map(Number); return new Date(y, (m || 1) - 1, d || 1); };
        const idToKey = {};
        (weeksJson.weeks || []).forEach(w => {
          const s = parseLocalDate(w.start_date);
          const e = parseLocalDate(w.end_date);
          idToKey[w.id] = `${pad(s.getDate())}/${pad(s.getMonth() + 1)}/${s.getFullYear()} - ${pad(e.getDate())}/${pad(e.getMonth() + 1)}/${e.getFullYear()}`;
          dataset[idToKey[w.id]] = {};
          evaluationSystem.managers[evaluationSystem.currentManager].employees.forEach(emp => {
            dataset[idToKey[w.id]][emp.id] = [];
            for (let i = 0; i < 5; i++) {
              const dayObj = {};
              evaluationSystem.categories.forEach(cat => { dayObj[cat] = { checked: true, item: 'Perfecto' }; });
              dataset[idToKey[w.id]][emp.id][i] = dayObj;
            }
          });
        });
        for (const week of (weeksJson.weeks || [])) {
          const res = await fetch(`api/index.php?action=evaluations_by_week&week_id=${encodeURIComponent(week.id)}`);
          const json = await res.json();
          if (!res.ok) continue;
          const key = idToKey[week.id];
          (json.evaluations || []).forEach(row => {
            const emp = row.employee_id, d = row.day_index, cat = row.category;
            if (dataset[key][emp] && dataset[key][emp][d]) {
              dataset[key][emp][d][cat] = { checked: !!row.checked, item: row.item };
            }
          });
        }
      }
    } catch (e) {
      console.error(e);
      resultsDiv.innerHTML = 'Error cargando datos de reportes';
      return;
    }
    const mgr = evaluationSystem.managers[evaluationSystem.currentManager];
    const selectedEmployees = (employeeId === 'ALL') ? mgr.employees.map(e => e.id) : [employeeId];
    const categories = (category === 'ALL') ? evaluationSystem.categories : [category];
    if (view === 'by_employee') {
      const cols = [...categories, 'TOTAL'];
      const table = document.createElement('table');
      table.className = 'evaluation-table';
      let html = '<thead><tr><th>Empleado</th>' + cols.map(c => `<th>${c} (%)</th>`).join('') + '</tr></thead><tbody>';
      selectedEmployees.forEach(empId => {
        let okTot = 0, totTot = 0;
        const perCat = {};
        categories.forEach(c => perCat[c] = { ok: 0, tot: 0 });
        Object.keys(dataset).forEach(weekKey => {
          const wk = dataset[weekKey];
          if (!wk[empId]) return;
          for (let d = 0; d < 5; d++) {
            categories.forEach(c => {
              const cell = wk[empId][d][c];
              if (!cell) return;
              perCat[c].tot++;
              perCat[c].ok += cell.checked ? 1 : 0;
              totTot++;
              okTot += cell.checked ? 1 : 0;
            });
          }
        });
        const rowVals = categories.map(c => perCat[c].tot ? Math.round(perCat[c].ok * 100 / perCat[c].tot) : 0);
        const totalPct = totTot ? Math.round(okTot * 100 / totTot) : 0;
        const name = (mgr.employees.find(e => e.id === empId) || {}).name || empId;
        html += `<tr><td class="employee-name">${name}</td>` + rowVals.map(v => `<td>${v}%</td>`).join('') + `<td>${totalPct}%</td></tr>`;
      });
      html += '</tbody>';
      table.innerHTML = html;
      resultsDiv.innerHTML = '';
      resultsDiv.appendChild(table);
    } else {
      const cols = [...categories, 'TOTAL'];
      const table = document.createElement('table');
      table.className = 'evaluation-table';
      let html = '<thead><tr><th>Semana</th>' + cols.map(c => `<th>${c} (%)</th>`).join('') + '</tr></thead><tbody>';
      Object.keys(dataset).sort().forEach(weekKey => {
        let okTot = 0, totTot = 0;
        const perCat = {};
        categories.forEach(c => perCat[c] = { ok: 0, tot: 0 });
        const emps = (employeeId === 'ALL') ? mgr.employees.map(e => e.id) : [employeeId];
        emps.forEach(empId => {
          if (!dataset[weekKey][empId]) return;
          for (let d = 0; d < 5; d++) {
            categories.forEach(c => {
              const cell = dataset[weekKey][empId][d][c];
              if (!cell) return;
              perCat[c].tot++;
              perCat[c].ok += cell.checked ? 1 : 0;
              totTot++;
              okTot += cell.checked ? 1 : 0;
            });
          }
        });
        const rowVals = categories.map(c => perCat[c].tot ? Math.round(perCat[c].ok * 100 / perCat[c].tot) : 0);
        const totalPct = totTot ? Math.round(okTot * 100 / totTot) : 0;
        html += `<tr><td>${weekKey}</td>` + rowVals.map(v => `<td>${v}%</td>`).join('') + `<td>${totalPct}%</td></tr>`;
      });
      html += '</tbody>';
      table.innerHTML = html;
      resultsDiv.innerHTML = '';
      resultsDiv.appendChild(table);
    }
  }

  async function logoutApp(){
    try { await fetch('api/index.php?action=logout', { method:'POST' }); } catch(e){}
    window.location.href='index.php';
  }

  async function initManager(){
    try{
      evaluationSystem = new EmployeeEvaluationSystem();
      await evaluationSystem.loadStaticData();
      // Usar sesión PHP directamente
      evaluationSystem.currentManager = window.PHP_SESSION.manager_id;
      if (window.feather && typeof window.feather.replace === 'function') window.feather.replace();
      const name = evaluationSystem.managers[window.PHP_SESSION.manager_id]?.name || window.PHP_SESSION.manager_id;
      document.getElementById('managerHeader').textContent = `MANAGER ${name}`;
      await evaluationSystem.loadWeekOptions();
      await evaluationSystem.autoLoadCurrentWeek();
    } catch(e){ console.error(e); window.location.href='index.php'; }
  }

  document.addEventListener('DOMContentLoaded', initManager);
  </script>
</body>
</html>
