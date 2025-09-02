<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Absensi QR Code
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden text-center bg-white shadow-sm sm:rounded-lg">

                <h3 class="mb-4 text-lg font-semibold">Silakan Scan Kartu Siswa</h3>

                <div id="reader" class="mx-auto" style="width:400px;"></div>

                <div id="result" class="hidden p-4 mt-4 font-medium text-center rounded">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const resultBox = document.getElementById("result");

                function showMessage(message, type = "success") {
                    resultBox.className = "mt-4 p-4 rounded text-center font-medium " +
                        (type === "success" ? "bg-green-100 text-green-800" :
                            type === "warning" ? "bg-yellow-100 text-yellow-800" :
                            "bg-red-100 text-red-800");
                    resultBox.innerHTML = message;
                    resultBox.classList.remove("hidden");
                }

                function sendToServer(nisn) {
                    fetch("{{ route('attendance.scan') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                nisn: nisn
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                showMessage("✅ " + data.message + "<br>Nama: " + data.student.nama, "success");
                            } else if (data.status === "warning") {
                                showMessage("⚠️ " + data.message + "<br>Nama: " + data.student.nama, "warning");
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

                const html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: 250
                    }
                );
                html5QrcodeScanner.render(onScanSuccess);
            });
        </script>
    @endpush
</x-app-layout>
