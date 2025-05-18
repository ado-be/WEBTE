<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserActivity; // Chýbajúci import
use Illuminate\Support\Facades\Http; // Pre HTTP požiadavky
use Illuminate\Support\Facades\Log; // Pre logovanie

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        UserActivity::create([
            'user_id' => $event->user->id,
            'action' => 'Prihlásenie do systému',
            'source' => 'auth',
            'ip' => request()->ip(),
            'location' => $this->getLocationFromIp(request()->ip()),
        ]);
    }

    /**
     * Get location information from IP address.
     *
     * @param string $ip
     * @return string
     */
    protected function getLocationFromIp($ip)
    {
        try {
            // Použitie bezplatného API na získanie informácií o lokácii
            $response = Http::get("http://ip-api.com/json/{$ip}");
            $data = $response->json();

            if ($response->successful() && isset($data['city']) && isset($data['country'])) {
                return $data['city'] . ', ' . $data['country'];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get location from IP: ' . $e->getMessage());
        }

        return 'Neznáma lokácia';
    }
}