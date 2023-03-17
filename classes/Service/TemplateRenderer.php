<?php

declare(strict_types=1);

namespace App\Service;

class TemplateRenderer
{
    public function render(string $template, array $parameters): void
    {
        extract($parameters, EXTR_SKIP);

        require $template;
    }
}
