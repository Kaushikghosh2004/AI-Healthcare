<?php




if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$pdo = new PDO("mysql:host=localhost;port=3306;port=3306;dbname=myhmsdb", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $pdo->prepare("INSERT INTO security_audit_log (event_type, user_type, user_id, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute(['PAGE_ACCESS', 'ADMIN', $_SESSION['admin'], 'Accessed Security Audit Center', $_SERVER['REMOTE_ADDR'] ?? 'N/A', $_SERVER['HTTP_USER_AGENT'] ?? 'N/A']);


$total_events = $pdo->query("SELECT COUNT(*) FROM security_audit_log")->fetchColumn();
$failed_logins = $pdo->query("SELECT COUNT(*) FROM security_audit_log WHERE event_type = 'LOGIN_FAILED'")->fetchColumn();
$successful_logins = $pdo->query("SELECT COUNT(*) FROM security_audit_log WHERE event_type = 'LOGIN_SUCCESS'")->fetchColumn();
$total_blocks = $pdo->query("SELECT COUNT(*) FROM blockchain_ledger")->fetchColumn();
$ai_triages = $pdo->query("SELECT COUNT(*) FROM ai_triage_log")->fetchColumn();


$logs = $pdo->query("SELECT * FROM security_audit_log ORDER BY created_at DESC LIMIT 50")->fetchAll();


$top_ips = $pdo->query("SELECT ip_address, COUNT(*) as count FROM security_audit_log GROUP BY ip_address ORDER BY count DESC LIMIT 5")->fetchAll();


$critical = $pdo->query("SELECT * FROM security_audit_log WHERE event_type = 'LOGIN_FAILED' ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ritsy Vitals | Security Audit Center</title>
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
        <div class="absolute top-0 right-[20%] w-96 h-96 bg-red-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-[10%] w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>

    <nav class="fixed top-0 w-full z-50 bg-slate-950/80 backdrop-blur-lg border-b border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="admin-panel1.php" class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-brand-500 to-blue-600 text-white p-2.5 rounded-xl">
                    <i class="fa-solid fa-hospital text-xl"></i>
                </div>
                <h1 class="text-xl font-extrabold text-white">Ritsy <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-accent-500">Vitals</span></h1>
            </a>
            <div class="flex items-center gap-4">
                <span class="bg-red-500/20 text-red-300 border border-red-500/30 px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                    <i class="fa-solid fa-shield-halved mr-1"></i> SECURITY CENTER
                </span>
                <a href="admin-panel1.php" class="text-slate-400 hover:text-white font-bold transition text-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Admin Panel
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto pt-28 pb-16 px-6">

        
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-red-500/20 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved text-red-400 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-white">Cybersecurity Audit Center</h2>
                    <p class="text-slate-400">Real-time system monitoring & security event log</p>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-slate-900/60 border border-slate-700/50 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-list-check text-brand-400 text-2xl mb-2 block"></i>
                <h3 class="text-3xl font-black text-white"><?= $total_events ?></h3>
                <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-wider">Total Events</p>
            </div>
            <div class="bg-slate-900/60 border border-slate-700/50 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-circle-check text-green-400 text-2xl mb-2 block"></i>
                <h3 class="text-3xl font-black text-white"><?= $successful_logins ?></h3>
                <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-wider">Successful Logins</p>
            </div>
            <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-circle-xmark text-red-400 text-2xl mb-2 block"></i>
                <h3 class="text-3xl font-black text-red-300"><?= $failed_logins ?></h3>
                <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-wider">Failed Attempts</p>
            </div>
            <div class="bg-highlight-500/10 border border-highlight-500/30 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-link text-purple-400 text-2xl mb-2 block"></i>
                <h3 class="text-3xl font-black text-white"><?= $total_blocks ?></h3>
                <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-wider">Blockchain Blocks</p>
            </div>
            <div class="bg-accent-500/10 border border-accent-500/30 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-robot text-accent-400 text-2xl mb-2 block"></i>
                <h3 class="text-3xl font-black text-white"><?= $ai_triages ?></h3>
                <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-wider">AI Triages Run</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <div class="bg-red-500/5 border border-red-500/20 rounded-3xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-400"></i> Recent Failed Logins
                </h3>
                <?php if (empty($critical)): ?>
                    <div class="text-center py-6 text-slate-500 text-sm">
                        <i class="fa-solid fa-shield-check text-green-400 text-3xl mb-2 block"></i>
                        No failed login attempts detected.
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($critical as $alert): ?>
                            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3">
                                <p class="text-red-300 text-sm font-bold"><?= htmlspecialchars($alert['user_type']) ?> — <?= htmlspecialchars($alert['user_id'] ?? 'Unknown') ?></p>
                                <p class="text-slate-500 text-xs mt-1">IP: <?= htmlspecialchars($alert['ip_address']) ?></p>
                                <p class="text-slate-600 text-xs"><?= date('M j H:i', strtotime($alert['created_at'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="bg-slate-900/60 border border-slate-700/50 rounded-3xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-globe text-accent-400"></i> Top Access IPs
                </h3>
                <div class="space-y-3">
                    <?php foreach ($top_ips as $ip): ?>
                        <div class="flex items-center justify-between bg-slate-800/60 rounded-xl px-4 py-3">
                            <span class="font-mono text-sm text-white"><?= htmlspecialchars($ip['ip_address']) ?></span>
                            <span class="bg-accent-500/20 text-accent-400 px-3 py-1 rounded-full text-xs font-bold"><?= $ip['count'] ?> requests</span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($top_ips)): ?>
                        <p class="text-slate-500 text-sm text-center py-4">No data yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-slate-900/60 border border-slate-700/50 rounded-3xl p-6 flex flex-col">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-gauge-high text-brand-400"></i> Security Score
                </h3>
                <?php
                $score = 100;
                if ($failed_logins > 5) $score -= 20;
                if ($failed_logins > 20) $score -= 20;
                $score_color = $score >= 80 ? 'text-green-400' : ($score >= 60 ? 'text-yellow-400' : 'text-red-400');
                $score_label = $score >= 80 ? 'STRONG' : ($score >= 60 ? 'MODERATE' : 'AT RISK');
                ?>
                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <div class="text-7xl font-black <?= $score_color ?> mb-2"><?= $score ?></div>
                    <div class="text-lg font-bold <?= $score_color ?>"><?= $score_label ?></div>
                    <div class="mt-4 w-full bg-slate-800 rounded-full h-3">
                        <div class="<?= $score >= 80 ? 'bg-green-500' : ($score >= 60 ? 'bg-yellow-500' : 'bg-red-500') ?> h-3 rounded-full transition-all" style="width: <?= $score ?>%"></div>
                    </div>
                </div>
                <div class="mt-6 space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-green-400"><i class="fa-solid fa-check w-4"></i> Blockchain data integrity</div>
                    <div class="flex items-center gap-2 text-green-400"><i class="fa-solid fa-check w-4"></i> Session-based authentication</div>
                    <div class="flex items-center gap-2 text-green-400"><i class="fa-solid fa-check w-4"></i> Audit logging active</div>
                    <div class="flex items-center gap-2 <?= $failed_logins > 5 ? 'text-red-400' : 'text-green-400' ?>">
                        <i class="fa-solid <?= $failed_logins > 5 ? 'fa-xmark' : 'fa-check' ?> w-4"></i>
                        Login brute force status
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-slate-900/60 border border-slate-700/50 rounded-3xl p-6 overflow-hidden">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-scroll text-brand-400"></i> Full Audit Event Log
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-slate-500 font-bold uppercase tracking-wider text-xs border-b border-slate-800">
                        <tr>
                            <th class="pb-3 pr-4">Timestamp</th>
                            <th class="pb-3 pr-4">Event</th>
                            <th class="pb-3 pr-4">User</th>
                            <th class="pb-3 pr-4">IP Address</th>
                            <th class="pb-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php foreach ($logs as $log):
                            $event_colors = [
                                'LOGIN_SUCCESS' => 'text-green-400 bg-green-500/10',
                                'LOGIN_FAILED' => 'text-red-400 bg-red-500/10',
                                'PAGE_ACCESS' => 'text-blue-400 bg-blue-500/10',
                                'LOGOUT' => 'text-slate-400 bg-slate-700/30',
                            ];
                            $ec = $event_colors[$log['event_type']] ?? 'text-slate-400 bg-slate-700/20';
                        ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 pr-4 text-slate-500 text-xs whitespace-nowrap"><?= date('M j H:i:s', strtotime($log['created_at'])) ?></td>
                                <td class="py-3 pr-4">
                                    <span class="<?= $ec ?> text-xs font-bold px-2 py-1 rounded-full whitespace-nowrap"><?= htmlspecialchars($log['event_type']) ?></span>
                                </td>
                                <td class="py-3 pr-4 text-white font-medium text-xs"><?= htmlspecialchars($log['user_type']) ?>/<?= htmlspecialchars($log['user_id'] ?? '-') ?></td>
                                <td class="py-3 pr-4 font-mono text-xs text-slate-400"><?= htmlspecialchars($log['ip_address']) ?></td>
                                <td class="py-3 text-slate-400 text-xs truncate max-w-xs"><?= htmlspecialchars($log['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="5" class="py-8 text-center text-slate-500">No audit events recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
