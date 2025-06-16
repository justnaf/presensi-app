<?php

namespace App\Http\Requests\Admin\Events;

// Note: This is identical to StoreEventRequest for this schema.
// It's kept separate for future flexibility (e.g., if a field can't be changed after creation).
class UpdateEventRequest extends StoreEventRequest
{
    // You can override rules here if needed for updates
}
