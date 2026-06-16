<?php

session_start();


$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ritsy Vitals | Secure Logout</title>
    
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
</head>
<body class="bg-slate-50 h-screen w-full flex items-center justify-center p-4">

    <div class="bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-slate-100 max-w-md w-full text-center relative overflow-hidden">
        
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full h-32 bg-gradient-to-b from-brand-50 to-transparent -z-10"></div>

        <div class="mx-auto w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-6 shadow-sm">
            <i class="fa-solid fa-shield-check text-4xl"></i>
        </div>

        <h2 class="text-2xl font-extrabold text-slate-800 mb-2">Securely Logged Out</h2>
        <p class="text-slate-500 font-medium mb-8">Thank you for using the Ritsy Vitals system. Your session has been safely closed.</p>

        <a href="index1.php" class="block w-full bg-slate-800 hover:bg-brand-600 text-white font-bold py-3.5 px-4 rounded-xl transition duration-300 shadow-md">
            Return to Login
        </a>

        <p class="text-xs text-slate-400 mt-6 font-medium">
            Auto-redirecting in <span id="countdown" class="text-brand-600 font-bold">5</span> seconds...
        </p>
    </div>

    <script>
        let timeLeft = 5;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                
                window.location.href = 'index1.php'; 
            }
        }, 1000);
    </script>
</body>
</html>