<?php

return [
    'pdf_generator' => [
        'endpoint' => env('NODE_SERVICES_URL', 'http://pdf-generator:3000') . '/generate',  
    ],
];