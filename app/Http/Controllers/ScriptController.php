<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//jednotlive services pre endpointy funkcionalit

class ScriptController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/images-to-pdf",
 *     summary="Konvertuje obrázky na PDF",
 *     tags={"Konverzie"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"image_folder"},
 *             @OA\Property(property="image_folder", type="string", example="storage/obrazky"),
 *             @OA\Property(property="output_pdf", type="string", example="storage/vystup.pdf")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PDF bolo úspešne vytvorené"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Skript zlyhal"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/extract-page",
 *     summary="Extrahuje jednu stranu z PDF",
 *     tags={"Manipulácia s PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path", "page_number"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/testPDF.pdf"),
 *             @OA\Property(property="page_number", type="integer", example=2),
 *             @OA\Property(property="output_path", type="string", example="storage/extracted_page.pdf")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Strana bola úspešne extrahovaná"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Skript zlyhal"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/extract-text",
 *     summary="Extrahuje text z PDF",
 *     tags={"Manipulácia s PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/suhlas.pdf"),
 *             @OA\Property(property="output_path", type="string", example="storage/text.txt")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Text bol úspešne extrahovaný"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Skript zlyhal"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/merge-pdfs",
 *     summary="Zlúči dve PDF do jedného",
 *     tags={"Manipulácia s PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf1_path", "pdf2_path"},
 *             @OA\Property(property="pdf1_path", type="string", example="storage/cast1.pdf"),
 *             @OA\Property(property="pdf2_path", type="string", example="storage/cast2.pdf"),
 *             @OA\Property(property="output_path", type="string", example="storage/zlucene.pdf")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PDF boli úspešne zlúčené"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Skript zlyhal"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/pdf-to-images",
 *     summary="Konvertuje PDF na obrázky (PNG)",
 *     tags={"Konverzie"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/testPDF.pdf"),
 *             @OA\Property(property="output_folder", type="string", example="storage/obrazky_z_pdf")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PDF bol úspešne konvertovaný na obrázky"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Skript zlyhal"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/pdf-to-pptx",
 *     summary="Konvertuje PDF na PowerPoint (PPTX)",
 *     tags={"Konverzie"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/testPDF.pdf"),
 *             @OA\Property(property="output_path", type="string", example="storage/vystup.pptx")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PDF bol úspešne konvertovaný na PowerPoint"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Konverzia zlyhala"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/pdf-to-word",
 *     summary="Konvertuje PDF na Word (DOCX)",
 *     tags={"Konverzie"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/suhlas.pdf"),
 *             @OA\Property(property="output_path", type="string", example="storage/vystup.docx")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Úspešná konverzia"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Chyba pri konverzii"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/protect-pdf",
 *     summary="Zabezpečí PDF heslom",
 *     tags={"Zabezpečenie"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path", "output_path", "password"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/testPDF.pdf"),
 *             @OA\Property(property="output_path", type="string", example="storage/zabezpeceny.pdf"),
 *             @OA\Property(property="password", type="string", example="tajneheslo")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PDF bol úspešne zabezpečený"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Zabezpečenie zlyhalo"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/remove-page",
 *     summary="Odstráni jednu stránku z PDF",
 *     tags={"Manipulácia s PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path", "page_index"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/testPDF.pdf"),
 *             @OA\Property(property="page_index", type="integer", example=1),
 *             @OA\Property(property="output_path", type="string", example="storage/bez_strany.pdf")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Strana bola úspešne odstránená"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Skript zlyhal"
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/split-pdf",
 *     summary="Rozdelí PDF na dve časti podľa zvolenej strany",
 *     tags={"Manipulácia s PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pdf_path", "split_at"},
 *             @OA\Property(property="pdf_path", type="string", example="storage/testPDF.pdf"),
 *             @OA\Property(property="split_at", type="integer", example=1),
 *             @OA\Property(property="output1", type="string", example="storage/cast1.pdf"),
 *             @OA\Property(property="output2", type="string", example="storage/cast2.pdf")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PDF bol úspešne rozdelený"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Rozdelenie zlyhalo"
 *     )
 * )
 */
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

