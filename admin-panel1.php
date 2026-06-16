<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN current_status VARCHAR(20) DEFAULT 'Outpatient'");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN bed_number VARCHAR(10) DEFAULT 'UNASSIGNED'");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN oxygen_level INT DEFAULT 98");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN oxygen_liters FLOAT DEFAULT 0.0");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN heart_rate INT DEFAULT 75");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN auto_o2_mode TINYINT(1) DEFAULT 0");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN alert_threshold INT DEFAULT 90");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN admission_date DATETIME NULL");
        $pdo->exec("ALTER TABLE appointmenttb ADD COLUMN daily_update TEXT NULL");
        $pdo->exec("UPDATE appointmenttb SET current_status = 'Outpatient'");
    }

} catch(PDOException $e) {
    die("<div style='background-color:#fee2e2; color:#991b1b; padding:20px; text-align:center; font-family:sans-serif;'>System Failure: Database connection lost. Please ensure 'myhmsdb' exists.</div>");
}

$notification = '';

if(isset($_POST['docsub'])) {
    $stmt = $pdo->prepare("INSERT INTO doctb (username, password, email, spec, docFees) VALUES (?, ?, ?, ?, ?)");
    if($stmt->execute([$_POST['doctor'], $_POST['dpassword'], $_POST['demail'], $_POST['special'], $_POST['docFees']])) {
        $notification = "<div class='alert-success'><i class='fa-solid fa-circle-check mr-2'></i> New Doctor successfully added to the roster.</div>";
    } else {
        $notification = "<div class='alert-error'><i class='fa-solid fa-triangle-exclamation mr-2'></i> Error adding doctor.</div>";
    }
}

if(isset($_POST['docsub1'])) {
    $stmt = $pdo->prepare("DELETE FROM doctb WHERE email = ?");
    if($stmt->execute([$_POST['demail']])) {
        $notification = "<div class='alert-success'><i class='fa-solid fa-circle-check mr-2'></i> Doctor successfully removed from the system.</div>";
    } else {
        $notification = "<div class='alert-error'><i class='fa-solid fa-triangle-exclamation mr-2'></i> Error removing doctor.</div>";
    }
}

