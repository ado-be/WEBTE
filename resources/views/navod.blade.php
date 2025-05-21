<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow text-gray-800">
        <h1 class="text-2xl font-bold mb-6 text-red-600">🛈 Používateľská príručka</h1>

        <p class="mb-6">Táto aplikácia umožňuje jednoduchú prácu s PDF súbormi. Po prihlásení do aplikácie nájdete na nástenke (Dashboard) 10 tlačidiel, ktoré predstavujú jednotlivé funkcionality. Po kliknutí na ktorékoľvek z týchto tlačidiel budete presmerovaní na príslušnú podstránku, kde môžete danú funkcionalitu využiť.</p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Dostupné funkcionality:</h2>

        <div class="space-y-6">
            <div>
                <h3 class="font-bold">1. Obrázky do PDF</h3>
                <p class="ml-5">Táto funkcia umožňuje konvertovať viacero obrázkov do jedného PDF súboru.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Kliknite na tlačidlo "Nahraj obrázky" a vyberte jeden alebo viacero obrázkov z vášho zariadenia (podporované formáty: JPG, PNG).</li>
                    <li>Po vybraní obrázkov kliknite na tlačidlo "Vytvoriť PDF".</li>
                    <li>PDF súbor sa automaticky vygeneruje a stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">2. Zlúčiť PDF</h3>
                <p class="ml-5">Pomocou tejto funkcie môžete spojiť dva samostatné PDF súbory do jedného dokumentu.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte prvý PDF súbor z vášho zariadenia.</li>
                    <li>Vyberte druhý PDF súbor z vášho zariadenia.</li>
                    <li>Kliknite na tlačidlo "Zlúčiť PDF".</li>
                    <li>Výsledný spojený PDF súbor sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">3. Odstrániť stranu</h3>
                <p class="ml-5">Táto funkcia umožňuje odstrániť konkrétnu stranu z PDF dokumentu.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Zadajte číslo strany, ktorú chcete odstrániť (číslovanie začína od 0, t.j. prvá strana má číslo 0).</li>
                    <li>Kliknite na tlačidlo "Odstrániť stránku".</li>
                    <li>Upravený PDF súbor bez vybranej strany sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">4. Zaheslovať PDF</h3>
                <p class="ml-5">Pomocou tejto funkcie môžete ochrániť PDF súbor heslom.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Zadajte heslo, ktorým chcete PDF súbor zabezpečiť.</li>
                    <li>Kliknite na tlačidlo "Zabezpečiť PDF".</li>
                    <li>Zabezpečený PDF súbor sa automaticky stiahne do vášho zariadenia. Pri jeho otváraní bude vyžadované zadané heslo.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">5. Konvertovať do WORD</h3>
                <p class="ml-5">Táto funkcia umožňuje konvertovať PDF súbor do editovateľného Word dokumentu (formát DOCX).</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Kliknite na tlačidlo "Konvertovať na Word".</li>
                    <li>Výsledný DOCX súbor sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">6. Konvertovať do PowerPoint</h3>
                <p class="ml-5">Pomocou tejto funkcie môžete konvertovať PDF súbor do PowerPoint prezentácie (formát PPTX).</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Kliknite na tlačidlo "Konvertovať na PowerPoint".</li>
                    <li>Výsledná PPTX prezentácia sa automaticky stiahne do vášho zariadenia. Každá strana PDF bude reprezentovaná ako samostatný snímok.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">7. Rozdeliť PDF</h3>
                <p class="ml-5">Táto funkcia umožňuje rozdeliť PDF dokument na dve časti podľa zvolenej strany.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Zadajte číslo strany, po ktorej chcete PDF rozdeliť (napr. ak zadáte číslo 3, prvá časť bude obsahovať strany 1-3 a druhá časť strany 4 až do konca).</li>
                    <li>Kliknite na tlačidlo "Rozdeliť PDF".</li>
                    <li>Výsledný ZIP archív obsahujúci obe časti rozdeleného PDF sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">8. Vytiahnuť stranu</h3>
                <p class="ml-5">Pomocou tejto funkcie môžete extrahovať konkrétnu stranu z PDF dokumentu ako samostatný PDF súbor.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Zadajte číslo strany, ktorú chcete extrahovať (číslovanie začína od 1, t.j. prvá strana má číslo 1).</li>
                    <li>Kliknite na tlačidlo "Extrahovať stranu".</li>
                    <li>Výsledný PDF súbor obsahujúci iba vybranú stranu sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">9. Extrahovať text</h3>
                <p class="ml-5">Táto funkcia umožňuje extrahovať text z PDF dokumentu do textového súboru.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Kliknite na tlačidlo "Extrahovať text".</li>
                    <li>Výsledný textový súbor sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">10. PDF do obrázkov</h3>
                <p class="ml-5">Pomocou tejto funkcie môžete konvertovať strany PDF dokumentu na samostatné obrázky (formát PNG).</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Vyberte PDF súbor z vášho zariadenia.</li>
                    <li>Kliknite na tlačidlo "Konvertovať na obrázky".</li>
                    <li>Výsledný ZIP archív obsahujúci všetky strany ako samostatné PNG obrázky sa automaticky stiahne do vášho zariadenia.</li>
                </ol>
            </div>
        </div>

        <h2 class="text-xl font-semibold mt-8 mb-4">API a používateľské nástroje</h2>

        <div class="space-y-6">
            <div>
                <h3 class="font-bold">API Tokeny</h3>
                <p class="ml-5">V tejto sekcii môžete spravovať svoje API tokeny, ktoré slúžia na prístup k API rozhraniu aplikácie.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>V menu kliknite na "API Tokeny".</li>
                    <li>Pre vytvorenie nového tokenu zadajte názov a kliknite na tlačidlo "Vytvoriť".</li>
                    <li>Vygenerovaný token si bezpečne uložte, keďže ho po opustení stránky už neuvidíte.</li>
                    <li>Tokeny môžete kedykoľvek vymazať pomocou tlačidla "Vymazať" pri príslušnom tokene.</li>
                </ol>
                <p class="ml-5">Tieto tokeny slúžia na to, aby ste mohli sledovať svoje aktivity v aplikácii a údaje, ktoré ste zadali.</p>
            </div>

            <div>
                <h3 class="font-bold">API Test</h3>
                <p class="ml-5">V tejto sekcii môžete testovať API rozhranie aplikácie použitím vygenerovaného tokenu.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>V menu kliknite na "API Test".</li>
                    <li>Zadajte váš vygenerovaný token a vyberte typ požiadavky.</li>
                    <li>Vyplňte potrebné parametre a odošlite požiadavku.</li>
                    <li>Systém zobrazí odpoveď z API vrátane vašich údajov a aktivít.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">Dokumentácia</h3>
                <p class="ml-5">V tejto sekcii nájdete podrobnú dokumentáciu API rozhrania aplikácie.</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>V menu kliknite na "Dokumentácia".</li>
                    <li>Prehliadajte dostupné endpointy, ich parametre a očakávané výstupy.</li>
                    <li>Môžete priamo testovať API volania pomocou zabudovaného testovacieho rozhrania.</li>
                    <li>Dokumentácia obsahuje podrobné inštrukcie ohľadom vstupov a výstupov API.</li>
                </ol>
            </div>
        </div>

        <h2 class="text-xl font-semibold mt-8 mb-4">Nastavenia používateľského účtu a jazyka</h2>

        <div class="space-y-6">
            <div>
                <h3 class="font-bold">Profil používateľa</h3>
                <p class="ml-5">V pravom hornom rohu aplikácie nájdete ikonku profilu, ktorá ponúka nasledovné možnosti:</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Zobraziť profil - zobrazí váš používateľský profil.</li>
                    <li>Upraviť údaje - môžete upraviť svoje osobné údaje.</li>
                    <li>Zmeniť heslo - umožňuje zmenu prístupového hesla.</li>
                    <li>Odhlásenie - bezpečné odhlásenie z aplikácie.</li>
                    <li>Zmazať účet - trvalé odstránenie vášho používateľského účtu a všetkých vašich údajov.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">Zmena jazyka</h3>
                <p class="ml-5">Napravo od ikonky profilu sa nachádza možnosť zmeny jazyka aplikácie:</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Kliknite na aktuálny jazyk pre zobrazenie dostupných možností.</li>
                    <li>Aktuálne sú k dispozícii dva jazyky: slovenčina a angličtina.</li>
                    <li>Vyberte preferovaný jazyk a rozhranie aplikácie sa automaticky preloží.</li>
                </ol>
            </div>
        </div>

        <h2 class="text-xl font-semibold mt-8 mb-4">Administrátorské nástroje</h2>

        <div class="space-y-6">
            <div>
                <h3 class="font-bold">Prístup administrátora</h3>
                <p class="ml-5">Administrátorské oprávnenia je možné nastaviť len cez phpMyAdmin nastavením hodnoty "1" v stĺpci "is_admin" pre konkrétneho používateľa.</p>
                <p class="ml-5">Po nastavení administrátorských práv sa v dashboarde zobrazia ďalšie možnosti:</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>V menu sa zobrazí nová položka "Admin nástroje".</li>
                    <li>Kliknutím na túto položku získate prístup k špeciálnym funkciám.</li>
                </ol>
            </div>

            <div>
                <h3 class="font-bold">Správa užívateľských aktivít</h3>
                <p class="ml-5">Administrátori majú možnosť prezerať a spravovať aktivity všetkých užívateľov:</p>
                <ol class="list-decimal ml-10 mt-2 text-gray-700">
                    <li>Zobrazenie histórie aktivít všetkých používateľov systému.</li>
                    <li>Filtrovanie aktivít podľa používateľa, typu akcie alebo dátumu.</li>
                    <li>Export kompletnej histórie aktivít do formátu CSV pre ďalšiu analýzu.</li>
                </ol>
            </div>
        </div>

        <p class="mt-6">Kliknutím na tlačidlo nižšie si môžete stiahnuť aktuálnu verziu návodu ako PDF.</p>

        <form action="{{ route('download.manual.pdf') }}" method="GET" class="mt-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                📥 Stiahnuť návod vo formáte PDF
            </button>
        </form>
    </div>
</x-app-layout>