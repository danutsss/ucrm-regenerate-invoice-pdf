<?php

declare(strict_types=1);

use App\Http;
use Dotenv\Dotenv;
use Psr\Log\LogLevel;
use Twig\Environment;
use App\Utility\Logger;
use App\Service\UcrmApi;
use App\Service\PdfRegenerator;
use Twig\Loader\FilesystemLoader;
use Ubnt\UcrmPluginSdk\Service\UcrmSecurity;
use Ubnt\UcrmPluginSdk\Security\PermissionNames;
use Ubnt\UcrmPluginSdk\Service\PluginLogManager;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Instantiate Twig template renderer.
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

// Instantiate API connection.
$api = new UcrmApi();

// Ensure that user is logged in and has permission to view invoices.
$security = UcrmSecurity::create();
$user = $security->getUser();
if (!$user || $user->isClient || !$user->hasViewPermission(PermissionNames::BILLING_INVOICES)) {
    Http::forbidden();
}

// Process submitted form.
if (isset($_GET['organization']) && isset($_GET['since']) && isset($_GET['until'])) {
    $parameters = [
        'organizationId' => $_GET['organization'],
        'createdDateFrom' => date_format(date_create($_GET['since']), 'Y-m-d'),
        'createdDateTo' => date_format(date_create($_GET['until']), 'Y-m-d'),
        'proforma' => 0,
    ];

    $pdfRegenerator = new PdfRegenerator($api);
    $invoices = $api::doRequest("invoices?" . http_build_query($parameters));
    $pdfRegenerator->generateView($invoices);

    exit;
}

// Process regenerate request.
if (isset($_GET['regenerate'])) {
    $pdfRegenerator = new PdfRegenerator($api);
    $logger = new Logger(new PluginLogManager());

    $invoiceIds = is_array($_GET['regenerate']) ? $_GET['regenerate'] : [$_GET['regenerate']];

    $count = 0;
    foreach ($invoiceIds as $invoiceId) {
        try {
            sleep($count % 100 === 0 ? 2 : 0);
            $response = $pdfRegenerator->regeneratePdf(intval($invoiceId));
        } catch (\Exception $e) {
            $logger->log(LogLevel::ERROR, "Eroare la regenerarea PDF-ului facturii cu ID-ul $invoiceId.");
            $logger->log(LogLevel::ERROR, $e->getMessage());
            $logger->log(LogLevel::ERROR, $e->getTraceAsString());
        }

        $count++;
    }

    var_dump("Regenerated $count invoices.");

    exit;
}

// Render form.
$organizations = $api::doRequest('organizations');

$optionsManager = UcrmOptionsManager::create();

echo $twig->render(
    'form.twig.html',
    [
        'title' => 'Regenerare PDF facturi',
        'organizations' => $organizations,
        'ucrmPublicUrl' => $optionsManager->loadOptions()->ucrmPublicUrl,
    ]
);
