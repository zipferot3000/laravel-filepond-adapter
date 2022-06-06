<?php

return [
    'filesystem' => env('FP_ADAPTER_TEMPORARY_FS', 'temporary'),
    'media_collection' => env('FP_ADAPTER_MC', 'temporary_files'),
    'custom_property_name' => env('FP_ADAPTER_CP_NAME', 'file_type'),
];