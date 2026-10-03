<?php

namespace App\Services;

use App\Models\Ticket;

class QrCodeService
{
    public function generate(Ticket $ticket): string
    {
        $data = $ticket->qrData();

        // Use Google Charts API as fallback (no GD required)
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($data).'&color=7C3AED';

        return $url;
    }

    public function generateInline(Ticket $ticket): string
    {
        $data = $ticket->qrData();
        $url = 'https://api.qrserver.com/v1/create-qr-code/';

        $svgUrl = $url.'?size=300x300&data='.urlencode($data).'&format=svg&color=7C3AED';

        // Return inline SVG if possible, fallback to img
        return $svgUrl;
    }
}
