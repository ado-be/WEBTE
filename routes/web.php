<?php
use App\Http\Middleware\Localization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Http\Controllers\TokenController; // Pridaný import
use App\Http\Controllers\UploadPdfController;
use App\Http\Controllers\LocalizationController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;     //kvoli generovaniu pdf z navodu

Route::get('/navod', function () {
    return view('navod');
})->name('navod');

Route::get('/navod-pdf', function () {
    $manualHtml = view('manual')->render(); // používa priamo manual_pdf.blade.php
    return Pdf::loadHTML($manualHtml)->download('navod-na-pouzitie.pdf');
})->name('download.manual.pdf');

Route::get('/localization/{locale}', LocalizationController::class)->name('localization');
Route::middleware(Localization::class)->group(function () {
    Route::get('/remove_page', [UploadPdfController::class, 'showRemovePageForm']);
    Route::post('/remove_page', [UploadPdfController::class, 'removePage']);
    Route::get('/protect_pdf', [UploadPdfController::class, 'showProtectPdfForm']);
    Route::post('/protect_pdf', [UploadPdfController::class, 'protectPdf']);
    Route::get('/pdf_to_word', [UploadPdfController::class, 'showPdfToWordForm']);
    Route::post('/pdf_to_word', [UploadPdfController::class, 'pdfToWord']);
    Route::get('/pdf_to_pptx', [UploadPdfController::class, 'showPdfToPptxForm']);
    Route::post('/pdf_to_pptx', [UploadPdfController::class, 'pdfToPptx']);
    Route::get('/split_pdf', [UploadPdfController::class, 'showSplitPdfForm']);
    Route::post('/split_pdf', [UploadPdfController::class, 'splitPdf']);
    Route::get('/extract_text', [UploadPdfController::class, 'showExtractTextForm']);
    Route::post('/extract_text', [UploadPdfController::class, 'extractTextFromPdf']);
    Route::get('/pdf_to_images', [UploadPdfController::class, 'showPdfToImagesForm']);
    Route::post('/pdf_to_images', [UploadPdfController::class, 'pdfToImages']);
    Route::get('/extract_page', [UploadPdfController::class, 'showExtractPageForm']);
    Route::post('/extract_page', [UploadPdfController::class, 'extractPage']);
    Route::get('/merge_pdfs', [UploadPdfController::class, 'showMergePdfsForm']);
    Route::post('/merge_pdfs', [UploadPdfController::class, 'mergePdfs']);


//ulozenie obrazkov
    Route::post('/upload-images', function (Request $request) {
        $files = $request->file('images');
        $folderName = $request->input('target_folder');
        $basePath = storage_path("obrazky/$folderName");

        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        foreach ($files as $file) {
            $file->move($basePath, $file->getClientOriginalName());
        }

        return response()->json([
            'success' => true,
            'folder' => "storage/obrazky/$folderName"
        ]);
    });

    Route::post('/upload-images-to-pdf', [UploadPdfController::class, 'generateFromImages'])->middleware('auth');

    Route::view('/merge-pdfs', 'merge-pdfs')->middleware('auth');
    Route::post('/upload-merge-pdfs', [UploadPdfController::class, 'mergePdfs'])->middleware('auth');

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        // Testovacia cesta
        Route::get('/test', function () {
            return view('test');
        });

        // Profilové routy
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // API Token Management routes - pridané nové routes
        Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
        Route::post('/tokens', [TokenController::class, 'create'])->name('tokens.create');
        Route::delete('/tokens/{token}', [TokenController::class, 'destroy'])->name('tokens.destroy');
    });

// Admin sekcia
    Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/user-activities', [UserActivityController::class, 'index'])->name('user-activities.index');
        Route::get('/user-activities/export', [UserActivityController::class, 'export'])->name('user-activities.export');
        Route::post('/user-activities/clear', [UserActivityController::class, 'clear'])->name('user-activities.clear');
    });

//api test
    Route::middleware('auth')->get('/api-test', function () {
        return view('api-test');
    })->name('api.test');

    Route::view('/images-to-pdf', 'images-to-pdf')->middleware('auth');

    require __DIR__ . '/auth.php';
});
//deokumentacia APi, swagger
Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});
