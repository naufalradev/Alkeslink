<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Langflow (lokal di 127.0.0.1:7860)
    |--------------------------------------------------------------------------
    */

    // Full endpoint: http://127.0.0.1:7860/api/v1/run/{flow_id}
    'langflow_url'    => env('LANGFLOW_URL',     'http://127.0.0.1:7860'),
    'langflow_flow'   => env('LANGFLOW_FLOW_ID', '0565cd37-dce2-4115-b81d-9a4f5c7bee7b'),
    'api_key'         => env('AI_API_KEY',        ''),          // x-api-key header

    // AstraDB token dikirim sebagai tweaks ke tiap node
    'astra_token'     => env('ASTRADB_TOKEN', ''),
    // Node IDs yang butuh token (pisahkan koma jika lebih dari satu)
    'astra_node_ids'  => env('ASTRADB_NODE_IDS', 'AstraDB-mzIX6,AstraDB-UCc36'),
];
