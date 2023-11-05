<?php

namespace Feadmin\Enums;

enum PostStatusEnum: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;
    case ARCHIVED = 2;
}
