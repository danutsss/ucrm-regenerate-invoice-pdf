<?php

declare(strict_types=1);

namespace App\Service;

use Twig\Environment;
use App\Service\UcrmApi;
use Twig\Loader\FilesystemLoader;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;

class PdfRegenerator
{
    /**
     * @var UcrmApi
     */
    private $ucrmApi;

    public function __construct(UcrmApi $ucrmApi)
    {
        $this->ucrmApi = $ucrmApi;
    }

    public function generateView(array $invoices): void
    {
        // Instantiate Twig template renderer.
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $twig = new Environment($loader);

        $optionsManager = UcrmOptionsManager::create();

        echo $twig->render(
            'pdf-regenerator.twig.html',
            [
                'title' => 'Selecteaza facturi pentru regenerare',
                'invoices' => $invoices,
                'ucrmPublicUrl' => $optionsManager->loadOptions()->ucrmPublicUrl,
            ]
        );
    }

    public function regeneratePdf(int $invoiceId): void
    {
        try {
            $this->ucrmApi->doRequest("invoices/$invoiceId/regenerate-pdf", 'PATCH');
        } catch (\Exception $e) {
            throw new \RuntimeException("Error regenerating PDF for invoice with ID $invoiceId: " . $e->getMessage(), 0, $e);
        }
    }
}
