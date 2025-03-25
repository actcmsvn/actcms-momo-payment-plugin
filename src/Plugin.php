<?php

namespace ACTCMS\MoMo;

use ACTCMS\PluginManagement\Abstracts\PluginOperationAbstract;
use ACTCMS\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::query()
            ->whereIn('key', [
                'payment_via_momo',
                'momo_description',
                'after_service_registration_msg',
                'enter_partner_code_and_secret',
                'momo_access_key',
                'momo_secret_key',
                'momo_public_key',
                'momo_mode',
            ])
            ->delete();
    }
}
