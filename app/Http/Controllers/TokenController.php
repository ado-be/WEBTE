<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;

class TokenController extends Controller
{
    /**
     * Zobrazí zoznam API tokenov používateľa
     */
    public function index()
    {
        $tokens = Auth::user()->tokens;
        return view('tokens.index', compact('tokens'));
    }

    /**
     * Vytvorí nový API token pre používateľa
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = Auth::user()->createToken($request->name);

        // Zaznamenanie aktivity
        if (class_exists('\App\Models\UserActivity')) {
            UserActivity::create([
                'user_id' => Auth::id(),
                'action' => 'Vytvorenie API tokenu',
                'source' => 'web',
                'ip' => $request->ip(),
                'location' => 'Token Management',
            ]);
        }

        return back()->with('token', $token->plainTextToken);
    }

    /**
     * Zruší (vymaže) existujúci API token
     */
    public function destroy($id)
    {
        Auth::user()->tokens()->where('id', $id)->delete();

        // Zaznamenanie aktivity
        if (class_exists('\App\Models\UserActivity')) {
            UserActivity::create([
                'user_id' => Auth::id(),
                'action' => 'Zrušenie API tokenu',
                'source' => 'web',
                'ip' => request()->ip(),
                'location' => 'Token Management',
            ]);
        }

        return back()->with('status', 'Token bol úspešne zrušený');
    }
}