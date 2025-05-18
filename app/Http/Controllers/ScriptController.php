<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function convertPdf(Request $request)
    {
        // validácia vstupov
        $request->validate([
            'pdf_path' => 'required|string',
            'docx_path' => 'nullable|string',
        ]);

        $pdf = escapeshellarg($request->input('pdf_path'));
        $docx = $request->input('docx_path') ? escapeshellarg($request->input('docx_path')) : '';

        // zostavíme príkaz
        $command = "python3 scripts/pdf_to_word.py $pdf $docx";

        // vykonáme a zachytíme výstup
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Skript zlyhal',
                'output' => $output,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Konverzia úspešná',
            'output' => $output,
        ]);
    }

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


}

