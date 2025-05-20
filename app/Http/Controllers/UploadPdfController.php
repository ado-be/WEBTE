<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class UploadPdfController extends Controller
{
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
                '/var/www/novy/venv/bin/python3',
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
    public function showMergePdfsForm()
    {
        return view('merge_pdfs');
    }


    public function showRemovePageForm()
    {
        return view('remove_page');
    }



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
                '/var/www/novy/venv/bin/python3',
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
                '/var/www/novy/venv/bin/python3',
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

    public function showProtectPdfForm()
    {
        return view('protect_pdf');
    }

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
                '/var/www/novy/venv/bin/python3',
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
    public function showPdfToWordForm()
    {
        return view('pdf_to_word');
    }

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
                '/var/www/novy/venv/bin/python3',
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
    public function showPdfToPptxForm()
    {
        return view('pdf_to_pptx');
    }

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
                '/var/www/novy/venv/bin/python3',
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
    public function showSplitPdfForm()
    {
        return view('split_pdf');
    }
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
                '/var/www/novy/venv/bin/python3',
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
    public function showExtractTextForm()
    {
        return view('extract_text');
    }

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
                '/var/www/novy/venv/bin/python3',
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
    public function showPdfToImagesForm()
    {
        return view('pdf_to_images');
    }
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
                '/var/www/novy/venv/bin/python3',
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
    public function showExtractPageForm()
    {
        return view('extract_page');
    }



}
