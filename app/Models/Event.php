<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'about',
        'status',
        'location_name',
        'location_url',
        'longitude',
        'latitude',
        'speaker',
        'poster_image',
        'max_attendees',
        'start_date',
        'end_date',
        'category_id',
        'type',
        'attendance_mode',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'longitude' => 'decimal:7',
        'latitude' => 'decimal:7',
    ];

    /**
     * Mendefinisikan relasi "milik" dengan model EventCategory.
     * Sebuah event milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    /**
     * Mendefinisikan relasi "satu ke banyak" dengan model EventRundown.
     * Satu event bisa memiliki banyak rundown.
     */
    public function rundowns(): HasMany
    {
        return $this->hasMany(EventRundown::class);
    }

    /**
     * Mendefinisikan relasi "satu ke banyak" dengan model EventAttendee (peserta terdaftar).
     * Satu event bisa memiliki banyak peserta terdaftar.
     */
    public function eventAttendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    /**
     * Mendefinisikan relasi "satu ke banyak" dengan model EventAttendance (kehadiran).
     * Satu event bisa memiliki banyak catatan kehadiran.
     */
    public function attendance(): HasMany
    {
        // Nama tabel eksplisit 'event_attendance' sudah sesuai konvensi,
        // jadi tidak perlu parameter tambahan.
        return $this->hasMany(EventAttendance::class);
    }

    public function staticQrs(): HasMany
    {
        return $this->hasMany(EventStaticQr::class);
    }
}
