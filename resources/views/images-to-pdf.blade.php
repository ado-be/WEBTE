<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">{{ __('Images to PDF') }}</h2>

        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block font-medium">{{ __('Nahraj obrázky') }}</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                {{ __('Vytvoriť PDF') }}
            </button>
        </form>

        <div id="status" class="mt-4 text-sm text-gray-700"></div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const status = document.getElementById('status');

            try {
                const timestamp = Date.now();
                const folderName = `upload_${timestamp}`;
                formData.append('target_folder', folderName);
                // 1. Upload obrázkov
                const uploadRes = await fetch('/upload-images', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const uploadJson = await uploadRes.json();

                if (!uploadJson.success) {
                    status.textContent = "❌ Nepodarilo sa nahrať obrázky.";
                    return;
                }

                // 2. Zavolaj API na generovanie PDF
                const imageFolder = uploadJson.folder;

                const pdfRes = await fetch('/api/images-to-pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image_folder: imageFolder,
                        output_pdf: 'storage/app/public/ImageToPdfVystup.pdf'
                    })
                });

                const pdfJson = await pdfRes.json();

                if (pdfJson.success) {
                    const rawPath = pdfJson.output[0].split(': ')[1]; // extrahuje cestu
                    const filename = rawPath.split('/').pop();
                    const url = `/storage/${filename}`; // ak máš storage:link

                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    a.click();

                    status.textContent = "✅ PDF úspešne vygenerované a stiahnuté.";
                } else {
                    status.textContent = "❌ Chyba pri generovaní PDF.";
                }

            } catch (err) {
                console.error(err);
                status.textContent = "❌ Neočakávaná chyba pri komunikácii s API.";
            }
        });
    </script>


</x-app-layout>