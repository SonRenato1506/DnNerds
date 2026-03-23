<?php

if (!isset($_POST['texto'])) {
    die("Texto não enviado.");
}

$texto = $_POST['texto'];

$apiKey = "sk_a5f3614727d7a4ee2096497cc018c0bece7c350a3013fe89";

$url = "https://api.elevenlabs.io/v1/text-to-speech/21m00Tcm4TlvDq8ikWAM";

$data = [
    "text" => $texto,
    "model_id" => "eleven_multilingual_v2"
];

$headers = [
    "Accept: audio/mpeg",
    "Content-Type: application/json",
    "xi-api-key: $apiKey"
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => $headers
]);

$response = curl_exec($ch);

if(curl_errno($ch)){
    echo curl_error($ch);
    exit;
}

curl_close($ch);

header("Content-Type: audio/mpeg");
echo $response;