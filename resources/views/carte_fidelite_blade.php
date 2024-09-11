<!DOCTYPE html>
<html>
<head>
    <style>
        .card {
            width: 600px;
            height: 300px;
            border: 1px solid #ddd;
            padding: 20px;
            display: flex;
            justify-content: space-between;
        }
        .photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
        }
        .photo img {
            width: 100%;
            height: auto;
        }
        .details {
            flex: 1;
            padding-left: 20px;
        }
        .qrcode {
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="photo">
            <img src="{{ $photoUrl }}" alt="Photo du client">
        </div>
        <div class="details">
            <h2>{{ $client->name }}</h2>
            <p>ID: {{ $client->id }}</p>
            <!-- Ajouter d'autres détails si nécessaire -->
        </div>
        <div class="qrcode">
            <img src="{{ $qrCodeUrl }}" alt="Code QR">
        </div>
    </div>
</body>
</html>
        