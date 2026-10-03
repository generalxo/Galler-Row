<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Make Command
    |---------------------------------------------------------------------------
    | Always generate class-based components: a PHP class in app/Livewire and
    | a separate Blade view in resources/views/livewire.
    |
    */

    'make_command' => [
        'type' => 'class',
        'emoji' => false,
        'with' => [
            'js' => false,
            'css' => false,
            'test' => false,
        ],
    ],

];
