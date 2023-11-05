<?php

namespace Feadmin\Enums;

enum HasOwnerEnum: int
{
    case CREATED_BY = 1;
    case UPDATED_BY = 2;
    case DELETED_BY = 3;
}
