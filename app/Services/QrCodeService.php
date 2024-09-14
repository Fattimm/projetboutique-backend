<?php
namespace App\Services;

use App\Models\User;
use App\Models\Client;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;

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
            ->size(200) // Taille du QR code
            ->margin(10) // Marge autour du QR code
            ->build(); // Construire le QR code

        // Obtenir l'image en base64
        $base64QrCode = base64_encode($result->getString());

        // Stocker le QR code dans le client
        $client->loyalty_card_qr_code = 'data:image/png;base64,' . $base64QrCode;
        $client->save();
    }
    public function generateQrCode(Client $client, User $user)
    {
        $fileName = "{$client->surname}_qr_code.png";
        $filePath = "public/qr_codes/{$fileName}";

        // Créer les données du QR code avec les informations du client
        $qrData = [
            'Nom' => $user->nom,
            'Prénom' => $user->prenom,
            'Adresse' => $client->adresse,
            'Téléphone' => $client->telephone
        ];

        // Convertir les données en chaîne de caractères
        $qrContent = '';
        foreach ($qrData as $key => $value) {
            $qrContent .= "{$key}: {$value}\n";
        }

        // Générer le QR code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->build();

        $path = storage_path("app/public/qr_codes/{$fileName}");
        Storage::put("public/qr_codes/{$fileName}", $result->getString());

        if (!Storage::exists("public/qr_codes/{$fileName}")) {
            throw new \Exception("Le fichier QR code n'a pas pu être sauvegardé.");
        }

        return Storage::url("public/qr_codes/{$fileName}");
    }
}
