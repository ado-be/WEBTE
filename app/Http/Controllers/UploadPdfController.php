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
                'pdfs.*' => 'required|mimes:pdf',
            ]);

            // Jednoduchý a správny názov priečinka
            $folderName = \Str::uuid()->toString();
            $folderPath = public_path("uploads_merge/$folderName");

            // Vytvor priečinok pre PDF
            mkdir($folderPath, 0755, true);

            $pdfPaths = [];

            // Presuň nahrané PDF súbory
            foreach ($request->file('pdfs') as $pdf) {
                $filename = $pdf->getClientOriginalName();
                $fullPath = $folderPath . '/' . $filename;
                $pdf->move($folderPath, $filename);
                $pdfPaths[] = $fullPath;
            }

            // Výstupný PDF súbor
            $outputPdf = $folderPath . '/merged.pdf';

            // Volanie API cez JSON
            $response = \Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(url('/api/merge-pdfs'), [
                'pdf_paths' => $pdfPaths,
                'output_pdf' => $outputPdf,
            ]);

            if (!$response->ok() || !$response->json('success')) {
                \Log::error('API merge-pdfs failed', ['response' => $response->body()]);
                return response('Chyba pri merge PDF (API).', 500);
            }

            if (!file_exists($outputPdf)) {
                \Log::error('Výstupné PDF nebolo nájdené.', ['path' => $outputPdf]);
                return response('PDF sa nevytvorilo.', 500);
            }

            return response()->download($outputPdf)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Merge PDFs výnimka', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Chyba servera.', 500);
        }
    }

    public function showRemovePageForm()
    {
        return view('remove_page');
    }

    public function handleRemovePage(Request $request)
    {
        try {
            $request->validate([
                'pdf_file' => 'required|mimes:pdf',
                'page' => 'required|integer|min:0',
            ]);

            $uuid = \Str::uuid()->toString();
            $uploadDir = public_path("uploads/$uuid");
            mkdir($uploadDir, 0755, true);

            $pdf = $request->file('pdf_file');
            $originalPath = "$uploadDir/" . $pdf->getClientOriginalName();
            $pdf->move($uploadDir, basename($originalPath));

            $outputPath = "$uploadDir/removed.pdf";

            // Cesta k python3 vo venv
            $pythonPath = '/var/www/novy/venv/bin/python3'; // <- ak sa zmení, uprav

            // Spustenie skriptu
            $process = new \Symfony\Component\Process\Process([
                $pythonPath,
                base_path('scripts/remove_page.py'),
                $originalPath,
                $request->input('page'),
                $outputPath
            ]);

            $process->setTimeout(60); // aby to neviselo večne
            $process->run();

            // Výpis priamo na stránku
            if (!$process->isSuccessful()) {
                return back()->with([
                    'error_exit' => $process->getExitCode(),
                    'error_stdout' => $process->getOutput(),
                    'error_stderr' => $process->getErrorOutput()
                ]);
            }

            if (!file_exists($outputPath)) {
                return back()->with([
                    'error_stderr' => 'Výstupný PDF súbor nebol vytvorený.',
                    'error_exit' => 1
                ]);
            }

            return response()->download($outputPath, 'upraveny.pdf')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with([
                'error_stderr' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
        }
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





}
