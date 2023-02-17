<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\UcrmApi;

chdir(__DIR__);

class PdfRegenerator
{
    /**
     * @var UcrmApi
     */
    private $ucrmApi;

    public function __construct(UcrmApi $ucrmApi)
    {
        $this->ucrmApi = new UcrmApi();
    }

    public function generateView(array $invoices): void
    {
        $renderer = new TemplateRenderer();
        $renderer->render(
            __DIR__ .
                '/../../templates/pdf-regenerator.php',
            [
                'invoices' => $invoices,
            ]
        );
    }

    public function regeneratePdf(int $invoiceId): void
    {
        try {
            $this->ucrmApi::doRequest("invoices/$invoiceId/regenerate-pdf", 'PATCH');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function updateInvoice(int $invoiceId): void
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "{$_ENV['API_URL']}/invoices/$invoiceId");
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

            curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                \"attributes\": [
                    {
                    \"value\": \"1\",
                    \"customAttributeId\": 21
                    }
                ]
                }");

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "X-Auth-App-Key: {$_ENV['API_KEY']}"
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            var_dump($response);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
