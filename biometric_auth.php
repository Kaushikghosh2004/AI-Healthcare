<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Biometric Security Matrix</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-lg bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-2xl backdrop-blur-md">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-3">
                <i class="fa-solid fa-fingerprint text-2xl animate-pulse"></i>
            </div>
            <h1 class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                LOCAL BIOMETRIC GATEWAY
            </h1>
            <p class="text-xs text-slate-400 mt-1">Zero-Cloud Local Vector Verification Subsystem</p>
        </div>

        <div id="modelLoaderStatus" class="bg-slate-950/80 border border-amber-500/30 rounded-xl p-4 text-center mb-4 transition-all duration-300">
            <p class="text-sm font-semibold text-amber-400 animate-pulse">
                <i class="fa-solid fa-microchip-ai mr-2"></i> Initializing Neural Network Models...
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Downloading network weights (SSD MobileNet & Landmarker) into browser cache.</p>
        </div>

        <div class="relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border-2 border-slate-700/50 shadow-inner flex items-center justify-center group transition-all duration-300" id="videoWrapper">
            <video id="webcam" autoplay muted playsinline class="w-full h-full object-cover scale-x-[-1]"></video>
            
            <div class="absolute inset-0 border-[30px] border-slate-950/40 pointer-events-none flex items-center justify-center">
                <div class="w-48 h-48 rounded-full border-2 border-dashed border-indigo-500/40 animate-[spin_20s_linear_infinite]"></div>
            </div>
        </div>

        <div id="scanStatus" class="mt-4 p-3 rounded-lg bg-slate-900/60 border border-slate-700 text-center text-sm font-medium text-slate-300">
            <i class="fa-solid fa-camera mr-2 text-slate-500"></i> Camera offline. Ready to initialize.
        </div>

        <div class="mt-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Target Patient ID (PID)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-sm">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    <input type="text" id="pid_input" placeholder="e.g., PAT-1002" 
                           class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-sm font-mono text-indigo-300 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button onclick="processBiometricFlow('VERIFY')" id="btnVerify" disabled
                        class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-lg transition-all duration-200 cursor-not-allowed opacity-50 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-shield-halved"></i> VERIFY FACE
                </button>
                <button onclick="processBiometricFlow('REGISTER')" id="btnRegister" disabled
                        class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-lg transition-all duration-200 cursor-not-allowed opacity-50 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> REGISTER FACE
                </button>
            </div>
        </div>
    </div>

    <script>
        let cameraStream = null;
        const video = document.getElementById('webcam');
        const statusBox = document.getElementById('scanStatus');
        const loaderBox = document.getElementById('modelLoaderStatus');
        const btnVerify = document.getElementById('btnVerify');
        const btnRegister = document.getElementById('btnRegister');
        const videoWrapper = document.getElementById('videoWrapper');

        // ==============================================================
        // 1. ENGINE INITIALIZATION: Load Local Neural Network Models
        // ==============================================================
        window.addEventListener('DOMContentLoaded', async () => {
            try {
                // Pointing directly to CDN model repository directory containing data matrix topologies
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
                
                await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                
                // Hide loader once memory mapping is complete
                loaderBox.classList.add('opacity-0', 'scale-95');
                setTimeout(() => loaderBox.remove(), 300);
                
                statusBox.innerHTML = "<i class='fa-solid fa-circle-check text-emerald-400 mr-2'></i> Neural Engines Operational. Starting Web Camera...";
                statusBox.className = "mt-4 p-3 rounded-lg bg-emerald-950/20 border border-emerald-500/30 text-center text-sm font-medium text-emerald-300";
                
                await startWebcam();
            } catch (error) {
                statusBox.innerHTML = "<i class='fa-solid fa-circle-xmark text-rose-400 mr-2'></i> Failed to load neural network weights.";
                statusBox.className = "mt-4 p-3 rounded-lg bg-rose-950/30 border border-rose-500/30 text-center text-sm font-medium text-rose-300";
                console.error(error);
            }
        });

        // ==============================================================
        // 2. HARDWARE ACCESS: Initialize Local Video Device Stream
        // ==============================================================
        async function startWebcam() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 640, height: 480, facingMode: "user" } 
                });
                video.srcObject = cameraStream;
                
                // Enable systemic actions once video stream coordinates hook successfully
                btnVerify.disabled = false;
                btnVerify.classList.remove('opacity-50', 'cursor-not-allowed');
                btnRegister.disabled = false;
                btnRegister.classList.remove('opacity-50', 'cursor-not-allowed');
                
                statusBox.innerHTML = "<i class='fa-solid fa-video text-indigo-400 mr-2'></i> Stream Established. System Ready.";
                statusBox.className = "mt-4 p-3 rounded-lg bg-slate-900/60 border border-slate-700 text-center text-sm font-medium text-slate-300";
            } catch (err) {
                statusBox.innerHTML = "<i class='fa-solid fa-video-slash text-rose-400 mr-2'></i> Camera connection blocked or unavailable.";
                statusBox.className = "mt-4 p-3 rounded-lg bg-rose-950/30 border border-rose-500/30 text-center text-sm font-medium text-rose-300";
            }
        }

        // ==============================================================
        // 3. CORE PROCESSING PIPELINE: Vector Scan, Extraction, & Handshake
        // ==============================================================
        async function processBiometricFlow(mode) {
            const pid = document.getElementById('pid_input').value.trim();
            
            if (!pid) {
                alert("Operation Aborted: A valid Patient ID (PID) is required.");
                document.getElementById('pid_input').focus();
                return;
            }

            // Lock UI controls to prevent concurrency collisions during evaluation frame computation
            setControlsLock(true);
            statusBox.innerHTML = "<i class='fa-solid fa-spinner animate-spin text-indigo-400 mr-2'></i> Extracting 128-Point Spatial Vectors...";
            statusBox.className = "mt-4 p-3 rounded-lg bg-indigo-950/20 border border-indigo-500/30 text-center text-sm font-medium text-indigo-300";
            
            videoWrapper.className = "relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border-2 border-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.4)] flex items-center justify-center transition-all duration-300";

            // Execute synchronous mathematical node frame analysis mapping via local face-api instance
            const detection = await faceapi.detectSingleFace(video)
                                           .withFaceLandmarks()
                                           .withFaceDescriptor();

            if (!detection) {
                statusBox.innerHTML = "<i class='fa-solid fa-triangle-exclamation text-rose-400 mr-2'></i> Vector Extraction Fault: Face Not Found inside Frame.";
                statusBox.className = "mt-4 p-3 rounded-lg bg-rose-950/30 border border-rose-500/30 text-center text-sm font-medium text-rose-400 animate-pulse";
                videoWrapper.className = "relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border-2 border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.3)] flex items-center justify-center transition-all duration-300";
                setControlsLock(false);
                return;
            }

            // Transform data arrays to string serializations for transit structure
            const continuousDescriptorArray = Array.from(detection.descriptor);

            if (mode === 'REGISTER') {
                // If registering, we pass the extracted raw data directly into our SQL insertion routine 
                // via a structured payload transaction form execution
                sendPayloadToBackend('biometric_register.php', pid, continuousDescriptorArray);
            } else if (mode === 'VERIFY') {
                // If verifying, standard handshake evaluation transits arrays to biometric_verify.php
                sendPayloadToBackend('biometric_verify.php', pid, continuousDescriptorArray);
            }
        }

        // ==============================================================
        // 4. BACKEND TRANSIT LAYER: POST Vector Package to PHP Core
        // ==============================================================
        async function sendPayloadToBackend(targetScript, pid, descriptorArray) {
            try {
                const formData = new FormData();
                formData.append('verify_face', '1'); // Keeps compatibility execution metrics intact
                formData.append('pid', pid);
                formData.append('face_descriptor', JSON.stringify(descriptorArray));

                const response = await fetch(targetScript, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error("HTTP Handshake Network Disconnect Exception.");
                
                const data = await response.json();

                if (data.match || data.registration_success) {
                    // Success Matrix UI Trigger
                    statusBox.innerHTML = `<i class='fa-solid fa-shield-check text-emerald-400 mr-2'></i> Operation Confirmed (${data.confidence || '100%'} Stability Matrix)`;
                    statusBox.className = "mt-4 p-3 rounded-lg bg-emerald-950/30 border border-emerald-500/50 text-center text-sm font-medium text-emerald-400 font-bold";
                    videoWrapper.className = "relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border-2 border-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.6)] flex items-center justify-center transition-all duration-300";
                    
                    setTimeout(() => {
                        // Redirect or release system loop context upon validation stability verification
                        window.location.href = "dashboard.php";
                    }, 1500);

                } else {
                    // Failed Match Security Rejection Alert Layout
                    statusBox.innerHTML = `<i class="fa-solid fa-user-shield text-rose-400 mr-2"></i> REJECTED: ${data.error || 'Identity Verification Match Fault'}`;
                    statusBox.className = "mt-4 p-3 rounded-lg bg-rose-950/40 border border-rose-500 text-center text-sm font-medium text-rose-400 font-bold animate-shake";
                    videoWrapper.className = "relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border-2 border-rose-600 shadow-[0_0_25px_rgba(220,38,38,0.7)] flex items-center justify-center transition-all duration-300";
                    setControlsLock(false);
                }

            } catch (err) {
                statusBox.innerHTML = "<i class='fa-solid fa-server-crack text-rose-400 mr-2'></i> Synchronization Failure with Secure PHP Node.";
                statusBox.className = "mt-4 p-3 rounded-lg bg-rose-950/30 border border-rose-500/30 text-center text-sm font-medium text-rose-400";
                setControlsLock(false);
            }
        }

        function setControlsLock(isLocked) {
            btnVerify.disabled = isLocked;
            btnRegister.disabled = isLocked;
            document.getElementById('pid_input').disabled = isLocked;
            if (isLocked) {
                btnVerify.classList.add('opacity-50');
                btnRegister.classList.add('opacity-50');
            } else {
                btnVerify.classList.remove('opacity-50');
                btnRegister.classList.remove('opacity-50');
            }
        }
    </script>
</body>
</html>