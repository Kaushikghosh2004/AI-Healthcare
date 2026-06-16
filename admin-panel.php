<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('func.php');  
// include('newfunc.php'); // Uncomment if needed

$host = 'localhost:3306';
$dbname = 'myhmsdb';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("<div style='background-color:#fee2e2; color:#991b1b; padding:20px; font-family: sans-serif;'>System Error: Database connection failed.</div>");
}

$pid = $_SESSION['pid'];
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$gender = $_SESSION['gender'];
$email = $_SESSION['email'];
$contact = $_SESSION['contact'];

$notification = '';

// --- NEW: BIOMETRIC REGISTRATION HANDLER ---
if(isset($_POST['register_biometrics'])) {
    // This now receives the 128-digit JSON array instead of a base64 image
    $image_data = $_POST['bio_image_data'];
    try {
        $update_bio = $pdo->prepare("UPDATE patreg SET face_image_path = ?, biometric_verified = 1 WHERE pid = ?");
        if($update_bio->execute([$image_data, $pid])) {
            $notification = "<div class='bg-emerald-500/20 border border-emerald-500/50 backdrop-blur-md text-emerald-300 px-4 py-3 rounded-xl mb-6 flex justify-between shadow-[0_0_15px_rgba(16,185,129,0.2)] animate-fade-in-up'><span><i class='fa-solid fa-fingerprint mr-2'></i> Local Biometric Vector successfully encrypted and registered.</span><button onclick='this.parentElement.style.display=\"none\"'><i class='fa-solid fa-xmark'></i></button></div>";
        }
    } catch(Exception $e) {
        $notification = "<div class='bg-rose-500/20 border border-rose-500/50 backdrop-blur-md text-rose-300 px-4 py-3 rounded-xl mb-6 flex justify-between shadow-[0_0_15px_rgba(244,63,94,0.2)] animate-fade-in-up'><span><i class='fa-solid fa-triangle-exclamation mr-2'></i> System Error: Could not save biometric vector.</span><button onclick='this.parentElement.style.display=\"none\"'><i class='fa-solid fa-xmark'></i></button></div>";
    }
}

// Check biometric status
$bio_stmt = $pdo->prepare("SELECT face_image_path, biometric_verified FROM patreg WHERE pid = ?");
$bio_stmt->execute([$pid]);
$bio_data = $bio_stmt->fetch();
$is_bio_registered = !empty($bio_data['face_image_path']);

