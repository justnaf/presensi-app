<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasFactory;

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'attendee_id',
        'scanned_barcode_value',
        'scanned_at',
        'location',
        'name',
        'origin_institution',
        'longitude',
        'latitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scanned_at' => 'datetime',
        'longitude' => 'decimal:7',
        'latitude' => 'decimal:7',
    ];

    /**
     * Mendapatkan data event dari kehadiran ini.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Mendapatkan data user dari kehadiran ini.
     * (Bisa null jika user dihapus atau jika ini adalah tamu walk-in).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan data pendaftaran (tiket) yang terkait dengan kehadiran ini.
     * Relasi ini sekarang menunjuk ke model EventAttendee.
     */
    public function eventAttendee(): BelongsTo
    {
        return $this->belongsTo(EventAttendee::class, 'attendee_id');
    }
}
