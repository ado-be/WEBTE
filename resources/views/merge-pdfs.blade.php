<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Zlúčiť PDF</h2>

        <form id="mergeForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block font-medium">Vyber PDF súbory</label>
                <input type="file" name="pdfs[]" id="pdfs" multiple accept="application/pdf" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Zlúčiť PDF
            </button>
        </form>

        <div id="merge-status" class="mt-4 text-sm text-gray-700"></div>
    </div>

    <script>
        document.getElementById('mergeForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);

            const res = await fetch('/upload-merge-pdfs', {
                method: 'POST',
                body: formData
            });

            const output = document.getElementById('merge-status');

            if (res.ok) {
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "zluceny.pdf";
                document.body.appendChild(a);
                a.click();
                a.remove();
                output.textContent = "✅ PDF úspešne zlúčené.";
            } else {
                output.textContent = "❌ Chyba pri zlučovaní PDF.";
            }
        });
    </script>
</x-app-layout>
