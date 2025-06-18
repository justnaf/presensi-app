<?php

namespace App\Exports;

use App\Models\EventAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EventAttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected int $eventId;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Mendefinisikan header kolom Excel.
     */
    public function headings(): array
    {
        return [
            'Nama Event',
            'Nama Peserta',
            'Asal Instansi',
            'Kode Pindai',
            'Waktu Kehadiran',
            'Longitude',
            'Latitude',
        ];
    }

    /**
     * Mengambil data dari database dengan relasi yang diperlukan.
     */
    public function collection()
    {
        return EventAttendance::where('event_id', $this->eventId)
            ->with(['event:id,name', 'user.institutions:id,name'])
            ->get();
    }

    /**
     * Memetakan data untuk setiap baris di Excel.
     *
     * @param EventAttendance $attendance
     */
    public function map($attendance): array
    {
        // Logika untuk menentukan nama institusi
        $institutionName = $attendance->user?->institutions->first()?->name ?? $attendance->origin_institution;

        return [
            $attendance->event->name,
            $attendance->name, // Nama sudah disiapkan saat check-in
            $institutionName,
            $attendance->scanned_barcode_value,
            $attendance->scanned_at->format('d M Y, H:i:s'),
            $attendance->longitude,
            $attendance->latitude,
        ];
    }
}
