<?php

return [
    'proxies' => '*',
    'headers' => [
        \Illuminate\Http\Request::HEADER_FORWARDED => \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT,
    ],
];
