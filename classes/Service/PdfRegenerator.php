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
            $this->ucrmApi::doRequest("invoices/$invoiceId/regenerate-pdf", 'PATCH');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
