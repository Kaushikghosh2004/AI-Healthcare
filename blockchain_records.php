<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['pid'])) { header("Location: index1.php"); exit(); }

// FIXED: Removed the duplicate port declaration
$pdo = new PDO("mysql:host=localhost;port=3306;dbname=myhmsdb", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pid = $_SESSION['pid'];

function generateBlockHash($index, $record_type, $record_id, $data_hash, $previous_hash, $timestamp) {
    return hash('sha256', $index . $record_type . $record_id . $data_hash . $previous_hash . $timestamp);
}

function hashRecordData($record) {
    return hash('sha256', json_encode($record));
}

function addToBlockchain($pdo, $record_type, $record_id, $pid, $doctor, $data) {
    $last = $pdo->query("SELECT block_index, block_hash FROM blockchain_ledger ORDER BY block_index DESC LIMIT 1")->fetch();
    $prev_hash = $last ? $last['block_hash'] : hash('sha256', 'Ritsy_VITALS_GENESIS_BLOCK');
    $next_index = $last ? ($last['block_index'] + 1) : 1;
    $timestamp = date('Y-m-d H:i:s');

    $data_hash = hashRecordData($data);
    $block_hash = generateBlockHash($next_index, $record_type, $record_id, $data_hash, $prev_hash, $timestamp);

    $stmt = $pdo->prepare("INSERT INTO blockchain_ledger (block_index, record_type, record_id, pid, doctor, data_hash, previous_hash, block_hash, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$next_index, $record_type, $record_id, $pid, $doctor, $data_hash, $prev_hash, $block_hash, $timestamp]);
    return $block_hash;
}

function verifyChainIntegrity($pdo, $pid) {
    $blocks = $pdo->prepare("SELECT * FROM blockchain_ledger WHERE pid = ? ORDER BY block_index ASC");
    $blocks->execute([$pid]);
    $chain = $blocks->fetchAll();
    
    $integrity = ['valid' => true, 'total' => count($chain), 'compromised' => []];
    
    for ($i = 0; $i < count($chain); $i++) {
        $block = $chain[$i];
        $expected_hash = generateBlockHash(
            $block['block_index'], $block['record_type'], $block['record_id'],
            $block['data_hash'], $block['previous_hash'], $block['timestamp']
        );
        if ($expected_hash !== $block['block_hash']) {
            $integrity['valid'] = false;
            $integrity['compromised'][] = $block['block_index'];
        }
    }
    return $integrity;
}

$pres_q = $pdo->prepare("SELECT p.*, b.block_hash, b.block_index, b.timestamp as block_time FROM prestb p LEFT JOIN blockchain_ledger b ON b.record_id = p.ID AND b.record_type = 'PRESCRIPTION' WHERE p.pid = ? ORDER BY p.appdate DESC");
$pres_q->execute([$pid]);
$prescriptions = $pres_q->fetchAll();

foreach ($prescriptions as $rx) {
    if (!$rx['block_hash']) {
        addToBlockchain($pdo, 'PRESCRIPTION', $rx['ID'], $pid, $rx['doctor'], [
            'disease' => $rx['disease'],
            'prescription' => $rx['prescription'],
            'date' => $rx['appdate']
        ]);
    }
}

$pres_q->execute([$pid]);
$prescriptions = $pres_q->fetchAll();

$integrity = verifyChainIntegrity($pdo, $pid);

$chain_q = $pdo->prepare("SELECT * FROM blockchain_ledger WHERE pid = ? ORDER BY block_index DESC LIMIT 10");
$chain_q->execute([$pid]);
$chain = $chain_q->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ritsy Vitals | Blockchain Medical Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }, colors: { brand: { 500: '#14b8a6', 600: '#0d9488' }, accent: { 500: '#3b82f6' }, highlight: { 500: '#8b5cf6' } } } }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-300">

    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-[20%] w-96 h-96 bg-highlight-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-[10%] w-80 h-80 bg-brand-500/20 rounded-full blur-3xl"></div>
    </div>

    <nav class="fixed top-0 w-full z-50 bg-slate-950/80 backdrop-blur-lg border-b border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="admin-panel.php" class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-brand-500 to-blue-600 text-white p-2.5 rounded-xl">
                    <i class="fa-solid fa-hospital text-xl"></i>
                </div>
                <h1 class="text-xl font-extrabold text-white">Ritsy <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-accent-500">Vitals</span></h1>
            </a>
            <div class="flex items-center gap-4">
                <span class="bg-highlight-500/20 text-purple-300 border border-highlight-500/30 px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fa-solid fa-link mr-1"></i> Blockchain Records
                </span>
                <a href="admin-panel.php" class="text-slate-400 hover:text-white font-bold transition text-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto pt-28 pb-16 px-6">
        
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-highlight-500 to-accent-500 rounded-3xl shadow-2xl mb-6">
                <i class="fa-solid fa-link text-white text-3xl"></i>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white mb-4">Blockchain Medical Records</h2>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto">Your medical records are secured using SHA-256 cryptographic hash chaining. Any tampering will be instantly detected.</p>
        </div>

        
        <div class="<?= $integrity['valid'] ? 'bg-green-500/10 border-green-500/30' : 'bg-red-500/10 border-red-500/30' ?> border rounded-3xl p-6 mb-8 flex items-center gap-6">
            <div class="w-16 h-16 <?= $integrity['valid'] ? 'bg-green-500/20' : 'bg-red-500/20' ?> rounded-2xl flex items-center justify-center shrink-0">
                <i class="fa-solid <?= $integrity['valid'] ? 'fa-shield-halved text-green-400' : 'fa-shield-exclamation text-red-400' ?> text-3xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold <?= $integrity['valid'] ? 'text-green-300' : 'text-red-300' ?> mb-1">
                    <?= $integrity['valid'] ? '✅ Blockchain Integrity Verified' : '⚠️ INTEGRITY VIOLATION DETECTED' ?>
                </h3>
                <!-- FIXED: Closed the implode function correctly -->
                <p class="text-slate-400 text-sm">
                    <?= $integrity['total'] ?> blocks verified &nbsp;•&nbsp;
                    SHA-256 Hash Chain &nbsp;•&nbsp;
                    <?= $integrity['valid'] ? 'All records authentic and untampered' : 'Compromised blocks: ' . implode(', ', $integrity['compromised']) ?>
                </p>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-2xl font-black <?= $integrity['valid'] ? 'text-green-400' : 'text-red-400' ?>"><?= $integrity['valid'] ? '100%' : 'FAIL' ?></p>
                <p class="text-xs text-slate-500">Integrity Score</p>
            </div>
        </div>

        
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 mb-8">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-file-medical text-brand-400"></i> Verified Medical Prescriptions
            </h3>
            <?php if (empty($prescriptions)): ?>
                <div class="text-center py-12 text-slate-500">
                    <i class="fa-solid fa-file-medical text-5xl mb-4 block"></i>
                    <p>No prescriptions found. Book an appointment to get started.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($prescriptions as $rx): ?>
                        <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6 hover:border-brand-500/30 transition">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <h4 class="text-white font-bold text-lg"><?= htmlspecialchars($rx['disease']) ?></h4>
                                    <p class="text-slate-400 text-sm">Dr. <?= htmlspecialchars($rx['doctor']) ?> &nbsp;•&nbsp; <?= htmlspecialchars($rx['appdate']) ?></p>
                                </div>
                                <?php if ($rx['block_hash']): ?>
                                    <span class="bg-green-500/20 text-green-400 border border-green-500/30 px-3 py-1.5 rounded-full text-xs font-bold shrink-0 flex items-center gap-1.5">
                                        <i class="fa-solid fa-link"></i> BLOCKCHAIN VERIFIED
                                    </span>
                                <?php else: ?>
                                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold shrink-0">PENDING</span>
                                <?php endif; ?>
                            </div>
                            <div class="bg-slate-900/60 rounded-xl p-4 font-mono text-sm text-slate-300 mb-4 border border-slate-700/30">
                                <?= nl2br(htmlspecialchars($rx['prescription'])) ?>
                            </div>
                            <?php if ($rx['block_hash']): ?>
                                <div class="flex flex-wrap gap-3">
                                    <div class="bg-slate-900/60 rounded-xl px-4 py-2 text-xs border border-slate-700/30">
                                        <!-- FIXED: Closed the span tag properly -->
                                        <span class="text-slate-500">Block</span> 
                                        <span class="text-white font-bold ml-1"><?= $rx['block_index'] ?></span>
                                    </div>
                                    <div class="bg-slate-900/60 rounded-xl px-4 py-2 text-xs border border-slate-700/30 flex-1 truncate">
                                        <span class="text-slate-500">Hash: </span>
                                        <span class="text-brand-400 font-mono"><?= substr($rx['block_hash'], 0, 20) ?>...<?= substr($rx['block_hash'], -8) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        
        <?php if (!empty($chain)): ?>
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8">
            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                <i class="fa-solid fa-cubes text-highlight-500"></i> Live Blockchain Ledger
            </h3>
            <p class="text-slate-500 text-sm mb-6">Each block is cryptographically linked to the previous block using SHA-256. Tampering with any record breaks the chain.</p>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-500 font-bold uppercase tracking-wider border-b border-slate-700">
                        <tr>
                            <!-- FIXED: Added closing th tag -->
                            <th class="pb-3 pr-4">Block</th>
                            <th class="pb-3 pr-4">Type</th>
                            <th class="pb-3 pr-4">Block Hash (SHA-256)</th>
                            <th class="pb-3 pr-4">Previous Hash</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($chain as $block): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 pr-4 font-bold text-white">#<?= $block['block_index'] ?></td>
                                <td class="py-3 pr-4">
                                    <span class="bg-highlight-500/20 text-purple-300 px-2 py-0.5 rounded-full"><?= $block['record_type'] ?></span>
                                </td>
                                <td class="py-3 pr-4 font-mono text-brand-400">
                                    <?= substr($block['block_hash'], 0, 16) ?>...
                                </td>
                                <td class="py-3 pr-4 font-mono text-slate-500">
                                    <?= substr($block['previous_hash'], 0, 16) ?>...
                                </td>
                                <td class="py-3">
                                    <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-check text-xs"></i> Valid
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>