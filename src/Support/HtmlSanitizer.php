<?php

namespace Feadmin\Support;

use Illuminate\Support\HtmlString;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonyHtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class HtmlSanitizer
{
    protected HtmlSanitizerConfig $config;

    public function __construct()
    {
        $this->config = $this->defaultConfig();
    }

    public function defaultConfig(): HtmlSanitizerConfig
    {
        return (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowStaticElements()
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeMedias();
    }

    public function sanitize(string|HtmlString $input): string
    {
        $sanitizer = new SymfonyHtmlSanitizer($this->config);

        return $sanitizer->sanitize($input);
    }
}