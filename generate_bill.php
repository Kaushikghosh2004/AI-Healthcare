<?php
session_start();

$host = 'localhost:3306';
$dbname = 'myhmsdb';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed.");
}

if(isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $stmt = $pdo->prepare("SELECT * FROM prestb WHERE ID = ?");
    $stmt->execute([$id]);
    $bill = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$bill) {
        die("Record not found.");
    }
} else {
    header("Location: patient-panel.php"); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice_<?= $id ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-3xl mx-auto bg-white p-10 shadow-lg rounded-sm border-t-8 border-teal-600">
        <div class="flex justify-between items-start mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Ritsy Vitals</h1>
                <p class="text-gray-500">Official Medical Invoice</p>
            </div>
            <div class="text-right">
                <p class="font-bold">Date: <?= $bill['appdate'] ?></p>
                <p class="text-sm text-gray-500">Invoice ID: 
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-10 border-y py-6">
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Patient Details</p>
                <p class="font-bold text-gray-700"><?= $_SESSION['fname'] ?> <?= $_SESSION['lname'] ?></p>
                <p class="text-sm text-gray-500">PID: <?= $bill['pid'] ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Consultant</p>
                <p class="font-bold text-gray-700">Dr. <?= $bill['doctor'] ?></p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Medical Summary</h3>
            <table class="w-full text-left">
                <tr class="text-gray-500 text-sm">
                    <th class="py-2">Description</th>
                    <th class="py-2 text-right">Details</th>
                </tr>
                <tr>
                    <td class="py-3 font-medium border-b">Diagnosis</td>
                    <td class="py-3 text-right border-b"><?= $bill['disease'] ?></td>
                </tr>
                <tr>
                    <td class="py-3 font-medium border-b">Prescription</td>
                    <td class="py-3 text-right border-b text-sm italic"><?= $bill['prescription'] ?></td>
                </tr>
            </table>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg text-right">
            <p class="text-gray-500 text-sm">Total Amount Payable</p>
            <h2 class="text-4xl font-black text-teal-600">Paid</h2>
        </div>

        <div class="mt-10 text-center text-xs text-gray-400">
            <p>This is a computer-generated document. No signature required.</p>
            <button onclick="window.print()" class="no-print mt-6 bg-gray-800 text-white px-6 py-2 rounded-full font-bold hover:bg-teal-600 transition">Print Invoice</button>
        </div>
    </div>
</body>
</html>