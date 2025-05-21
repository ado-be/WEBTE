<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Extrahovanie strany z PDF</h2>

        <form id="extractForm">
            @csrf
            <div class="mb-4">
                <label class="block font-medium">Vyber PDF súbor:</label>
                <input type="file" name="pdf" id="pdf" accept="application/pdf" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Číslo strany (1 = prvá):</label>
                <input type="number" name="page" id="page" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Extrahovať stranu
            </button>
        </form>

        <div id="status" class="mt-4 text-sm text-gray-700"></div>
    </div>

    <script>
        document.getElementById('extractForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData();
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const pdfFile = document.getElementById('pdf').files[0];
            const page = document.getElementById('page').value;
            const status = document.getElementById('status');

            if (!pdfFile || !page) {
                status.textContent = "❌ Vyplň všetky polia.";
                return;
            }

            const timestamp = Date.now();
            const folderName = `upload_pdf_${timestamp}`;

            formData.append('pdf', pdfFile);
            formData.append('target_folder', folderName);

            try {
                const uploadRes = await fetch('/upload_extract', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                const uploadJson = await uploadRes.json();

                if (!uploadJson.success) {
                    status.textContent = "❌ Nepodarilo sa nahrať PDF.";
                    return;
                }

                const filename = uploadJson.output;
                const url = `/storage/extracted/${filename}`;

                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                a.click();

                status.textContent = "✅ Strana bola úspešne extrahovaná a PDF stiahnuté.";
            } catch (err) {
                console.error(err);
                status.textContent = "❌ Chyba pri komunikácii so serverom.";
            }
        });
    </script>
</x-app-layout>
