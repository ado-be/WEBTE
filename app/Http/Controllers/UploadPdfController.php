<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
/**
 * Class UploadPdfController
 *
 * Controller na spracovanie PDF súborov pomocou externých Python skriptov.
 * Obsahuje funkcie ako zlučovanie, extrakcia strán, konverzia, ochrana heslom, atď.
 *
 * @package App\Http\Controllers
 */
class UploadPdfController extends Controller
{
   /**
 * @OA\Post(
 *     path="/api/images-to-pdf2",
 *     summary="Vytvorenie PDF z obrázkov",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="images[]",
 *                     type="array",
 *                     @OA\Items(type="string", format="binary"),
 *                     description="Obrázky na vloženie do PDF"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Úspešne vygenerovaný PDF súbor"),
 *     @OA\Response(response=500, description="Chyba pri generovaní PDF")
 * )
 */
    public function generateFromImages(Request $request)
    {
        try {
            $request->validate([
                'images.*' => 'required|image',
            ]);

            // Unikátne ID pre priečinok a výstup
            $folderId = \Str::uuid()->toString();
            //$uploadDir = public_path("uploads/$folderId");
            $uploadDir = "uploads/$folderId";
            $outputPdf = public_path("uploads/{$folderId}_output.pdf");

            // Vytvor adresár ak neexistuje
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Ulož súbory
            foreach ($request->file('images') as $image) {
                $filename = $image->getClientOriginalName();
                $image->move($uploadDir, $filename);
            }

            // Zavolaj API endpoint (ktorý spustí Python skript)
            $response = \Http::acceptJson()->post(url('/api/images-to-pdf'), [
                'image_folder' => $uploadDir,
                'output_pdf' => $outputPdf,
            ]);

            if (!$response->ok() || !$response->json('success')) {
                \Log::error('API images-to-pdf failed', ['response' => $response->body()]);
                return response('Chyba pri generovaní PDF (API).', 500);
            }

            if (!file_exists($outputPdf)) {
                \Log::error('PDF neexistuje po API volaní.', ['expected_path' => $outputPdf]);
                return response('PDF sa nevytvorilo.', 500);
            }

            return response()->download($outputPdf)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Výnimka pri generateFromImages()', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Interná chyba servera.', 500);
        }
    }
