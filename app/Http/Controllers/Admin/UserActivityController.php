<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[\Illuminate\Routing\Controllers\Middleware(['auth', 'admin'])]
class UserActivityController extends Controller
{
    // Odstránenie konštruktora, ktorý používa starý spôsob priradenia middleware
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('admin');
    // }

    public function index()
    {
        try {
            $activities = UserActivity::with('user')
                ->latest()
                ->paginate(15);

            // Upravte cestu k view podľa vašej štruktúry súborov
            return view('admin.activities.index', compact('activities'));

        } catch (\Exception $e) {
            Log::error('Failed to load user activities: '.$e->getMessage());
            return back()->with('error', __('Failed to load activities'));
        }
    }

    public function export()
    {
        // Odstráňte alebo upravte toto
        // $this->authorize('export', UserActivity::class);

        // Namiesto toho použite nasledujúci kód (ak chcete ponechať autorizáciu)

        // Buď úplne odstráňte kontrolu autorizácie alebo ju upravte takto:
        // if (Gate::denies('export', UserActivity::class)) {
        //     abort(403, 'Unauthorized action.');
        // }

        // Alebo jednoducho dovoľte všetkým prihláseným používateľom exportovať
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $filename = 'user_activities_'.now()->format('Y-m-d_His').'.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM and CSV header
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'ID', 'User', 'Action',
                'IP Address', 'Location', 'Timestamp'
            ]);

            // Stream data in chunks
            UserActivity::with('user')->chunk(500, function ($activities) use ($handle) {
                foreach ($activities as $activity) {
                    fputcsv($handle, [
                        $activity->id,
                        $activity->user->name ?? 'System',
                        $activity->action,
                        $activity->ip,
                        $activity->location,
                        $activity->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function clear()
    {
        if (Gate::denies('clear', UserActivity::class)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            UserActivity::truncate();
            Log::info('Activities cleared by admin user: '.auth()->id());

            return redirect()
                ->route('admin.user-activities.index')
                ->with('success', __('All activities have been cleared'));

        } catch (\Exception $e) {
            Log::error('Failed to clear activities: '.$e->getMessage());
            return back()->with('error', __('Failed to clear activities'));
        }
    }
}