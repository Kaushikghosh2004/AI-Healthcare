<?php
include("header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KASSAH Vitals | Advanced Healthcare</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { 
                        neon: {
                            cyan: '#00f3ff',
                            pink: '#ff00ff',
                            purple: '#bc13fe',
                            green: '#00ff66',
                        },
                        dark: {
                            900: '#050505',
                            800: '#0a0a0a',
                            700: '#111111'
                        }
                    },
                    boxShadow: {
                        'glow-cyan': '0 0 15px rgba(0, 243, 255, 0.5), inset 0 0 10px rgba(0, 243, 255, 0.2)',
                        'glow-cyan-lg': '0 0 25px rgba(0, 243, 255, 0.8), inset 0 0 15px rgba(0, 243, 255, 0.4)',
                        'glow-pink': '0 0 15px rgba(255, 0, 255, 0.5), inset 0 0 10px rgba(255, 0, 255, 0.2)',
                        'glow-pink-lg': '0 0 25px rgba(255, 0, 255, 0.8), inset 0 0 15px rgba(255, 0, 255, 0.4)',
                        'glow-purple': '0 0 15px rgba(188, 19, 254, 0.5), inset 0 0 10px rgba(188, 19, 254, 0.2)',
                        'glow-purple-lg': '0 0 25px rgba(188, 19, 254, 0.8), inset 0 0 15px rgba(188, 19, 254, 0.4)',
                        'glow-green': '0 0 15px rgba(0, 255, 102, 0.5), inset 0 0 10px rgba(0, 255, 102, 0.2)',
                        'glow-green-lg': '0 0 25px rgba(0, 255, 102, 0.8), inset 0 0 15px rgba(0, 255, 102, 0.4)',
                    },
                    dropShadow: {
                        'neon-cyan': '0 0 8px rgba(0, 243, 255, 0.8)',
                        'neon-pink': '0 0 8px rgba(255, 0, 255, 0.8)',
                        'neon-purple': '0 0 8px rgba(188, 19, 254, 0.8)',
                    },
                    animation: { 
                        'blob': 'blob 7s infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'pulse-fast': 'pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' }
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
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #050505; }
        ::-webkit-scrollbar-thumb { background: #00f3ff; border-radius: 4px; box-shadow: 0 0 10px #00f3ff; }
        
        /* Custom selection color */
        ::selection { background: #ff00ff; color: #fff; text-shadow: 0 0 5px #fff; }
    </style>
</head>

<body class="w-full font-sans text-slate-300 antialiased relative bg-dark-900 overflow-x-hidden selection:bg-neon-pink selection:text-white">

    <!-- Glowing Background Orbs -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[10%] w-[500px] h-[500px] bg-neon-cyan/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] right-[5%] w-[400px] h-[400px] bg-neon-pink/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-10%] left-[40%] w-[600px] h-[600px] bg-neon-purple/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob" style="animation-delay: 4s;"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div> <!-- Subtle tech grid overlay -->
    </div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-dark-900/80 backdrop-blur-xl border-b border-neon-cyan/30 shadow-[0_4px_30px_rgba(0,243,255,0.1)] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer group" onclick="window.scrollTo(0,0)">
                <div class="bg-dark-800 text-neon-cyan p-2.5 rounded-xl border border-neon-cyan shadow-glow-cyan group-hover:shadow-glow-cyan-lg transition duration-300">
                    <i class="fa-solid fa-hospital text-xl"></i>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow-neon-cyan">KASSAH <span class="text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-neon-purple">Vitals</span></h1>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#home" class="text-neon-cyan font-bold hover:text-white hover:drop-shadow-neon-cyan transition">Home</a>
                <a href="services.html" class="text-slate-400 font-bold hover:text-neon-pink hover:drop-shadow-neon-pink transition">About Us</a>
                <a href="contact.html" class="text-slate-400 font-bold hover:text-neon-purple hover:drop-shadow-neon-purple transition">Contact</a>
                <a href="#portals" class="bg-dark-800 text-neon-cyan border border-neon-cyan px-5 py-2.5 rounded-xl font-bold shadow-glow-cyan hover:shadow-glow-cyan-lg hover:bg-neon-cyan hover:text-black transition duration-300 uppercase tracking-widest text-sm">Access Portals</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-40 pb-20 px-6 min-h-[90vh] flex flex-col justify-center items-center text-center max-w-5xl mx-auto animate-fade-in-up">
        <div class="inline-block bg-dark-800/80 border border-neon-pink/50 shadow-glow-pink backdrop-blur-sm px-5 py-2 rounded-full text-sm font-bold text-neon-pink mb-8 uppercase tracking-widest">
            <i class="fa-solid fa-bolt text-white mr-2 animate-pulse-fast"></i> Next-Gen Medical Network
        </div>
        <h2 class="text-5xl md:text-7xl font-black text-white mb-6 tracking-tight leading-tight drop-shadow-lg">
            Advanced Healthcare,<br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan via-neon-pink to-neon-purple bg-[length:200%_auto] animate-pulse drop-shadow-neon-pink">Cybernetic Care.</span>
        </h2>
        <p class="text-lg md:text-xl text-slate-300 font-medium mb-12 max-w-3xl leading-relaxed">
            Experience world-class medical treatment interfacing with cutting-edge technology and a network of dedicated specialists. Your vitals are our ultimate priority.
        </p>
        <div class="flex flex-col sm:flex-row gap-6">
            <a href="register.php" class="bg-dark-800 border-2 border-neon-cyan text-neon-cyan shadow-glow-cyan hover:shadow-glow-cyan-lg hover:bg-neon-cyan hover:text-black font-extrabold py-4 px-8 rounded-xl transition duration-300 flex items-center justify-center text-lg uppercase tracking-wider">
                Initialize Registration <i class="fa-solid fa-microchip ml-3"></i>
            </a>
            <a href="#portals" class="bg-dark-800 border-2 border-neon-purple text-neon-purple shadow-glow-purple hover:shadow-glow-purple-lg hover:bg-neon-purple hover:text-white font-extrabold py-4 px-8 rounded-xl transition duration-300 flex items-center justify-center text-lg uppercase tracking-wider">
                System Login <i class="fa-solid fa-network-wired ml-3"></i>
            </a>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 border-y border-neon-cyan/20 bg-dark-800/40 backdrop-blur-xl relative">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="group">
                <h4 class="text-4xl font-black text-neon-cyan drop-shadow-neon-cyan mb-2 group-hover:scale-110 transition-transform">50+</h4>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Network Specialists</p>
            </div>
            <div class="group">
                <h4 class="text-4xl font-black text-neon-pink drop-shadow-neon-pink mb-2 group-hover:scale-110 transition-transform">24/7</h4>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Emergency Uplink</p>
            </div>
            <div class="group">
                <h4 class="text-4xl font-black text-neon-green drop-shadow-neon-green mb-2 group-hover:scale-110 transition-transform">10k+</h4>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Active Users</p>
            </div>
            <div class="group">
                <h4 class="text-4xl font-black text-neon-purple drop-shadow-neon-purple mb-2 group-hover:scale-110 transition-transform">100%</h4>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Encrypted Records</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 px-6 max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-4 drop-shadow-lg">Core <span class="text-neon-cyan drop-shadow-neon-cyan">Subsystems</span></h2>
            <p class="text-slate-400 font-medium max-w-2xl mx-auto">We provide a wide array of medical services utilizing state-of-the-art algorithms and top-tier medical operatives.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Service 1: Pink -->
            <div class="bg-dark-800/80 p-8 rounded-2xl border border-neon-pink/30 shadow-[0_0_15px_rgba(255,0,255,0.1)] hover:border-neon-pink hover:shadow-glow-pink transition duration-300 backdrop-blur-md group">
                <div class="w-16 h-16 bg-dark-900 border border-neon-pink text-neon-pink shadow-glow-pink rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-truck-medical"></i></div>
                <h3 class="text-xl font-bold text-white mb-3 tracking-wide group-hover:text-neon-pink transition">Trauma Response</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Round-the-clock emergency protocols with rapid response units and state-of-the-art trauma bays ready to handle critical errors.</p>
            </div>
            <!-- Service 2: Cyan -->
            <div class="bg-dark-800/80 p-8 rounded-2xl border border-neon-cyan/30 shadow-[0_0_15px_rgba(0,243,255,0.1)] hover:border-neon-cyan hover:shadow-glow-cyan transition duration-300 backdrop-blur-md group">
                <div class="w-16 h-16 bg-dark-900 border border-neon-cyan text-neon-cyan shadow-glow-cyan rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-microscope"></i></div>
                <h3 class="text-xl font-bold text-white mb-3 tracking-wide group-hover:text-neon-cyan transition">Deep Diagnostics</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Comprehensive laboratory telemetry and imaging scanners ensuring precise, high-fidelity diagnostics for targeted patching.</p>
            </div>
            <!-- Service 3: Purple -->
            <div class="bg-dark-800/80 p-8 rounded-2xl border border-neon-purple/30 shadow-[0_0_15px_rgba(188,19,254,0.1)] hover:border-neon-purple hover:shadow-glow-purple transition duration-300 backdrop-blur-md group">
                <div class="w-16 h-16 bg-dark-900 border border-neon-purple text-neon-purple shadow-glow-purple rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-heart-pulse"></i></div>
                <h3 class="text-xl font-bold text-white mb-3 tracking-wide group-hover:text-neon-purple transition">Cybernetic Surgery</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Expert surgical operatives executing within advanced sterile fields, specializing in neuro-augmentation and cardiovascular repairs.</p>
            </div>
        </div>
    </section>

    <!-- Portals Section -->
    <section id="portals" class="py-24 px-6 bg-dark-900 text-white relative overflow-hidden border-t border-neon-cyan/20">
        <!-- Intense background glowing spots for the portal section -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-neon-cyan/10 rounded-full blur-[120px] mix-blend-screen pointer-events-none"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[600px] h-[600px] bg-neon-pink/10 rounded-full blur-[120px] mix-blend-screen pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-white mb-4 drop-shadow-lg">Access <span class="text-transparent bg-clip-text bg-gradient-to-r from-neon-pink to-neon-purple drop-shadow-neon-pink">Terminals</span></h2>
                <p class="text-slate-400 font-medium max-w-2xl mx-auto uppercase tracking-widest text-sm">Select your designated access node below to securely interface with the mainframe.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 w-full">

                <!-- Patient Portal: Green Theme -->
                <div class="bg-dark-800/60 backdrop-blur-xl p-8 rounded-2xl border border-neon-green/40 shadow-[0_0_20px_rgba(0,255,102,0.1)] flex flex-col hover:-translate-y-2 hover:shadow-glow-green transition-all duration-300 group">
                    <div class="w-16 h-16 bg-dark-900 border-2 border-neon-green text-neon-green shadow-glow-green rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-bed-pulse"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-wide group-hover:text-neon-green transition">Patient Terminal</h3>
                    <p class="text-slate-400 text-sm font-medium mb-8 flex-1">Access your encrypted medical records, view live telemetry vitals, and ping specialists.</p>
                    
                    <div class="space-y-4 mt-auto">
                        <a href="index1.php" class="flex justify-center items-center w-full bg-dark-900 border border-neon-green text-neon-green hover:bg-neon-green hover:text-black shadow-glow-green font-bold py-3.5 rounded-xl transition duration-300 uppercase tracking-widest text-sm">
                            Initialize Login <i class="fa-solid fa-terminal ml-3"></i>
                        </a>
                        <a href="register.php" class="flex justify-center items-center w-full bg-transparent hover:bg-dark-700 text-slate-300 border border-slate-600 hover:border-neon-green/50 font-bold py-3.5 rounded-xl transition duration-300 uppercase tracking-widest text-sm">
                            Create Identity
                        </a>
                    </div>
                </div>

                <!-- Doctor Portal: Pink Theme -->
                <div class="bg-dark-800/60 backdrop-blur-xl p-8 rounded-2xl border border-neon-pink/40 shadow-[0_0_20px_rgba(255,0,255,0.1)] flex flex-col hover:-translate-y-2 hover:shadow-glow-pink transition-all duration-300 group">
                    <div class="w-16 h-16 bg-dark-900 border-2 border-neon-pink text-neon-pink shadow-glow-pink rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-wide group-hover:text-neon-pink transition">Operative Access</h3>
                    <p class="text-slate-400 text-sm font-medium mb-6">Manage patient queues, inject medical log updates, and authorize treatment protocols.</p>
                    
                    <form method="post" action="func1.php" class="mt-auto space-y-4">
                        <div class="relative">
                            <i class="fa-solid fa-id-badge absolute left-4 top-4 text-neon-pink"></i>
                            <input type="email" name="email3" placeholder="Operative ID (Email)" required autocomplete="off"
                                class="w-full pl-11 pr-4 py-3.5 bg-dark-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-neon-pink focus:ring-1 focus:ring-neon-pink focus:shadow-glow-pink outline-none transition text-sm font-mono">
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-fingerprint absolute left-4 top-4 text-neon-pink"></i>
                            <input type="password" name="password3" placeholder="Passkey" required autocomplete="new-password"
                                class="w-full pl-11 pr-4 py-3.5 bg-dark-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-neon-pink focus:ring-1 focus:ring-neon-pink focus:shadow-glow-pink outline-none transition text-sm font-mono">
                        </div>
                        <button type="submit" name="docsub1" class="w-full bg-dark-900 border border-neon-pink text-neon-pink hover:bg-neon-pink hover:text-white shadow-glow-pink font-bold py-3.5 rounded-xl transition duration-300 flex justify-center items-center uppercase tracking-widest text-sm mt-2">
                            Authenticate <i class="fa-solid fa-shield-halved ml-3"></i>
                        </button>
                    </form>
                </div>

                <!-- Admin Portal: Cyan Theme -->
                <div class="bg-dark-800/60 backdrop-blur-xl p-8 rounded-2xl border border-neon-cyan/40 shadow-[0_0_20px_rgba(0,243,255,0.1)] flex flex-col hover:-translate-y-2 hover:shadow-glow-cyan transition-all duration-300 group">
                    <div class="w-16 h-16 bg-dark-900 border-2 border-neon-cyan text-neon-cyan shadow-glow-cyan rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-wide group-hover:text-neon-cyan transition">Core Command</h3>
                    <p class="text-slate-400 text-sm font-medium mb-6">System architecture configuration, personnel management, and global oversight.</p>
                    
                    <form method="post" action="func3.php" class="mt-auto space-y-4">
                        <div class="relative">
                            <i class="fa-solid fa-terminal absolute left-4 top-4 text-neon-cyan"></i>
                            <input type="text" name="username1" placeholder="Root Username" required autocomplete="off"
                                class="w-full pl-11 pr-4 py-3.5 bg-dark-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan focus:shadow-glow-cyan outline-none transition text-sm font-mono">
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-4 text-neon-cyan"></i>
                            <input type="password" name="password2" placeholder="Root Passkey" required autocomplete="new-password"
                                class="w-full pl-11 pr-4 py-3.5 bg-dark-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan focus:shadow-glow-cyan outline-none transition text-sm font-mono">
                        </div>
                        <button type="submit" name="adsub" class="w-full bg-dark-900 border border-neon-cyan text-neon-cyan hover:bg-neon-cyan hover:text-black shadow-glow-cyan font-bold py-3.5 rounded-xl transition duration-300 flex justify-center items-center uppercase tracking-widest text-sm mt-2">
                            Execute Override <i class="fa-solid fa-bolt ml-3"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark-900 text-slate-500 py-12 px-6 border-t border-neon-cyan/30 relative">
        <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-cyan to-transparent opacity-50"></div>
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <h3 class="text-white font-black text-lg mb-4 drop-shadow-neon-cyan tracking-widest uppercase">KASSAH <span class="text-neon-cyan">Vitals</span></h3>
                <p class="text-sm leading-relaxed">Providing cybernetically enhanced medical facilities and top-tier healthcare operatives to ensure your ultimate survival.</p>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg mb-4 uppercase tracking-widest">Fast Links</h3>
                <ul class="space-y-2 text-sm font-mono">
                    <li><a href="#home" class="hover:text-neon-cyan transition">> Initialize_Home</a></li>
                    <li><a href="services.html" class="hover:text-neon-pink transition">> About_Network</a></li>
                    <li><a href="#portals" class="hover:text-neon-green transition">> Access_Terminals</a></li>
                    <li><a href="contact.html" class="hover:text-neon-purple transition">> Ping_Support</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg mb-4 uppercase tracking-widest">Comm Link</h3>
                <ul class="space-y-3 text-sm font-mono">
                    <li class="flex items-center"><i class="fa-solid fa-location-crosshairs text-neon-cyan mr-3 text-lg"></i> Sector: Kolkata, WB, IN</li>
                    <li class="flex items-center"><i class="fa-solid fa-satellite-dish text-neon-pink mr-3 text-lg"></i> Freq: +91 70031 23456</li>
                    <li class="flex items-center"><i class="fa-solid fa-envelope-open-text text-neon-purple mr-3 text-lg"></i> Node: support@kassah.org</li>
                </ul>
            </div>
        </div>
        <div class="text-center pt-8 border-t border-slate-800 text-xs font-mono uppercase tracking-widest text-slate-600">
            &copy; <?= date('Y') ?> KASSAH Vitals Megacorp. All network rights reserved.
        </div>
    </footer>

</body>
</html>