<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//jednotlive services pre endpointy funkcionalit

class ScriptController extends Controller
{
    
    public function imagesToPdf(Request $request)
{
    $request->validate([
        'image_folder' => 'required|string',
        'output_pdf' => 'nullable|string',
    ]);

    // Absolútne cesty
    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/images_to_pdf.py');
    $folder = base_path($request->input('image_folder'));
    $output = $request->input('output_pdf') ? base_path($request->input('output_pdf')) : '';

    $command = "$python $script $folder $output";

    // Debug log do súboru
    file_put_contents(storage_path('logs/script.log'), "CMD: $command" . PHP_EOL, FILE_APPEND);

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    file_put_contents(storage_path('logs/script.log'), implode("\n", $execOutput) . PHP_EOL, FILE_APPEND);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Skript zlyhal',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF bolo úspešne vytvorené',
        'output' => $execOutput,
    ]);
}

public function extractPage(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'page_number' => 'required|integer|min:1',
        'output_path' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/extract_page.py');
    $pdf = base_path($request->input('pdf_path'));
    $page = $request->input('page_number');
    $output = $request->input('output_path') ? base_path($request->input('output_path')) : '';

    $command = "$python $script $pdf $page $output";

    $outputLog = [];
    $returnCode = 0;
    exec($command . " 2>&1", $outputLog, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Skript zlyhal',
            'output' => $outputLog,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => "Strana $page bola úspešne extrahovaná",
        'output' => $outputLog,
    ]);
}

public function extractText(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'output_path' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/extract_text_from_pdf.py');
    $pdf = base_path($request->input('pdf_path'));
    $output = $request->input('output_path') ? base_path($request->input('output_path')) : '';

    $command = "$python $script $pdf $output";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Skript zlyhal',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Text bol úspešne extrahovaný',
        'output' => $execOutput,
    ]);
}

public function mergePdfs(Request $request)
{
    $request->validate([
        'pdf1_path' => 'required|string',
        'pdf2_path' => 'required|string',
        'output_path' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/merge_pdf.py');

    $pdf1 = base_path($request->input('pdf1_path'));
    $pdf2 = base_path($request->input('pdf2_path'));
    $output = $request->input('output_path') ? base_path($request->input('output_path')) : '';

    $command = "$python $script $pdf1 $pdf2 $output";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Zlúčenie PDF zlyhalo',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF boli úspešne zlúčené',
        'output' => $execOutput,
    ]);
}

public function pdfToImages(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'output_folder' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/pdf_to_images.py');

    $pdf = base_path($request->input('pdf_path'));
    $outputFolder = $request->input('output_folder') ? base_path($request->input('output_folder')) : '';

    $command = "$python $script $pdf $outputFolder";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Konverzia PDF na obrázky zlyhala',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF bol úspešne konvertovaný na obrázky',
        'output' => $execOutput,
    ]);
}

public function pdfToPptx(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'output_path' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/pdf_to_pptx.py');

    $pdf = base_path($request->input('pdf_path'));
    $output = $request->input('output_path') ? base_path($request->input('output_path')) : base_path('storage/output.pptx');
    $imagesTempDir = storage_path('storage/output_images');

    $command = "$python $script $pdf $output $imagesTempDir";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Konverzia PDF do PowerPointu zlyhala',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF bol úspešne konvertovaný na PowerPoint',
        'output' => $execOutput,
    ]);
}

public function pdfToWord(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'output_path' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/pdf_to_word.py');

    $pdf = base_path($request->input('pdf_path'));
    $output = $request->input('output_path') 
        ? base_path($request->input('output_path')) 
        : base_path('storage/output.docx');

    $command = "$python $script $pdf $output";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Konverzia PDF do Wordu zlyhala',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF bol úspešne konvertovaný do Wordu',
        'output' => $execOutput,
    ]);
}

public function protectPdf(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'output_path' => 'required|string',
        'password' => 'required|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/protect_pdf.py');

    $input = base_path($request->input('pdf_path'));
    $output = base_path($request->input('output_path'));
    $password = escapeshellarg($request->input('password')); // ochrana pred injection

    $command = "$python $script $input $output $password";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Zabezpečenie PDF zlyhalo',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF bol úspešne zabezpečený heslom',
        'output' => $execOutput,
    ]);
}

public function removePage(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'page_index' => 'required|integer|min:0',
        'output_path' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/remove_page.py');

    $pdf = base_path($request->input('pdf_path'));
    $index = $request->input('page_index');
    $output = $request->input('output_path') 
        ? base_path($request->input('output_path')) 
        : base_path('storage/output_removed.pdf');

    $command = "$python $script $pdf $index $output";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Odstránenie strany z PDF zlyhalo',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Strana bola úspešne odstránená z PDF',
        'output' => $execOutput,
    ]);
}

public function splitPdf(Request $request)
{
    $request->validate([
        'pdf_path' => 'required|string',
        'split_at' => 'required|integer|min:1',
        'output1' => 'nullable|string',
        'output2' => 'nullable|string',
    ]);

    $python = base_path('venv/bin/python3');
    $script = base_path('scripts/split_pdf.py');

    $pdf = base_path($request->input('pdf_path'));
    $splitAt = $request->input('split_at');

    $output1 = $request->input('output1') 
        ? base_path($request->input('output1')) 
        : base_path('storage/split_part1.pdf');

    $output2 = $request->input('output2') 
        ? base_path($request->input('output2')) 
        : base_path('storage/split_part2.pdf');

    $command = "$python $script $pdf $splitAt $output1 $output2";

    $execOutput = [];
    $returnCode = 0;
    exec($command . " 2>&1", $execOutput, $returnCode);

    if ($returnCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Rozdelenie PDF zlyhalo',
            'output' => $execOutput,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'PDF bol úspešne rozdelený',
        'output' => $execOutput,
    ]);
}



}

