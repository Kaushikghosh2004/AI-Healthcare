<?php
include("header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KASSAH Vitals | Patient Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { 
                        brand: { 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 500: '#14b8a6', 600: '#0d9488', 900: '#134e4a' },
                        accent: { 500: '#3b82f6', 600: '#2563eb' },
                        highlight: { 500: '#8b5cf6', 600: '#7c3aed' }
                    },
                    animation: { 
                        'blob': 'blob 7s infinite',
                    },
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

<body class="min-h-screen w-full font-sans text-slate-300 antialiased relative flex flex-col items-center justify-center bg-slate-950 overflow-hidden">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[20%] w-96 h-96 bg-brand-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-[20%] right-[10%] w-96 h-96 bg-accent-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-20%] left-[30%] w-96 h-96 bg-highlight-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <nav class="absolute top-0 w-full p-6 z-50 flex justify-center md:justify-end gap-6">
        <a href="index.php" class="text-slate-400 font-bold hover:text-brand-500 transition">Home</a>
        <a href="services.html" class="text-slate-400 font-bold hover:text-brand-500 transition">About Us</a>
        <a href="contact.html" class="text-slate-400 font-bold hover:text-brand-500 transition">Contact</a>
    </nav>

    <div class="w-full max-w-md px-6 relative z-10">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center bg-gradient-to-br from-brand-500 to-blue-600 text-white w-16 h-16 rounded-2xl shadow-lg shadow-brand-500/30 mb-4">
                <i class="fa-solid fa-hospital-user text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white">KASSAH <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-accent-500">Vitals</span></h1>
            <p class="text-slate-400 font-medium mt-2">Secure Patient Portal</p>
        </div>

        <div class="bg-slate-900/60 backdrop-blur-2xl p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.5)] border border-slate-700/50">
            
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white">Welcome Back</h2>
                <p class="text-sm text-slate-400 mt-1 font-medium">Please enter your credentials to continue.</p>
            </div>

            <form method="POST" action="func.php" class="space-y-5">
                
                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-slate-500"></i>
                        </div>
                        <input type="email" name="email" placeholder="name@example.com" required 
                            class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-brand-500 transition shadow-sm placeholder-slate-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-500"></i>
                        </div>
                        <input type="password" name="password2" placeholder="••••••••" required 
                            class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-brand-500 transition shadow-sm placeholder-slate-500">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center text-sm text-slate-400 cursor-pointer group">
                        <input type="checkbox" class="w-4 h-4 text-brand-500 bg-slate-800 border-slate-600 rounded focus:ring-brand-500 mr-2 cursor-pointer transition shadow-sm">
                        <span class="group-hover:text-white transition font-medium">Remember me</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-brand-400 hover:text-brand-300 transition">Forgot Password?</a>
                </div>

                <button type="submit" name="patsub" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-xl transition duration-300 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/30 flex justify-center items-center gap-2 group mt-2">
                    Sign In <i class="fa-solid fa-arrow-right-to-bracket group-hover:translate-x-1 transition-transform"></i>
                </button>
                
            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-slate-400 font-medium inline-block px-5 py-2">
                Don't have an account? 
                <a href="register.php" class="text-brand-400 font-bold hover:text-brand-300 transition ml-1">Register here</a>
            </p>
        </div>

    </div>

</body>
</html>