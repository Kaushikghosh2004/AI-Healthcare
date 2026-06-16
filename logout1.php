<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KASSAH Vitals | Logged Out</title>
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
                        accent: { 500: '#3b82f6' },
                        highlight: { 500: '#8b5cf6' }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-up': 'fadeUp 0.8s ease-out forwards',
                        'ping-slow': 'ping 2s cubic-bezier(0, 0, 0.2, 1) infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0,0) scale(1)' },
                            '33%': { transform: 'translate(30px,-50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px,20px) scale(0.9)' },
                            '100%': { transform: 'translate(0,0) scale(1)' }
                        },
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-950 font-sans flex items-center justify-center overflow-hidden relative">

    
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[15%] w-96 h-96 bg-brand-500/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-[40%] right-[5%] w-80 h-80 bg-accent-500/20 rounded-full blur-3xl animate-blob" style="animation-delay:2s"></div>
        <div class="absolute bottom-[-10%] left-[40%] w-96 h-96 bg-highlight-500/20 rounded-full blur-3xl animate-blob" style="animation-delay:4s"></div>
    </div>

    
    <div class="animate-fade-up text-center px-6 max-w-lg w-full">

        
        <div class="relative inline-flex items-center justify-center mb-8">
            <div class="absolute w-32 h-32 bg-brand-500/20 rounded-full animate-ping-slow"></div>
            <div class="relative w-24 h-24 bg-gradient-to-br from-brand-500 to-accent-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-brand-500/30">
                <i class="fa-solid fa-right-from-bracket text-white text-4xl"></i>
            </div>
        </div>

        
        <h1 class="text-4xl font-black text-white mb-3">You've Logged Out</h1>
        <p class="text-slate-400 text-lg mb-10 font-medium">Your session has been securely ended.<br>Thank you for using <span class="text-brand-400 font-bold">KASSAH Vitals</span>.</p>

        
        <div class="inline-flex items-center gap-2 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-2.5 rounded-full text-sm font-semibold mb-10">
            <i class="fa-solid fa-shield-check"></i>
            Session data securely cleared
        </div>

        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="index1.php"
               class="bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 px-8 rounded-2xl transition shadow-lg shadow-brand-500/25 flex items-center justify-center gap-2 group">
                <i class="fa-solid fa-right-to-bracket group-hover:translate-x-1 transition-transform"></i>
                Sign In Again
            </a>
            <a href="index.php"
               class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white font-bold py-4 px-8 rounded-2xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i>
                Back to Home
            </a>
        </div>

        
        <p class="mt-16 text-slate-700 text-sm font-medium">
            KASSAH <span class="text-slate-600">Vitals</span> &nbsp;•&nbsp; Secure Healthcare Platform
        </p>
    </div>

</body>
</html>