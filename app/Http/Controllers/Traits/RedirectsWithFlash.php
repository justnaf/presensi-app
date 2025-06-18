<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

trait RedirectsWithFlash
{
    /**
     * Core redirect method for maximum flexibility.
     *
     * @param 'route'|'back'|'to' $type The type of redirect.
     * @param string|null $destination The route name or URL path.
     * @param array $parameters Parameters for the route.
     * @param string|null $messageType The key for the flash message (e.g., 'success', 'error').
     * @param string|null $message The flash message content.
     */
    protected function flashRedirect(string $type, ?string $destination = null, array $parameters = [], ?string $messageType = null, ?string $message = null): RedirectResponse
    {
        $redirector = match ($type) {
            'route' => redirect()->route($destination, $parameters),
            'to'    => redirect($destination),
            default => back(), // 'back' is the default
        };

        if ($messageType && $message) {
            $redirector->with($messageType, $message);
        }

        return $redirector;
    }

    /**
     * Wrapper to redirect to a named route with a success message.
     */
    protected function redirectSuccess(string $routeName, string $message = 'Success!', array $parameters = []): RedirectResponse
    {
        return $this->flashRedirect('route', $routeName, $parameters, 'success', $message);
    }

    /**
     * Wrapper to redirect back with a simple error message for logical failures.
     */
    protected function redirectBackWithError(string $message): RedirectResponse
    {
        return $this->flashRedirect('back', null, [], 'error', $message);
    }

    /**
     * Wrapper to handle unexpected exceptions, log them, and redirect back.
     * Shows detailed error message only in debug mode.
     */
    protected function redirectError(\Exception $e, string $message): RedirectResponse
    {
        Log::error($e->getMessage() . ' - ' . $e->getTraceAsString());

        $errorMessage = $message;
        if (config('app.debug')) {
            $errorMessage .= ' (Error: ' . $e->getMessage() . ')';
        }

        return $this->flashRedirect('back', null, [], 'error', $errorMessage);
    }
}
