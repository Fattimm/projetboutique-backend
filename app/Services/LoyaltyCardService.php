<?php

namespace App\Services;

use Dompdf\Dompdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use App\Models\User;
use App\Models\Client;
use App\Services\ClientServiceImpl;

class LoyaltyCardService
{
    public function createLoyaltyCard(Client $client, string $qrCodePath, User $user,String $photoPath): string
    {

        $html = $this->generateLoyaltyCardHtml($client, $qrCodePath, $photoPath);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = "carte_fidelite_{$client->surname}.pdf";
        $filePath = storage_path("app/public/Loyalty_cards/{$fileName}");

        file_put_contents($filePath, $dompdf->output());

        return $filePath;
    }


    private function generateLoyaltyCardHtml(Client $client, string $qrCodePath, string $photoPath): string
    {

        // Lire les fichiers pour les convertir en base64
        $qrCodeContent = file_get_contents(storage_path("app/public/qr_codes/" . basename($qrCodePath)));
        $photoContent = file_get_contents(storage_path("app/public/photos/" . basename($photoPath)));


        if ($qrCodeContent === false || $photoContent === false) {
            throw new \Exception("Impossible de lire les fichiers QR code ou photo.");
        }

        // Convertir les images en base64
        $qrCodeBase64 = base64_encode($qrCodeContent);
        $photoBase64 = base64_encode($photoContent);

        return "
            <html lang='fr'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Carte de fidélité</title>
                <style>
                    body {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        background-color: #f0f0f0;
                        font-family: Arial, sans-serif;
                    }
                    .card {
                        width: 300px;
                        background-color: white;
                        border-radius: 20px;
                        padding: 20px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                        position: relative;
                        overflow: hidden;
                        text-align: center;
                    }
                    .title {
                        color: #8a2be2;
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 20px;
                    }
                    .photo-container {
                        width: 100px;
                        height: 100px;
                        border-radius: 50%;
                        overflow: hidden;
                        margin: 0 auto 20px;
                        border: 3px solid #8a2be2;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                    .photo {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                    .name, .tel {
                        font-size: 16px;
                        margin-bottom: 10px;
                    }
                    .qr-code {
                        width: 150px;
                        height: 150px;
                        margin: 20px auto 0;
                    }
                    .qr-code img {
                        width: 100%;
                        height: 100%;
                    }
                    .background-shapes {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        z-index: -1;
                        overflow: hidden;
                    }
                    .shape {
                        position: absolute;
                        border-radius: 50%;
                    }
                    .shape-1 {
                        width: 100px;
                        height: 100px;
                        background-color: rgba(138, 43, 226, 0.1);
                        top: -50px;
                        left: -50px;
                    }
                    .shape-2 {
                        width: 150px;
                        height: 150px;
                        background-color: rgba(255, 165, 0, 0.1);
                        bottom: -75px;
                        right: -75px;
                    }
                    .shape-3 {
                        width: 80px;
                        height: 80px;
                        background-color: rgba(255, 0, 255, 0.1);
                        bottom: 20px;
                        left: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='card'>
                    <div class='background-shapes'>
                        <div class='shape shape-1'></div>
                        <div class='shape shape-2'></div>
                        <div class='shape shape-3'></div>
                    </div>
                    <div class='title'>Carte de fidélité</div>
                    <div class='photo-container'>
                        <img src='data:image/jpeg;base64,{$photoBase64}' alt='Photo du Client' class='photo'/>
                    </div>
                    <div class='name'>Nom : {$client->surname}</div>
                    <div class='tel'>Téléphone : {$client->telephone}</div>
                    <div class='qr-code'>
                        <img src='data:image/png;base64,{$qrCodeBase64}' alt='QR Code'/>
                    </div>
                </div>
            </body>
        </html>
        ";
    }
}
