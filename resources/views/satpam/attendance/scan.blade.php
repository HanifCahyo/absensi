<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi Scanner</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-900">Absensi Scanner</h1>
                <p class="mt-2 text-gray-600">Scan Barcode atau QR Code pada kartu siswa</p>
            </div>

            <!-- Scanner Container -->
            <div class="p-6 overflow-hidden text-center bg-white shadow-lg sm:rounded-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Silakan Scan Kartu Siswa</h3>

                <!-- Scanner Mode Toggle -->
                <div class="mb-4">
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button" id="barcode-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-l-lg hover:bg-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700">
                            Barcode Scanner
                        </button>
                        <button type="button" id="qr-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-blue-700">
                            QR Code Scanner
                        </button>
                    </div>
                </div>

                <!-- Auto Restart Toggle -->
                <div class="mb-4">
                    <label class="flex items-center justify-center gap-2">
                        <input type="checkbox" id="auto-restart" checked
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Auto scan ulang (3 detik)</span>
                    </label>
                </div>

                <!-- Status Info -->
                <div id="status" class="p-3 mb-4 text-blue-800 bg-blue-100 rounded">
                    Memulai scanner...
                </div>

                <!-- Counter for successful scans -->
                <div id="scan-counter" class="p-2 mb-4 text-sm text-gray-600 rounded bg-gray-50">
                    Scan berhasil: <span id="counter-number" class="font-bold text-green-600">0</span>
                </div>

                <div id="reader" class="mx-auto" style="width:400px;"></div>

                <div id="result" class="hidden p-4 mt-4 font-medium text-center rounded">
                </div>

                <!-- Manual Controls -->
                <div class="mt-4 space-x-2">
                    <button id="restart-btn" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                        Restart Scanner Manual
                    </button>
                    <button id="stop-btn" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                        Stop Scanner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const resultBox = document.getElementById("result");
            const restartBtn = document.getElementById("restart-btn");
            const stopBtn = document.getElementById("stop-btn");
            const barcodeBtn = document.getElementById("barcode-btn");
            const qrBtn = document.getElementById("qr-btn");
            const statusDiv = document.getElementById("status");
            const autoRestartCheckbox = document.getElementById("auto-restart");
            const counterNumber = document.getElementById("counter-number");

            let html5QrcodeScanner;
            let currentMode = 'barcode';
            let scanCounter = 0;
            let autoRestartTimeout;
            let isScanning = false;

            function updateCounter() {
                counterNumber.textContent = scanCounter;
            }

            function updateStatus(message, type = 'info') {
                statusDiv.className = `mb-4 p-3 rounded ${
                    type === 'success' ? 'bg-green-100 text-green-800' :
                    type === 'error' ? 'bg-red-100 text-red-800' :
                    type === 'warning' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-blue-100 text-blue-800'
                }`;
                statusDiv.textContent = message;
            }

            function showMessage(message, type = "success", duration = 3000) {
                resultBox.className = "mt-4 p-4 rounded text-center font-medium " +
                    (type === "success" ? "bg-green-100 text-green-800" :
                        type === "warning" ? "bg-yellow-100 text-yellow-800" :
                        "bg-red-100 text-red-800");
                resultBox.innerHTML = message;
                resultBox.classList.remove("hidden");

                // Auto hide message after duration
                setTimeout(() => {
                    if (!resultBox.classList.contains("hidden")) {
                        resultBox.classList.add("hidden");
                    }
                }, duration);
            }

            function extractNIS(decodedText) {
                console.log(`Mode: ${currentMode}, Raw: ${decodedText}`);

                if (currentMode === 'barcode') {
                    return decodedText;
                }

                if (currentMode === 'qr') {
                    try {
                        const data = JSON.parse(decodedText);
                        if (data.nis) {
                            console.log(`Extracted NIS: ${data.nis}`);
                            return data.nis;
                        }
                    } catch (e) {
                        console.log("Not JSON, treating as direct NIS");
                        return decodedText;
                    }
                }

                return decodedText;
            }

            function sendToServer(rawText) {
                const nis = extractNIS(rawText);

                fetch("{{ route('satpam.attendance.scan') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            nis: nis
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            scanCounter++;
                            updateCounter();
                            showMessage("✅ " + data.message + "<br><strong>" + data.student.name + "</strong>",
                                "success");

                            // Play success sound (optional)
                            playSuccessSound();

                        } else if (data.status === "warning") {
                            showMessage("⚠️ " + data.message + "<br><strong>" + data.student.name + "</strong>",
                                "warning");
                        } else {
                            showMessage("❌ " + data.message, "error");
                        }
                    })
                    .catch(() => {
                        showMessage("❌ Terjadi kesalahan server", "error");
                    });
            }

            function playSuccessSound() {
                // Simple beep sound using Web Audio API
                try {
                    const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.2);
                } catch (e) {
                    console.log("Sound not supported");
                }
            }

            function onScanSuccess(decodedText) {
                if (!isScanning) return; // Prevent multiple scans

                updateStatus("✅ Scan berhasil! Memproses...", 'success');
                sendToServer(decodedText);

                // Clear scanner
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        console.log("Scanner cleared");
                        isScanning = false;

                        // Auto restart if enabled
                        if (autoRestartCheckbox.checked) {
                            let countdown = 3;
                            updateStatus(`🔄 Auto scan dalam ${countdown} detik...`, 'warning');

                            const countdownInterval = setInterval(() => {
                                countdown--;
                                if (countdown > 0) {
                                    updateStatus(`🔄 Auto scan dalam ${countdown} detik...`,
                                        'warning');
                                } else {
                                    clearInterval(countdownInterval);
                                    if (autoRestartCheckbox
                                        .checked) { // Check again in case user unchecked
                                        startScanner();
                                    }
                                }
                            }, 1000);

                        } else {
                            updateStatus(
                                "Scanner dihentikan. Klik 'Restart Scanner Manual' untuk scan lagi.",
                                'warning');
                        }

                    }).catch(err => {
                        console.error("Clear error:", err);
                        isScanning = false;
                        updateStatus("Scanner dihentikan.", 'warning');
                    });
                }
            }

            function onScanFailure(error) {
                // Silent - don't show scan failures
            }

            function startScanner() {
                // Clear any existing timeout
                if (autoRestartTimeout) {
                    clearTimeout(autoRestartTimeout);
                }

                updateStatus("🎥 Memulai scanner...", 'info');

                // Clear previous scanner if exists
                if (html5QrcodeScanner && isScanning) {
                    html5QrcodeScanner.clear().catch(err => {
                        console.log("Clear error:", err);
                    });
                }

                const config = {
                    fps: 10,
                    qrbox: 250
                };

                try {
                    html5QrcodeScanner = new Html5QrcodeScanner("reader", config);
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

                    setTimeout(() => {
                        updateStatus(`📱 Scanner ${currentMode} aktif! Siap scan kartu siswa.`, 'success');
                        isScanning = true;
                    }, 1000);

                    resultBox.classList.add("hidden");

                } catch (error) {
                    console.error("Scanner error:", error);
                    updateStatus("❌ Gagal memulai scanner: " + error.message, 'error');
                    isScanning = false;
                }
            }

            function stopScanner() {
                if (html5QrcodeScanner && isScanning) {
                    html5QrcodeScanner.clear().then(() => {
                        isScanning = false;
                        updateStatus("⏹️ Scanner dihentikan manual.", 'warning');
                    }).catch(err => {
                        console.error("Stop error:", err);
                        isScanning = false;
                        updateStatus("Scanner dihentikan.", 'warning');
                    });
                }

                if (autoRestartTimeout) {
                    clearTimeout(autoRestartTimeout);
                }
            }

            function updateButtonStyles() {
                if (currentMode === 'barcode') {
                    barcodeBtn.className =
                        "px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-l-lg hover:bg-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700";
                    qrBtn.className =
                        "px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-blue-700";
                } else {
                    qrBtn.className =
                        "px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-r-lg hover:bg-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700";
                    barcodeBtn.className =
                        "px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-blue-700";
                }
            }

            function checkLibraryLoaded() {
                if (typeof Html5QrcodeScanner === 'undefined') {
                    updateStatus("⏳ Library belum dimuat. Menunggu...", 'warning');
                    setTimeout(checkLibraryLoaded, 1000);
                    return false;
                }
                return true;
            }

            // Event listeners
            barcodeBtn.addEventListener("click", function() {
                currentMode = 'barcode';
                updateButtonStyles();
                if (checkLibraryLoaded()) {
                    stopScanner();
                    setTimeout(startScanner, 500);
                }
            });

            qrBtn.addEventListener("click", function() {
                currentMode = 'qr';
                updateButtonStyles();
                if (checkLibraryLoaded()) {
                    stopScanner();
                    setTimeout(startScanner, 500);
                }
            });

            restartBtn.addEventListener("click", function() {
                if (checkLibraryLoaded()) {
                    stopScanner();
                    setTimeout(startScanner, 500);
                }
            });

            stopBtn.addEventListener("click", function() {
                stopScanner();
            });

            // Initialize
            updateButtonStyles();
            updateCounter();

            // Check camera permission and start
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(function(stream) {
                        updateStatus("✅ Akses kamera berhasil. Memulai scanner...", 'success');
                        stream.getTracks().forEach(track => track.stop());

                        setTimeout(() => {
                            if (checkLibraryLoaded()) {
                                startScanner();
                            }
                        }, 1500);
                    })
                    .catch(function(error) {
                        updateStatus("❌ Akses kamera ditolak. Klik tombol kamera di browser untuk mengizinkan.",
                            'error');
                        console.error("Camera error:", error);
                    });
            } else {
                updateStatus("❌ Browser tidak mendukung akses kamera", 'error');
            }
        });
    </script>
</body>

</html>
