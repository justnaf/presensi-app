<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Mendefinisikan relasi "satu ke banyak" dengan model Event.
     * Satu kategori bisa memiliki banyak event.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }
}
