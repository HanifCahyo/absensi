<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi QR Code Scanner</title>

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
                <h1 class="text-3xl font-bold text-gray-900">Absensi QR Code</h1>
                <p class="mt-2 text-gray-600">Silakan scan kartu siswa untuk melakukan absensi</p>
            </div>

            <!-- Scanner Container -->
            <div class="p-6 overflow-hidden text-center bg-white shadow-lg sm:rounded-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Silakan Scan Kartu Siswa</h3>

                <div id="reader" class="mx-auto" style="width:400px;"></div>

                <div id="result" class="hidden p-4 mt-4 font-medium text-center rounded">
                </div>

                <!-- Restart Scanner Button -->
                <button id="restart-btn" class="hidden px-4 py-2 mt-4 text-white bg-blue-500 rounded hover:bg-blue-600">
                    Scan Lagi
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const resultBox = document.getElementById("result");
            const restartBtn = document.getElementById("restart-btn");
            let html5QrcodeScanner;

            function showMessage(message, type = "success") {
                resultBox.className = "mt-4 p-4 rounded text-center font-medium " +
                    (type === "success" ? "bg-green-100 text-green-800" :
                        type === "warning" ? "bg-yellow-100 text-yellow-800" :
                        "bg-red-100 text-red-800");
                resultBox.innerHTML = message;
                resultBox.classList.remove("hidden");
                restartBtn.classList.remove("hidden");
            }

            function sendToServer(nis) {
                fetch("{{ route('attendance.scan') }}", {
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
                            showMessage("✅ " + data.message + "<br>Nama: " + data.student.name, "success");
                        } else if (data.status === "warning") {
                            showMessage("⚠️ " + data.message + "<br>Nama: " + data.student.name, "warning");
                        } else {
                            showMessage("❌ " + data.message, "error");
                        }
                    })
                    .catch(() => showMessage("❌ Terjadi kesalahan server", "error"));
            }

            function onScanSuccess(decodedText) {
                sendToServer(decodedText);
                html5QrcodeScanner.clear().then(() => {
                    console.log("Scanner berhenti setelah 1x scan");
                }).catch(err => console.error("Clear error:", err));
            }

            function startScanner() {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: 250
                    }
                );
                html5QrcodeScanner.render(onScanSuccess);

                // Hide result and restart button when scanner starts
                resultBox.classList.add("hidden");
                restartBtn.classList.add("hidden");
            }

            // Restart scanner functionality
            restartBtn.addEventListener("click", function() {
                startScanner();
            });

            // Initialize scanner
            startScanner();
        });
    </script>
</body>

</html>
