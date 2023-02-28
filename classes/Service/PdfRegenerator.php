<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\UcrmApi;
use App\Utility\Logger;
use Ubnt\UcrmPluginSdk\Service\PluginLogManager;

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
            $logger = new \App\Utility\Logger(new PluginLogManager());

            $response = $this->ucrmApi::doRequest("invoices/$invoiceId/regenerate-pdf", 'PATCH');

            if ($response) {
                $logger->log(\Psr\Log\LogLevel::INFO, "PDF-ul facturii cu ID-ul $invoiceId a fost regenerat cu succes.");
            }
        } catch (\Exception $e) {
            $logger->log(\Psr\Log\LogLevel::ERROR, "Eroare la regenerarea PDF-ului facturii cu ID-ul $invoiceId.");
            $logger->log(\Psr\Log\LogLevel::ERROR, $e->getMessage());
            $logger->log(\Psr\Log\LogLevel::ERROR, $e->getTraceAsString());
        }
    }
}
