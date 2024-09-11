<?php

namespace App\Repositories;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Repositories\Interfaces\QrCodeRepositoryInterface;

class QrCodeRepository implements QrCodeRepositoryInterface
{
    public function generateQrCode(string $data): string
    {
        $qrCode = QrCode::create($data);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getString();  // Renvoie le contenu du QR code en base64 ou binaire
    }
}
