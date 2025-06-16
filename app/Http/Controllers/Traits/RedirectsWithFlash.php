<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

trait RedirectsWithFlash
{
    /**
     * Redirect ke halaman index dengan pesan sukses.
     */
    protected function redirectSuccess(string $routeName, string $message): RedirectResponse
    {
        return redirect()->route($routeName)
            ->with('success', $message);
    }

    /**
     * Redirect kembali ke halaman sebelumnya dengan pesan error.
     */
    protected function redirectError(\Exception $e, string $message): RedirectResponse
    {
        // Catat error sebenarnya yang lengkap di file log untuk developer
        Log::error($e->getMessage() . ' - ' . $e->getTraceAsString());

        // Buat pesan error dasar
        $errorMessage = $message;

        // Jika mode debug aktif (di file .env), tambahkan detail error
        if (config('app.debug')) {
            $errorMessage .= ' Error: ' . $e->getMessage();
        }

        // Redirect pengguna kembali dengan pesan yang sudah diformat
        return back()->with('error', $errorMessage);
    }
}
