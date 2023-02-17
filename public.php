<?php

declare(strict_types=1);

use App\Service\PdfRegenerator;
use App\Service\TemplateRenderer;
use Ubnt\UcrmPluginSdk\Security\PermissionNames;
use Ubnt\UcrmPluginSdk\Service\UcrmApi;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;
use Ubnt\UcrmPluginSdk\Service\UcrmSecurity;

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';

// Retrieve API connection.
$api = UcrmApi::create();

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
        'proforma' => false
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

    $invoices = $api->get('invoices', $parameters);

    $pdfRegenerator->generateView($invoices);

    exit;
}

// Process regenerate request.
if (array_key_exists('regenerate', $_GET)) {
    $pdfRegenerator = new PdfRegenerator($api);

    $invoices = $api->get('invoices', ['id' => $_GET['regenerate']]);

    $pdfRegenerator->regenerate($invoices);

    exit;
}

// Render form.
$organizations = $api->get('organizations');

$optionsManager = UcrmOptionsManager::create();

$renderer = new TemplateRenderer();
$renderer->render(
    __DIR__ . '/templates/form.php',
    [
        'organizations' => $organizations,
        'ucrmPublicUrl' => $optionsManager->loadOptions()->ucrmPublicUrl,
    ]
);
