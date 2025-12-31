<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cuaderno Pedagógico Digital - Asistencia por QR</title>

  <!-- Librerías -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

  <!-- Escáner con cámara -->
  <script src="https://unpkg.com/html5-qrcode@2.3.10/minified/html5-qrcode.min.js"></script>

  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height:100vh; padding:20px;
    }
    .container {
      max-width:1200px; margin:0 auto; background:#fff;
      border-radius:20px; box-shadow:0 20px 40px rgba(0,0,0,.1); overflow:hidden;
    }
    header { background:#4a90e2; color:#fff; padding:28px; text-align:center; }
    h1 { font-size:2.2em; margin-bottom:8px; }
    .tabs { display:flex; background:#f8f9fa; border-bottom:3px solid #dee2e6; flex-wrap:wrap; }
    .tab {
      flex:1; min-width:180px; padding:16px; text-align:center; cursor:pointer;
      transition:all .2s; font-weight:800;
    }
    .tab.active { background:#4a90e2; color:#fff; }
    .content { padding:28px; min-height:520px; }
    .hidden { display:none; }
    .section { text-align:center; }
    .cardBox { background:#f8f9fa; border-radius:16px; padding:18px; margin-top:16px; }
    .row { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px; }
    .row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-top:12px; }
    input {
      width:100%; padding:12px 14px; border:2px solid #dee2e6; border-radius:12px; font-size:16px; outline:none;
    }
    input:focus { border-color:#4a90e2; box-shadow:0 0 0 3px rgba(74,144,226,.15); }
    .btn {
      padding:12px 22px; border:none; border-radius:25px; cursor:pointer;
      font-size:15px; font-weight:900; transition:all .2s; margin:8px;
    }
    .btn-primary { background:#4a90e2; color:#fff; }
    .btn-success { background:#28a745; color:#fff; }
    .btn-danger { background:#dc3545; color:#fff; }
    .btn-ghost { background:#eef3ff; color:#113a70; }
    .btn:hover { transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.12); }
    .file-input-wrapper { position:relative; overflow:hidden; display:inline-block; margin:10px; }
    .file-input-wrapper input[type=file] { position:absolute; left:-9999px; }
    .file-label {
      display:inline-block; padding:14px 22px; background:#28a745; color:#fff;
      border-radius:25px; cursor:pointer; font-weight:900;
    }
    table { width:100%; border-collapse:collapse; margin-top:18px; }
    th, td { padding:12px; text-align:left; border-bottom:1px solid #dee2e6; }
    th { background:#4a90e2; color:#fff; }
    .muted { color:#6c757d; font-size:13px; margin-top:8px; line-height:1.3; }

    .student-item{
      display:flex; justify-content:space-between; align-items:center; padding:14px; margin:10px 0;
      background:#f8f9fa; border-radius:12px; border-left:6px solid #4a90e2;
    }
    .student-present { border-left-color:#28a745!important; background:#d4edda!important; }
    .student-absent { border-left-color:#dc3545!important; background:#f8d7da!important; }

    /* Tarjetas QR */
    .qrGrid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:12px; margin-top:16px; }
    .qrCard {
      background:#0b57a6; border-radius:16px; padding:12px; color:#fff; position:relative; overflow:hidden;
    }
    .qrCard .title { color:#f5c400; font-weight:900; line-height:1.1; }
    .qrCard .school { font-size:18px; font-weight:900; margin-top:6px; }
    .qrCard .meta { font-size:12px; color:#e8f0ff; margin-top:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .qrBox { background:#083f7a; border-radius:12px; padding:10px; display:flex; justify-content:center; align-items:center; margin-top:10px; }
    .qrInner { background:#fff; border-radius:10px; padding:8px; }
    .bar { position:absolute; left:0; right:0; bottom:0; height:16px; background:#f5c400; }

    /* Cámara */
    .cameraWrap{
      margin-top:12px; display:grid; grid-template-columns: 420px 1fr; gap:14px; align-items:start;
    }
    #reader{
      width:100%; max-width:420px; margin:0 auto; border-radius:14px; overflow:hidden; background:#000;
    }
    .cameraInfo{
      text-align:left; background:#fff; border-radius:14px; padding:12px;
      box-shadow:0 10px 30px rgba(0,0,0,.08);
    }
    .pill{
      display:inline-block; padding:6px 10px; border-radius:999px; font-weight:900; font-size:12px;
      background:#eef3ff; color:#113a70; margin-right:8px; margin-top:8px;
    }

    @media (max-width: 900px){
      .cameraWrap{ grid-template-columns: 1fr; }
      #reader{ max-width: 520px; }
    }

    @media print{
      body{ background:#fff; padding:0; }
      header, .tabs, .no-print{ display:none!important; }
      .container{ box-shadow:none; border-radius:0; }
      .content{ padding:10px; }
      .qrCard{ break-inside: avoid; box-shadow:none; }
    }
  </style>
</head>

<body>
<div class="container">
  <header>
    <h1>📋 Cuaderno Pedagógico Digital</h1>
    <p>Asistencia por QR — Escaneo con cámara (docente)</p>
  </header>

  <div class="tabs">
    <div class="tab active" data-tab="import">📥 Importar Estudiantes</div>
    <div class="tab" data-tab="cards">🪪 Tarjetas QR Estudiantes</div>
    <div class="tab" data-tab="attendance">📌 Tomar Asistencia</div>
    <div class="tab" data-tab="history">📈 Historial</div>
  </div>

  <div class="content">
    <!-- IMPORT -->
    <div id="importTab" class="tab-content">
      <div class="section">
        <h2>📊 Importar estudiantes desde Excel</h2>
        <p class="muted">Encabezados recomendados: <b>Apellidos</b> | <b>Nombres</b>. También acepta CSV.</p>

        <div class="row3">
          <div><label class="muted">Curso</label><input id="curso" value="4" /></div>
          <div><label class="muted">Paralelo</label><input id="paralelo" value="B" /></div>
          <div><label class="muted">Gestión (año)</label><input id="gestion" value="2025" /></div>
        </div>

        <div class="cardBox no-print">
          <button class="btn btn-ghost" onclick="downloadTemplate()">⬇️ Descargar plantilla CSV</button>
          <div class="file-input-wrapper">
            <input type="file" id="excelFile" accept=".xlsx,.xls,.csv" onchange="importStudents(event)">
            <label for="excelFile" class="file-label">📁 Seleccionar Excel/CSV</label>
          </div>

          <div class="row">
            <button class="btn btn-danger" onclick="clearStudents()">🗑️ Vaciar lista (curso)</button>
            <button class="btn btn-success" onclick="saveStudentsToLocal()">💾 Guardar lista</button>
          </div>

          <p id="importStatus" class="muted"></p>
        </div>

        <div style="margin-top:16px;">
          <p><strong>Estudiantes cargados:</strong> <span id="studentCount">0</span></p>
          <table id="studentsTable" class="hidden">
            <thead><tr><th>#</th><th>Apellidos</th><th>Nombres</th><th>ID (QR)</th><th>Acción</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- CARDS -->
    <div id="cardsTab" class="tab-content hidden">
      <div class="section">
        <h2>🪪 Tarjetas QR por estudiante</h2>
        <p class="muted">Estas tarjetas se imprimen. El docente escanea y se marca asistencia automáticamente.</p>

        <div class="cardBox no-print">
          <div class="row">
            <button class="btn btn-primary" onclick="renderStudentCards()">🔄 Generar / Actualizar tarjetas</button>
            <button class="btn btn-success" onclick="window.print()">🖨️ Imprimir</button>
          </div>
        </div>

        <div id="cardsGrid" class="qrGrid"></div>
      </div>
    </div>

    <!-- ATTENDANCE -->
    <div id="attendanceTab" class="tab-content hidden">
      <div class="section">
        <h2>📌 Tomar asistencia con cámara (docente)</h2>

        <div class="cardBox no-print">
          <div class="row3">
            <div>
              <label class="muted">Código de clase (ej: LENG-01)</label>
              <input id="classCode" placeholder="LENG-01" />
            </div>
            <div><label class="muted">Hora inicio</label><input id="horaIni" value="13:00" /></div>
            <div><label class="muted">Hora fin</label><input id="horaFin" value="18:00" /></div>
          </div>

          <div class="row">
            <button class="btn btn-success" onclick="startClass()">▶️ Iniciar clase</button>
            <button class="btn btn-danger" onclick="endClass()">⏹️ Finalizar clase</button>
          </div>

          <div class="row">
            <button class="btn btn-primary" onclick="startCamera()">📷 Iniciar cámara</button>
            <button class="btn btn-danger" onclick="stopCamera()">🛑 Detener cámara</button>
          </div>

          <div class="row">
            <input id="manualScan" placeholder="⌨️ Alternativa: pega aquí el ID escaneado (ej: 4B-013) y Enter"
                   onkeydown="if(event.key==='Enter'){handleScan(this.value); this.value='';}" />
            <button class="btn btn-ghost" onclick="focusManual()">⌨️ Enfocar entrada</button>
          </div>

          <p id="status" class="muted">🔵 Listo. Inicia clase para comenzar.</p>

          <div class="cameraWrap">
            <div id="reader"></div>
            <div class="cameraInfo">
              <div><span class="pill">Escaneo continuo</span><span class="pill">Evita duplicados</span><span class="pill">Registra fecha y hora</span></div>
              <p class="muted" style="margin-top:10px;">
                Enfoca el QR del estudiante (tarjeta). Cada lectura válida marca “PRESENTE” automáticamente.<br>
                Si se repite el mismo QR, se ignora (anti-duplicados).
              </p>
              <p class="muted">
                Si la cámara no abre, revisa: permisos del navegador, que no esté usada por otra app y que estés en Chrome.
              </p>
            </div>
          </div>
        </div>

        <div style="margin-top:16px; text-align:left;">
          <div class="row no-print">
            <button class="btn btn-ghost" onclick="exportAttendanceCSV()">⬇️ Exportar asistencia CSV</button>
            <button class="btn btn-danger" onclick="clearAttendance()">🗑️ Limpiar asistencia de esta clase</button>
          </div>

          <h3 style="margin-top:12px;">Asistencia en tiempo real</h3>
          <div id="studentList"></div>
        </div>
      </div>
    </div>

    <!-- HISTORY -->
    <div id="historyTab" class="tab-content hidden">
      <div class="section">
        <h2>📈 Historial de sesiones</h2>
        <p class="muted">Cada “Finalizar clase” guarda una sesión con fecha, hora y presentes.</p>

        <table id="historyTable">
          <thead>
          <tr>
            <th>Fecha</th><th>Curso</th><th>Clase</th><th>Inicio</th><th>Fin</th><th>Presentes</th><th>CSV</th><th>Eliminar</th>
          </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script>
  const STORAGE = {
    studentsKey: () => `students_${getGroupKey()}`,
    historyKey: () => `history_${getGroupKey()}`,
    sessionKey: () => `session_${getGroupKey()}`
  };

  let students = [];
  let attendance = [];
  let classActive = false;
  let currentSession = null;

  let html5Qr = null;
  let lastScanValue = "";
  let lastScanAt = 0;
  const SCAN_COOLDOWN_MS = 1200;

  function norm(str){ return (str ?? '').toString().trim(); }
  function pad3(n){ return String(n).padStart(3,'0'); }
  function safeUUID(){
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
      const r = (Math.random()*16)|0, v = c==='x'?r:(r&0x3|0x8);
      return v.toString(16);
    });
  }

  function getGroupKey(){
    const c = norm(document.getElementById('curso')?.value || '4').replace(/\s+/g,'');
    const p = norm(document.getElementById('paralelo')?.value || 'B').replace(/\s+/g,'');
    const g = norm(document.getElementById('gestion')?.value || '2025').replace(/\s+/g,'');
    return `${g}_${c}${p}`.toUpperCase();
  }
  function makeStudentId(index1){
    const c = norm(document.getElementById('curso').value).replace(/\s+/g,'');
    const p = norm(document.getElementById('paralelo').value).replace(/\s+/g,'');
    return `${(c+p).toUpperCase()}-${pad3(index1)}`;
  }

  function todayBO(){
    return new Date().toLocaleDateString('es-BO', { year:'numeric', month:'2-digit', day:'2-digit' });
  }
  function timeBO(){
    return new Date().toLocaleTimeString('es-BO', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
  }
  function nowISO(){ return new Date().toISOString(); }

  function escapeHtml(str){
    return (str ?? '').toString()
      .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
      .replaceAll('"','&quot;').replaceAll("'","&#039;");
  }

  function setStatus(msg){
    document.getElementById('status').innerHTML = msg;
  }

  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => showTab(tab.dataset.tab, tab));
  });

  function showTab(tabName, tabEl){
    document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tabName + 'Tab').classList.remove('hidden');
    tabEl.classList.add('active');

    if(tabName === 'import') updateStudentsPreview();
    if(tabName === 'cards') renderStudentCards();
    if(tabName === 'attendance') updateStudentList();
    if(tabName === 'history') loadHistory();
  }

  ['curso','paralelo','gestion'].forEach(id=>{
    document.getElementById(id).addEventListener('input', () => loadAll());
  });

  function downloadTemplate(){
    const csv = "Apellidos,Nombres\nPérez,Gabriel\nMamani,Lucía\n";
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `plantilla_estudiantes_${getGroupKey()}.csv`;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  function importStudents(event){
    const file = event.target.files?.[0];
    if(!file) return;

    if(typeof XLSX === "undefined"){
      document.getElementById('importStatus').textContent = "❌ No se cargó la librería XLSX.";
      return;
    }

    const reader = new FileReader();
    reader.onload = function(e){
      try{
        const data = new Uint8Array(e.target.result);
        const wb = XLSX.read(data, {type:'array'});
        const ws = wb.Sheets[wb.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(ws, {defval:"", header:1});

        if(rows.length < 2){
          document.getElementById('importStatus').textContent = "⚠️ El archivo parece vacío.";
          return;
        }

        const header = rows[0].map(h => (h||'').toString().trim().toLowerCase());
        const idxApellidos = header.findIndex(h => ["apellidos","apellido","apellido paterno"].includes(h));
        const idxNombres = header.findIndex(h => ["nombres","nombre"].includes(h));
        const idxApeNom = header.findIndex(h => ["apellidos y nombres","apellido y nombre"].includes(h));
        const useAB = (idxApellidos === -1 && idxNombres === -1 && idxApeNom === -1);

        const imported = [];
        for(let i=1;i<rows.length;i++){
          const r = rows[i] || [];
          let apellidos = "", nombres = "";

          if(!useAB){
            if(idxApeNom !== -1){
              const full = (r[idxApeNom]||"").toString().trim();
              if(!full) continue;
              if(full.includes(',')){
                const [a,n] = full.split(',').map(x=>x.trim());
                apellidos = a || ""; nombres = n || "";
              } else {
                const parts = full.split(/\s+/).filter(Boolean);
                if(parts.length >= 2){
                  apellidos = parts.slice(0, Math.min(2, parts.length-1)).join(' ');
                  nombres = parts.slice(Math.min(2, parts.length-1)).join(' ');
                } else { apellidos = full; }
              }
            } else {
              apellidos = (r[idxApellidos]||"").toString().trim();
              nombres = (r[idxNombres]||"").toString().trim();
            }
          } else {
            apellidos = (r[0]||"").toString().trim();
            nombres = (r[1]||"").toString().trim();
          }

          const fullName = `${apellidos} ${nombres}`.replace(/\s+/g,' ').trim();
          if(!fullName) continue;
          imported.push({ apellidos, nombres, fullName });
        }

        const existingSet = new Set(students.map(s => s.fullName.toLowerCase()));
        const toAdd = imported.filter(s => !existingSet.has(s.fullName.toLowerCase()));

        const baseCount = students.length;
        const newStudents = toAdd.map((s, idx) => ({
          uuid: safeUUID(),
          order: baseCount + idx + 1,
          apellidos: s.apellidos,
          nombres: s.nombres,
          fullName: s.fullName,
          studentId: makeStudentId(baseCount + idx + 1)
        }));

        students = [...students, ...newStudents];
        saveStudentsToLocal();
        updateStudentsPreview();

        document.getElementById('importStatus').textContent =
          `✅ Importados: ${imported.length} | Agregados: ${newStudents.length} | Duplicados omitidos: ${imported.length - newStudents.length}`;

        alert(`✅ Importación lista.\nAgregados: ${newStudents.length}\nOmitidos (duplicados): ${imported.length - newStudents.length}`);
      } catch(err){
        console.error(err);
        document.getElementById('importStatus').textContent = "❌ Error al leer el archivo.";
      }
    };
    reader.readAsArrayBuffer(file);
  }

  function normalizeOrdering(){
    students = students.map((s,i)=>({ ...s, order:i+1, studentId: makeStudentId(i+1) }));
  }

  function updateStudentsPreview(){
    document.getElementById('studentCount').textContent = students.length;
    const table = document.getElementById('studentsTable');
    const tbody = table.querySelector('tbody');

    if(students.length === 0){
      table.classList.add('hidden');
      tbody.innerHTML = "";
      return;
    }

    table.classList.remove('hidden');
    tbody.innerHTML = students.map((s,i) => `
      <tr>
        <td>${i+1}</td>
        <td>${escapeHtml(s.apellidos)}</td>
        <td>${escapeHtml(s.nombres)}</td>
        <td><b>${escapeHtml(s.studentId)}</b></td>
        <td><button class="btn btn-danger" style="padding:6px 10px;font-size:12px;" onclick="deleteStudent('${s.uuid}')">❌</button></td>
      </tr>
    `).join('');
  }

  function saveStudentsToLocal(){
    localStorage.setItem(STORAGE.studentsKey(), JSON.stringify(students));
  }

  function clearStudents(){
    if(!confirm(`¿Vaciar lista del grupo ${getGroupKey()}?`)) return;
    students = [];
    saveStudentsToLocal();
    updateStudentsPreview();
    alert("✅ Lista vaciada.");
  }

  function deleteStudent(uuid){
    if(!confirm("¿Eliminar este estudiante?")) return;
    students = students.filter(s => s.uuid !== uuid);
    normalizeOrdering();
    saveStudentsToLocal();
    updateStudentsPreview();
  }

  function renderStudentCards(){
    const grid = document.getElementById('cardsGrid');
    grid.innerHTML = "";

    if(typeof QRCode === "undefined"){
      grid.innerHTML = `<p class="muted">❌ No se cargó QRCode.</p>`;
      return;
    }
    if(students.length === 0){
      grid.innerHTML = `<p class="muted">No hay estudiantes. Importa primero.</p>`;
      return;
    }

    const curso = norm(document.getElementById('curso').value);
    const paralelo = norm(document.getElementById('paralelo').value);

    students.forEach((s, idx) => {
      const card = document.createElement('div');
      card.className = "qrCard";
      const qrId = `qr_${idx}_${s.uuid.slice(0,6)}`;
      card.innerHTML = `
        <div class="title">Unidad Educativa Adventista</div>
        <div class="school">Reid Shepard</div>
        <div class="meta">${escapeHtml(s.fullName)} • ${escapeHtml(curso)} “${escapeHtml(paralelo)}”</div>
        <div class="qrBox"><div class="qrInner"><div id="${qrId}"></div></div></div>
        <div class="meta"><b>ID:</b> ${escapeHtml(s.studentId)}</div>
        <div class="bar"></div>
      `;
      grid.appendChild(card);

      new QRCode(document.getElementById(qrId), {
        text: s.studentId,
        width: 130,
        height: 130,
        correctLevel: QRCode.CorrectLevel.M
      });
    });
  }

  function loadAll(){
    students = JSON.parse(localStorage.getItem(STORAGE.studentsKey()) || '[]');

    const sess = JSON.parse(localStorage.getItem(STORAGE.sessionKey()) || 'null');
    if(sess && sess.classActive){
      classActive = true;
      currentSession = sess;
      attendance = sess.attendance || [];
      setStatus(`🟢 CLASE ACTIVA: <b>${escapeHtml(currentSession.classCode)}</b> | Fecha: <b>${escapeHtml(currentSession.date)}</b> | Presentes: <b>${attendance.length}</b>`);
    } else {
      classActive = false;
      currentSession = null;
      attendance = [];
      setStatus('🔵 Listo. Inicia clase para comenzar.');
    }

    updateStudentsPreview();
    updateStudentList();
  }

  function persistSession(){
    localStorage.setItem(STORAGE.sessionKey(), JSON.stringify({
      ...currentSession,
      classActive,
      attendance
    }));
  }

  function startClass(){
    if(students.length === 0){
      alert("⚠️ Importa estudiantes primero.");
      showTab('import', document.querySelector('.tab[data-tab="import"]'));
      return;
    }
    const classCode = norm(document.getElementById('classCode').value) || "CLASE";
    const horaIni = norm(document.getElementById('horaIni').value) || "13:00";
    const horaFin = norm(document.getElementById('horaFin').value) || "18:00";

    classActive = true;
    attendance = [];

    currentSession = {
      sessionId: safeUUID(),
      groupKey: getGroupKey(),
      classCode,
      date: todayBO(),
      startISO: nowISO(),
      horaIni, horaFin,
      attendance: []
    };

    persistSession();
    setStatus(`🟢 CLASE ACTIVA: <b>${escapeHtml(classCode)}</b> | Fecha: <b>${escapeHtml(currentSession.date)}</b> | Horario: <b>${escapeHtml(horaIni)}–${escapeHtml(horaFin)}</b>`);
    updateStudentList();
    focusManual();
  }

  function endClass(){
    if(!classActive || !currentSession){
      alert("⚠️ No hay clase activa.");
      return;
    }

    stopCamera();

    currentSession.attendance = attendance;
    currentSession.endISO = nowISO();

    const history = JSON.parse(localStorage.getItem(STORAGE.historyKey()) || '[]');
    history.unshift(currentSession);
    localStorage.setItem(STORAGE.historyKey(), JSON.stringify(history));

    classActive = false;
    currentSession = null;
    attendance = [];
    localStorage.removeItem(STORAGE.sessionKey());

    setStatus('🔴 CLASE FINALIZADA — Sesión guardada en Historial.');
    updateStudentList();
    alert("✅ Clase finalizada y guardada.");
  }

  function focusManual(){
    document.getElementById('manualScan').focus();
  }

  function handleScan(raw){
    const code = norm(raw).toUpperCase();
    if(!code) return;

    if(!classActive || !currentSession){
      alert("⚠️ Inicia clase primero.");
      return;
    }

    // Anti-doble lectura por cámara (rebotes)
    const now = Date.now();
    if(code === lastScanValue && (now - lastScanAt) < SCAN_COOLDOWN_MS){
      return;
    }
    lastScanValue = code;
    lastScanAt = now;

    const student = students.find(s => s.studentId.toUpperCase() === code);
    if(!student){
      setStatus(`❌ ID no encontrado: <b>${escapeHtml(code)}</b> (grupo: ${escapeHtml(getGroupKey())})`);
      return;
    }

    const already = attendance.find(a => a.studentUUID === student.uuid);
    if(already){
      setStatus(`🟡 Duplicado ignorado: <b>${escapeHtml(student.fullName)}</b> (${escapeHtml(student.studentId)})`);
      return;
    }

    attendance.push({
      id: safeUUID(),
      date: currentSession.date,
      classCode: currentSession.classCode,
      groupKey: currentSession.groupKey,
      studentUUID: student.uuid,
      studentId: student.studentId,
      student: student.fullName,
      time: timeBO(),
      status: 'present'
    });

    persistSession();
    setStatus(`✅ Registrado: <b>${escapeHtml(student.fullName)}</b> — ${escapeHtml(student.studentId)} | Fecha: <b>${escapeHtml(currentSession.date)}</b> | Presentes: <b>${attendance.length}</b>`);
    updateStudentList();
  }

  function clearAttendance(){
    if(!confirm("¿Limpiar asistencia de la clase actual?")) return;
    attendance = [];
    if(currentSession) persistSession();
    updateStudentList();
    setStatus("🧹 Asistencia limpia para esta clase.");
  }

  function updateStudentList(){
    const list = document.getElementById('studentList');

    if(students.length === 0){
      list.innerHTML = `<p class="muted">No hay estudiantes importados.</p>`;
      return;
    }

    const presentSet = new Set(attendance.map(a => a.studentUUID));
    list.innerHTML = students.map(s => {
      const isPresent = presentSet.has(s.uuid);
      const record = attendance.find(a => a.studentUUID === s.uuid);

      return `
        <div class="student-item ${isPresent ? 'student-present' : 'student-absent'}">
          <div>
            <b>${escapeHtml(s.fullName)}</b><br>
            <span class="muted">ID: ${escapeHtml(s.studentId)}</span>
          </div>
          <div style="text-align:right;">
            <b>${isPresent ? 'PRESENTE' : 'AUSENTE'}</b><br>
            <span class="muted">${isPresent ? `${escapeHtml(record.date)} ${escapeHtml(record.time)}` : ''}</span>
          </div>
        </div>
      `;
    }).join('');
  }

  function exportAttendanceCSV(){
    if(!currentSession){
      alert("⚠️ No hay sesión activa para exportar. Exporta desde Historial o inicia clase.");
      return;
    }
    exportArrayAsCSV(attendance, `asistencia_${currentSession.groupKey}_${currentSession.classCode}_${currentSession.date}.csv`);
  }

  function exportSessionCSV(sessionId){
    const history = JSON.parse(localStorage.getItem(STORAGE.historyKey()) || '[]');
    const sess = history.find(h => h.sessionId === sessionId);
    if(!sess){ alert("No encontré la sesión."); return; }
    exportArrayAsCSV(sess.attendance || [], `asistencia_${sess.groupKey}_${sess.classCode}_${sess.date}.csv`);
  }

  function exportArrayAsCSV(arr, filename){
    const header = ["date","groupKey","classCode","studentId","student","time","status"].join(",");
    const lines = (arr || []).map(a => [
      csvEscape(a.date), csvEscape(a.groupKey), csvEscape(a.classCode),
      csvEscape(a.studentId), csvEscape(a.student), csvEscape(a.time), csvEscape(a.status)
    ].join(","));
    const csv = [header, ...lines].join("\n");
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  function csvEscape(v){
    const s = (v ?? '').toString();
    if(/[",\n]/.test(s)) return `"${s.replaceAll('"','""')}"`;
    return s;
  }

  function loadHistory(){
    const tbody = document.querySelector('#historyTable tbody');
    const history = JSON.parse(localStorage.getItem(STORAGE.historyKey()) || '[]');

    if(!history.length){
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:#6c757d;">No hay sesiones guardadas.</td></tr>`;
      return;
    }

    tbody.innerHTML = history.map(h => {
      const n = (h.attendance || []).length;
      const start = h.startISO ? new Date(h.startISO).toLocaleTimeString('es-BO', {hour:'2-digit', minute:'2-digit'}) : '';
      const end = h.endISO ? new Date(h.endISO).toLocaleTimeString('es-BO', {hour:'2-digit', minute:'2-digit'}) : '';
      return `
        <tr>
          <td>${escapeHtml(h.date || '')}</td>
          <td>${escapeHtml(h.groupKey || '')}</td>
          <td>${escapeHtml(h.classCode || '')}</td>
          <td>${escapeHtml(start)}</td>
          <td>${escapeHtml(end)}</td>
          <td><b>${n}</b></td>
          <td><button class="btn btn-ghost" style="padding:6px 10px;font-size:12px;" onclick="exportSessionCSV('${h.sessionId}')">CSV</button></td>
          <td><button class="btn btn-danger" style="padding:6px 10px;font-size:12px;" onclick="deleteSession('${h.sessionId}')">❌</button></td>
        </tr>
      `;
    }).join('');
  }

  function deleteSession(sessionId){
    if(!confirm("¿Eliminar esta sesión del historial?")) return;
    const history = JSON.parse(localStorage.getItem(STORAGE.historyKey()) || '[]');
    const next = history.filter(h => h.sessionId !== sessionId);
    localStorage.setItem(STORAGE.historyKey(), JSON.stringify(next));
    loadHistory();
  }

  async function startCamera(){
    if(!classActive || !currentSession){
      alert("⚠️ Inicia clase primero.");
      return;
    }

    if(typeof Html5Qrcode === "undefined"){
      alert("❌ No se cargó html5-qrcode.");
      return;
    }

    if(html5Qr){
      setStatus("🟡 La cámara ya está activa.");
      return;
    }

    try{
      html5Qr = new Html5Qrcode("reader");

      const config = {
        fps: 12,
        qrbox: { width: 260, height: 260 },
        disableFlip: false
      };

      // Usa cámara trasera si existe
      await html5Qr.start(
        { facingMode: "environment" },
        config,
        (decodedText) => {
          // decodedText debería ser: 4B-001, 4B-013, etc.
          handleScan(decodedText);
        },
        () => {}
      );

      setStatus(`📷 Cámara activa. Escanea QRs… | Fecha: <b>${escapeHtml(currentSession.date)}</b> | Presentes: <b>${attendance.length}</b>`);
    } catch(err){
      console.error(err);
      html5Qr = null;
      alert("❌ No se pudo abrir la cámara. Revisa permisos del navegador y vuelve a intentar.");
    }
  }

  async function stopCamera(){
    try{
      if(html5Qr){
        await html5Qr.stop();
        await html5Qr.clear();
        html5Qr = null;
      }
    } catch(e){
      html5Qr = null;
    }
  }

  window.addEventListener('load', () => {
    document.getElementById('importTab').classList.remove('hidden');
    loadAll();
  });
</script>
</body>
</html>
