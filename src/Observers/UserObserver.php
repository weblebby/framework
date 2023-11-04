<?php

namespace Feadmin\Observers;

use App\Models\User;
use Feadmin\Facades\Localization;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->locale_id = Localization::getCurrentLocaleId();
    }
}
