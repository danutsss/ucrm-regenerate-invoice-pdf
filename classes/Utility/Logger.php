<?php

declare(strict_types=1);

namespace App\Utility;

use Psr\Log\LogLevel;
use Psr\Log\LoggerTrait;
use Psr\Log\LoggerInterface;
use Ubnt\UcrmPluginSdk\Service\PluginLogManager;

final class Logger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var PluginLogManager
     */
    private $pluginLogManager;

    public function __construct(PluginLogManager $pluginLogManager)
    {
        $this->pluginLogManager = $pluginLogManager;
    }

    public function log($level, $message, array $context = []): void
    {
        $level = $level !== LogLevel::INFO
            ? mb_strtoupper((string) $level, 'UTF-8') . ': '
            : '';

        $this->pluginLogManager->appendLog(
            $level . $message,
            $context
        );
    }
}
