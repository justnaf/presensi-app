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
        // Catat error sebenarnya di file log untuk developer
        Log::error($e->getMessage() . ' - ' . $e->getTraceAsString());

        // Redirect pengguna kembali dengan pesan yang ramah
        return back()->with('error', $message);
    }
}
