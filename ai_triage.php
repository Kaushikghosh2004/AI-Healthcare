<?php




if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['pid'])) { header("Location: index1.php"); exit(); }

$pdo = new PDO("mysql:host=localhost;port=3306;port=3306;dbname=myhmsdb", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pid = $_SESSION['pid'];




$ai_knowledge_base = [
    'chest pain' => [
        'conditions' => ['Angina', 'Myocardial Infarction', 'Costochondritis', 'GERD'],
        'specialist' => 'Cardiologist',
        'urgency' => 'CRITICAL',
        'advice' => 'Seek emergency care immediately. Do not drive yourself.'
    ],
    'heart pounding' => [
        'conditions' => ['Arrhythmia', 'Palpitations', 'Anxiety Disorder', 'Hyperthyroidism'],
        'specialist' => 'Cardiologist',
        'urgency' => 'HIGH',
        'advice' => 'Avoid caffeine and stress. Monitor heart rate regularly.'
    ],
    'shortness of breath' => [
        'conditions' => ['Asthma', 'COPD', 'Pulmonary Embolism', 'Pneumonia'],
        'specialist' => 'Pulmonologist',
        'urgency' => 'HIGH',
        'advice' => 'Avoid exertion. Seek immediate care if severe.'
    ],
    'headache' => [
        'conditions' => ['Migraine', 'Tension Headache', 'Hypertension', 'Cluster Headache'],
        'specialist' => 'Neurologist',
        'urgency' => 'MEDIUM',
        'advice' => 'Rest in a dark room. Stay hydrated. Monitor blood pressure.'
    ],
    'fever' => [
        'conditions' => ['Viral Infection', 'Bacterial Infection', 'COVID-19', 'Malaria'],
        'specialist' => 'General Physician',
        'urgency' => 'MEDIUM',
        'advice' => 'Stay hydrated. Rest. Seek care if fever exceeds 103°F.'
    ],
    'cough' => [
        'conditions' => ['Common Cold', 'Bronchitis', 'Pneumonia', 'Tuberculosis'],
        'specialist' => 'Pulmonologist',
        'urgency' => 'LOW',
        'advice' => 'Use honey and warm water. Avoid cold drinks. See doctor if persistent.'
    ],
    'stomach pain' => [
        'conditions' => ['Gastritis', 'Appendicitis', 'IBS', 'Kidney Stones'],
        'specialist' => 'Gastroenterologist',
        'urgency' => 'MEDIUM',
        'advice' => 'Avoid spicy food. Stay hydrated. Seek urgent care if pain is severe/sharp.'
    ],
    'back pain' => [
        'conditions' => ['Muscle Strain', 'Herniated Disc', 'Sciatica', 'Kidney Infection'],
        'specialist' => 'Orthopedist',
        'urgency' => 'LOW',
        'advice' => 'Apply ice/heat. Avoid heavy lifting. Physiotherapy may help.'
    ],
    'dizziness' => [
        'conditions' => ['Vertigo', 'Low Blood Pressure', 'Anemia', 'Inner Ear Disorder'],
        'specialist' => 'Neurologist',
        'urgency' => 'MEDIUM',
        'advice' => 'Sit down immediately. Avoid driving. Stay hydrated.'
    ],
    'joint pain' => [
        'conditions' => ['Arthritis', 'Gout', 'Lupus', 'Osteoporosis'],
        'specialist' => 'Rheumatologist',
        'urgency' => 'LOW',
        'advice' => 'Apply ice. Rest the joint. Anti-inflammatory medications may help.'
    ],
    'skin rash' => [
        'conditions' => ['Eczema', 'Psoriasis', 'Allergic Reaction', 'Chickenpox'],
        'specialist' => 'Dermatologist',
        'urgency' => 'LOW',
        'advice' => 'Avoid scratching. Use moisturizer. Identify and avoid triggers.'
    ],
    'blurred vision' => [
        'conditions' => ['Diabetic Retinopathy', 'Cataracts', 'Glaucoma', 'Migraine Aura'],
        'specialist' => 'Ophthalmologist',
        'urgency' => 'HIGH',
        'advice' => 'Do not drive. Avoid screens. Seek urgent eye examination.'
    ],
];




$lab_kb = [
    'glucose' => [
        'unit'=>'mg/dL','normal'=>[70,99],
        'low_msg'=>'Low blood sugar (Hypoglycemia). Eat immediately, consult an Endocrinologist.',
        'high_msg'=>'Elevated glucose detected. Risk of Diabetes/Pre-diabetes. Consult Endocrinologist.',
        'specialist'=>'Endocrinologist'
    ],
    'hemoglobin' => [
        'unit'=>'g/dL','normal'=>[12,17],
        'low_msg'=>'Low hemoglobin (Anemia). Increase iron-rich foods. Consult Hematologist.',
        'high_msg'=>'High hemoglobin. May indicate Polycythemia or dehydration. Consult Hematologist.',
        'specialist'=>'Hematologist'
    ],
    'cholesterol' => [
        'unit'=>'mg/dL','normal'=>[0,200],
        'low_msg'=>'Cholesterol is within range.',
        'high_msg'=>'High cholesterol. Risk of cardiovascular disease. Consult Cardiologist.',
        'specialist'=>'Cardiologist'
    ],
    'triglycerides' => [
        'unit'=>'mg/dL','normal'=>[0,150],
        'low_msg'=>'Triglycerides normal.',
        'high_msg'=>'Elevated triglycerides. Risk of heart disease. Reduce fatty food. Consult Cardiologist.',
        'specialist'=>'Cardiologist'
    ],
    'creatinine' => [
        'unit'=>'mg/dL','normal'=>[0.6,1.2],
        'low_msg'=>'Low creatinine. Could indicate muscle wasting. Consult Nephrologist.',
        'high_msg'=>'High creatinine. Possible kidney impairment. Consult Nephrologist urgently.',
        'specialist'=>'Nephrologist'
    ],
    'urea' => [
        'unit'=>'mg/dL','normal'=>[7,20],
        'low_msg'=>'Low urea. Possible liver disease or malnutrition.',
        'high_msg'=>'High urea (BUN). Kidney stress detected. Consult Nephrologist.',
        'specialist'=>'Nephrologist'
    ],
    'wbc' => [
        'unit'=>'×10³/µL','normal'=>[4,11],
        'low_msg'=>'Low WBC (Leukopenia). Immune system may be compromised. Consult Hematologist.',
        'high_msg'=>'High WBC (Leukocytosis). Possible infection or immune disorder. Consult Hematologist.',
        'specialist'=>'Hematologist'
    ],
    'platelets' => [
        'unit'=>'×10³/µL','normal'=>[150,400],
        'low_msg'=>'Low platelets (Thrombocytopenia). Bleeding risk. Consult Hematologist urgently.',
        'high_msg'=>'High platelets. Clotting risk. Consult Hematologist.',
        'specialist'=>'Hematologist'
    ],
    'tsh' => [
        'unit'=>'mIU/L','normal'=>[0.4,4.0],
        'low_msg'=>'Low TSH indicates Hyperthyroidism. Consult Endocrinologist.',
        'high_msg'=>'High TSH indicates Hypothyroidism. Consult Endocrinologist.',
        'specialist'=>'Endocrinologist'
    ],
];





function extractTextFromPDF(string $filepath): string {
    $raw = file_get_contents($filepath);
    if ($raw === false) return '';

    $text = '';

    
    preg_match_all('/stream([\s\S]*?)endstream/m', $raw, $streams);
    $decoded_streams = [];
    foreach ($streams[1] as $stream) {
        $s = ltrim($stream, "\r\n");
        
        $decompressed = @zlib_decode($s);
        $decoded_streams[] = $decompressed !== false ? $decompressed : $s;
    }
    $all_content = implode(' ', $decoded_streams) . ' ' . $raw;

    
    
    preg_match_all('/\(([^)\\\\]*)\)\s*Tj/', $all_content, $tj_matches);
    foreach ($tj_matches[1] as $match) {
        $text .= ' ' . $match;
    }

    
    preg_match_all('/\[([^\]]*)\]\s*TJ/', $all_content, $tj_arr_matches);
    foreach ($tj_arr_matches[1] as $block) {
        preg_match_all('/\(([^)]*)\)/', $block, $parts);
        foreach ($parts[1] as $part) {
            $text .= ' ' . $part;
        }
    }

    
    
    preg_match_all('/([A-Za-z][A-Za-z ]{2,20})[:\s=]+([0-9]+\.?[0-9]*)/', $raw, $raw_matches, PREG_SET_ORDER);
    foreach ($raw_matches as $m) {
        $text .= ' ' . $m[1] . ': ' . $m[2];
    }

    
    $text = preg_replace('/\\\\[0-7]{3}/', ' ', $text);
    $text = preg_replace('/[^\x20-\x7E\n]/', ' ', $text);
    return trim($text);
}

function extractLabValues(string $text, array $kb): array {
    $findings = [];
    $text_lower = strtolower($text);
    foreach ($kb as $marker => $info) {
        
        if (preg_match('/' . preg_quote($marker, '/') . '[\s:=]+([0-9]+\.?[0-9]*)/i', $text_lower, $m)) {
            $val = (float)$m[1];
            $status = 'NORMAL';
            $msg = "$marker is $val {$info['unit']} — within normal range ({$info['normal'][0]}–{$info['normal'][1]} {$info['unit']}).";
            if ($val < $info['normal'][0]) { $status = 'LOW';  $msg = "$marker: $val {$info['unit']} — " . $info['low_msg']; }
            if ($val > $info['normal'][1]) { $status = 'HIGH'; $msg = "$marker: $val {$info['unit']} — " . $info['high_msg']; }
            $findings[] = ['marker'=>strtoupper($marker),'value'=>$val,'unit'=>$info['unit'],'status'=>$status,'msg'=>$msg,'specialist'=>$info['specialist'],'normal'=>$info['normal']];
        }
    }
    return $findings;
}

$report_result = null;
$report_text_used = '';

if (isset($_POST['analyze_report'])) {
    $report_text = '';
    $file_type_used = '';
    
    if (!empty($_FILES['report_file']['tmp_name']) && $_FILES['report_file']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['report_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['txt','csv'])) {
            $report_text = file_get_contents($_FILES['report_file']['tmp_name']);
            $file_type_used = strtoupper($ext);
        } elseif ($ext === 'pdf') {
            
            $extracted = extractTextFromPDF($_FILES['report_file']['tmp_name']);
            if (empty(trim($extracted))) {
                $report_result = ['error' => 'PDF could not be parsed (may be a scanned image PDF). Please paste the report text manually in the box below.'];
            } else {
                $report_text = $extracted;
                $file_type_used = 'PDF';
            }
        } else {
            $report_result = ['error' => 'Unsupported file type. Please upload a .pdf, .txt, or .csv file.'];
        }
    }
    
    if (!empty(trim($_POST['report_text']))) {
        $report_text .= ' ' . $_POST['report_text'];
    }
    if (empty(trim($report_text)) && !isset($report_result)) {
        $report_result = ['error' => 'Please upload a file or paste your report text.'];
    }
    if (!empty(trim($report_text))) {
        $findings = extractLabValues($report_text, $lab_kb);
        $report_text_used = substr($report_text, 0, 500);
        if (empty($findings)) {
            $report_result = ['error' => 'No recognizable lab values found. Make sure the report contains values like: glucose, hemoglobin, cholesterol, creatinine, wbc, platelets, tsh, etc.'];
        } else {
            $abnormal = array_filter($findings, fn($f) => $f['status'] !== 'NORMAL');
            $specialists = array_unique(array_column(array_filter($findings, fn($f) => $f['status'] !== 'NORMAL'), 'specialist'));
            $overall_urgency = count($abnormal) >= 3 ? 'HIGH' : (count($abnormal) >= 1 ? 'MEDIUM' : 'LOW');
            $report_result = ['findings'=>$findings,'abnormal_count'=>count($abnormal),'specialists'=>$specialists,'urgency'=>$overall_urgency];
            
            $stmt = $pdo->prepare("INSERT INTO ai_triage_log (pid, symptoms, ai_analysis, recommended_specialist, urgency_level) VALUES (?,?,?,?,?)");
            $stmt->execute([$pid, 'MEDICAL_REPORT_UPLOAD', json_encode(array_column($findings,'msg')), implode(', ',$specialists), $overall_urgency]);
        }
    }
}




$ai_result = null;
$triage_done = false;

if (isset($_POST['analyze'])) {
    $symptoms_input = strtolower(trim($_POST['symptoms']));
    $age = (int)$_POST['age'];
    $gender_input = $_POST['gender_input'];
    
    
    $matched = [];
    foreach ($ai_knowledge_base as $keyword => $data) {
        if (strpos($symptoms_input, $keyword) !== false) {
            $matched[] = array_merge($data, ['keyword' => $keyword]);
        }
    }

    if (!empty($matched)) {
        
        $urgency_order = ['CRITICAL' => 0, 'HIGH' => 1, 'MEDIUM' => 2, 'LOW' => 3];
        usort($matched, fn($a, $b) => $urgency_order[$a['urgency']] <=> $urgency_order[$b['urgency']]);
        $primary = $matched[0];

        
        $risk_note = '';
        if ($age > 60) $risk_note = "⚠️ Age risk factor detected (60+). Conditions may present more severely.";
        if ($age < 12) $risk_note = "⚠️ Pediatric patient. Specialist pediatric care recommended.";
        if ($gender_input === 'Female' && $primary['specialist'] === 'Cardiologist') {
            $risk_note .= " Women may present with atypical cardiac symptoms.";
        }

        $ai_result = [
            'conditions' => $primary['conditions'],
            'specialist' => $primary['specialist'],
            'urgency' => $primary['urgency'],
            'advice' => $primary['advice'],
            'risk_note' => $risk_note,
            'all_matched' => $matched,
            'confidence' => min(95, 60 + (count($matched) * 10)),
            'symptoms' => $symptoms_input
        ];

        
        $stmt = $pdo->prepare("INSERT INTO ai_triage_log (pid, symptoms, ai_analysis, recommended_specialist, urgency_level) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $pid,
            $symptoms_input,
            json_encode($primary['conditions']),
            $primary['specialist'],
            $primary['urgency']
        ]);
        $triage_done = true;
    } else {
        $ai_result = ['error' => 'No specific pattern matched. Please describe your symptoms in more detail or consult a General Physician.'];
    }
}


