<?php

return [
    'platform_admin_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PLATFORM_ADMIN_EMAILS', 'admin@nexora.local')),
    ))),
];
