<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use App\Service\UcrmApi;

chdir(__DIR__);
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Instantiate API connection.
$api = new UcrmApi();

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "{$_ENV['API_URL']}/invoices/40113/regenerate-pdf");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "X-Auth-App-Key: {$_ENV['API_KEY']}"
));

$response = curl_exec($ch);
curl_close($ch);
