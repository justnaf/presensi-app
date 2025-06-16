<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsWithFlash; // 1. Import the Trait
use App\Http\Requests\Admin\Events\StoreEventCategoryRequest;
use App\Http\Requests\Admin\Events\UpdateEventCategoryRequest;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    use RedirectsWithFlash; // 2. Use the Trait
    /**
     * Apply Spatie Permission middleware to all methods in this controller.
     */
    public function __construct()
    {
        $this->middleware('permission:view events', ['only' => ['index']]);
        $this->middleware('permission:create events', ['only' => ['store']]);
        $this->middleware('permission:edit events', ['only' => ['update']]);
        $this->middleware('permission:delete events', ['only' => ['destroy']]);
    }

    public function index(Request $request): Response
    {
        // Get search filters from the request
        $filters = $request->only('search');

        $categories = EventCategory::query() // Start with query() to build upon it
            // Apply filter only if search input exists
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Append query string to pagination links

        return Inertia::render('Admin/Events/Categories', [
            'categories' => $categories,
            'filters' => $filters, // Always pass filters back to the frontend
        ]);
    }

    public function store(StoreEventCategoryRequest $request): RedirectResponse
    {
        try {
            EventCategory::create($request->validated());
            // 3. Use the trait for success redirect
            return $this->redirectSuccess('admin.event-categories.index', 'Category created successfully.');
        } catch (\Exception $e) {
            // 4. Use the trait for error redirect
            return $this->redirectError($e, 'Failed to create category.');
        }
    }

    public function update(UpdateEventCategoryRequest $request, EventCategory $event_category): RedirectResponse
    {
        try {
            $event_category->update($request->validated());
            return $this->redirectSuccess('admin.event-categories.index', 'Category updated successfully.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Failed to update category.');
        }
    }

    public function destroy(EventCategory $event_category): RedirectResponse
    {
        try {
            $event_category->delete();
            return $this->redirectSuccess('admin.event-categories.index', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Failed to delete category.');
        }
    }
}
