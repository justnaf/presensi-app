<?php

namespace App\Exports;

use App\Models\EventAttendee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventAttendeesExport implements FromCollection, WithHeadings, WithMapping
{
    protected int $eventId;

    /**
     * Menerima event ID untuk memfilter peserta.
     */
    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Mendefinisikan header untuk kolom Excel.
     */
    public function headings(): array
    {
        return [
            'Nama Peserta',
            'Kode Tiket',
            'Tanggal Registrasi',
        ];
    }

    /**
     * Mengambil data peserta dari database.
     */
    public function collection()
    {
        return EventAttendee::where('event_id', $this->eventId)
            ->with('user:id,name') // Eager load untuk efisiensi
            ->select('id', 'user_id', 'ticket_code', 'registered_at')
            ->get();
    }

    /**
     * Memetakan setiap baris data ke format yang diinginkan.
     *
     * @param EventAttendee $attendee
     */
    public function map($attendee): array
    {
        return [
            $attendee->user->name,
            $attendee->ticket_code,
            // Format tanggal agar mudah dibaca di Excel
            $attendee->registered_at->format('d-m-Y H:i:s'),
        ];
    }
}
