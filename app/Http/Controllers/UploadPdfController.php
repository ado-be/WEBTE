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
            $uploadDir = public_path("uploads/$folderId");
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



}
