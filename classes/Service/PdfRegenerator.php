<?php

declare(strict_types=1);

namespace App\Service;

use Ubnt\UcrmPluginSdk\Service\UcrmApi;


class PdfRegenerator
{
    /**
     * @var UcrmApi
     */
    private $ucrmApi;

    public function __construct(UcrmApi $ucrmApi)
    {
        $this->ucrmApi = UcrmApi::create();
    }

    public function regenerate(array $invoices): void
    {
        foreach ($invoices as $invoice) {
            $invoiceId = $invoice['id'];
            $updateTemplate = $this->ucrmApi->patch("invoices/$invoiceId", [
                'invoiceTemplateId' => 1000
            ]);

            $updatedTemplateId = $updateTemplate['id'];
            $regeneratePdf = $this->ucrmApi->patch("invoices/$updatedTemplateId/regenerate-pdf");

            var_dump($regeneratePdf);
        }
    }
}