// Fetch Patient Vitals
$vitals_stmt = $pdo->prepare("
    SELECT ID as appt_id, bed_number, current_status, oxygen_level, oxygen_liters, heart_rate, admission_date, daily_update, DATEDIFF(CURRENT_TIMESTAMP, admission_date) AS days_occupied 
    FROM appointmenttb 
    WHERE pid = ? 
    ORDER BY appdate DESC, apptime DESC LIMIT 1
");
$vitals_stmt->execute([$pid]);
$my_appt = $vitals_stmt->fetch();

$is_admitted = false;
$current_ui_status = "Outpatient";

if ($my_appt) {
    $current_ui_status = $my_appt['current_status'];
    if ($current_ui_status === 'Admitted') {
        $is_admitted = true;
    }
}

$graph_labels = []; $graph_spo2 = []; $graph_liters = [];
if ($is_admitted && $my_appt) {
    $history_stmt = $pdo->prepare("
        SELECT * FROM (
            SELECT * FROM patient_vitals_log 
            WHERE appt_id = ? 
            ORDER BY recorded_at DESC LIMIT 10
        ) sub 
        ORDER BY recorded_at ASC
    ");
    $history_stmt->execute([$my_appt['appt_id']]);
    $vitals_history = $history_stmt->fetchAll();

    foreach($vitals_history as $log) {
        $graph_labels[] = date('h:i A', strtotime($log['recorded_at']));
        $graph_spo2[] = $log['oxygen_level'];
        $graph_liters[] = $log['oxygen_liters'];
    }
}

$meds_stmt = $pdo->prepare("SELECT doctor, disease, prescription, appdate FROM prestb WHERE pid = ? ORDER BY appdate DESC, apptime DESC LIMIT 1");
$meds_stmt->execute([$pid]);
$latest_meds = $meds_stmt->fetch();

// Booking Handler
if(isset($_POST['app-submit'])) {
    $doctor = $_POST['doctor']; $docFees = $_POST['docFees']; $appdate = $_POST['appdate']; $apptime = $_POST['apptime'];
    date_default_timezone_set('Asia/Kolkata');
    $cur_date = date("Y-m-d"); $cur_time = date("H:i:s");
    $apptime1 = strtotime($apptime); $appdate1 = strtotime($appdate);
  
    if(date("Y-m-d", $appdate1) >= $cur_date) {
        if((date("Y-m-d", $appdate1) == $cur_date && date("H:i:s", $apptime1) > $cur_time) || date("Y-m-d", $appdate1) > $cur_date) {
            $stmt = $pdo->prepare("SELECT apptime FROM appointmenttb WHERE doctor = ? AND appdate = ? AND apptime = ?");
            $stmt->execute([$doctor, $appdate, $apptime]);
            if($stmt->rowCount() == 0) {
                $insert = $pdo->prepare("INSERT INTO appointmenttb(pid, fname, lname, gender, email, contact, doctor, docFees, appdate, apptime, userStatus, doctorStatus, current_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1', '1', 'Scheduled')");
                if($insert->execute([$pid, $fname, $lname, $gender, $email, $contact, $doctor, $docFees, $appdate, $apptime])) {
                    $notification = "<div class='bg-emerald-500/20 border border-emerald-500/50 backdrop-blur-md text-emerald-300 px-4 py-3 rounded-xl mb-6 flex justify-between shadow-[0_0_15px_rgba(16,185,129,0.2)] animate-fade-in-up'><span><i class='fa-solid fa-check-circle mr-2'></i> Appointment booked successfully.</span><button onclick='this.parentElement.style.display=\"none\"'><i class='fa-solid fa-xmark'></i></button></div>";
                    echo "<meta http-equiv='refresh' content='2'>";
                }
            } else {
                $notification = "<div class='bg-amber-500/20 border border-amber-500/50 backdrop-blur-md text-amber-300 px-4 py-3 rounded-xl mb-6 flex justify-between shadow-[0_0_15px_rgba(245,158,11,0.2)] animate-fade-in-up'><span><i class='fa-solid fa-triangle-exclamation mr-2'></i> Doctor unavailable at this time.</span><button onclick='this.parentElement.style.display=\"none\"'><i class='fa-solid fa-xmark'></i></button></div>";
            }
        } else {
            $notification = "<div class='bg-rose-500/20 border border-rose-500/50 backdrop-blur-md text-rose-300 px-4 py-3 rounded-xl mb-6 flex justify-between shadow-[0_0_15px_rgba(244,63,94,0.2)] animate-fade-in-up'><span><i class='fa-solid fa-circle-xmark mr-2'></i> Please select a future time.</span><button onclick='this.parentElement.style.display=\"none\"'><i class='fa-solid fa-xmark'></i></button></div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ritsy Vitals | Zero-G Patient Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdfa', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 900: '#134e4a' },
                        accent: { 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb' },
                        highlight: { 400: '#a78bfa', 500: '#8b5cf6' },
                        space: { 900: '#050a14', 950: '#02040a' }
                    },
                    animation: { 
                        'float': 'float 12s ease-in-out infinite',
                        'float-delayed': 'float 15s ease-in-out 4s infinite',
                        'float-slow': 'float 20s ease-in-out 2s infinite',
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                    },
                    keyframes: { 
                        float: { 
                            '0%, 100%': { transform: 'translateY(0) scale(1)' }, 
                            '50%': { transform: 'translateY(-40px) scale(1.05)' } 
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            background-color: #02040a;
            color: #e2e8f0;
            color-scheme: dark; /* Forces default browser popups/calendars into dark mode */
        }
        
        .glass-card { 
            background: rgba(15, 23, 42, 0.35); 
            backdrop-filter: blur(28px); 
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.05); 
            border-top: 1px solid rgba(255, 255, 255, 0.15); 
            border-left: 1px solid rgba(255, 255, 255, 0.08); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(255,255,255,0.02); 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -15px rgba(20, 184, 166, 0.2), inset 0 0 25px rgba(255,255,255,0.06);
            border-top: 1px solid rgba(255, 255, 255, 0.25);
        }

        .tab-content { display: none; animation: fadeInUp 0.4s ease-out forwards; }
        .tab-content.active { display: block; }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(20, 184, 166, 0.4); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(20, 184, 166, 0.8); }

        .stardust {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* * ZERO-LAG GPU ACCELERATED SCANNER ANIMATION 
         * Replaced "top" with "transform: translateY" for buttery smooth 60FPS rendering
         */
        @keyframes scanGpu {
            0% { transform: translateY(0px); opacity: 0; }
            15% { opacity: 1; }
            85% { opacity: 1; }
            100% { transform: translateY(250px); opacity: 0; } /* 250px traverses the h-64 box */
        }
        .animate-scan-gpu { 
            animation: scanGpu 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite; 
            will-change: transform; 
        }

        /* Fixes the white calendar icon in dark mode date inputs */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
            opacity: 0.6;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
    </style>
</head>

<body class="h-screen w-full flex flex-col overflow-hidden relative selection:bg-brand-500 selection:text-white">
    
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-space-900 via-space-950 to-black">
        <div class="absolute top-[0%] left-[10%] w-[50rem] h-[50rem] bg-brand-500/10 rounded-full blur-[120px] animate-float mix-blend-screen"></div>
        <div class="absolute top-[30%] right-[0%] w-[45rem] h-[45rem] bg-accent-500/10 rounded-full blur-[120px] animate-float-delayed mix-blend-screen"></div>
        <div class="absolute bottom-[-20%] left-[30%] w-[60rem] h-[60rem] bg-highlight-500/10 rounded-full blur-[150px] animate-float-slow mix-blend-screen"></div>
        <div class="absolute inset-0 stardust opacity-30"></div>
    </div>

    <nav class="glass-card h-20 px-8 flex justify-between items-center shrink-0 z-40 rounded-b-none border-t-0 border-x-0">
        <div class="flex items-center gap-4">
            <div class="bg-gradient-to-br from-brand-400 to-accent-600 text-white p-2.5 rounded-2xl shadow-[0_0_20px_rgba(20,184,166,0.4)] border border-brand-400/30">
                <i class="fa-solid fa-hospital-user text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow-md">Ritsy <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-accent-400">Vitals</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden md:block">
                <p class="text-sm font-bold text-white tracking-wide drop-shadow-sm"><?= htmlspecialchars($fname . ' ' . $lname) ?></p>
                <p class="text-[10px] uppercase tracking-widest <?= $is_admitted ? 'text-brand-300 bg-brand-500/20 border border-brand-500/50 shadow-[0_0_10px_rgba(20,184,166,0.3)]' : 'text-slate-300 bg-white/10 border border-white/20 shadow-sm' ?> font-bold px-3 py-1 rounded-full inline-block mt-1 backdrop-blur-md">
                    PID: <?= htmlspecialchars($pid) ?>
                </p>
            </div>
            <a href="logout.php" class="bg-white/5 hover:bg-rose-500/20 border border-white/10 hover:border-rose-500/50 text-slate-300 hover:text-rose-400 px-5 py-2.5 rounded-xl text-sm font-bold transition duration-300 backdrop-blur-md shadow-sm">
                <i class="fa fa-sign-out-alt mr-2"></i> Disconnect
            </a>
        </div>
    </nav>

    <div class="flex w-full h-[calc(100vh-5rem)] overflow-hidden">
        
        <aside class="w-72 shrink-0 glass-card p-6 hidden md:flex flex-col border-y-0 border-l-0 z-30 m-4 rounded-3xl mr-0">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-6 px-2 drop-shadow-sm">Patient Modules</p>
            <nav class="flex flex-col gap-3" id="nav-tabs">
                <button onclick="switchTab('dash')" class="tab-btn w-full text-left px-5 py-4 rounded-2xl bg-brand-500/20 border border-brand-500/50 text-brand-300 font-bold shadow-[0_0_15px_rgba(20,184,166,0.15)] transition-all duration-300 group backdrop-blur-md" data-target="dash">
                    <i class="fa-solid fa-chart-pie w-6 text-center text-brand-400 group-hover:scale-110 transition-transform drop-shadow-[0_0_8px_rgba(45,212,191,0.5)]"></i> <?= $is_admitted ? 'Telemetry Link' : 'Dashboard' ?>
                </button>
                <button onclick="switchTab('book')" class="tab-btn w-full text-left px-5 py-4 rounded-2xl bg-white/5 border border-transparent text-slate-400 hover:bg-white/10 hover:border-white/20 hover:text-white font-semibold transition-all duration-300 group" data-target="book">
                    <i class="fa-solid fa-calendar-plus w-6 text-center group-hover:scale-110 transition-transform"></i> Schedule Consult
                </button>
                <button onclick="switchTab('history')" class="tab-btn w-full text-left px-5 py-4 rounded-2xl bg-white/5 border border-transparent text-slate-400 hover:bg-white/10 hover:border-white/20 hover:text-white font-semibold transition-all duration-300 group" data-target="history">
                    <i class="fa-solid fa-clock-rotate-left w-6 text-center group-hover:scale-110 transition-transform"></i> Timelines
                </button>
                <button onclick="switchTab('prescriptions')" class="tab-btn w-full text-left px-5 py-4 rounded-2xl bg-white/5 border border-transparent text-slate-400 hover:bg-white/10 hover:border-white/20 hover:text-white font-semibold transition-all duration-300 group" data-target="prescriptions">
                    <i class="fa-solid fa-file-prescription w-6 text-center group-hover:scale-110 transition-transform"></i> Archives & Invoices
                </button>
                
                <div class="my-3 border-t border-white/10 mx-2"></div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 px-2 drop-shadow-sm">Security & Tech</p>
                
                <button onclick="switchTab('biosec')" class="tab-btn w-full text-left px-5 py-4 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-transparent border border-emerald-500/20 text-slate-300 hover:text-white hover:border-emerald-500/50 font-semibold transition-all duration-300 flex items-center gap-2 group hover:shadow-[0_0_15px_rgba(16,185,129,0.2)]" data-target="biosec">
                    <i class="fa-solid fa-face-viewfinder w-6 text-center text-emerald-400 group-hover:scale-110 transition-transform"></i> Biometric ID
                    <?php if(!$is_bio_registered): ?>
                        <span class="ml-auto w-2 h-2 rounded-full bg-rose-500 animate-pulse shadow-[0_0_8px_#f43f5e]"></span>
                    <?php else: ?>
                        <span class="ml-auto w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981]"></span>
                    <?php endif; ?>
                </button>

                <a href="ai_triage.php" class="w-full text-left px-5 py-4 rounded-2xl bg-gradient-to-r from-brand-500/10 to-transparent border border-brand-500/20 text-slate-300 hover:text-white hover:border-brand-500/50 font-semibold transition-all duration-300 flex items-center gap-2 group hover:shadow-[0_0_15px_rgba(20,184,166,0.2)]">
                    <i class="fa-solid fa-robot w-6 text-center text-brand-400 group-hover:rotate-12 transition-transform drop-shadow-[0_0_5px_rgba(45,212,191,0.5)]"></i> AI Triage
                    <span class="ml-auto text-[9px] font-black bg-brand-500/30 text-brand-300 px-2 py-1 rounded-full tracking-wider border border-brand-500/50 shadow-[0_0_10px_rgba(20,184,166,0.3)]">BETA</span>
                </a>
                <a href="blockchain_records.php" class="w-full text-left px-5 py-4 rounded-2xl bg-gradient-to-r from-highlight-500/10 to-transparent border border-highlight-500/20 text-slate-300 hover:text-white hover:border-highlight-500/50 font-semibold transition-all duration-300 flex items-center gap-2 group hover:shadow-[0_0_15px_rgba(139,92,246,0.2)]">
                    <i class="fa-solid fa-link w-6 text-center text-highlight-400 group-hover:-rotate-12 transition-transform drop-shadow-[0_0_5px_rgba(167,139,250,0.5)]"></i> Ledger
                    <span class="ml-auto text-[9px] font-black bg-highlight-500/30 text-highlight-300 px-2 py-1 rounded-full tracking-wider border border-highlight-500/50 shadow-[0_0_10px_rgba(139,92,246,0.3)]">SECURE</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 w-full h-full p-6 md:p-8 overflow-y-auto relative z-10">
            <?= $notification ?>

            <div id="dash" class="tab-content active max-w-7xl mx-auto mt-4">
                
                <?php if(!$is_bio_registered): ?>
                    <div class="glass-card border-l-4 border-l-rose-500 p-6 rounded-2xl mb-8 flex justify-between items-center shadow-[0_0_20px_rgba(244,63,94,0.15)] bg-rose-500/5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-rose-500/20 rounded-full flex items-center justify-center text-rose-400 text-xl border border-rose-500/40 shadow-[0_0_10px_rgba(244,63,94,0.3)]">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg drop-shadow-md">Action Required: Setup Biometric ID</h3>
                                <p class="text-slate-300 text-sm mt-0.5">Your facial telemetry is required for surgical authorization and hospital admission.</p>
                            </div>
                        </div>
                        <button onclick="switchTab('biosec')" class="bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl text-sm font-bold transition duration-300 shadow-[0_0_15px_rgba(244,63,94,0.4)]">Secure Profile</button>
                    </div>
                <?php endif; ?>

                <?php if($is_admitted): ?>
                    <div class="flex justify-between items-end mb-10">
                        <div>
                            <h2 class="text-4xl font-extrabold text-white drop-shadow-md">Live Telemetry</h2>
                            <p class="text-slate-400 font-medium mt-2 tracking-wide">Monitoring Unit: <span class="text-brand-300 font-bold bg-brand-500/20 px-3 py-1.5 rounded-lg border border-brand-500/40 shadow-[0_0_10px_rgba(20,184,166,0.2)]"><?= htmlspecialchars($my_appt['bed_number']) ?></span> <span class="mx-2 text-slate-600">•</span> Day <?= max(1, $my_appt['days_occupied']) ?></p>
                        </div>
                        <?php if($my_appt['oxygen_level'] <= 92): ?>
                            <div class="bg-rose-500/20 border border-rose-500/50 text-rose-300 px-5 py-3 rounded-2xl flex items-center shadow-[0_0_25px_rgba(244,63,94,0.5)] animate-pulse backdrop-blur-md">
                                <i class="fa-solid fa-triangle-exclamation text-2xl mr-3"></i> <span class="font-black tracking-widest text-sm">CRITICAL O2 LEVEL</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                        <div class="glass-card p-8 rounded-3xl relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-accent-500/10 rounded-full blur-2xl group-hover:bg-accent-500/20 transition-colors duration-500"></div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center"><i class="fa-solid fa-lungs text-accent-400 mr-2 drop-shadow-[0_0_5px_rgba(96,165,250,0.5)]"></i> Saturation (SpO2)</p>
                            <h3 class="text-6xl font-black <?= ($my_appt['oxygen_level'] <= 92) ? 'text-rose-400 drop-shadow-[0_0_15px_rgba(244,63,94,0.6)] animate-pulse' : 'text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]' ?>"><?= $my_appt['oxygen_level'] ?><span class="text-2xl text-slate-500 font-medium ml-1">%</span></h3>
                        </div>
                        <div class="glass-card p-8 rounded-3xl relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 rounded-full blur-2xl group-hover:bg-brand-500/20 transition-colors duration-500"></div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center"><i class="fa-solid fa-wind text-brand-400 mr-2 drop-shadow-[0_0_5px_rgba(45,212,191,0.5)]"></i> O2 Flow Rate</p>
                            <h3 class="text-6xl font-black text-brand-300 drop-shadow-[0_0_20px_rgba(45,212,191,0.5)]"><?= $my_appt['oxygen_liters'] ?><span class="text-2xl text-slate-500 font-medium ml-2">L/m</span></h3>
                        </div>
                        <div class="glass-card p-8 rounded-3xl relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-colors duration-500"></div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center"><i class="fa-solid fa-heart-pulse text-rose-400 mr-2 drop-shadow-[0_0_5px_rgba(244,63,94,0.5)]"></i> Heart Rate</p>
                            <h3 class="text-6xl font-black text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]"><?= $my_appt['heart_rate'] ?><span class="text-2xl text-slate-500 font-medium ml-2">bpm</span></h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                        <div class="lg:col-span-2 glass-card p-8 rounded-3xl">
                            <h3 class="text-xl font-black text-white mb-6 tracking-wide drop-shadow-sm flex items-center"><i class="fa-solid fa-chart-line text-brand-400 mr-3"></i> Administration Feed</h3>
                            <div class="w-full h-72"><canvas id="vitalsChart"></canvas></div>
                        </div>

                        <div class="lg:col-span-1 flex flex-col gap-8">
                            <div class="glass-card p-8 rounded-3xl flex-1 flex flex-col">
                                <h3 class="text-xl font-black text-white mb-6 tracking-wide flex items-center"><i class="fa-regular fa-clipboard text-highlight-400 mr-3 drop-shadow-[0_0_5px_rgba(167,139,250,0.5)]"></i> Clinical Log</h3>
                                <?php if(!empty($my_appt['daily_update'])): ?>
                                    <div class="bg-black/30 border border-white/10 p-6 rounded-2xl shadow-inner flex-1 overflow-y-auto">
                                        <p class="text-slate-300 text-sm italic leading-relaxed">"<?= nl2br(htmlspecialchars($my_appt['daily_update'])) ?>"</p>
                                    </div>
                                <?php else: ?>
                                    <div class="flex-1 flex items-center justify-center text-slate-500 text-sm font-medium italic border border-white/5 bg-black/20 rounded-2xl">No active logs for today.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="mb-12 mt-4 animate-fade-in-up">
                        <h2 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg tracking-tight mb-2">Welcome, <?= htmlspecialchars($fname) ?> ✨</h2>
                        <p class="text-slate-400 text-lg font-medium">Awaiting your directives.</p>
                    </div>
                    
                    <div class="glass-card border-l-4 border-l-brand-400 p-6 rounded-2xl mb-10 flex items-center justify-between animate-fade-in-up shadow-[0_0_20px_rgba(20,184,166,0.1)]" style="animation-delay: 0.1s;">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 drop-shadow-sm">Entity Status</h3>
                            <p class="text-sm text-slate-400">Current Designation: <span class="font-bold text-brand-300 tracking-wider uppercase ml-1 px-3 py-1.5 bg-brand-500/20 rounded-lg border border-brand-500/30 shadow-[0_0_10px_rgba(20,184,166,0.3)]"><?= $current_ui_status ?></span></p>
                        </div>
                        <div class="w-14 h-14 bg-white/5 rounded-full flex items-center justify-center text-slate-300 border border-white/10 shadow-inner">
                            <i class="fa-solid fa-satellite-dish text-xl"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                        <div onclick="switchTab('book')" class="glass-card p-8 rounded-3xl cursor-pointer group animate-fade-in-up" style="animation-delay: 0.2s;">
                             <div class="w-16 h-16 bg-accent-500/10 border border-accent-500/40 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-[0_0_20px_rgba(59,130,246,0.3)]">
                                 <i class="fa-solid fa-calendar-plus text-2xl text-accent-400 drop-shadow-[0_0_5px_rgba(96,165,250,0.5)]"></i>
                             </div>
                             <h3 class="text-xl font-bold text-white mb-3 tracking-wide drop-shadow-sm">Schedule Link</h3>
                             <p class="text-slate-400 text-sm leading-relaxed">Establish a new consultation node with a specialized operative.</p>
                        </div>
                        
                        <div onclick="switchTab('history')" class="glass-card p-8 rounded-3xl cursor-pointer group animate-fade-in-up" style="animation-delay: 0.3s;">
                             <div class="w-16 h-16 bg-brand-500/10 border border-brand-500/40 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-[0_0_20px_rgba(20,184,166,0.3)]">
                                 <i class="fa-solid fa-clock-rotate-left text-2xl text-brand-400 drop-shadow-[0_0_5px_rgba(45,212,191,0.5)]"></i>
                             </div>
                             <h3 class="text-xl font-bold text-white mb-3 tracking-wide drop-shadow-sm">Timelines</h3>
                             <p class="text-slate-400 text-sm leading-relaxed">Review your upcoming queued events and historical chronologies.</p>
                        </div>
                        
                        <div onclick="switchTab('prescriptions')" class="glass-card p-8 rounded-3xl cursor-pointer group animate-fade-in-up" style="animation-delay: 0.4s;">
                             <div class="w-16 h-16 bg-highlight-500/10 border border-highlight-500/40 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-[0_0_20px_rgba(139,92,246,0.3)]">
                                 <i class="fa-solid fa-file-prescription text-2xl text-highlight-400 drop-shadow-[0_0_5px_rgba(167,139,250,0.5)]"></i>
                             </div>
                             <h3 class="text-xl font-bold text-white mb-3 tracking-wide drop-shadow-sm">Data Archives</h3>
                             <p class="text-slate-400 text-sm leading-relaxed">Extract your prescription payloads and secure invoice packets.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div id="biosec" class="tab-content max-w-4xl mx-auto mt-4">
                <div class="mb-10">
                    <h2 class="text-4xl font-black text-white drop-shadow-md">Biometric Identity Enrollment</h2>
                    <p class="text-slate-400 mt-2">Secure your profile with facial telemetry for advanced procedural authorization.</p>
                </div>
                
                <div class="glass-card p-10 rounded-3xl flex flex-col md:flex-row gap-10 items-center justify-center text-center md:text-left">
                    <?php if($is_bio_registered): ?>
                        <div class="w-32 h-32 rounded-full bg-emerald-500/10 border-2 border-emerald-400 shadow-[0_0_40px_rgba(16,185,129,0.4)] flex items-center justify-center text-5xl text-emerald-400 backdrop-blur-md">
                            <i class="fa-solid fa-shield-check drop-shadow-[0_0_10px_rgba(16,185,129,0.8)]"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-white mb-3 drop-shadow-md">Profile Secured</h3>
                            <p class="text-slate-300 text-sm mb-5 max-w-sm leading-relaxed">Your facial telemetry is securely hashed and stored in the mainframe. You are fully authorized for inpatient admission procedures.</p>
                            <span class="bg-emerald-500/20 text-emerald-300 px-4 py-2 rounded-lg text-xs uppercase tracking-widest font-black border border-emerald-500/40 shadow-[0_0_15px_rgba(16,185,129,0.3)]"><i class="fa-solid fa-lock mr-2"></i>Verification Active</span>
                        </div>
                    <?php else: ?>
                        <div class="w-full max-w-md mx-auto text-center flex flex-col items-center">
                            <div class="w-20 h-20 bg-rose-500/10 rounded-full border-2 border-rose-500/50 text-rose-400 flex items-center justify-center text-3xl mb-6 shadow-[0_0_30px_rgba(244,63,94,0.4)] animate-pulse backdrop-blur-md">
                                <i class="fa-solid fa-user-lock drop-shadow-[0_0_8px_rgba(244,63,94,0.8)]"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-3 drop-shadow-md">Identity Unverified</h3>
                            <p class="text-slate-300 text-sm mb-8 leading-relaxed">Scan your face to create a secure biometric vector hash. This is required before doctors can admit you for any surgical procedures.</p>
                            
                            <button type="button" id="startScanBtn" onclick="startPatientBioScan()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-xl transition duration-300 shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-3 border border-emerald-400/50">
                                <i class="fa-solid fa-camera-viewfinder text-xl"></i> Initialize Scanner
                            </button>

                            <div id="bioScannerUI" class="hidden w-full flex flex-col items-center mt-2">
                                <div class="relative w-full h-64 bg-slate-950 rounded-2xl overflow-hidden border-2 border-emerald-500/50 shadow-[0_0_25px_rgba(16,185,129,0.3)] backdrop-blur-xl">
                                    <video id="patientVideo" class="w-full h-full object-cover transform -scale-x-100" autoplay playsinline></video>
                                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 shadow-[0_0_15px_#34d399,0_0_30px_#34d399] animate-scan-gpu"></div>
                                    <div class="absolute inset-0 border-[2px] border-dashed border-white/20 m-6 rounded-2xl pointer-events-none"></div>
                                </div>
                                
                                <p id="bioScanStatus" class="text-emerald-400 font-bold text-sm mt-5 animate-pulse drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]">Establishing camera link...</p>
                                
                                <form method="post" id="bioForm" class="w-full mt-5">
                                    <input type="hidden" name="bio_image_data" id="bio_image_data">
                                    <input type="hidden" name="register_biometrics" value="1">
                                    <button type="button" onclick="captureAndSaveBio()" id="captureBioBtn" class="w-full bg-brand-600 hover:bg-brand-500 text-white font-bold py-4 rounded-xl transition duration-300 shadow-[0_0_20px_rgba(20,184,166,0.4)] flex items-center justify-center gap-2 opacity-50 cursor-not-allowed border border-brand-400/50" disabled>
                                        <i class="fa-solid fa-fingerprint"></i> Capture & Secure Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="book" class="tab-content max-w-4xl mx-auto mt-4">
                <div class="mb-10"><h2 class="text-4xl font-black text-white drop-shadow-md">Establish Connection</h2><p class="text-slate-400 mt-2">Initialize a booking sequence with a medical operative.</p></div>
                <div class="glass-card p-10 rounded-3xl">
                    <form method="post" action="" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php
                                $specs = $pdo->query("SELECT DISTINCT spec FROM doctb")->fetchAll();
                                $docs = $pdo->query("SELECT username, spec, docFees FROM doctb")->fetchAll();
                            ?>
                            <div>
                                <label class="block text-slate-300 text-xs font-bold uppercase tracking-widest mb-3 drop-shadow-sm">Target Sector</label>
                                <select id="spec" required class="w-full bg-slate-900/80 border border-slate-700/50 rounded-2xl py-4 px-5 text-white outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 shadow-inner appearance-none cursor-pointer backdrop-blur-md">
                                    <option value="" disabled selected class="bg-slate-900">Select Specialization...</option>
                                    <?php foreach($specs as $s): ?><option value="<?= $s['spec'] ?>" class="bg-slate-900"><?= $s['spec'] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs font-bold uppercase tracking-widest mb-3 drop-shadow-sm">Operative Node</label>
                                <select name="doctor" id="doctor" required class="w-full bg-slate-900/80 border border-slate-700/50 rounded-2xl py-4 px-5 text-white outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 shadow-inner appearance-none cursor-pointer backdrop-blur-md">
                                    <option value="" disabled selected class="bg-slate-900">Awaiting Sector Selection...</option>
                                    <?php foreach($docs as $d): ?>
                                        <option value="<?= $d['username'] ?>" data-spec="<?= $d['spec'] ?>" data-fees="<?= $d['docFees'] ?>" style="display:none;" class="bg-slate-900">Dr. <?= $d['username'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs font-bold uppercase tracking-widest mb-3 drop-shadow-sm">Timestamp (Date)</label>
                                <input type="date" name="appdate" required class="w-full bg-slate-900/80 border border-slate-700/50 rounded-2xl py-4 px-5 text-white outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 shadow-inner backdrop-blur-md" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs font-bold uppercase tracking-widest mb-3 drop-shadow-sm">Frequency (Time)</label>
                                <select name="apptime" required class="w-full bg-slate-900/80 border border-slate-700/50 rounded-2xl py-4 px-5 text-white outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 shadow-inner appearance-none cursor-pointer backdrop-blur-md">
                                    <option value="08:00:00" class="bg-slate-900">08:00 AM</option><option value="10:00:00" class="bg-slate-900">10:00 AM</option><option value="12:00:00" class="bg-slate-900">12:00 PM</option><option value="14:00:00" class="bg-slate-900">02:00 PM</option><option value="16:00:00" class="bg-slate-900">04:00 PM</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="docFees" id="docFees_hidden">
                        <button type="submit" name="app-submit" class="w-full bg-white/5 border border-brand-500/40 text-brand-300 hover:text-white hover:bg-brand-600 font-bold py-5 rounded-2xl transition duration-300 shadow-[0_0_20px_rgba(20,184,166,0.2)] hover:shadow-[0_0_30px_rgba(20,184,166,0.4)] tracking-wider uppercase text-sm mt-4">
                            Commit Sequence <i class="fa-solid fa-satellite-dish ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div id="history" class="tab-content max-w-6xl mx-auto mt-4">
                <div class="mb-10"><h2 class="text-4xl font-black text-white drop-shadow-md">Chronology</h2></div>
                <div class="glass-card rounded-3xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 border-b border-white/10 text-slate-300 font-bold uppercase tracking-widest text-[10px]">
                            <tr><th class="p-6">Operative</th><th class="p-6">Timestamp</th><th class="p-6">State</th><th class="p-6 text-right">Execute</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300">
                            <?php 
                            $appts_q = $pdo->prepare("SELECT ID, doctor, appdate, apptime, userStatus, doctorStatus, current_status FROM appointmenttb WHERE pid = ? ORDER BY appdate DESC");
                            $appts_q->execute([$pid]);
                            $results = $appts_q->fetchAll();
                            if(count($results) > 0):
                                foreach($results as $row): ?>
                                    <tr class="hover:bg-white/5 transition duration-300">
                                        <td class="p-6 font-bold text-white tracking-wide">Dr. <?= htmlspecialchars($row['doctor']) ?></td>
                                        <td class="p-6 text-slate-400 font-mono text-xs"><?= htmlspecialchars($row['appdate']) ?> <br><span class="text-slate-500 mt-1 inline-block"><?= date('h:i A', strtotime($row['apptime'])) ?></span></td>
                                        <td class="p-6">
                                            <span class="bg-white/10 border border-white/20 px-3 py-1.5 rounded-lg text-[10px] uppercase tracking-wider font-bold shadow-sm"><?= $row['current_status'] ?></span>
                                        </td>
                                        <td class="p-6 text-right">
                                            <?php if($row['userStatus']==1 && $row['doctorStatus']==1): ?>
                                                <a href="?ID=<?= $row['ID'] ?>&cancel=update" onclick="return confirm('Abort this sequence?')" class="text-rose-400 font-bold hover:bg-rose-500/20 border border-transparent hover:border-rose-500/50 px-4 py-2.5 rounded-xl transition duration-300 text-xs uppercase tracking-wider shadow-sm">Abort</a>
                                            <?php else: ?>
                                                <span class="text-slate-500 text-[10px] uppercase tracking-wider font-bold bg-black/20 px-3 py-1.5 rounded-lg border border-white/5 shadow-inner">Terminated</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; 
                            else: ?>
                                <tr><td colspan="4" class="p-12 text-center text-slate-500 italic font-medium">No chronological data found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="prescriptions" class="tab-content max-w-6xl mx-auto mt-4">
                <div class="mb-10"><h2 class="text-4xl font-black text-white drop-shadow-md">Data Archives</h2></div>
                <div class="glass-card rounded-3xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 border-b border-white/10 text-slate-300 font-bold uppercase tracking-widest text-[10px]">
                            <tr><th class="p-6">Author</th><th class="p-6">Timestamp</th><th class="p-6">Detected Anomaly</th><th class="p-6 text-right">Extract</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300">
                            <?php 
                            $pres_q = $pdo->prepare("SELECT doctor, ID, appdate, disease, prescription FROM prestb WHERE pid= ? ORDER BY appdate DESC");
                            $pres_q->execute([$pid]);
                            $p_results = $pres_q->fetchAll();
                            if(count($p_results) > 0):
                                foreach($p_results as $row): ?>
                                    <tr class="hover:bg-white/5 transition duration-300">
                                        <td class="p-6 font-bold text-white tracking-wide">Dr. <?= htmlspecialchars($row['doctor']) ?></td>
                                        <td class="p-6 text-slate-400 font-mono text-xs"><?= htmlspecialchars($row['appdate']) ?></td>
                                        <td class="p-6 font-medium text-brand-400"><?= htmlspecialchars($row['disease']) ?></td>
                                        <td class="p-6 text-right">
                                            <form method="get" action="generate_bill.php" target="_blank">
                                                <input type="hidden" name="ID" value="<?= $row['ID'] ?>"/>
                                                <button type="submit" class="bg-white/5 hover:bg-highlight-500/20 border border-white/10 hover:border-highlight-500/50 text-highlight-300 px-5 py-2.5 rounded-xl text-[10px] uppercase tracking-widest font-bold transition duration-300 flex items-center justify-end ml-auto shadow-sm hover:shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                                                    Download <i class="fa-solid fa-download ml-2"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr><td colspan="4" class="p-12 text-center text-slate-500 italic font-medium">No archive payloads available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn w-full text-left px-5 py-4 rounded-2xl bg-white/5 border border-transparent text-slate-400 hover:bg-white/10 hover:border-white/20 hover:text-white font-semibold transition-all duration-300 group";
            });
            document.getElementById(tabId).classList.add('active');
            let activeBtn = document.querySelector(`button[data-target="${tabId}"]`);
            
            if(activeBtn) {
                if (tabId === 'biosec') {
                    activeBtn.className = "tab-btn w-full text-left px-5 py-4 rounded-2xl bg-gradient-to-r from-emerald-500/20 to-transparent border border-emerald-500/50 text-emerald-300 font-bold shadow-[0_0_15px_rgba(16,185,129,0.15)] transition-all duration-300 group flex items-center gap-2 backdrop-blur-md";
                } else {
                    activeBtn.className = "tab-btn w-full text-left px-5 py-4 rounded-2xl bg-brand-500/20 border border-brand-500/50 text-brand-300 font-bold shadow-[0_0_15px_rgba(20,184,166,0.15)] transition-all duration-300 group backdrop-blur-md";
                }
            }
        }

        // NEW: Patient Biometric Setup Logic (OPTIMIZED FOR ZERO LAG)
        let bioStream = null;

        window.addEventListener('DOMContentLoaded', async () => {
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL); // Swapped to TinyFace
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            console.log("Lightweight Biometric Weights Loaded Successfully.");
        });

        async function startPatientBioScan() {
            const video = document.getElementById('patientVideo');
            const btn = document.getElementById('captureBioBtn');
            const status = document.getElementById('bioScanStatus');
            
            document.getElementById('bioScannerUI').classList.remove('hidden');
            document.getElementById('startScanBtn').classList.add('hidden');

            try {
                // Force low resolution mapping to prevent lag
                bioStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "user", width: { ideal: 320 }, height: { ideal: 240 } } 
                });
                video.srcObject = bioStream;
                status.innerHTML = "Face alignment required...";
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } catch (err) {
                status.innerHTML = "<i class='fa-solid fa-triangle-exclamation'></i> Camera access denied.";
                status.className = "text-rose-400 font-bold text-sm mt-5";
            }
        }

        async function captureAndSaveBio() {
            const video = document.getElementById('patientVideo');
            const status = document.getElementById('bioScanStatus');
            const btn = document.getElementById('captureBioBtn');
            
            status.innerHTML = "<i class='fa-solid fa-spinner animate-spin'></i> Extracting Fast Vector...";
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                // Use TinyFaceDetectorOptions for instant scanning
                const detection = await faceapi.detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                ).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    status.innerHTML = "<i class='fa-solid fa-triangle-exclamation'></i> No face detected. Reposition and try again.";
                    status.className = "text-rose-400 font-bold text-sm mt-5 animate-pulse drop-shadow-[0_0_5px_rgba(244,63,94,0.5)]";
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    return;
                }

                const localDescriptor = Array.from(detection.descriptor);
                document.getElementById('bio_image_data').value = JSON.stringify(localDescriptor);

                if (bioStream) { bioStream.getTracks().forEach(track => track.stop()); }

                status.innerHTML = "<i class='fa-solid fa-check-circle'></i> Vector Secured. Encrypting to Database...";
                status.className = "text-emerald-400 font-bold text-sm mt-5 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]";
                
                document.getElementById('bioForm').submit();
            } catch (err) {
                status.innerHTML = "<i class='fa-solid fa-triangle-exclamation'></i> Processing error. Try again.";
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Form Logic
        document.getElementById('spec')?.addEventListener('change', function() {
            let selectedSpec = this.value;
            let docSelect = document.getElementById('doctor');
            docSelect.value = "";
            docSelect.querySelectorAll('option:not([disabled])').forEach(opt => {
                opt.style.display = (opt.getAttribute('data-spec') === selectedSpec) ? 'block' : 'none';
            });
        });

        document.getElementById('doctor')?.addEventListener('change', function() {
            document.getElementById('docFees_hidden').value = this.options[this.selectedIndex].getAttribute('data-fees');
        });

        const canvas = document.getElementById('vitalsChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($graph_labels ?? []) ?>,
                    datasets: [
                        { 
                            label: 'SpO2 (%)', 
                            data: <?= json_encode($graph_spo2 ?? []) ?>, 
                            borderColor: '#3b82f6', 
                            backgroundColor: 'rgba(59, 130, 246, 0.15)', 
                            borderWidth: 3, 
                            tension: 0.5, 
                            pointRadius: 0,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#60a5fa',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            fill: true, 
                            yAxisID: 'y' 
                        },
                        { 
                            label: 'O2 Flow (L/m)', 
                            data: <?= json_encode($graph_liters ?? []) ?>, 
                            borderColor: '#2dd4bf', 
                            borderWidth: 3, 
                            borderDash: [6, 6], 
                            tension: 0.5, 
                            pointRadius: 0,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#2dd4bf',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            fill: false, 
                            yAxisID: 'y1' 
                        }
                    ]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: { weight: 'bold' } } },
                        tooltip: { 
                            padding: 16, cornerRadius: 12, backgroundColor: 'rgba(2, 6, 10, 0.8)',
                            titleColor: '#fff', bodyColor: '#cbd5e1', borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1, backdropFilter: 'blur(10px)'
                        }
                    },
                    scales: { 
                        x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 10 } } },
                        y: { type: 'linear', position: 'left', min: 70, max: 100, grid: { color: 'rgba(255,255,255,0.05)', borderDash: [4, 4] } }, 
                        y1: { type: 'linear', position: 'right', min: 0, max: 15, grid: { display: false } } 
                    } 
                }
            });
        }
    </script>
</body>
</html>