$past_logs = $pdo->prepare("SELECT * FROM ai_triage_log WHERE pid = ? ORDER BY created_at DESC LIMIT 5");
$past_logs->execute([$pid]);
$logs = $past_logs->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ritsy Vitals | AI Health Triage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: { 500: '#14b8a6', 600: '#0d9488' },
                        accent: { 500: '#3b82f6', 600: '#2563eb' },
                        highlight: { 500: '#8b5cf6' }
                    },
                    animation: { 'pulse-slow': 'pulse 3s infinite', 'blob': 'blob 7s infinite' },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-300 overflow-x-hidden">

    
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[10%] w-96 h-96 bg-brand-500/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-[30%] right-[5%] w-80 h-80 bg-accent-500/20 rounded-full blur-3xl animate-blob" style="animation-delay:2s"></div>
        <div class="absolute bottom-0 left-[40%] w-96 h-96 bg-highlight-500/20 rounded-full blur-3xl animate-blob" style="animation-delay:4s"></div>
    </div>

    
    <nav class="fixed top-0 w-full z-50 bg-slate-950/80 backdrop-blur-lg border-b border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="admin-panel.php" class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-brand-500 to-blue-600 text-white p-2.5 rounded-xl shadow-lg">
                    <i class="fa-solid fa-hospital text-xl"></i>
                </div>
                <h1 class="text-xl font-extrabold text-white">Ritsy <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-accent-500">Vitals</span></h1>
            </a>
            <div class="flex items-center gap-4">
                <span class="bg-brand-500/20 text-brand-400 border border-brand-500/30 px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fa-solid fa-robot mr-1"></i> AI Health Triage
                </span>
                <a href="admin-panel.php" class="text-slate-400 hover:text-white font-bold transition text-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto pt-28 pb-16 px-6">

        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-brand-500 to-accent-500 rounded-3xl shadow-2xl shadow-brand-500/30 mb-6">
                <i class="fa-solid fa-robot text-white text-3xl"></i>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white mb-4">AI Health Triage</h2>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto">Analyze symptoms <strong class="text-white">or upload your medical report</strong> — our AI engine detects abnormal values, urgency levels, and recommends the right specialist.</p>
            <div class="mt-4 inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 px-4 py-2 rounded-full text-sm font-medium">
                <i class="fa-solid fa-triangle-exclamation"></i>
                AI-assisted only. Always consult a real doctor for final diagnosis.
            </div>
        </div>

        
        <div class="flex gap-3 mb-8 bg-slate-900/60 border border-slate-700/50 rounded-2xl p-2">
            <button onclick="showTab('symptoms-tab','report-tab',this)" id="btn-symptoms"
                class="flex-1 py-3 rounded-xl font-bold text-sm bg-brand-500 text-white transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-stethoscope"></i> Symptom Checker
            </button>
            <button onclick="showTab('report-tab','symptoms-tab',this)" id="btn-report"
                class="flex-1 py-3 rounded-xl font-bold text-sm text-slate-400 hover:text-white transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-medical"></i> Medical Report Analysis
                <span class="bg-accent-500/20 text-accent-400 text-xs px-2 py-0.5 rounded-full">NEW</span>
            </button>
        </div>

        
        <div id="symptoms-tab">
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 mb-8">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-stethoscope text-brand-400"></i> Describe Your Symptoms
            </h3>
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Your Age</label>
                        <input type="number" name="age" placeholder="e.g. 35" min="1" max="120" required
                            class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Biological Gender</label>
                        <select name="gender_input" required class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                            <option value="" disabled selected>Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 text-sm font-bold mb-2">Symptoms Description</label>
                    <textarea name="symptoms" rows="4" required placeholder="e.g. I have a severe headache, chest pain, and I feel dizzy..."
                        class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition"></textarea>
                    <p class="text-xs text-slate-500 mt-2">Keywords: chest pain, headache, fever, cough, shortness of breath, dizziness, back pain, joint pain, skin rash, blurred vision, stomach pain</p>
                </div>
                <button type="submit" name="analyze" class="w-full bg-gradient-to-r from-brand-500 to-accent-500 text-white font-bold py-4 rounded-xl hover:opacity-90 transition shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2 text-lg">
                    <i class="fa-solid fa-brain"></i> Analyze with AI
                </button>
            </form>
        </div>
        </div>

        
        <div id="report-tab" style="display:none">
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 mb-8">
            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                <i class="fa-solid fa-file-waveform text-accent-400"></i> Upload Medical Report
            </h3>
            <p class="text-slate-400 text-sm mb-6">Upload a <strong class="text-white">.pdf, .txt, or .csv</strong> file — or use the <strong class="text-white">OCR Scanner</strong> below for scanned/photo-based reports (.jpg, .png). AI extracts and analyzes all lab values automatically.</p>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div>
                    <label class="block text-slate-400 text-sm font-bold mb-2">Upload Report File <span class="text-brand-400">.pdf</span> / <span class="text-brand-400">.txt</span> / <span class="text-brand-400">.csv</span></label>
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-600 rounded-2xl cursor-pointer bg-slate-800/40 hover:border-accent-500 hover:bg-slate-800/60 transition group">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-500 group-hover:text-accent-400 mb-2 transition"></i>
                        <span class="text-slate-400 text-sm font-medium">Click to browse or drag & drop</span>
                        <span class="text-slate-600 text-xs mt-1">Supported: PDF, TXT, CSV</span>
                        <input type="file" name="report_file" accept=".txt,.csv,.pdf" class="hidden" onchange="document.getElementById('fname-display').textContent = this.files[0]?.name || ''">
                    </label>
                    <p id="fname-display" class="text-brand-400 text-sm mt-2 font-medium"></p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex-1 h-px bg-slate-700"></div>
                    <span class="text-slate-500 text-sm font-bold">OR PASTE TEXT</span>
                    <div class="flex-1 h-px bg-slate-700"></div>
                </div>

                
                <div>
                    <label class="block text-slate-400 text-sm font-bold mb-2">Paste Report Text</label>
                    <textarea name="report_text" rows="7" placeholder="Paste your lab report here...
