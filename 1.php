<?php
[
    "algorithm" => 'sha1',
    "appId" => '72928888',
    "service" => 'CallCenter',
    //    "call" => 'User\Profile::getById',
    //    "args" => [],
    "timestamp" => '123123123123',
    "signature" => '4fe3f2640608a55d14f9630eb476a1cea6d9b9da',
    "batch" => [
        [
            "call" => 'User\Profile::getById',
            "args" => ['robert'],
        ],
        [
            "call" => 'User\Profile::getById',
            "args" => ['kille2r'],
        ],
    ],
];