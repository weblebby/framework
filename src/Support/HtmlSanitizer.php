<?php

namespace Weblebby\Framework\Support;

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
            ->allowElement('oembed', ['url', 'maxwidth', 'maxheight', 'format'])
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeMedias();
    }

    public function sanitize(string|HtmlString $input): string
    {
        $sanitizer = new SymfonyHtmlSanitizer($this->config);

        return $sanitizer->sanitize($input);
    }

    public function sanitizeToHtml(string|HtmlString $input): HtmlString
    {
        return new HtmlString($this->sanitize($input));
    }
}