/**
 * @OA\Post(
 *     path="/api/merge-pdf",
 *     summary="Zlúčenie dvoch PDF súborov",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file1", type="string", format="binary", description="Prvý PDF súbor"),
 *                 @OA\Property(property="pdf_file2", type="string", format="binary", description="Druhý PDF súbor")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Zlúčený PDF súbor"),
 *     @OA\Response(response=500, description="Chyba pri zlučovaní PDF")
 * )
 */
    public function mergePdfs(Request $request)
    {
        try {
            $request->validate([
                'pdf_file1' => 'required|mimes:pdf',
                'pdf_file2' => 'required|mimes:pdf',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputPdf = "$uploadDir/zluceny.pdf";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf1 = $request->file('pdf_file1');
            $pdf2 = $request->file('pdf_file2');

            $inputPath1 = "$uploadDir/" . $pdf1->getClientOriginalName();
            $inputPath2 = "$uploadDir/" . $pdf2->getClientOriginalName();

            $pdf1->move($uploadDir, basename($inputPath1));
            $pdf2->move($uploadDir, basename($inputPath2));

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/merge_pdf.py'),
                $inputPath1,
                $inputPath2,
                $outputPdf
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri zlučovaní PDF.');
                \Log::error('merge_pdf.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($outputPdf)) {
                session()->flash('error', 'Výstupný PDF súbor sa nevytvoril.');
                return back();
            }

            return response()->download($outputPdf, 'zluceny.pdf')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('mergePdfs výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár pre zlúčenie PDF súborov.
 *
 * @return \Illuminate\View\View
 */
    public function showMergePdfsForm()
    {
        return view('merge_pdfs');
    }

/**
 * Zobrazí formulár na odstránenie stránky z PDF.
 *
 * @return \Illuminate\View\View
 */
    public function showRemovePageForm()
    {
        return view('remove_page');
    }


/**
 * @OA\Post(
 *     path="/api/remove-page2",
 *     summary="Odstránenie strany z PDF",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF súbor"),
 *                 @OA\Property(property="page", type="integer", description="Index strany na odstránenie (od 0)")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Upravený PDF bez danej strany"),
 *     @OA\Response(response=500, description="Chyba pri spracovaní PDF")
 * )
 */
    public function removePage(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
                'page' => 'required|integer|min:0',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputPdf = "$uploadDir/removed.pdf";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            // Ulož PDF
            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            // Spustenie skriptu
            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/remove_page.py'),
                $inputPath,
                $request->input('page'),
                $outputPdf
            ]);

            $process->setTimeout(60);
            $process->run();

            // Ak nastane chyba
            if (!$process->isSuccessful()) {
                session()->flash('error_exit', $process->getExitCode());
                session()->flash('error_stderr', $process->getErrorOutput());
                session()->flash('error_stdout', $process->getOutput());
                return back();
            }

            if (!file_exists($outputPdf)) {
                session()->flash('error', 'Výstupný PDF súbor sa nevytvoril.');
                return back();
            }

            return response()->download($outputPdf, 'upraveny.pdf')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            session()->flash('error_trace', $e->getTraceAsString());
            return back();
        }
    }

/**
 * @OA\Post(
 *     path="/api/protect-pdf2",
 *     summary="Ochrana PDF heslom",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF súbor"),
 *                 @OA\Property(property="password", type="string", description="Heslo")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Zašifrovaný PDF"),
 *     @OA\Response(response=500, description="Chyba pri šifrovaní PDF")
 * )
 */
    public function protectPdf(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
                'password' => 'required|string|min:1',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputPdf = "$uploadDir/protected.pdf";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/protect_pdf.py'),
                $inputPath,
                $outputPdf,
                $request->input('password')
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri šifrovaní PDF.');
                \Log::error('protect_pdf.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($outputPdf)) {
                session()->flash('error', 'Výstupný súbor neexistuje.');
                return back();
            }

            return response()->download($outputPdf, 'zabezpeceny.pdf')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('protectPdf výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
/**
 * Zobrazí formulár na zabezpečenie PDF heslom.
 *
 * @return \Illuminate\View\View
 */
    public function showProtectPdfForm()
    {
        return view('protect_pdf');
    }
/**
 * @OA\Post(
 *     path="/api/pdf-to-word2",
 *     summary="Konverzia PDF na Word",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF dokument")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="DOCX dokument"),
 *     @OA\Response(response=500, description="Chyba pri konverzii PDF na Word")
 * )
 */
    public function pdfToWord(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputDocx = "$uploadDir/converted.docx";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/pdf_to_word.py'),
                $inputPath,
                $outputDocx
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri konverzii PDF na Word.');
                \Log::error('pdf_to_word.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($outputDocx)) {
                session()->flash('error', 'Výstupný DOCX súbor sa nevytvoril.');
                return back();
            }

            return response()->download($outputDocx, 'dokument.docx')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('pdfToWord výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár na konverziu PDF na Word.
 *
 * @return \Illuminate\View\View
 */
    public function showPdfToWordForm()
    {
        return view('pdf_to_word');
    }
/**
 * @OA\Post(
 *     path="/api/pdf-to-pptx2",
 *     summary="Konverzia PDF na PowerPoint",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF dokument")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="PowerPoint prezentácia"),
 *     @OA\Response(response=500, description="Chyba pri konverzii PDF na PPTX")
 * )
 */
    public function pdfToPptx(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputPptx = "$uploadDir/prezentacia.pptx";
            $tempImageDir = "$uploadDir/images";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/pdf_to_pptx.py'),
                $inputPath,
                $outputPptx,
                $tempImageDir
            ]);

            $process->setTimeout(90);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri konverzii PDF na PPTX.');
                \Log::error('pdf_to_pptx.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($outputPptx)) {
                session()->flash('error', 'Výstupný PPTX súbor sa nevytvoril.');
                return back();
            }

            return response()->download($outputPptx, 'prezentacia.pptx')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('pdfToPptx výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár na konverziu PDF na PPTX.
 *
 * @return \Illuminate\View\View
 */
    public function showPdfToPptxForm()
    {
        return view('pdf_to_pptx');
    }
/**
 * @OA\Post(
 *     path="/api/split-pdf2",
 *     summary="Rozdelenie PDF dokumentu",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF súbor"),
 *                 @OA\Property(property="split_at", type="integer", description="Strana, kde rozdeliť PDF (od 1)")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="ZIP archív s dvoma PDF časťami"),
 *     @OA\Response(response=500, description="Chyba pri rozdelení PDF")
 * )
 */
    public function splitPdf(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
                'split_at' => 'required|integer|min:1',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $output1 = "$uploadDir/split_part1.pdf";
            $output2 = "$uploadDir/split_part2.pdf";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/split_pdf.py'),
                $inputPath,
                $request->input('split_at'),
                $output1,
                $output2
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri rozdeľovaní PDF.');
                \Log::error('split_pdf.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($output1) || !file_exists($output2)) {
                session()->flash('error', 'Nevytvorili sa všetky výstupné PDF súbory.');
                return back();
            }

            // Vytvor zip s oboma PDF
            $zipPath = "$uploadDir/split_output.zip";
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                $zip->addFile($output1, 'cast1.pdf');
                $zip->addFile($output2, 'cast2.pdf');
                $zip->close();
            } else {
                session()->flash('error', 'Nepodarilo sa vytvoriť ZIP archív.');
                return back();
            }

            return response()->download($zipPath, 'rozdeleny.pdf.zip')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('splitPdf výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár na rozdelenie PDF.
 *
 * @return \Illuminate\View\View
 */
    public function showSplitPdfForm()
    {
        return view('split_pdf');
    }
  /**
 * @OA\Post(
 *     path="/api/extract-text2",
 *     summary="Extrakcia textu z PDF",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF súbor")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Textový súbor s extrahovaným obsahom"),
 *     @OA\Response(response=500, description="Chyba pri extrakcii textu")
 * )
 */
    public function extractTextFromPdf(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputText = "$uploadDir/extracted.txt";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/extract_text_from_pdf.py'),
                $inputPath,
                $outputText
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri extrakcii textu z PDF.');
                \Log::error('extract_text_from_pdf.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($outputText)) {
                session()->flash('error', 'Výstupný textový súbor sa nevytvoril.');
                return back();
            }

            return response()->download($outputText, 'extrahovany_text.txt')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('extractTextFromPdf výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár na extrakciu textu z PDF.
 *
 * @return \Illuminate\View\View
 */
    public function showExtractTextForm()
    {
        return view('extract_text');
    }
/**
 * @OA\Post(
 *     path="/api/pdf-to-images2",
 *     summary="Konverzia PDF na obrázky",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF dokument")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="ZIP archív s obrázkami"),
 *     @OA\Response(response=500, description="Chyba pri konverzii PDF na obrázky")
 * )
 */
    public function pdfToImages(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $imageDir = "$uploadDir/images";
            $zipPath = "$uploadDir/pdf_obrazky.zip";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/pdf_to_images.py'),
                $inputPath,
                $imageDir
            ]);

            $process->setTimeout(90);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri konverzii PDF na obrázky.');
                \Log::error('pdf_to_images.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($imageDir) || count(glob("$imageDir/*.png")) === 0) {
                session()->flash('error', 'Obrázky sa nevytvorili.');
                return back();
            }

            // Vytvor ZIP archív z obrázkov
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                foreach (glob("$imageDir/*.png") as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
            } else {
                session()->flash('error', 'Nepodarilo sa vytvoriť ZIP archív.');
                return back();
            }

            return response()->download($zipPath, 'stranky_obrazky.zip')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('pdfToImages výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár na konverziu PDF na obrázky.
 *
 * @return \Illuminate\View\View
 */
    public function showPdfToImagesForm()
    {
        return view('pdf_to_images');
    }
/**
 * @OA\Post(
 *     path="/api/extract-page2",
 *     summary="Extrahovanie jednej strany z PDF",
 *     tags={"PDF"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="pdf_file", type="string", format="binary", description="PDF súbor"),
 *                 @OA\Property(property="page_number", type="integer", description="Číslo strany (od 1)")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="PDF so zvolenou stranou"),
 *     @OA\Response(response=500, description="Chyba pri extrahovaní strany")
 * )
 */
    public function extractPage(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
                'page_number' => 'required|integer|min:1',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            $outputPdf = "$uploadDir/extracted_page.pdf";

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    session()->flash('error', 'Laravel nemôže vytvoriť priečinok pre upload.');
                    return back();
                }
            }

            $pdf = $request->file('pdf_file');
            $originalName = $pdf->getClientOriginalName();
            $inputPath = "$uploadDir/$originalName";
            $pdf->move($uploadDir, $originalName);

            $process = new \Symfony\Component\Process\Process([
                '/var/www/skuskove2/venv/bin/python3',
                base_path('scripts/extract_page.py'),
                $inputPath,
                $request->input('page_number'),
                $outputPdf
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                session()->flash('error', 'Chyba pri extrakcii strany z PDF.');
                \Log::error('extract_page.py error', [
                    'stdout' => $process->getOutput(),
                    'stderr' => $process->getErrorOutput(),
                    'exit' => $process->getExitCode()
                ]);
                return back();
            }

            if (!file_exists($outputPdf)) {
                session()->flash('error', 'Výstupný PDF súbor sa nevytvoril.');
                return back();
            }

            return response()->download($outputPdf, 'extrahovana_strana.pdf')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('extractPage výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back();
        }
    }
    /**
 * Zobrazí formulár na extrakciu jednej strany z PDF.
 *
 * @return \Illuminate\View\View
 */
    public function showExtractPageForm()
    {
        return view('extract_page');
    }



}
