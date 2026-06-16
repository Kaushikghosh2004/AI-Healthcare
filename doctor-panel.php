<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('func1.php');

$host = 'localhost:3306';
$dbname = 'myhmsdb';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $columns = $pdo->query("SHOW COLUMNS FROM appointmenttb")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('current_status', $columns)) {
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN current_status VARCHAR(20) DEFAULT 'Scheduled'");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN bed_number VARCHAR(10) DEFAULT 'UNASSIGNED'");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN oxygen_level INT DEFAULT 98");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN oxygen_liters FLOAT DEFAULT 0.0");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN heart_rate INT DEFAULT 75");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN auto_o2_mode TINYINT(1) DEFAULT 0");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN alert_threshold INT DEFAULT 90");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN admission_date DATETIME NULL");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN daily_update TEXT NULL");
        $pdo->exec("UPDATE appointmenttb SET current_status = 'Scheduled'");
    } else {
        $pdo->exec("ALTER TABLE appointmenttb ALTER current_status SET DEFAULT 'Scheduled'");
    }
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS patient_vitals_log (
        id INT AUTO_INCREMENT PRIMARY KEY, appt_id INT, pid INT NOT NULL, oxygen_level INT NOT NULL, 
        oxygen_liters FLOAT NOT NULL, heart_rate INT NOT NULL, changed_by VARCHAR(50), 
        change_type VARCHAR(20), recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch(PDOException $e) {
    die("<div style='background-color:#fee2e2; color:#991b1b; padding:20px;'>System Error: Database connection failed.</div>");
}

if(isset($_GET['ajax_get_vitals'])) {
    header('Content-Type: application/json');
    $appt_id = $_GET['ajax_get_vitals'];
    
    $stmt = $pdo->prepare("SELECT oxygen_level, heart_rate, oxygen_liters, daily_update FROM appointmenttb WHERE ID = ?");
    $stmt->execute([$appt_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    $log_stmt = $pdo->prepare("SELECT changed_by FROM patient_vitals_log WHERE appt_id = ? ORDER BY recorded_at DESC LIMIT 1");
    $log_stmt->execute([$appt_id]);
    $data['last_edited'] = $log_stmt->fetchColumn() ?: "System";

    echo json_encode($data);
    exit(); 
}

if(!isset($_SESSION['dname'])) { header("Location: index.php"); exit(); }
$doctor = $_SESSION['dname'];
$notification = '';

if(isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("UPDATE appointmenttb SET doctorStatus='0' WHERE ID = ?");
    if($stmt->execute([$_GET['ID']])) {
        $notification = "<div class='bg-rose-50 text-rose-700 px-4 py-3 rounded-xl mb-6 shadow-sm border border-rose-200'><i class='fa-solid fa-circle-check mr-2'></i> Appointment Cancelled.</div>";
    }
}

if(isset($_POST['check_in_patient'])) {
    $target_appt = $_POST['appt_id'];
    try {
        $stmt = $pdo->prepare("UPDATE appointmenttb SET current_status = 'Outpatient' WHERE ID = ?");
        $stmt->execute([$target_appt]);
        $notification = "<div class='bg-blue-50 text-blue-800 px-4 py-3 rounded-xl mb-6 shadow-sm border border-blue-200'><i class='fa-solid fa-clipboard-check mr-2'></i> Patient arrival confirmed. Checked-in as Outpatient.</div>";
    } catch (Exception $e) {
        $notification = "<div class='bg-rose-50 text-rose-700 px-4 py-3 rounded-xl mb-6 shadow-sm border border-rose-200'>Error updating status.</div>";
    }
}

if(isset($_POST['admit_patient'])) {
    $target_appt = $_POST['appt_id'];
    $target_pid = $_POST['pid'];
    $bed_num = $_POST['bed_number'];
    $threshold = $_POST['alert_threshold'];
    
    $init_spo2 = rand(88, 96);
    $init_hr = rand(70, 100);
    $init_liters = ($init_spo2 < 92) ? 2.5 : 0;

    $stmt = $pdo->prepare("UPDATE appointmenttb SET current_status = 'Admitted', bed_number = ?, alert_threshold = ?, oxygen_level = ?, heart_rate = ?, oxygen_liters = ?, admission_date = CURRENT_TIMESTAMP WHERE ID = ?");
    if($stmt->execute([$bed_num, $threshold, $init_spo2, $init_hr, $init_liters, $target_appt])) {
        $pdo->prepare("INSERT INTO patient_vitals_log (appt_id, pid, oxygen_level, oxygen_liters, heart_rate, changed_by, change_type) VALUES (?, ?, ?, ?, ?, ?, 'Admission')")->execute([$target_appt, $target_pid, $init_spo2, $init_liters, $init_hr, "Dr. ".$doctor]);
        $notification = "<div class='bg-indigo-50 text-indigo-800 px-4 py-3 rounded-xl mb-6 shadow-sm border border-indigo-200'><i class='fa-solid fa-bed-pulse mr-2'></i> Patient admitted to Bed $bed_num following Biometric Verification.</div>";
    }
}

if(isset($_POST['discharge_patient'])) {
    $target_appt = $_POST['appt_id'];
    $target_pid = $_POST['pid'];
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE appointmenttb SET current_status = 'Discharged', bed_number = 'UNASSIGNED', oxygen_level = NULL, heart_rate = NULL, oxygen_liters = 0, auto_o2_mode = 0 WHERE ID = ?");
        $stmt->execute([$target_appt]);
        
        $log = $pdo->prepare("INSERT INTO patient_vitals_log (appt_id, pid, oxygen_level, oxygen_liters, heart_rate, changed_by, change_type) VALUES (?, ?, 0, 0, 0, ?, 'Discharge')");
        $log->execute([$target_appt, $target_pid, "Dr. " . $doctor]);
        
        $pdo->commit();
        $notification = "<div class='bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 shadow-sm border border-emerald-200'><i class='fa-solid fa-house-medical-circle-check mr-2'></i> Episode successfully discharged.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $notification = "<div class='bg-rose-50 text-rose-700 px-4 py-3 rounded-xl mb-6 shadow-sm border border-rose-200'>Error processing discharge.</div>";
    }
}

if(isset($_POST['update_vitals'])) {
    $target_appt = $_POST['appt_id'];
    $target_pid = $_POST['pid'];
    $new_spo2 = $_POST['new_spo2'];
    $new_liters = $_POST['new_liters'];
    $new_hr = $_POST['new_hr'];
    $daily_update = trim($_POST['daily_update']);
    $auto_mode = isset($_POST['auto_o2_mode']) ? 1 : 0;

    try {
        $pdo->beginTransaction();
        $update = $pdo->prepare("UPDATE appointmenttb SET oxygen_level = ?, oxygen_liters = ?, heart_rate = ?, auto_o2_mode = ?, daily_update = ? WHERE ID = ?");
        $update->execute([$new_spo2, $new_liters, $new_hr, $auto_mode, $daily_update, $target_appt]);

        $log = $pdo->prepare("INSERT INTO patient_vitals_log (appt_id, pid, oxygen_level, oxygen_liters, heart_rate, changed_by, change_type) VALUES (?, ?, ?, ?, ?, ?, 'Manual')");
        $log->execute([$target_appt, $target_pid, $new_spo2, $new_liters, $new_hr, "Dr. " . $doctor]);

        $pdo->commit();
        $notification = "<div class='bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 shadow-sm border border-emerald-200'><i class='fa-solid fa-check-double mr-2'></i> Vitals saved successfully.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

$appts = $pdo->prepare("
    SELECT a.ID, a.pid, a.fname, a.lname, a.gender, a.email, a.contact, a.appdate, a.apptime, a.userStatus, a.doctorStatus,
           a.current_status, a.bed_number, a.oxygen_level, a.oxygen_liters, a.heart_rate, a.alert_threshold, a.auto_o2_mode, a.daily_update,
           (SELECT ID FROM prestb WHERE ID = a.ID LIMIT 1) as pres_id 
    FROM appointmenttb a WHERE a.doctor = ? ORDER BY a.appdate DESC, a.apptime DESC
");
$appts->execute([$doctor]);
$all_appointments = $appts->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KASSAH Vitals Clinical | Doctor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
    <style>
        body { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.8); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .tab-content { display: none; animation: slideUp 0.3s ease-out; }
        .tab-content.active { display: block; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Biometric Scan Animation */
        @keyframes scan {
            0% { top: 0; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan { animation: scan 2s ease-in-out infinite; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        input[type=range] { -webkit-appearance: none; background: transparent; width: 100%; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; height: 32px; width: 32px; border-radius: 50%; background: #0d9488; cursor: pointer; margin-top: -12px;}
        input[type=range]::-webkit-slider-runnable-track { width: 100%; height: 8px; cursor: pointer; background: #cbd5e1; border-radius: 4px; }
        .toggle-checkbox:checked { right: 0; border-color: #0d9488; }
        .toggle-checkbox:checked + .toggle-label { background-color: #0d9488; }
        .toggle-checkbox { right: 0; z-index: 1; border-color: #e2e8f0; transition: all 0.3s; }
        .toggle-label { width: 3.5rem; height: 1.75rem; background-color: #cbd5e1; transition: all 0.3s; }
    </style>
</head>
<body class="h-screen w-full flex flex-col overflow-hidden">

    <nav class="glass-card h-20 px-8 flex justify-between items-center shrink-0 z-40 border-b border-white/50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-teal-500 to-blue-600 text-white p-2.5 rounded-xl shadow-lg">
                <i class="fa-solid fa-user-doctor text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">KASSAH Vitals <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-blue-600">Clinical</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden md:block">
                <p class="text-sm font-bold text-slate-800">Dr. <?= htmlspecialchars($doctor) ?></p>
                <p class="text-xs text-teal-600 bg-teal-50 font-semibold px-2 py-0.5 rounded-full inline-block mt-1">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse mr-1"></i> On Duty
                </p>
            </div>
            <a href="logout1.php" class="bg-white border border-slate-200 text-slate-600 hover:text-red-600 px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm hover:shadow">
                <i class="fa fa-sign-out-alt mr-2"></i> Lock Station
            </a>
        </div>
    </nav>

    <div class="flex w-full h-[calc(100vh-5rem)] overflow-hidden">
        <aside class="w-72 shrink-0 glass-card p-6 hidden md:flex flex-col border-r border-white/50 z-30">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Command Modules</p>
            <nav class="flex flex-col gap-2" id="nav-tabs">
                <button onclick="switchTab('dashboard')" class="tab-btn active w-full text-left px-4 py-3.5 rounded-xl bg-teal-500 text-white font-bold shadow-md transition group" data-target="dashboard">
                    <i class="fa-solid fa-chart-pie text-center w-6"></i> All Appointments
                </button>
                <button onclick="switchTab('prescriptions')" class="tab-btn w-full text-left px-4 py-3.5 rounded-xl text-slate-600 hover:bg-white hover:shadow font-semibold transition" data-target="prescriptions">
                    <i class="fa-solid fa-file-prescription text-center w-6 text-slate-400"></i> Prescription History
                </button>
            </nav>
        </aside>

        <main class="flex-1 w-full h-full p-6 md:p-8 overflow-y-auto relative z-10">
            <?= $notification ?>

            <div id="dashboard" class="tab-content active max-w-screen-2xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-800">Master Patient List</h2>
                        <p class="text-slate-500 font-medium mt-1">Manage appointments, admit patients, and monitor clinical status.</p>
                    </div>
                    <button onclick="window.location.reload();" class="bg-white border border-slate-200 text-teal-600 px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:shadow hover:bg-slate-50 transition">
                        <i class="fa-solid fa-rotate-right mr-1"></i> Pull Live Data
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="p-4">PID/Appt</th>
                                    <th class="p-4">Patient Name</th>
                                    <th class="p-4">Contact Info</th>
                                    <th class="p-4">Date & Time</th>
                                    <th class="p-4">Clinical Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                <?php 
                                if(count($all_appointments) > 0):
                                    foreach($all_appointments as $row): 
                                        $is_active = ($row['userStatus']==1 && $row['doctorStatus']==1);
                                        $status = $row['current_status']; 
                                        $is_prescribed = !empty($row['pres_id']); 
                                ?>
                                <tr class="hover:bg-white/50 transition">
                                    <td class="p-4 text-slate-400 font-mono text-xs">P#<?= $row['pid'] ?><br>A#<?= $row['ID'] ?></td>
                                    <td class="p-4 font-bold text-slate-900"><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?><br><span class="text-xs text-slate-400 font-normal"><?= htmlspecialchars($row['gender']) ?></span></td>
                                    <td class="p-4 text-slate-500"><?= htmlspecialchars($row['contact']) ?><br><span class="text-xs"><?= htmlspecialchars($row['email']) ?></span></td>
                                    <td class="p-4"><?= htmlspecialchars($row['appdate']) ?><br><span class="text-slate-400 text-xs"><?= date('h:i A', strtotime($row['apptime'])) ?></span></td>
                                    
                                    <td class="p-4">
                                        <?php if(!$is_active): ?>
                                            <span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full text-xs font-bold">CANCELLED</span>
                                        <?php elseif($is_prescribed): ?>
                                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-bold border border-blue-200"><i class="fa-solid fa-clipboard-check mr-1"></i> Prescribed</span>
                                        <?php elseif($status == 'Admitted'): ?>
                                            <div class="flex flex-col gap-1">
                                                <span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full text-xs font-bold w-max"><i class="fa-solid fa-bed mr-1"></i> Bed: <?= $row['bed_number'] ?></span>
                                                <?php if($row['oxygen_level'] <= $row['alert_threshold']): ?>
                                                    <span class="text-red-600 text-[10px] font-bold animate-pulse"><i class="fa-solid fa-triangle-exclamation"></i> O2 Critical: <?= $row['oxygen_level'] ?>%</span>
                                                <?php else: ?>
                                                    <span class="text-emerald-600 text-[10px] font-bold"><i class="fa-solid fa-lungs"></i> SpO2: <?= $row['oxygen_level'] ?>% | HR: <?= $row['heart_rate'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif($status == 'Discharged'): ?>
                                            <span class="bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Discharged</span>
                                        <?php elseif($status == 'Outpatient'): ?>
                                            <span class="bg-teal-50 text-teal-700 px-2.5 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-user-doctor mr-1"></i> Outpatient</span>
                                        <?php else: ?>
                                            <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full text-xs font-bold border border-amber-200"><i class="fa-solid fa-calendar-check mr-1"></i> Scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="p-4">
                                        <div class="flex justify-end gap-2 items-center">
                                            <?php if($is_active): ?>
                                                <?php if($is_prescribed): ?>
                                                    <span class="text-slate-400 text-xs font-bold mr-2">Consult Completed</span>
                                                <?php elseif($status == 'Admitted'): ?>
                                                    <button onclick="openVitalsModal(<?= $row['ID'] ?>)" class="bg-slate-900 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm border border-slate-900" title="Vitals & Daily Update">
                                                        <i class="fa-solid fa-chart-pie mr-1"></i> Vitals
                                                    </button>
                                                    <form method="POST" class="inline-block m-0" onsubmit="return confirm('Discharge <?= htmlspecialchars($row['fname']) ?>?');">
                                                        <input type="hidden" name="appt_id" value="<?= $row['ID'] ?>">
                                                        <input type="hidden" name="pid" value="<?= $row['pid'] ?>">
                                                        <button type="submit" name="discharge_patient" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm border border-emerald-500" title="Discharge Patient">
                                                            <i class="fa-solid fa-person-walking-arrow-right"></i>
                                                        </button>
                                                    </form>
                                                <?php elseif($status == 'Discharged'): ?>
                                                    <span class="text-slate-400 text-xs font-bold italic mr-2">Visit Concluded</span>
                                                <?php elseif($status == 'Outpatient'): ?>
                                                    <button onclick="openAdmitModal(<?= $row['ID'] ?>)" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                                        <i class="fa-solid fa-bed-pulse mr-1"></i> Admit
                                                    </button>
                                                    <a href="prescribe.php?pid=<?= $row['pid'] ?>&ID=<?= $row['ID'] ?>&fname=<?= $row['fname'] ?>&lname=<?= $row['lname'] ?>&appdate=<?= $row['appdate'] ?>&apptime=<?= $row['apptime'] ?>" 
                                                       class="bg-teal-500 hover:bg-teal-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm" title="Prescribe Medicine">
                                                        <i class="fa-solid fa-notes-medical"></i>
                                                    </a>
                                                    <a href="?ID=<?= $row['ID'] ?>&cancel=update" onclick="return confirm('Cancel this appointment?')" class="bg-white border border-slate-200 text-rose-500 hover:bg-rose-50 px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <form method="POST" class="inline-block m-0" onsubmit="return confirm('Confirm patient arrival and check them in?');">
                                                        <input type="hidden" name="appt_id" value="<?= $row['ID'] ?>">
                                                        <button type="submit" name="check_in_patient" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm" title="Check-in Patient">
                                                            <i class="fa-solid fa-clipboard-check"></i> Check-in
                                                        </button>
                                                    </form>
                                                    <a href="?ID=<?= $row['ID'] ?>&cancel=update" onclick="return confirm('Cancel this appointment?')" class="bg-white border border-slate-200 text-rose-500 hover:bg-rose-50 px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm ml-1">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-slate-300 text-xs font-bold">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?><tr><td colspan="6" class="p-8 text-center text-slate-400 font-medium">No appointments found.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="prescriptions" class="tab-content max-w-7xl mx-auto">
                <div class="mb-8"><h2 class="text-3xl font-extrabold text-slate-800">Prescription History</h2></div>
                <div class="glass-card rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="p-5">PID / Name</th>
                                    <th class="p-5">Date & Time</th>
                                    <th class="p-5">Diagnosis</th>
                                    <th class="p-5">Allergy</th>
                                    <th class="p-5">Prescription Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                <?php 
                                $prescriptions = $pdo->prepare("SELECT pid, fname, lname, ID, appdate, apptime, disease, allergy, prescription FROM prestb WHERE doctor = ? ORDER BY appdate DESC");
                                $prescriptions->execute([$doctor]);
                                $results = $prescriptions->fetchAll();
                                if(count($results) > 0):
                                    foreach($results as $row): ?>
                                    <tr class="hover:bg-white/50 transition">
                                        <td class="p-5 text-slate-900 font-bold">P#<?= htmlspecialchars($row['pid']) ?> <br><span class="text-teal-600"><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></span></td>
                                        <td class="p-5 text-slate-500"><?= htmlspecialchars($row['appdate']) ?> <br><span class="text-xs"><?= date('h:i A', strtotime($row['apptime'])) ?></span></td>
                                        <td class="p-5 font-bold text-slate-800"><?= htmlspecialchars($row['disease']) ?></td>
                                        <td class="p-5 text-rose-500 text-xs font-bold"><?= htmlspecialchars($row['allergy']) ?: 'None' ?></td>
                                        <td class="p-5 font-mono text-xs max-w-xs truncate text-slate-500 bg-white/50 rounded border border-slate-100" title="<?= htmlspecialchars($row['prescription']) ?>">
                                            <?= htmlspecialchars($row['prescription']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; else: ?><tr><td colspan="5" class="p-8 text-center text-slate-400 font-medium">No prescriptions logged yet.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <?php 
    if(count($all_appointments) > 0):
        foreach($all_appointments as $row): 
            $is_active = ($row['userStatus']==1 && $row['doctorStatus']==1);
            $status = $row['current_status'];
            $is_admitted = ($status == 'Admitted');
            $is_prescribed = !empty($row['pres_id']);
            
            $last_log_stmt = $pdo->prepare("SELECT changed_by FROM patient_vitals_log WHERE appt_id = ? ORDER BY recorded_at DESC LIMIT 1");
            $last_log_stmt->execute([$row['ID']]);
            $last_modifier = $last_log_stmt->fetchColumn() ?: "System";
            
            if($is_active && !$is_admitted && $status != 'Discharged' && !$is_prescribed): 
    ?>
        <div id="admitModal_<?= $row['ID'] ?>" class="hidden fixed inset-0 z-[100] flex justify-center items-center backdrop-blur-md bg-slate-900/60 p-4">
            <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4"><i class="fa-solid fa-bed-pulse"></i></div>
                    <h3 class="text-2xl font-bold text-slate-800">Admit Patient</h3>
                    <p class="text-slate-500 text-sm mt-1"><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?> (Appt #<?= $row['ID'] ?>)</p>
                </div>
                
                <form method="post" action="" id="admitForm_<?= $row['ID'] ?>" class="space-y-5">
                    <input type="hidden" name="appt_id" value="<?= $row['ID'] ?>">
                    <input type="hidden" name="pid" value="<?= $row['pid'] ?>">
                    
                    <div id="admitFields_<?= $row['ID'] ?>">
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Assign Ward/Bed Number</label>
                            <input type="text" name="bed_number" placeholder="e.g. ICU-01" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SpO2 Alert Threshold (%)</label>
                            <input type="number" name="alert_threshold" value="90" required min="50" max="100" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            <p class="text-xs text-slate-500 mt-2"><i class="fa-solid fa-info-circle mr-1"></i> System flags critical if O2 drops below this level.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4" id="admitActions_<?= $row['ID'] ?>">
                        <button type="button" onclick="closeAdmitModal(<?= $row['ID'] ?>)" class="w-1/2 px-4 py-3 text-slate-600 font-bold bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">Cancel</button>
                        <button type="button" onclick="startBiometricScan(<?= $row['ID'] ?>, <?= $row['pid'] ?>)" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-face-viewfinder"></i> Verify & Admit
                        </button>
                    </div>

                    <div id="biometricUI_<?= $row['ID'] ?>" class="hidden flex flex-col items-center pt-2">
                        <div class="relative w-full h-56 bg-slate-900 rounded-2xl overflow-hidden border border-indigo-500/50 shadow-[0_0_15px_rgba(99,102,241,0.4)] backdrop-blur-xl">
                            <video id="videoFeed_<?= $row['ID'] ?>" class="w-full h-full object-cover transform -scale-x-100" autoplay playsinline></video>
                            
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-indigo-400 shadow-[0_0_10px_#818cf8,0_0_20px_#818cf8] animate-scan"></div>
                            <div class="absolute inset-0 border-[2px] border-dashed border-white/40 m-6 rounded-xl pointer-events-none opacity-50"></div>
                            
                            <div class="absolute top-3 left-3 flex gap-1">
                                <div class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
                                <span class="text-[8px] font-mono text-white/80 uppercase tracking-widest">LIVE</span>
                            </div>
                        </div>
                        
                        <p id="scanStatus_<?= $row['ID'] ?>" class="text-indigo-600 font-bold text-sm mt-4 animate-pulse">Initializing Security Node...</p>
                        
                        <div class="flex gap-3 w-full mt-5">
                            <button type="button" onclick="cancelScan(<?= $row['ID'] ?>)" class="w-1/3 px-4 py-3 text-slate-600 font-bold bg-slate-100 rounded-xl hover:bg-slate-200 transition text-sm">Abort</button>
                            <button type="button" onclick="captureAndVerify(<?= $row['ID'] ?>, <?= $row['pid'] ?>)" id="captureBtn_<?= $row['ID'] ?>" class="w-2/3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold transition shadow-md opacity-50 cursor-not-allowed text-sm flex items-center justify-center gap-2" disabled>
                                <i class="fa-solid fa-fingerprint"></i> Authenticate
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    <?php 
            endif;
            
            if($is_active && $is_admitted): 
    ?>
        <div id="vitalsModal_<?= $row['ID'] ?>" class="hidden fixed inset-0 z-[100] flex justify-center items-center backdrop-blur-md bg-slate-900/80 p-4 sm:p-8">
            <div class="bg-slate-50 w-full max-w-6xl h-full max-h-[90vh] rounded-3xl shadow-2xl flex flex-col border border-slate-300 overflow-hidden relative">
                
                <div class="bg-slate-900 text-white p-6 md:p-8 flex justify-between items-start shrink-0 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10 flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <p class="text-teal-400 text-xs font-bold uppercase tracking-widest">Ward Telemetry & Notes</p>
                            <span class="last-edited-tag text-[10px] font-bold text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i> Last Edited: <?= htmlspecialchars($last_modifier) ?>
                            </span>
                        </div>
                        <h3 class="text-3xl font-black mb-2"><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></h3>
                        <div class="flex gap-4 text-sm font-medium">
                            <span class="bg-white/10 px-3 py-1 rounded-full"><i class="fa-solid fa-bed mr-2"></i><?= $row['bed_number'] ?></span>
                            <span class="bg-white/10 px-3 py-1 rounded-full"><i class="fa-solid fa-bell mr-2"></i>Alert @ <?= $row['alert_threshold'] ?>%</span>
                        </div>
                    </div>
                    <button type="button" onclick="closeVitalsModal(<?= $row['ID'] ?>)" class="relative z-10 w-12 h-12 bg-white/10 hover:bg-red-500 hover:text-white rounded-full flex items-center justify-center text-xl transition ml-4"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form method="post" action="" class="flex-1 overflow-y-auto p-6 md:p-8 flex flex-col">
                    <input type="hidden" name="appt_id" value="<?= $row['ID'] ?>">
                    <input type="hidden" name="pid" value="<?= $row['pid'] ?>">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 flex-1">
                        <div class="flex flex-col gap-6">
                            <h4 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2"><i class="fa-solid fa-heart-pulse text-rose-500 mr-2"></i> Log Live Vitals</h4>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">SpO2 (%)</p>
                                    <div class="flex items-center justify-center gap-3">
                                        <i class="fa-solid fa-lungs text-blue-400 text-3xl"></i>
                                        <input type="number" name="new_spo2" value="<?= $row['oxygen_level'] ?>" required min="0" max="100" class="w-24 text-5xl font-black text-slate-800 bg-transparent border-b-4 border-slate-100 focus:border-blue-500 outline-none text-center transition">
                                    </div>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Heart Rate</p>
                                    <div class="flex items-center justify-center gap-3">
                                        <i class="fa-solid fa-heart-pulse text-rose-400 text-3xl"></i>
                                        <input type="number" name="new_hr" value="<?= $row['heart_rate'] ?>" required min="0" max="250" class="w-24 text-5xl font-black text-slate-800 bg-transparent border-b-4 border-slate-100 focus:border-rose-500 outline-none text-center transition">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mt-auto">
                                <h4 class="text-sm font-bold text-slate-700 mb-3"><i class="fa-solid fa-notes-medical text-teal-500 mr-2"></i>Daily Clinical Update / Notes</h4>
                                <textarea name="daily_update" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 transition" placeholder="Enter daily observations, medication changes, or update for the patient..."><?= htmlspecialchars($row['daily_update'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden flex flex-col justify-center">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>
                            
                            <h4 class="text-2xl font-bold mb-8 relative z-10"><i class="fa-solid fa-wind text-teal-400 mr-2"></i> Respiratory Control</h4>
                            
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 mb-8 border border-white/10 relative z-10 flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold text-lg flex items-center gap-2">Auto O2 Regulation (IoMT)</h4>
                                    <p class="text-sm text-slate-400 mt-1">System adapts flow if SpO2 drops below <?= $row['alert_threshold'] ?>%.</p>
                                </div>
                                <div class="relative inline-block w-14 align-middle select-none transition duration-200 ease-in">
                                    <input type="checkbox" name="auto_o2_mode" id="toggle_<?= $row['ID'] ?>" <?= $row['auto_o2_mode'] ? 'checked' : '' ?> class="toggle-checkbox absolute block w-7 h-7 rounded-full bg-white border-4 appearance-none cursor-pointer"/>
                                    <label for="toggle_<?= $row['ID'] ?>" class="toggle-label block overflow-hidden h-7 rounded-full bg-slate-700 cursor-pointer"></label>
                                </div>
                            </div>

                            <div class="relative z-10 mb-4 flex justify-between items-end">
                                <p class="text-lg font-bold text-slate-300">Set Manual Flow Rate</p>
                                <div class="text-teal-400 font-black text-5xl">
                                    <output id="litersOut_<?= $row['ID'] ?>"><?= $row['oxygen_liters'] ?></output> <span class="text-xl font-bold text-slate-500">L/min</span>
                                </div>
                            </div>
                            
                            <input type="range" name="new_liters" value="<?= $row['oxygen_liters'] ?>" min="0" max="15" step="0.5" 
                                   oninput="document.getElementById('litersOut_<?= $row['ID'] ?>').value = this.value" class="mt-4 mb-8 relative z-10">

                            <button type="submit" name="update_vitals" class="w-full mt-auto bg-teal-600 hover:bg-teal-500 text-white font-extrabold py-5 rounded-2xl transition duration-300 shadow-lg shadow-teal-500/30 flex items-center justify-center gap-3 text-xl relative z-10">
                                Sign & Commit Updates <i class="fa-solid fa-signature"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php 
            endif;
        endforeach; 
    endif; 
    ?>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.className = "tab-btn w-full text-left px-4 py-3.5 rounded-xl text-slate-600 hover:bg-white hover:shadow font-semibold transition");
            document.getElementById(tabId).classList.add('active');
            let activeBtn = document.querySelector(`button[data-target="${tabId}"]`);
            if(activeBtn) activeBtn.className = "tab-btn active w-full text-left px-4 py-3.5 rounded-xl bg-teal-500 text-white font-bold shadow-md transition group";
        }

        // BIOMETRIC AND MODAL LOGIC (OPTIMIZED FOR ZERO LAG)
        let activeStream = null;
        
        // 1. Load the LIGHTWEIGHT models instead of the heavy ones
        window.addEventListener('DOMContentLoaded', async () => {
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL); // Swapped to TinyFace
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            console.log("Lightweight Biometric Weights Loaded Successfully.");
        });

        function openAdmitModal(id) {
            document.getElementById('admitModal_' + id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeAdmitModal(id) {
            document.getElementById('admitModal_' + id).classList.add('hidden');
            document.body.style.overflow = 'auto';
            if(typeof cancelScan === 'function') cancelScan(id);
        }

        async function startBiometricScan(apptId, pid) {
            document.getElementById('admitActions_' + apptId).classList.add('hidden');
            document.getElementById('admitFields_' + apptId).classList.add('hidden');
            
            const bioUI = document.getElementById('biometricUI_' + apptId);
            bioUI.classList.remove('hidden');
            
            const video = document.getElementById('videoFeed_' + apptId);
            const status = document.getElementById('scanStatus_' + apptId);
            const capBtn = document.getElementById('captureBtn_' + apptId);

            try {
                // 2. Force the camera to a very low resolution to prevent browser freezing
                activeStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "user", width: { ideal: 320 }, height: { ideal: 240 } } 
                });
                video.srcObject = activeStream;
                status.innerHTML = "Awaiting face alignment...";
                status.className = "text-emerald-600 font-bold text-sm mt-4 animate-pulse";
                capBtn.disabled = false;
                capBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } catch (err) {
                status.innerHTML = "<i class='fa-solid fa-triangle-exclamation'></i> Camera access denied.";
                status.className = "text-rose-600 font-bold text-sm mt-4";
                console.error(err);
            }
        }

        function cancelScan(apptId) {
            if (activeStream) {
                activeStream.getTracks().forEach(track => track.stop());
                activeStream = null;
            }
            document.getElementById('biometricUI_' + apptId).classList.add('hidden');
            document.getElementById('admitFields_' + apptId).classList.remove('hidden');
            document.getElementById('admitActions_' + apptId).classList.remove('hidden');
            
            const status = document.getElementById('scanStatus_' + apptId);
            status.innerHTML = "Initializing Security Node...";
            status.className = "text-indigo-600 font-bold text-sm mt-4 animate-pulse";
            
            const uiBox = document.getElementById('biometricUI_' + apptId).querySelector('.relative');
            uiBox.classList.remove('border-rose-500', 'shadow-[0_0_20px_rgba(244,63,94,0.8)]', 'border-emerald-500', 'shadow-[0_0_20px_rgba(16,185,129,0.8)]');
            uiBox.classList.add('border-indigo-500/50', 'shadow-[0_0_15px_rgba(99,102,241,0.4)]');
        }

        async function captureAndVerify(apptId, pid) {
            const video = document.getElementById('videoFeed_' + apptId);
            const status = document.getElementById('scanStatus_' + apptId);
            const capBtn = document.getElementById('captureBtn_' + apptId);
            const form = document.getElementById('admitForm_' + apptId);
            const uiBox = document.getElementById('biometricUI_' + apptId).querySelector('.relative');

            status.innerHTML = "<i class='fa-solid fa-spinner animate-spin'></i> Extracting Fast Vector...";
            status.className = "text-indigo-600 font-bold text-sm mt-4";
            capBtn.disabled = true;

            try {
                // 3. Use TinyFaceDetectorOptions for instant, lag-free scanning
                const detection = await faceapi.detectSingleFace(
                    video, 
                    new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                ).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    status.innerHTML = "<i class='fa-solid fa-triangle-exclamation'></i> No face detected. Reposition and try again.";
                    status.className = "text-rose-600 font-bold text-sm mt-4 animate-pulse";
                    capBtn.disabled = false;
                    uiBox.classList.add('border-rose-500');
                    return;
                }

                const localDescriptor = Array.from(detection.descriptor);
                const formData = new FormData();
                formData.append('verify_face', '1');
                formData.append('pid', pid);
                formData.append('face_descriptor', JSON.stringify(localDescriptor));

                const response = await fetch('biometric_verify.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.match) {
                    status.innerHTML = `<i class='fa-solid fa-check-circle'></i> Match Confirmed (${result.confidence}). Authorizing...`;
                    status.className = "text-emerald-600 font-bold text-sm mt-4";
                    uiBox.classList.replace('border-indigo-500/50', 'border-emerald-500');
                    
                    setTimeout(() => {
                        if (activeStream) activeStream.getTracks().forEach(track => track.stop());
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'admit_patient';
                        input.value = 'true';
                        form.appendChild(input);
                        form.submit();
                    }, 1000);
                } else {
                    status.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> MISMATCH: Identity Verification Failed!`;
                    status.className = "text-rose-600 font-bold text-sm mt-4 animate-pulse";
                    capBtn.disabled = false;
                    uiBox.classList.replace('border-indigo-500/50', 'border-rose-500');
                }
            } catch (err) {
                status.textContent = "Server communication error.";
                status.className = "text-rose-600 font-bold text-sm mt-4";
                capBtn.disabled = false;
            }
        }
        
        function openVitalsModal(id) {
            fetch('?ajax_get_vitals=' + id)
                .then(response => response.json())
                .then(data => {
                    let modal = document.getElementById('vitalsModal_' + id);
                    
                    modal.querySelector('input[name="new_spo2"]').value = data.oxygen_level;
                    modal.querySelector('input[name="new_hr"]').value = data.heart_rate;
                    modal.querySelector('input[name="new_liters"]').value = data.oxygen_liters;
                    modal.querySelector('textarea[name="daily_update"]').value = data.daily_update;
                    
                    modal.querySelector('output[id="litersOut_' + id + '"]').innerHTML = data.oxygen_liters;
                    
                    let tag = modal.querySelector('.last-edited-tag');
                    tag.innerHTML = '<i class="fa-solid fa-clock-rotate-left mr-1"></i> Last Edited: ' + data.last_edited;
                    if(data.last_edited === 'System Admin') {
                        tag.classList.replace('text-slate-400', 'text-rose-400');
                    } else {
                        tag.classList.replace('text-rose-400', 'text-slate-400');
                    }

                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error("Error fetching live data:", error);
                    document.getElementById('vitalsModal_' + id).classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
        }
        
        function closeVitalsModal(id) {
            document.getElementById('vitalsModal_' + id).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</body>
</html>