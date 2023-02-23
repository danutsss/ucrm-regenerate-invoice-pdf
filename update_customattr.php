<?php

require_once __DIR__ . '/vendor/autoload.php';


use App\Service\UcrmApi;

chdir(__DIR__);


$api = new UcrmApi();

$parameters = [
    'customAttributeKey' => 'factRegenerata',
    'customAttributeValue' => '1',
];

$invoices =  $api::doRequest("invoices?" . http_build_query($parameters));

$count = 0;
foreach ($invoices as $invoice) {
    $invoiceId = $invoice['id'];
    if (($count % 100) == 0) {
        print("$invoiceId - date: " . date('h:i:s') . "\n");
        sleep(2);


        print("slept for 2 seconds\n\n");
        print("$invoiceId - date: " . date('h:i:s') . "\n\n");
    }
    $count++;
}
var_dump($count);
