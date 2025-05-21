<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow text-gray-800">
        <h1 class="text-2xl font-bold mb-6 text-red-600">🛈 Používateľská príručka</h1>

        <p class="mb-4">Táto aplikácia umožňuje jednoduchú prácu s PDF súbormi. Nižšie sú uvedené jednotlivé funkcionality:</p>

        <ul class="list-disc ml-6 space-y-2">
            <li><strong>Obrázky do PDF</strong> – prevedie nahrané obrázky (JPG/PNG) do jedného PDF súboru.</li>
            <li><strong>Zlúčiť PDF</strong> – spojí dva PDF súbory do jedného dokumentu.</li>
            <li><strong>Odstrániť stranu</strong> – odstráni zvolenú stranu z PDF.</li>
            <li><strong>Zaheslovať PDF</strong> – ochráni PDF súbor heslom podľa voľby používateľa.</li>
            <li><strong>Konvertovať do WORD</strong> – prevedie PDF do upraviteľného Word dokumentu.</li>
            <li><strong>Konvertovať do PowerPoint</strong> – vytvorí PowerPoint prezentáciu zo stránok PDF.</li>
            <li><strong>Rozdeliť PDF</strong> – rozdelí PDF dokument na jednotlivé strany alebo rozsahy.</li>
            <li><strong>Vytiahnuť stranu</strong> – extrahuje jednu konkrétnu stranu z dokumentu.</li>
            <li><strong>Extrahovať text</strong> – získa čistý text z PDF.</li>
            <li><strong>PDF do obrázkov</strong> – prevedie PDF stránky do obrázkov (napr. PNG).</li>
        </ul>

        <p class="mt-6">Návod sa generuje dynamicky. Kliknutím na tlačidlo nižšie si môžete stiahnuť aktuálnu verziu návodu ako PDF.</p>

        <form action="{{ route('download.manual.pdf') }}" method="GET" class="mt-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                📥 Stiahnuť návod vo formáte PDF
            </button>
        </form>
    </div>
</x-app-layout>
