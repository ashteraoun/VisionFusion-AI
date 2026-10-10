<?php

return [
    'max_size_mb' => (int) env('MAX_IMAGE_UPLOAD_MB', 10),
    'max_size_kb' => (int) env('MAX_IMAGE_UPLOAD_MB', 10) * 1024,
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
];
