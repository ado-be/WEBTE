<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Images to PDF</h2>

        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block font-medium">Nahraj obrázky</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Vytvoriť PDF
            </button>
        </form>

        <div id="status" class="mt-4 text-sm text-gray-700"></div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            const res = await fetch('/upload-images-to-pdf', {
                method: 'POST',
                body: formData
            });

            if (res.ok) {
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "vysledok.pdf";
                document.body.appendChild(a);
                a.click();
                a.remove();
                document.getElementById('status').textContent = "✅ PDF úspešne vygenerované.";
            } else {
                document.getElementById('status').textContent = "❌ Chyba pri generovaní PDF.";
            }
        });
    </script>
</x-app-layout>