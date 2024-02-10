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

    public function sanitize(HtmlString|string|null $input): ?string
    {
        if (is_null($input)) {
            return null;
        }

        $sanitizer = new SymfonyHtmlSanitizer($this->config);

        return $sanitizer->sanitize($input);
    }

    public function sanitizeToHtml(HtmlString|string|null $input): ?HtmlString
    {
        if (is_null($input)) {
            return null;
        }

        return new HtmlString($this->sanitize($input));
    }
}
