<?php
include("header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ritsy Vitals | Patient Registration</title>

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

<body class="min-h-screen w-full font-sans text-slate-300 antialiased relative flex flex-col items-center justify-center bg-slate-950 py-12 px-4 overflow-x-hidden">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[20%] w-96 h-96 bg-brand-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-[20%] right-[10%] w-96 h-96 bg-accent-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-20%] left-[30%] w-96 h-96 bg-highlight-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <nav class="absolute top-0 w-full p-6 z-50 flex justify-center md:justify-end gap-6">
        <a href="index.php" class="text-slate-500 font-bold hover:text-brand-600 transition">Home</a>
        <a href="services.html" class="text-slate-500 font-bold hover:text-brand-600 transition">About Us</a>
        <a href="contact.html" class="text-slate-500 font-bold hover:text-brand-600 transition">Contact</a>
    </nav>

    <div class="w-full max-w-2xl relative z-10">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center bg-gradient-to-br from-brand-500 to-accent-600 text-white w-14 h-14 rounded-2xl shadow-lg shadow-brand-500/20 mb-4">
                <i class="fa-solid fa-hospital-user text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">Ritsy <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-accent-500">Vitals</span></h1>
        </div>

        <div class="bg-slate-900/60 backdrop-blur-2xl p-8 md:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.5)] border border-slate-700/50">
            
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white">Create an Account</h2>
                <p class="text-slate-400 mt-2 text-sm font-medium">Join our advanced healthcare network.</p>
            </div>

            <form method="POST" action="func.php" class="space-y-5">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">First Name</label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-4 top-3.5 text-slate-500"></i>
                            <input type="text" name="fname" placeholder="John" required onkeydown="return alphaOnly(event);"
                                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Last Name</label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-4 top-3.5 text-slate-500"></i>
                            <input type="text" name="lname" placeholder="Doe" required onkeydown="return alphaOnly(event);"
                                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Email Address</label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-4 top-3.5 text-slate-500"></i>
                            <input type="email" name="email" placeholder="name@example.com" required autocomplete="off"
                                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Contact Number</label>
                        <div class="relative">
                            <i class="fa-solid fa-phone absolute left-4 top-3.5 text-slate-500"></i>
                            <input type="tel" name="contact" minlength="10" maxlength="10" placeholder="10-digit number" required 
                                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500"></i>
                            <input type="password" id="password" name="password" placeholder="Create a password" required onkeyup='check();' autocomplete="new-password"
                                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-sm font-bold mb-2">Confirm Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500"></i>
                            <input type="password" id="cpassword" name="cpassword" placeholder="Repeat password" required onkeyup='check();' autocomplete="new-password"
                                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition text-sm">
                        </div>
                        <span id="message" class="text-xs font-bold mt-1 block"></span>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-slate-400 text-sm font-bold mb-2">Gender</label>
                    <div class="flex gap-6 mt-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="gender" value="Male" required class="form-radio text-brand-500 h-5 w-5 bg-slate-800 border-slate-600">
                            <span class="ml-2 text-slate-300 font-medium">Male</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="gender" value="Female" required class="form-radio text-brand-500 h-5 w-5 bg-slate-800 border-slate-600">
                            <span class="ml-2 text-slate-300 font-medium">Female</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="gender" value="Other" required class="form-radio text-brand-500 h-5 w-5 bg-slate-800 border-slate-600">
                            <span class="ml-2 text-slate-300 font-medium">Other</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="patreg" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg shadow-brand-500/20 flex justify-center items-center gap-2 group">
                        Register Account <i class="fa-solid fa-user-plus group-hover:scale-110 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-center mt-8 text-slate-400 font-medium">
                Already have an account? 
                <a href="index.php#portals" class="text-brand-400 hover:text-brand-300 font-bold transition">Log in here</a>
            </p>
        </div>

    </div>

    <script>
        
        function alphaOnly(event) {
            var key = event.keyCode;
            return ((key >= 65 && key <= 90) || key == 8 || key == 32 || key == 9 || key == 46);
        };

        
        var check = function() {
            var pass = document.getElementById('password').value;
            var cpass = document.getElementById('cpassword').value;
            var msg = document.getElementById('message');
            
            if(cpass === "") {
                msg.innerHTML = "";
                return;
            }
            if (pass === cpass) {
                msg.style.color = '#10b981'; 
                msg.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i> Passwords match';
            } else {
                msg.style.color = '#ef4444'; 
                msg.innerHTML = '<i class="fa-solid fa-circle-xmark mr-1"></i> Passwords do not match';
            }
        }
    </script>
</body>
</html>