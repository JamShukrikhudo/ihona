<?php

return ['base_url' => env('ZOOPLA_BASE_URL', 'https://realtime-listings-api.webservices.zpg.co.uk/live/v1'), 'send_path' => env('ZOOPLA_SEND_PATH', '/property/sendpropertydetails'), 'remove_path' => env('ZOOPLA_REMOVE_PATH', '/property/removeproperty'), 'branch_list_path' => env('ZOOPLA_BRANCH_LIST_PATH', '/property/getbranchpropertylist'), 'certificate' => env('ZOOPLA_CERTIFICATE'), 'key' => env('ZOOPLA_KEY'), 'key_password' => env('ZOOPLA_KEY_PASSWORD'), 'timeout' => env('ZOOPLA_TIMEOUT', 30)];