Example:
Glucose: 210 mg/dL
Hemoglobin: 9.5 g/dL
Cholesterol: 240 mg/dL
Creatinine: 1.8 mg/dL
WBC: 12.5 x10³/µL
Platelets: 95 x10³/µL
TSH: 6.2 mIU/L"
                        class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 transition"></textarea>
                </div>

                <button type="submit" name="analyze_report" class="w-full bg-gradient-to-r from-accent-500 to-highlight-500 text-white font-bold py-4 rounded-xl hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2 text-lg">
                    <i class="fa-solid fa-microscope"></i> Analyze Medical Report
                </button>
            </form>
        </div>

        
        <div class="bg-slate-900/60 backdrop-blur-xl border border-orange-500/30 rounded-3xl p-8 mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-orange-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-camera text-orange-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">OCR Scanner — Scanned / Photo Reports</h3>
                    <p class="text-slate-400 text-xs">Upload a photo or scanned image of your report. AI reads the text automatically using Tesseract OCR (runs in your browser, no upload to any server).</p>
                </div>
            </div>
            <div class="mt-5">
                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-orange-500/40 rounded-2xl cursor-pointer bg-orange-500/5 hover:bg-orange-500/10 hover:border-orange-500/70 transition group">
                    <i class="fa-solid fa-image text-4xl text-orange-400/60 group-hover:text-orange-400 mb-2 transition"></i>
                    <span class="text-slate-400 text-sm font-medium">Upload Scanned Report Image</span>
                    <span class="text-slate-600 text-xs mt-1">Supported: JPG, PNG, WEBP, BMP</span>
                    <input type="file" id="ocr-file-input" accept="image/*" class="hidden">
                </label>
            </div>

            
            <div id="ocr-status" class="hidden mt-4">
                <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-700/50">
                    <div class="flex items-center gap-3 mb-3">
                        <div id="ocr-spinner" class="w-6 h-6 border-2 border-orange-400 border-t-transparent rounded-full animate-spin"></div>
                        <p id="ocr-status-text" class="text-orange-300 font-semibold text-sm">Initializing OCR engine...</p>
                    </div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div id="ocr-progress-bar" class="bg-gradient-to-r from-orange-500 to-amber-400 h-2 rounded-full transition-all duration-300" style="width:0%"></div>
                    </div>
                </div>
            </div>

            
            <div id="ocr-preview" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Uploaded Image</p>
                    <img id="ocr-img-preview" src="" alt="Report preview" class="w-full rounded-xl border border-slate-700 max-h-64 object-contain bg-slate-800">
                </div>
                <div class="flex flex-col">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Extracted Text (OCR)</p>
                    <textarea id="ocr-extracted-text" rows="8" readonly
                        class="flex-1 w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-3 text-green-300 font-mono text-xs focus:outline-none resize-none"
                        placeholder="OCR extracted text will appear here..."></textarea>
                </div>
            </div>

            <button id="ocr-use-btn" onclick="useOcrText()" class="hidden mt-4 w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Use This Text for AI Analysis
            </button>
        </div>

        
        <?php if ($report_result): ?>
            <?php if (isset($report_result['error'])): ?>
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-3xl p-8 mb-8 text-center">
                    <i class="fa-solid fa-circle-question text-amber-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-white mb-2">Could Not Analyze</h3>
                    <p class="text-slate-400"><?= htmlspecialchars($report_result['error']) ?></p>
                </div>
            <?php else: ?>
                <?php
                $urg_map = ['HIGH'=>['text-orange-400','bg-orange-500/10','border-orange-500/40'],'MEDIUM'=>['text-yellow-400','bg-yellow-500/10','border-yellow-500/40'],'LOW'=>['text-green-400','bg-green-500/10','border-green-500/40']];
                [$uc,$ubg,$ubr] = $urg_map[$report_result['urgency']];
                ?>
                
                <div class="<?= $ubg ?> border <?= $ubr ?> rounded-3xl p-6 mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 <?= $ubg ?> rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-flask <?= $uc ?> text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-white font-black text-xl"><?= $report_result['abnormal_count'] ?> Abnormal Value<?= $report_result['abnormal_count'] != 1 ? 's' : '' ?> Detected</p>
                            <p class="text-slate-400 text-sm"><?= count($report_result['findings']) ?> parameters analyzed &nbsp;•&nbsp; Overall Risk: <span class="<?= $uc ?> font-bold"><?= $report_result['urgency'] ?></span></p>
                        </div>
                    </div>
                    <?php if (!empty($report_result['specialists'])): ?>
                    <div class="text-right">
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Recommended Specialists</p>
                        <p class="text-white font-bold"><?= implode(', ', $report_result['specialists']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <?php foreach ($report_result['findings'] as $f):
                        $sc = ['NORMAL'=>['text-green-400','bg-green-500/10','border-green-500/30','fa-check-circle'],
                               'HIGH'  =>['text-red-400',  'bg-red-500/10',  'border-red-500/30',  'fa-arrow-up'],
                               'LOW'   =>['text-blue-400', 'bg-blue-500/10', 'border-blue-500/30', 'fa-arrow-down']];
                        [$fc,$fbg,$fbr,$fic] = $sc[$f['status']];
                        $pct = min(100, max(0, ($f['value'] - $f['normal'][0]) / max(1, $f['normal'][1] - $f['normal'][0]) * 100));
                    ?>
                    <div class="<?= $fbg ?> border <?= $fbr ?> rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid <?= $fic ?> <?= $fc ?>"></i>
                                <span class="text-white font-bold"><?= $f['marker'] ?></span>
                            </div>
                            <span class="<?= $fc ?> font-black text-xl"><?= $f['value'] ?> <span class="text-sm font-normal text-slate-400"><?= $f['unit'] ?></span></span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-1.5 mb-3">
                            <div class="<?= $f['status']==='NORMAL'?'bg-green-500':($f['status']==='HIGH'?'bg-red-500':'bg-blue-500') ?> h-1.5 rounded-full" style="width:<?= $pct ?>%"></div>
                        </div>
                        <p class="text-xs text-slate-300"><?= htmlspecialchars($f['msg']) ?></p>
                        <p class="text-xs text-slate-500 mt-1">Normal Range: <?= $f['normal'][0] ?>–<?= $f['normal'][1] ?> <?= $f['unit'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($report_result['specialists'])): ?>
                <a href="admin-panel.php" class="w-full bg-accent-500 hover:bg-accent-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition mb-8">
                    <i class="fa-solid fa-calendar-plus"></i> Book a Specialist Now
                </a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        </div>

        
        <?php if ($ai_result): ?>
            <?php if (isset($ai_result['error'])): ?>
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-3xl p-8 mb-8 text-center">
                    <i class="fa-solid fa-circle-question text-amber-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-white mb-2">No Pattern Matched</h3>
                    <p class="text-slate-400"><?= $ai_result['error'] ?></p>
                </div>
            <?php else: ?>
                <?php
                $urgency_styles = [
                    'CRITICAL' => ['bg' => 'bg-red-500/10', 'border' => 'border-red-500/40', 'text' => 'text-red-400', 'badge' => 'bg-red-500/20 text-red-300', 'icon' => 'fa-triangle-exclamation'],
                    'HIGH'     => ['bg' => 'bg-orange-500/10', 'border' => 'border-orange-500/40', 'text' => 'text-orange-400', 'badge' => 'bg-orange-500/20 text-orange-300', 'icon' => 'fa-circle-exclamation'],
                    'MEDIUM'   => ['bg' => 'bg-yellow-500/10', 'border' => 'border-yellow-500/40', 'text' => 'text-yellow-400', 'badge' => 'bg-yellow-500/20 text-yellow-300', 'icon' => 'fa-circle-info'],
                    'LOW'      => ['bg' => 'bg-green-500/10', 'border' => 'border-green-500/40', 'text' => 'text-green-400', 'badge' => 'bg-green-500/20 text-green-300', 'icon' => 'fa-circle-check'],
                ];
                $style = $urgency_styles[$ai_result['urgency']];
                ?>
                <div class="<?= $style['bg'] ?> border <?= $style['border'] ?> rounded-3xl p-8 mb-8">
                    
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid <?= $style['icon'] ?> <?= $style['text'] ?> text-3xl"></i>
                            <div>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-wider">Urgency Level</p>
                                <h3 class="text-2xl font-black <?= $style['text'] ?>"><?= $ai_result['urgency'] ?></h3>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-xs text-slate-500">AI Confidence</p>
                                <p class="text-2xl font-black text-white"><?= $ai_result['confidence'] ?>%</p>
                            </div>
                            <div class="w-16 h-16 rounded-full border-4 <?= $style['border'] ?> flex items-center justify-center">
                                <i class="fa-solid fa-robot <?= $style['text'] ?> text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="bg-slate-900/60 rounded-2xl p-6 border border-slate-700/50">
                            <h4 class="text-white font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-microscope text-brand-400"></i> Possible Conditions</h4>
                            <ul class="space-y-2">
                                <?php foreach ($ai_result['conditions'] as $i => $condition): ?>
                                    <li class="flex items-center gap-2 text-slate-300">
                                        <span class="w-6 h-6 bg-brand-500/20 text-brand-400 rounded-full flex items-center justify-center text-xs font-bold shrink-0"><?= $i+1 ?></span>
                                        <?= htmlspecialchars($condition) ?>
                                        <?php if ($i === 0): ?><span class="ml-auto text-xs bg-brand-500/20 text-brand-400 px-2 py-0.5 rounded-full">Most Likely</span><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        
                        <div class="bg-slate-900/60 rounded-2xl p-6 border border-slate-700/50">
                            <h4 class="text-white font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-user-doctor text-accent-500"></i> Recommended Specialist</h4>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 bg-accent-500/20 rounded-2xl flex items-center justify-center">
                                    <i class="fa-solid fa-user-md text-accent-400 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-white"><?= htmlspecialchars($ai_result['specialist']) ?></p>
                                    <p class="text-slate-400 text-sm">AI Recommendation</p>
                                </div>
                            </div>
                            <a href="admin-panel.php" class="w-full bg-accent-500 hover:bg-accent-600 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition">
                                <i class="fa-solid fa-calendar-plus"></i> Book This Specialist
                            </a>
                        </div>
                    </div>

                    
                    <div class="mt-6 bg-slate-900/60 rounded-2xl p-6 border border-slate-700/50">
                        <h4 class="text-white font-bold mb-2 flex items-center gap-2"><i class="fa-solid fa-lightbulb text-yellow-400"></i> AI Health Advice</h4>
                        <p class="text-slate-300"><?= htmlspecialchars($ai_result['advice']) ?></p>
                        <?php if (!empty($ai_result['risk_note'])): ?>
                            <div class="mt-3 bg-amber-500/10 border border-amber-500/30 rounded-xl px-4 py-3 text-amber-300 text-sm font-medium">
                                <?= htmlspecialchars($ai_result['risk_note']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="mt-4 flex items-center gap-2 text-brand-400 text-sm font-medium">
                        <i class="fa-solid fa-link"></i>
                        This triage session has been securely logged to your health record.
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        
        <?php if (!empty($logs)): ?>
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-highlight-500"></i> Previous Triage Sessions
            </h3>
            <div class="space-y-3">
                <?php foreach ($logs as $log): 
                    $urgency_colors = ['CRITICAL'=>'red','HIGH'=>'orange','MEDIUM'=>'yellow','LOW'=>'green'];
                    $c = $urgency_colors[$log['urgency_level']] ?? 'slate';
                ?>
                    <div class="flex items-center justify-between bg-slate-800/60 rounded-xl px-5 py-4 border border-slate-700/30">
                        <div class="flex items-center gap-4">
                            <span class="bg-<?= $c ?>-500/20 text-<?= $c ?>-400 text-xs font-bold px-3 py-1 rounded-full"><?= $log['urgency_level'] ?></span>
                            <div>
                                <p class="text-white font-semibold text-sm truncate max-w-xs"><?= htmlspecialchars(ucfirst($log['symptoms'])) ?></p>
                                <p class="text-slate-500 text-xs">→ <?= htmlspecialchars($log['recommended_specialist']) ?></p>
                            </div>
                        </div>
                        <span class="text-slate-500 text-xs"><?= date('M j, Y', strtotime($log['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script>
    function showTab(show, hide, btn) {
        document.getElementById(show).style.display = 'block';
        document.getElementById(hide).style.display = 'none';
        document.querySelectorAll('[id^=btn-]').forEach(b => {
            b.className = b.className.replace('bg-brand-500 text-white','text-slate-400').replace('bg-accent-500 text-white','text-slate-400');
        });
        if (show === 'report-tab') btn.className = btn.className.replace('text-slate-400','bg-accent-500 text-white');
        else btn.className = btn.className.replace('text-slate-400','bg-brand-500 text-white');
    }

    
    document.getElementById('ocr-file-input').addEventListener('change', async function() {
        const file = this.files[0];
        if (!file) return;

        
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('ocr-img-preview').src = e.target.result;
            document.getElementById('ocr-preview').classList.remove('hidden');
            document.getElementById('ocr-preview').classList.add('grid');
        };
        reader.readAsDataURL(file);

        
        document.getElementById('ocr-status').classList.remove('hidden');
        document.getElementById('ocr-use-btn').classList.add('hidden');
        document.getElementById('ocr-extracted-text').value = '';

        try {
            const result = await Tesseract.recognize(file, 'eng', {
                logger: m => {
                    if (m.status === 'recognizing text') {
                        const pct = Math.round(m.progress * 100);
                        document.getElementById('ocr-progress-bar').style.width = pct + '%';
                        document.getElementById('ocr-status-text').textContent = `Reading text... ${pct}%`;
                    } else {
                        document.getElementById('ocr-status-text').textContent = m.status;
                    }
                }
            });

            const text = result.data.text.trim();
            document.getElementById('ocr-extracted-text').value = text;
            document.getElementById('ocr-spinner').classList.add('hidden');
            document.getElementById('ocr-status-text').textContent = '✅ OCR complete! ' + text.split('\n').length + ' lines extracted.';
            document.getElementById('ocr-status-text').classList.add('text-green-400');
            document.getElementById('ocr-progress-bar').classList.remove('from-orange-500','to-amber-400');
            document.getElementById('ocr-progress-bar').classList.add('bg-green-500');
            document.getElementById('ocr-progress-bar').style.width = '100%';

            if (text.length > 20) {
                document.getElementById('ocr-use-btn').classList.remove('hidden');
            }
        } catch (err) {
            document.getElementById('ocr-status-text').textContent = '❌ OCR failed: ' + err.message;
        }
    });

    function useOcrText() {
        const ocrText = document.getElementById('ocr-extracted-text').value;
        
        const reportTextarea = document.querySelector('textarea[name="report_text"]');
        if (reportTextarea) {
            reportTextarea.value = ocrText;
            reportTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            reportTextarea.focus();
            
            reportTextarea.style.borderColor = '#f97316';
            setTimeout(() => { reportTextarea.style.borderColor = ''; }, 1500);
        }
        
        document.getElementById('ocr-use-btn').innerHTML = '<i class="fa-solid fa-check"></i> Text copied to report box — click Analyze!';
        document.getElementById('ocr-use-btn').classList.remove('bg-orange-500','hover:bg-orange-600');
        document.getElementById('ocr-use-btn').classList.add('bg-green-600');
    }
    </script>
</body>
</html>
