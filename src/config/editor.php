<?php

return [
    /**
     * Editor settings
     */
    'editorSettings' => [
        'placeholder' => '',
        'initialBlock' => 'paragraph',
        'autofocus' => false,
    ],

    /**
     * Configure tools
     */
    'toolSettings' => [
        'header' => [
            'activated' => true,
            'shortcut' => 'CMD+SHIFT+H'
        ],
        'list' => [
            'activated' => true,
            'shortcut' => 'CMD+SHIFT+L'
        ],
        'image' => [
            'activated' => true,
            'shortcut' => 'CMD+SHIFT+I',
            'path' => 'public/images',
            'disk' => 'local',
            'alterations' => [
                'resize' => [
                    'width' => false, // integer
                    'height' => false, // integer
                ],
                'optimize' => true, // true or false
                'adjustments' => [
                    'brightness' => false, // -100 to 100
                    'contrast' => false, // -100 to 100
                    'gamma' => false // 0.1 to 9.99
                ],
                'effects' => [
                    'blur' => false, // 0 to 100
                    'pixelate' => false, // 0 to 100
                    'greyscale' => false, // true or false
                    'sepia' => false, // true or false
                    'sharpen' => false, // 0 to 100
                ]
            ],
            'thumbnails' => [
                // Specify as many thumbnails as required. Key is used as the name.
                '_small' => [
                    'resize' => [
                        'width' => 250, // integer
                        'height' => 250, // integer
                    ],
                    'optimize' => true, // true or false
                    'adjustments' => [
                        'brightness' => false, // -100 to 100
                        'contrast' => false, // -100 to 100
                        'gamma' => false // 0.1 to 9.99
                    ],
                    'effects' => [
                        'blur' => false, // 0 to 100
                        'pixelate' => false, // 0 to 100
                        'greyscale' => false, // true or false
                        'sepia' => false, // true or false
                        'sharpen' => false, // 0 to 100
                    ]
                ]
            ]
        ],
        'table' => [
            'activated' => true,
        ],
        'video' => [
            'activated' => true
        ],
        'imageByUrl' => [
            'activated' => true
        ],
    ],

    /**
     * Output validation config
     * https://github.com/editor-js/editorjs-php
     */
    'validationSettings' => [
        'tools' => [
            'header' => [
                'text' => [
                    'type' => 'string',
                ],
                'level' => [
                    'type' => 'int',
                    'canBeOnly' => [2, 3, 4]
                ]
            ],
            'paragraph' => [
                'text' => [
                    'type' => 'string',
                    'allowedTags' => 'i,b,u,a[href],span[class],code[class],mark[class]'
                ]
            ],
            'list' => [
                'style' => [
                    'type' => 'string',
                    'canBeOnly' => [
                        'ordered',
                        'unordered',
                    ],
                ],
                'items' => [
                    'type' => 'array',
                    'data' => [
                        '-' => [
                            'type' => 'string',
                            'allowedTags' => 'i,b,u,a[href]',
                        ],
                    ],
                ],
            ],
            'image' => [
                'file' => [
                    'type' => 'array',
                    'data' => [
                        'url' => [
                            'type' => 'string',
                        ],
                        'thumbnails' => [
                            'type' => 'array',
                            'required' => false,
                            'data' => [
                                '-' => [
                                    'type' => 'string',
                                ]
                            ],
                        ]
                    ],
                ],
                'caption' => [
                    'type' => 'string'
                ],
                'withBorder' => [
                    'type' => 'boolean'
                ],
                'withBackground' => [
                    'type' => 'boolean'
                ],
                'stretched' => [
                    'type' => 'boolean'
                ]
            ],
            'table' => [
                'content' => [
                    'type' => 'array',
                    'data' => [
                        '-' => [
                            'type' => 'array',
                            'data' => [
                                '-' => [
                                    'type' => 'string',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'video' => [
                'url' => [
                    'type' => 'string',
                    'required' => true
                ]
            ],
            'button' => [
                'title' => [
                    'type' => 'string',
                    'required' => true
                ],
                'url' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ]
    ]
];
