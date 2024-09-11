<?php
namespace App\Services;

use App\Models\Client;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function generateLoyaltyCard(Client $client)
    {
        // Préparer les informations à encoder dans le QR code
        $data = [
            'surname' => $client->surname,
            'telephone' => $client->telephone,
            'adresse' => $client->adresse,
        ];

        $jsonData = json_encode($data);

        // Générer le QR code
        $result = Builder::create()
            ->writer(new PngWriter()) // Définir le format de sortie comme PNG
            ->data($jsonData) // Les données à encoder
            ->encoding(new Encoding('UTF-8')) // L'encodage du texte
            // ->errorCorrectionLevel(new ErrorCorrectionLevelLow()) // Niveau de correction d'erreurs
            ->size(200) // Taille du QR code
            ->margin(10) // Marge autour du QR code
            // ->roundBlockSizeMode(new RoundBlockSizeModeMargin()) // Mode de taille des blocs
            ->build(); // Construire le QR code

        // Obtenir l'image en base64
        $base64QrCode = base64_encode($result->getString());

        // Stocker le QR code dans le client
        $client->loyalty_card_qr_code = 'data:image/png;base64,' . $base64QrCode;
        $client->save();
    }
}
