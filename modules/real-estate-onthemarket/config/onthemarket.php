<?php

return ['base_url' => env('ONTHEMARKET_BASE_URL', 'https://realtime-api.onthemarket.com/v1'), 'send_path' => env('ONTHEMARKET_SEND_PATH', '/property/sendpropertydetails'), 'remove_path' => env('ONTHEMARKET_REMOVE_PATH', '/property/removeproperty'), 'branch_list_path' => env('ONTHEMARKET_BRANCH_LIST_PATH', '/property/getbranchpropertylist'), 'certificate' => env('ONTHEMARKET_CERTIFICATE'), 'key' => env('ONTHEMARKET_KEY'), 'key_password' => env('ONTHEMARKET_KEY_PASSWORD'), 'timeout' => env('ONTHEMARKET_TIMEOUT', 30)];
