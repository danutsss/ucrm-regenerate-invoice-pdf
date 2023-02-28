<?php

declare(strict_types=1);

use App\Service\UcrmApi;
use App\Service\PdfRegenerator;
use App\Service\TemplateRenderer;
use Ubnt\UcrmPluginSdk\Service\UcrmSecurity;
use Ubnt\UcrmPluginSdk\Security\PermissionNames;
use Ubnt\UcrmPluginSdk\Service\PluginLogManager;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve API connection.
$api = new UcrmApi();

// Ensure that user is logged in and has permission to view invoices.
$security = UcrmSecurity::create();
$user = $security->getUser();
if (!$user || $user->isClient || !$user->hasViewPermission(PermissionNames::BILLING_INVOICES)) {
    \App\Http::forbidden();
}

// Process submitted form.
if (array_key_exists('organization', $_GET) && array_key_exists('since', $_GET) && array_key_exists('until', $_GET)) {
    $parameters = [
        'organizationId' => $_GET['organization'],
        'createdDateFrom' => $_GET['since'],
        'createdDateTo' => $_GET['until'],
        'proforma' => 0,
        'customAttributeKey' => 'factRegenerata',
        'customAttributeValue' => '0',
    ];

    // make sure the dates are in YYYY-MM-DD format
    if ($parameters['createdDateFrom']) {
        $parameters['createdDateFrom'] = new \DateTimeImmutable($parameters['createdDateFrom']);
        $parameters['createdDateFrom'] = $parameters['createdDateFrom']->format('Y-m-d');
    }
    if ($parameters['createdDateTo']) {
        $parameters['createdDateTo'] = new \DateTimeImmutable($parameters['createdDateTo']);
        $parameters['createdDateTo'] = $parameters['createdDateTo']->format('Y-m-d');
    }


    $pdfRegenerator = new PdfRegenerator($api);

    $invoices = $api::doRequest("invoices?" . http_build_query($parameters));

    $pdfRegenerator->generateView($invoices);

    exit;
}

// Process regenerate request.
if (array_key_exists('regenerate', $_GET)) {
    $pdfRegenerator = new PdfRegenerator($api);
    $parameter = [
        'id' => $_GET['regenerate'],
    ];

    $count = 0;
    foreach ($_GET['regenerate'] as $invoiceId) {
        try {
            if (($count % 100) == 0) {
                sleep(2);
                $pdfRegenerator->regeneratePdf(intval($invoiceId));
            } else {
                $pdfRegenerator->regeneratePdf(intval($invoiceId));
            }
        } catch (\Exception $e) {
            $logger = new \App\Utility\Logger(new PluginLogManager());

            $logger->log(\Psr\Log\LogLevel::ERROR, "Eroare la regenerarea PDF-ului facturii cu ID-ul $invoiceId.");
            $logger->log(\Psr\Log\LogLevel::ERROR, $e->getMessage());
            $logger->log(\Psr\Log\LogLevel::ERROR, $e->getTraceAsString());
        }

        $count++;
    }

    var_dump("Regenerated $count invoices.");

    exit;
}

// Render form.
$organizations = $api::doRequest('organizations');

$optionsManager = UcrmOptionsManager::create();

$renderer = new TemplateRenderer();
$renderer->render(
    __DIR__ . '/templates/form.php',
    [
        'organizations' => $organizations,
        'ucrmPublicUrl' => $optionsManager->loadOptions()->ucrmPublicUrl,
    ]
);
