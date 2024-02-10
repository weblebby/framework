<?php

namespace Weblebby\Framework\Enums;

enum InjectionTypeEnum: string
{
    case MIDDLEWARE_WEB = 'middleware:web';
    case MIDDLEWARE_API = 'middleware:api';
}
