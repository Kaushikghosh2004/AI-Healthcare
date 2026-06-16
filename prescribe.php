<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }


if(!isset($_SESSION['dname'])) {
    header("Location: index.php");
    exit();
}


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

$doctor = $_SESSION['dname'];
$notification = '';


if(isset($_POST['prescribe'])) {
    $appdate = $_POST['appdate'];
    $apptime = $_POST['apptime'];
    $disease = $_POST['disease'];
    $allergy = $_POST['allergy'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $pid = $_POST['pid'];
    $ID = $_POST['ID'];
    $prescription = $_POST['prescription'];
  
    try {
        $stmt = $pdo->prepare("INSERT INTO prestb (doctor, pid, ID, fname, lname, appdate, apptime, disease, allergy, prescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if($stmt->execute([$doctor, $pid, $ID, $fname, $lname, $appdate, $apptime, $disease, $allergy, $prescription])) {
            $notification = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl mb-6 flex justify-between items-center shadow-sm'>
                                <div><i class='fa-solid fa-check-double mr-2 text-emerald-600'></i> <span class='font-bold'>Prescription Saved Successfully!</span> The medical log has been updated.</div>
                                <a href='doctor-panel.php' class='bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-sm'>Return to Ward</a>
                             </div>";
        }
    } catch(PDOException $e) {
        $notification = "<div class='bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex justify-between shadow-sm'><span><i class='fa-solid fa-triangle-exclamation mr-2'></i> Database Error: Could not save prescription.</span></div>";
    }
}


$pid = $_GET['pid'] ?? $_POST['pid'] ?? '';
$ID = $_ID = $_GET['ID'] ?? $_POST['ID'] ?? '';
$fname = $_GET['fname'] ?? $_POST['fname'] ?? '';
$lname = $_GET['lname'] ?? $_POST['lname'] ?? '';
$appdate = $_GET['appdate'] ?? $_POST['appdate'] ?? '';
$apptime = $_GET['apptime'] ?? $_POST['apptime'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KASSAH Vitals Clinical | Prescribe</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { brand: { 50: '#f0fdfa', 500: '#14b8a6', 600: '#0d9488', 900: '#134e4a' } }
                }
            }
        }
    </script>
    <style>
        body { background: linear-gradient(135deg, 
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.8); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>

<body class="min-h-screen w-full flex flex-col overflow-x-hidden">

    <nav class="glass-card h-20 px-8 flex justify-between items-center shrink-0 z-40 border-b border-white/50 sticky top-0">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-brand-500 to-blue-600 text-white p-2.5 rounded-xl shadow-lg shadow-brand-500/30">
                <i class="fa-solid fa-notes-medical text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">KASSAH Vitals <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-blue-600">Clinical</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden md:block">
                <p class="text-sm font-bold text-slate-800">Dr. <?= htmlspecialchars($doctor) ?></p>
                <p class="text-xs text-brand-600 font-semibold bg-brand-50 px-2 py-0.5 rounded-full inline-block mt-1">Authorized Provider</p>
            </div>
            <a href="doctor-panel.php" class="bg-white border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-200 px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm hover:shadow">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Ward
            </a>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-4xl mx-auto p-6 md:p-8">
        
        <?= $notification ?>

        <div class="bg-slate-900 text-white rounded-t-3xl p-6 md:p-8 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/20 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:justify-between md:items-end relative z-10">
                <div>
                    <p class="text-brand-400 text-sm font-bold uppercase tracking-widest mb-1">Prescribing For</p>
                    <h2 class="text-3xl md:text-4xl font-black mb-2"><?= htmlspecialchars($fname . ' ' . $lname) ?></h2>
                    <p class="text-slate-400 font-medium">
                        Patient ID: <span class="text-white font-bold mr-4">#<?= htmlspecialchars($pid) ?></span>
                        Appt ID: <span class="text-white font-bold">#<?= htmlspecialchars($ID) ?></span>
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-left md:text-right bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/20">
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Consultation Time</p>
                    <p class="font-bold"><i class="fa-regular fa-calendar mr-1"></i> <?= htmlspecialchars($appdate) ?></p>
                    <p class="text-brand-300 text-sm"><i class="fa-regular fa-clock mr-1"></i> <?= htmlspecialchars(date('h:i A', strtotime($apptime))) ?></p>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 md:p-8 rounded-b-3xl border-t-0 shadow-xl">
            <form method="post" action="prescribe.php" class="space-y-6">
                
                <input type="hidden" name="fname" value="<?= htmlspecialchars($fname) ?>" />
                <input type="hidden" name="lname" value="<?= htmlspecialchars($lname) ?>" />
                <input type="hidden" name="appdate" value="<?= htmlspecialchars($appdate) ?>" />
                <input type="hidden" name="apptime" value="<?= htmlspecialchars($apptime) ?>" />
                <input type="hidden" name="pid" value="<?= htmlspecialchars($pid) ?>" />
                <input type="hidden" name="ID" value="<?= htmlspecialchars($ID) ?>" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-stethoscope text-brand-500 mr-1"></i> Primary Diagnosis (Disease)</label>
                        <input type="text" name="disease" required placeholder="e.g. Acute Bronchitis"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm font-medium">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-triangle-exclamation text-rose-500 mr-1"></i> Known Allergies</label>
                        <textarea name="allergy" required rows="2" placeholder="List any known drug or environmental allergies (or type 'None')"
                            class="w-full bg-rose-50/30 border border-rose-200 rounded-xl py-3 px-4 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-rose-400 transition shadow-sm"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-capsules text-blue-500 mr-1"></i> Prescription & Treatment Plan</label>
                        <div class="relative">
                            <textarea name="prescription" required rows="8" placeholder="Rx:&#10;1. Medication Name - Dosage - Frequency&#10;2. Specific care instructions..."
                                class="w-full bg-white border border-slate-300 rounded-xl py-4 px-4 text-slate-700 font-mono text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-inner"></textarea>
                            
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-5">
                                <i class="fa-solid fa-staff-snake text-9xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-200 flex justify-end gap-4">
                    <a href="doctor-panel.php" class="px-6 py-3.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition">Cancel</a>
                    <button type="submit" name="prescribe" class="bg-slate-900 hover:bg-brand-600 text-white font-bold py-3.5 px-8 rounded-xl transition duration-300 shadow-lg shadow-slate-900/20 hover:shadow-brand-600/30 flex items-center gap-2">
                        Sign & Finalize Prescription <i class="fa-solid fa-signature"></i>
                    </button>
                </div>
            </form>
        </div>

    </main>

</body>
</html>