try { $active_admissions = $pdo->query("SELECT *, DATEDIFF(CURRENT_TIMESTAMP, admission_date) AS days_occupied FROM appointmenttb WHERE current_status = 'Admitted' AND userStatus = 1 AND doctorStatus = 1")->fetchAll(); } catch(Exception $e) { $active_admissions = []; }
try { $all_patients = $pdo->query("SELECT * FROM patreg ORDER BY pid DESC")->fetchAll(); } catch(Exception $e) { $all_patients = []; }
try { $docs = $pdo->query("SELECT * FROM doctb")->fetchAll(); } catch(Exception $e) { $docs = []; }
try { $appts = $pdo->query("SELECT * FROM appointmenttb ORDER BY appdate DESC, apptime DESC")->fetchAll(); } catch(Exception $e) { $appts = []; }
try { $prescriptions = $pdo->query("SELECT * FROM prestb ORDER BY appdate DESC, apptime DESC")->fetchAll(); } catch(Exception $e) { $prescriptions = []; }
try { $messages = $pdo->query("SELECT * FROM contact ORDER BY id DESC")->fetchAll(); } catch(Exception $e) { $messages = []; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ritsy Vitals Admin | Command Center</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { 
                        brand: { 50: '#f0fdfa', 300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e' },
                        void: { 900: '#0a0f1c', 950: '#05080f' }
                    },
                    animation: {
                        'blob': 'blob 10s infinite',
                        'blob-delayed': 'blob 10s infinite 2s',
                        'blob-slow': 'blob 14s infinite 4s',
                    },
                    keyframes: {
                        blob: {
                            '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            background-color: #05080f; 
            color: #e2e8f0;
        }
        
        /* Advanced Dark Glassmorphism */
        .glass-card { 
            background: rgba(15, 23, 42, 0.4); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08); 
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3), inset 0 0 20px rgba(255,255,255,0.02); 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card-interactive:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px -10px rgba(20, 184, 166, 0.2), inset 0 0 25px rgba(255,255,255,0.05);
            border-top: 1px solid rgba(255, 255, 255, 0.25);
        }

        /* Glass Input Fields */
        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            outline: none;
            border-color: #14b8a6;
            box-shadow: 0 0 15px rgba(20, 184, 166, 0.3);
            background: rgba(15, 23, 42, 0.8);
        }

        .tab-content { display: none; animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .tab-content.active { display: block; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        
        ::-webkit-scrollbar { width: 6px; height: 6px;}
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(20, 184, 166, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(20, 184, 166, 0.6); }
        
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid rgba(16, 185, 129, 0.3); backdrop-filter: blur(10px); }
        .alert-error { background: rgba(244, 63, 94, 0.1); color: #fda4af; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid rgba(244, 63, 94, 0.3); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="h-screen w-full flex flex-col overflow-hidden relative selection:bg-brand-500 selection:text-white">

    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-void-900 to-black">
        <div class="absolute top-[-10%] left-[-10%] w-[40rem] h-[40rem] bg-brand-500/10 rounded-full blur-[100px] mix-blend-screen animate-blob"></div>
        <div class="absolute top-[20%] right-[-5%] w-[35rem] h-[35rem] bg-blue-500/10 rounded-full blur-[100px] mix-blend-screen animate-blob-delayed"></div>
        <div class="absolute bottom-[-10%] left-[30%] w-[45rem] h-[45rem] bg-purple-500/10 rounded-full blur-[120px] mix-blend-screen animate-blob-slow"></div>
    </div>

    <nav class="glass-card h-20 px-8 flex justify-between items-center shrink-0 z-40 rounded-none border-t-0 border-x-0">
        <div class="flex items-center gap-4">
            <div class="bg-gradient-to-br from-slate-700 to-slate-900 text-white p-2.5 rounded-2xl shadow-[0_0_15px_rgba(255,255,255,0.1)] border border-white/10">
                <i class="fa-solid fa-shield-halved text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow-md">Ritsy Vitals <span class="text-brand-400 font-medium">Admin</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden md:block">
                <p class="text-sm font-bold text-white tracking-wide">System Administrator</p>
                <p class="text-[10px] uppercase tracking-widest text-brand-300 bg-brand-500/20 border border-brand-500/30 font-semibold px-3 py-1 rounded-full inline-block mt-1 shadow-[0_0_10px_rgba(20,184,166,0.2)]">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse mr-1"></i> Core Online
                </p>
            </div>
            <a href="logout1.php" class="bg-white/5 border border-white/10 text-slate-300 hover:text-rose-400 hover:border-rose-500/50 hover:bg-rose-500/10 px-5 py-2.5 rounded-xl text-sm font-bold transition duration-300 backdrop-blur-md">
                <i class="fa fa-power-off mr-2"></i> Disconnect
            </a>
        </div>
    </nav>

    <div class="flex w-full h-[calc(100vh-5rem)] overflow-hidden">
        
        <aside class="w-72 shrink-0 glass-card p-6 hidden md:flex flex-col border-y-0 border-l-0 z-30 overflow-y-auto m-4 mr-0 rounded-3xl">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4 px-2">Dashboard</p>
            <nav class="flex flex-col gap-2 mb-8" id="nav-tabs">
                <button onclick="switchTab('telemetry')" class="tab-btn active-tab w-full text-left px-5 py-3.5 rounded-2xl bg-brand-500/20 border border-brand-500/50 text-brand-300 font-bold shadow-[0_0_15px_rgba(20,184,166,0.15)] transition-all group backdrop-blur-md" data-target="telemetry">
                    <i class="fa-solid fa-bed-pulse text-center w-6 group-hover:scale-110 transition-transform"></i> Live Ward Status
                </button>
                <button onclick="switchTab('citizens')" class="tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group" data-target="citizens">
                    <i class="fa-solid fa-users text-center w-6 group-hover:scale-110 transition-transform"></i> Patient Directory
                </button>
                <button onclick="switchTab('roster')" class="tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group" data-target="roster">
                    <i class="fa-solid fa-user-doctor text-center w-6 group-hover:scale-110 transition-transform"></i> Doctor Roster
                </button>
                <button onclick="switchTab('quests')" class="tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group" data-target="quests">
                    <i class="fa-solid fa-calendar-check text-center w-6 group-hover:scale-110 transition-transform"></i> All Appointments
                </button>
            </nav>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4 px-2">Records</p>
            <nav class="flex flex-col gap-2 mb-8">
                <button onclick="switchTab('prescriptions')" class="tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group" data-target="prescriptions">
                    <i class="fa-solid fa-file-prescription text-center w-6 group-hover:scale-110 transition-transform"></i> Medical Logs
                </button>
                <button onclick="switchTab('messages')" class="tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group" data-target="messages">
                    <i class="fa-solid fa-envelope text-center w-6 group-hover:scale-110 transition-transform"></i> Contact Messages
                </button>
            </nav>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4 mt-auto px-2">System</p>
            <nav class="flex flex-col gap-2">
                <button onclick="switchTab('config')" class="tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group" data-target="config">
                    <i class="fa-solid fa-sliders text-center w-6 group-hover:scale-110 transition-transform"></i> Configuration
                </button>
            </nav>
        </aside>

        <main class="flex-1 w-full h-full p-6 md:p-8 overflow-y-auto relative z-10">
            
            <?= $notification ?>

            <div id="telemetry" class="tab-content active max-w-screen-2xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-4xl font-extrabold text-white drop-shadow-md">Live Ward Status</h2>
                        <p class="text-slate-400 font-medium mt-2">Real-time monitoring of admitted patients and procedural authorization.</p>
                    </div>
                    <button onclick="window.location.reload();" class="bg-white/5 border border-brand-500/30 text-brand-300 hover:bg-brand-500/20 hover:text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(20,184,166,0.1)] transition duration-300 backdrop-blur-md">
                        <i class="fa-solid fa-rotate-right mr-2"></i> Sync Data
                    </button>
                </div>
                
                <div class="grid grid-cols-1 xl:grid-cols-3 lg:grid-cols-2 gap-8">
                    <?php 
                    if(count($active_admissions) > 0):
                        foreach($active_admissions as $pat): 
                            $isCritical = ($pat['oxygen_level'] <= $pat['alert_threshold']);
                            $days = max(1, $pat['days_occupied']);
                            
                            $last_log_stmt = $pdo->prepare("SELECT changed_by FROM patient_vitals_log WHERE appt_id = ? ORDER BY recorded_at DESC LIMIT 1");
                            $last_log_stmt->execute([$pat['ID']]);
                            $last_modifier = $last_log_stmt->fetchColumn() ?: "System";
                    ?>
                    <div class="glass-card glass-card-interactive rounded-3xl border <?= $isCritical ? 'border-rose-500/50 shadow-[0_0_20px_rgba(244,63,94,0.2)]' : '' ?> flex flex-col">
                        
                        <div class="p-8 pb-6 relative overflow-hidden">
                            <?php if($isCritical): ?>
                                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/20 rounded-full blur-2xl animate-pulse"></div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-start mb-6 relative z-10">
                                <div>
                                    <span class="bg-blue-500/20 border border-blue-500/40 text-blue-300 font-bold px-3 py-1 rounded-md text-[10px] uppercase tracking-widest mb-3 inline-block shadow-[0_0_10px_rgba(59,130,246,0.2)]"><i class="fa-solid fa-bed mr-1"></i> Bed: <?= htmlspecialchars($pat['bed_number']) ?></span>
                                    <h3 class="text-2xl font-bold text-white"><?= htmlspecialchars($pat['fname'] . ' ' . $pat['lname']) ?></h3>
                                    <p class="text-slate-400 text-xs mt-1">Dr. <?= htmlspecialchars($pat['doctor']) ?> • Appt #<?= $pat['ID'] ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="bg-white/5 border border-white/10 text-slate-300 text-[10px] px-2 py-1 rounded uppercase tracking-widest font-mono">PID: <?= $pat['pid'] ?></span>
                                    <p class="text-brand-400 text-xs font-black mt-3 uppercase tracking-widest">DAY <?= $days ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-2 relative z-10">
                                <div class="bg-white/5 p-5 rounded-2xl border border-white/10 backdrop-blur-sm">
                                    <p class="text-slate-400 text-[10px] uppercase font-bold tracking-widest mb-2">O2 Saturation</p>
                                    <span class="text-3xl font-black <?= $isCritical ? 'text-rose-400 drop-shadow-[0_0_10px_rgba(244,63,94,0.5)] animate-pulse' : 'text-white' ?>"><?= $pat['oxygen_level'] ?><span class="text-sm text-slate-500 ml-1">%</span></span>
                                    <div class="w-full h-1.5 bg-slate-800 rounded-full mt-3 overflow-hidden">
                                        <div class="h-full <?= $isCritical ? 'bg-rose-500 shadow-[0_0_10px_#f43f5e]' : 'bg-brand-500 shadow-[0_0_10px_#14b8a6]' ?>" style="width: <?= $pat['oxygen_level'] ?>%;"></div>
                                    </div>
                                </div>
                                <div class="bg-white/5 p-5 rounded-2xl border border-white/10 backdrop-blur-sm">
                                    <p class="text-slate-400 text-[10px] uppercase font-bold tracking-widest mb-2">Heart Rate</p>
                                    <span class="text-3xl font-black text-white"><?= $pat['heart_rate'] ?> <span class="text-sm text-slate-500 font-normal">bpm</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4 pb-8 px-8 bg-white/5 border-t border-white/10 flex flex-col gap-5 backdrop-blur-md rounded-b-3xl">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><i class="fa-solid fa-notes-medical text-brand-400 mr-2"></i> Clinical Status</span>
                                <span class="text-[10px] font-bold text-slate-500"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Edited: <?= htmlspecialchars($last_modifier) ?></span>
                            </div>

                            <div>
                                <div class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-300 italic min-h-[3.5rem] font-medium shadow-inner">
                                    <?= !empty($pat['daily_update']) ? nl2br(htmlspecialchars($pat['daily_update'])) : 'No recent updates provided.' ?>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-end">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">O2 Flow Rate</label>
                                <span class="text-2xl font-black text-brand-400 drop-shadow-[0_0_10px_rgba(45,212,191,0.3)]"><?= $pat['oxygen_liters'] ?> <span class="text-sm font-bold text-slate-500">L/min</span></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                        <div class="col-span-full glass-card p-16 rounded-3xl text-center text-slate-400 border border-white/10 flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4 border border-white/10 shadow-inner">
                                <i class="fa-solid fa-bed text-3xl text-slate-500"></i>
                            </div>
                            <p class="font-medium text-lg text-white tracking-wide">Ward is Currently Empty</p>
                            <p class="text-sm mt-2">No patients are currently admitted to the inpatient ward.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="citizens" class="tab-content max-w-screen-2xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-4xl font-extrabold text-white drop-shadow-md">Patient Directory</h2>
                        <p class="text-slate-400 font-medium mt-2">Master database of all registered identities.</p>
                    </div>
                    <form action="patientsearch.php" method="post" class="flex gap-3 relative">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-slate-500"></i>
                        <input type="text" name="patient_contact" placeholder="Search by Contact..." required class="glass-input pl-12 pr-4 py-3 rounded-xl text-sm w-72 shadow-inner">
                        <button type="submit" name="patient_search_submit" class="bg-brand-600 hover:bg-brand-500 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-[0_0_15px_rgba(20,184,166,0.2)]">Locate</button>
                    </form>
                </div>
                
                <div class="glass-card rounded-3xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-white/5 border-b border-white/10 text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                                <tr>
                                    <th class="p-6">PID</th>
                                    <th class="p-6">Full Name</th>
                                    <th class="p-6">Gender</th>
                                    <th class="p-6">Contact Data</th>
                                    <th class="p-6 text-right">Medical History</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-slate-300 font-medium">
                                <?php foreach($all_patients as $pat): ?>
                                <tr class="hover:bg-white/5 transition duration-300">
                                    <td class="p-6 font-mono text-slate-500">#<?= htmlspecialchars($pat['pid']) ?></td>
                                    <td class="p-6 font-bold text-white tracking-wide"><?= htmlspecialchars($pat['fname'] . ' ' . $pat['lname']) ?></td>
                                    <td class="p-6"><span class="bg-white/10 text-slate-300 border border-white/10 px-3 py-1.5 rounded-md text-[10px] uppercase tracking-wider font-bold shadow-sm"><?= htmlspecialchars($pat['gender']) ?></span></td>
                                    <td class="p-6 text-slate-400">
                                        <?= htmlspecialchars($pat['contact']) ?><br>
                                        <span class="text-[10px] text-slate-500 font-mono mt-1 block"><?= htmlspecialchars($pat['email']) ?></span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <button onclick="openHistoryModal(<?= $pat['pid'] ?>)" class="bg-white/5 border border-brand-500/30 text-brand-400 hover:bg-brand-500/20 hover:text-white px-4 py-2.5 rounded-xl text-[10px] uppercase tracking-widest font-bold transition shadow-sm hover:shadow-[0_0_15px_rgba(20,184,166,0.2)]">
                                            <i class="fa-solid fa-folder-open mr-2"></i> View Records
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php foreach($all_patients as $pat): ?>
                <div id="modal-<?= $pat['pid'] ?>" class="hidden fixed inset-0 z-[100] flex justify-center items-center backdrop-blur-xl bg-slate-900/60 p-4">
                    <div class="glass-card w-full max-w-4xl max-h-[85vh] rounded-3xl overflow-hidden flex flex-col shadow-2xl border-white/20 relative">
                        <div class="p-8 border-b border-white/10 flex justify-between items-center bg-white/5 relative z-10">
                            <div>
                                <h3 class="font-black text-3xl text-white tracking-wide drop-shadow-md">Patient Dossier</h3>
                                <p class="text-slate-400 text-sm mt-1 uppercase tracking-widest font-bold"><?= htmlspecialchars($pat['fname'] . ' ' . $pat['lname']) ?> <span class="mx-2">•</span> PID <?= $pat['pid'] ?></p>
                            </div>
                            <button onclick="closeHistoryModal(<?= $pat['pid'] ?>)" class="w-12 h-12 bg-white/10 border border-white/20 rounded-full flex items-center justify-center text-slate-300 hover:bg-rose-500/20 hover:text-rose-400 hover:border-rose-500/50 transition duration-300 shadow-sm"><i class="fa-solid fa-xmark text-lg"></i></button>
                        </div>
                        <div class="p-8 overflow-y-auto flex-1 relative z-10">
                            <h4 class="text-brand-400 font-bold uppercase tracking-widest mb-5 text-[10px] flex items-center"><i class="fa-solid fa-clock-rotate-left mr-3 text-lg"></i>Prescription History</h4>
                            <div class="border border-white/10 rounded-2xl overflow-hidden bg-black/20 shadow-inner">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-white/5 border-b border-white/10 text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                                        <tr><th class="p-4">Date</th><th class="p-4">Doctor</th><th class="p-4">Diagnosis</th><th class="p-4">Allergy</th><th class="p-4">Prescription</th></tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5 text-slate-300">
                                        <?php 
                                        $history = [];
                                        try {
                                            $stmt = $pdo->prepare("SELECT * FROM prestb WHERE pid = ? ORDER BY appdate DESC");
                                            $stmt->execute([$pat['pid']]);
                                            $history = $stmt->fetchAll();
                                        } catch(Exception $e) {}
                                        
                                        if($history):
                                            foreach($history as $rec): ?>
                                            <tr class="hover:bg-white/5 transition duration-300">
                                                <td class="p-4 whitespace-nowrap text-slate-400 text-xs font-mono"><?= htmlspecialchars($rec['appdate']) ?></td>
                                                <td class="p-4 font-bold text-white tracking-wide">Dr. <?= htmlspecialchars($rec['doctor']) ?></td>
                                                <td class="p-4 text-rose-400 font-medium"><?= htmlspecialchars($rec['disease']) ?></td>
                                                <td class="p-4 text-xs text-slate-400"><?= htmlspecialchars($rec['allergy']) ?></td>
                                                <td class="p-4 font-mono text-[10px] text-slate-300 bg-white/5 rounded m-2 block p-3 shadow-inner border border-white/5"><?= htmlspecialchars($rec['prescription']) ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="5" class="p-10 text-center text-slate-500 italic">No prescription records found for this patient.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="roster" class="tab-content max-w-screen-2xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-4xl font-extrabold text-white drop-shadow-md">Operative Roster</h2>
                        <p class="text-slate-400 font-medium mt-2">Manage active medical personnel and specialties.</p>
                    </div>
                    <form action="doctorsearch.php" method="post" class="flex gap-3 relative">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-slate-500"></i>
                        <input type="email" name="doctor_contact" placeholder="Search Doctor Email..." required class="glass-input pl-12 pr-4 py-3 rounded-xl text-sm w-72 shadow-inner">
                        <button type="submit" name="doctor_search_submit" class="bg-brand-600 hover:bg-brand-500 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-[0_0_15px_rgba(20,184,166,0.2)]">Locate</button>
                    </form>
                </div>
                
                <div class="glass-card rounded-3xl border-white/10 shadow-sm overflow-hidden">
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <thead class="bg-white/5 border-b border-white/10 text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                            <tr><th class="p-6">Name</th><th class="p-6">Specialization</th><th class="p-6">Email Contact</th><th class="p-6 text-right">Consult Fee</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300">
                            <?php foreach($docs as $doc): ?>
                            <tr class="hover:bg-white/5 transition duration-300">
                                <td class="p-6 font-bold text-white tracking-wide">Dr. <?= htmlspecialchars($doc['username']) ?></td>
                                <td class="p-6"><span class="bg-blue-500/10 border border-blue-500/30 text-blue-300 px-3 py-1.5 rounded-md text-[10px] uppercase tracking-wider font-bold shadow-sm"><?= htmlspecialchars($doc['spec']) ?></span></td>
                                <td class="p-6 text-slate-400 font-mono text-xs"><?= htmlspecialchars($doc['email']) ?></td>
                                <td class="p-6 text-brand-400 font-black text-right font-mono tracking-wider drop-shadow-[0_0_8px_rgba(45,212,191,0.5)]">₹<?= htmlspecialchars($doc['docFees']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="quests" class="tab-content max-w-screen-2xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-4xl font-extrabold text-white drop-shadow-md">Global Queues</h2>
                        <p class="text-slate-400 font-medium mt-2">System-wide view of all scheduled sequences.</p>
                    </div>
                    <form action="appsearch.php" method="post" class="flex gap-3 relative">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-slate-500"></i>
                        <input type="text" name="app_contact" placeholder="Search by Contact..." required class="glass-input pl-12 pr-4 py-3 rounded-xl text-sm w-72 shadow-inner">
                        <button type="submit" name="app_search_submit" class="bg-brand-600 hover:bg-brand-500 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-[0_0_15px_rgba(20,184,166,0.2)]">Locate</button>
                    </form>
                </div>
                
                <div class="glass-card rounded-3xl border-white/10 shadow-sm overflow-hidden overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <thead class="bg-white/5 border-b border-white/10 text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                            <tr>
                                <th class="p-6">Appt ID</th><th class="p-6">Patient Name</th><th class="p-6">Assigned Doctor</th>
                                <th class="p-6">Date & Time</th><th class="p-6">Fee</th><th class="p-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300 font-medium">
                            <?php foreach($appts as $appt): ?>
                            <tr class="hover:bg-white/5 transition duration-300">
                                <td class="p-6 font-mono text-slate-500 text-xs">A#<?= htmlspecialchars($appt['ID']) ?></td>
                                <td class="p-6 font-bold text-white tracking-wide"><?= htmlspecialchars($appt['fname'] . ' ' . $appt['lname']) ?></td>
                                <td class="p-6 text-brand-400 font-bold">Dr. <?= htmlspecialchars($appt['doctor']) ?></td>
                                <td class="p-6 text-slate-400 text-xs font-mono"><?= htmlspecialchars($appt['appdate']) ?><br><span class="text-slate-500 mt-1 block"><?= htmlspecialchars($appt['apptime']) ?></span></td>
                                <td class="p-6 font-mono text-brand-300 tracking-wider">₹<?= htmlspecialchars($appt['docFees']) ?></td>
                                <td class="p-6 text-center">
                                    <?php 
                                        if($appt['userStatus'] == 1 && $appt['doctorStatus'] == 1) {
                                            echo '<span class="bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-md text-[9px] uppercase tracking-widest font-black border border-emerald-500/30 shadow-[0_0_10px_rgba(16,185,129,0.2)]">Active</span>';
                                        } elseif($appt['userStatus'] == 0) {
                                            echo '<span class="bg-rose-500/10 text-rose-400 px-3 py-1.5 rounded-md text-[9px] uppercase tracking-widest font-black border border-rose-500/30 shadow-[0_0_10px_rgba(244,63,94,0.2)]">User Abort</span>';
                                        } else {
                                            echo '<span class="bg-amber-500/10 text-amber-400 px-3 py-1.5 rounded-md text-[9px] uppercase tracking-widest font-black border border-amber-500/30 shadow-[0_0_10px_rgba(245,158,11,0.2)]">Doc Abort</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="prescriptions" class="tab-content max-w-screen-2xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-4xl font-extrabold text-white drop-shadow-md">Data Archives</h2>
                    <p class="text-slate-400 font-medium mt-2">Archive of all encrypted prescriptions across the mainframe.</p>
                </div>
                
                <div class="glass-card rounded-3xl border-white/10 shadow-sm overflow-hidden overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <thead class="bg-white/5 border-b border-white/10 text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                            <tr><th class="p-6">Doctor</th><th class="p-6">Patient</th><th class="p-6">Date/Time</th><th class="p-6">Diagnosis</th><th class="p-6">Allergy</th><th class="p-6">Prescription Payload</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300 font-medium">
                            <?php foreach($prescriptions as $p): ?>
                            <tr class="hover:bg-white/5 transition duration-300">
                                <td class="p-6 font-bold text-white tracking-wide">Dr. <?= htmlspecialchars($p['doctor']) ?></td>
                                <td class="p-6 text-brand-400 font-bold">PID <?= htmlspecialchars($p['pid']) ?></td>
                                <td class="p-6 text-slate-400 text-xs font-mono"><?= htmlspecialchars($p['appdate']) ?><br><span class="text-slate-500 mt-1 block"><?= htmlspecialchars($p['apptime']) ?></span></td>
                                <td class="p-6 text-rose-400"><?= htmlspecialchars($p['disease']) ?></td>
                                <td class="p-6 text-xs text-slate-400"><?= htmlspecialchars($p['allergy']) ?></td>
                                <td class="p-6 font-mono text-[10px] max-w-xs truncate bg-black/30 rounded-lg shadow-inner border border-white/5 p-3 m-3 inline-block text-slate-400" title="<?= htmlspecialchars($p['prescription']) ?>"><?= htmlspecialchars($p['prescription']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="messages" class="tab-content max-w-screen-2xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-4xl font-extrabold text-white drop-shadow-md">External Transmissions</h2>
                        <p class="text-slate-400 font-medium mt-2">Inquiries and signals received from the public network.</p>
                    </div>
                    <form action="messearch.php" method="post" class="flex gap-3 relative">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-slate-500"></i>
                        <input type="text" name="mes_contact" placeholder="Search by Contact..." required class="glass-input pl-12 pr-4 py-3 rounded-xl text-sm w-72 shadow-inner">
                        <button type="submit" name="mes_search_submit" class="bg-brand-600 hover:bg-brand-500 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-[0_0_15px_rgba(20,184,166,0.2)]">Locate</button>
                    </form>
                </div>
                
                <div class="glass-card rounded-3xl border-white/10 shadow-sm overflow-hidden overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 border-b border-white/10 text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                            <tr><th class="p-6 w-1/4">Sender Signature</th><th class="p-6 w-3/4">Transmission Content</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300">
                            <?php foreach($messages as $msg): ?>
                            <tr class="hover:bg-white/5 transition duration-300">
                                <td class="p-6 align-top">
                                    <span class="font-bold text-white block mb-2 tracking-wide"><?= htmlspecialchars($msg['name']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-mono block mb-1 uppercase tracking-widest"><i class="fa-solid fa-envelope mr-2 text-brand-400"></i> <?= htmlspecialchars($msg['email']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-mono block uppercase tracking-widest"><i class="fa-solid fa-phone mr-2 text-brand-400"></i> <?= htmlspecialchars($msg['contact']) ?></span>
                                </td>
                                <td class="p-6 text-slate-300 bg-black/20 italic leading-relaxed text-sm shadow-inner m-4 rounded-xl border border-white/5">
                                    "<?= htmlspecialchars($msg['message']) ?>"
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="config" class="tab-content max-w-screen-2xl mx-auto">
                <div class="mb-10">
                    <h2 class="text-4xl font-extrabold text-white drop-shadow-md">System Parameters</h2>
                    <p class="text-slate-400 font-medium mt-2">Manage medical operatives and administrative overrides.</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    
                    <div class="glass-card p-10 rounded-3xl border border-white/10 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-brand-500/10 rounded-full blur-3xl group-hover:bg-brand-500/20 transition-colors pointer-events-none"></div>
                        <div class="w-16 h-16 bg-brand-500/20 border border-brand-500/40 text-brand-300 rounded-2xl flex items-center justify-center text-2xl mb-8 shadow-[0_0_15px_rgba(20,184,166,0.2)]">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-8 tracking-wide">Initialize Operative</h3>
                        
                        <form method="post" action="" class="space-y-6 relative z-10" onsubmit="return validatePasswords()">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Operative Designation (Name)</label>
                                <input type="text" name="doctor" required class="glass-input w-full rounded-xl px-5 py-4 text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sector Specialization</label>
                                <select name="special" required class="glass-input w-full rounded-xl px-5 py-4 text-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected class="bg-void-900 text-slate-400">Select Medical Field</option>
                                    <option value="General" class="bg-void-900 text-white">General Physician</option>
                                    <option value="Cardiologist" class="bg-void-900 text-white">Cardiologist</option>
                                    <option value="Neurologist" class="bg-void-900 text-white">Neurologist</option>
                                    <option value="Pediatrician" class="bg-void-900 text-white">Pediatrician</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Network ID (Email)</label>
                                <input type="email" name="demail" required class="glass-input w-full rounded-xl px-5 py-4 text-sm font-mono">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Encryption Key</label>
                                    <input type="password" name="dpassword" id="dpass" required class="glass-input w-full rounded-xl px-5 py-4 text-sm font-mono">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Verify Key</label>
                                    <input type="password" name="cdpassword" id="cdpass" onkeyup="checkPass()" required class="glass-input w-full rounded-xl px-5 py-4 text-sm font-mono">
                                </div>
                            </div>
                            <span id="pass-msg" class="text-[10px] font-bold block uppercase tracking-widest"></span>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Consultancy Value (₹)</label>
                                <input type="number" name="docFees" required class="glass-input w-full rounded-xl px-5 py-4 text-sm font-mono">
                            </div>
                            
                            <button type="submit" name="docsub" class="w-full bg-white/5 border border-brand-500/50 text-brand-300 hover:text-white hover:bg-brand-500 font-bold py-4 rounded-xl transition duration-300 shadow-[0_0_15px_rgba(20,184,166,0.2)] hover:shadow-[0_0_25px_rgba(20,184,166,0.4)] tracking-wider uppercase text-sm mt-4">
                                Grant Access <i class="fa-solid fa-satellite-dish ml-2"></i>
                            </button>
                        </form>
                    </div>

                    <div class="glass-card p-10 rounded-3xl border border-white/10 shadow-sm h-max relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl group-hover:bg-rose-500/20 transition-colors pointer-events-none"></div>
                        <div class="w-16 h-16 bg-rose-500/20 border border-rose-500/40 text-rose-400 rounded-2xl flex items-center justify-center text-2xl mb-8 shadow-[0_0_15px_rgba(244,63,94,0.2)]">
                            <i class="fa-solid fa-user-minus"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3 tracking-wide">Terminate Contract</h3>
                        <p class="text-slate-400 text-sm mb-8 leading-relaxed">Warning: Executing this command immediately revokes the operative's access to the Ritsy Vitals mainframe.</p>
                        
                        <form method="post" action="" class="space-y-6 relative z-10" onsubmit="return confirm('CRITICAL WARNING: Are you certain you wish to terminate this operative\'s system access?');">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Operative Network ID</label>
                                <input type="email" name="demail" placeholder="operative@Ritsy.org" required class="glass-input w-full rounded-xl px-5 py-4 text-sm font-mono focus:border-rose-500 focus:shadow-[0_0_15px_rgba(244,63,94,0.3)]">
                            </div>
                            <button type="submit" name="docsub1" class="w-full bg-rose-500/10 border border-rose-500/50 text-rose-400 hover:bg-rose-500 hover:text-white font-bold py-4 rounded-xl transition duration-300 shadow-[0_0_15px_rgba(244,63,94,0.2)] hover:shadow-[0_0_25px_rgba(244,63,94,0.4)] tracking-wider uppercase text-sm">
                                Execute Termination <i class="fa-solid fa-skull-crossbones ml-2"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => {
                el.style.display = 'none';
                el.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn w-full text-left px-5 py-3.5 rounded-2xl bg-transparent border border-transparent text-slate-400 hover:bg-white/5 hover:border-white/10 hover:text-white font-semibold transition-all group";
            });
            
            const targetContent = document.getElementById(tabId);
            if(targetContent) {
                targetContent.style.display = 'block';
                targetContent.classList.add('active');
            }
            
            const activeBtn = document.querySelector(`button[data-target="${tabId}"]`);
            if(activeBtn) {
                activeBtn.className = "tab-btn active-tab w-full text-left px-5 py-3.5 rounded-2xl bg-brand-500/20 border border-brand-500/50 text-brand-300 font-bold shadow-[0_0_15px_rgba(20,184,166,0.15)] transition-all group backdrop-blur-md";
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            switchTab('telemetry');
        });

        function checkPass() {
            const pass = document.getElementById('dpass').value;
            const confirm = document.getElementById('cdpass').value;
            const msg = document.getElementById('pass-msg');
            
            if(confirm === "") { msg.innerHTML = ""; return; }
            
            if(pass === confirm) {
                msg.style.color = "#34d399"; 
                msg.style.textShadow = "0 0 10px rgba(52, 211, 153, 0.5)";
                msg.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Encryption Keys Match';
            } else {
                msg.style.color = "#fb7185"; 
                msg.style.textShadow = "0 0 10px rgba(251, 113, 133, 0.5)";
                msg.innerHTML = '<i class="fa-solid fa-xmark mr-2"></i> Key Mismatch';
            }
        }
        
        function validatePasswords() {
            const pass = document.getElementById('dpass').value;
            const confirm = document.getElementById('cdpass').value;
            if (pass !== confirm) {
                alert("Authorization Denied: Encryption keys do not match.");
                return false;
            }
            return true;
        }

        function openHistoryModal(pid) {
            document.getElementById('modal-' + pid).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeHistoryModal(pid) {
            document.getElementById('modal-' + pid).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</body>
</html>