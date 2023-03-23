<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    public array $user_registration = [
        'email' => [
            'label' => 'Registration Email',
            'rules' => [
                'required',
                'valid_email',
                'is_unique[users.email]',
            ],
            'errors' => [
                'is_unique' => 'This email address has been registered! Please login instead.'
            ]
        ],
        'password' => [
            'label' => 'Password',
            'rules' => [
                'required',
                'string',
                'min_length[8]'
            ]
        ],
        'confirm-password' => [
            'label' => 'Confirm Password',
            'rules' => [
                'required',
                'string',
                'matches[password]'
            ]
        ],
        'display-name' => [
            'label' => 'Display Name',
            'rules' => [
                'required'
            ]
        ]
    ];

    public array $user_login = [
        'email' => [
            'label' => 'User Email',
            'rules' => [
                'required',
                'valid_email',
            ]
        ],
        'password' => [
            'label' => 'User Password',
            'rules' => [
                'required',
                'min_length[8]'
            ]
        ]
    ];

    public array $content_upload = [
        'title' => [
            'label' => 'Page Title',
            'rules' =>  [ 
                'required',
                'alpha_numeric_punct'
            ]
        ],
        'published_time' => [
            'label' => 'Page Publish Time',
            'rules' => [
                'required',
                'valid_date'
            ]
        ],
        'is_active' => [
            'label' => 'Page Status',
            'rules' => [
                'required'
            ]
        ],
        'content' => [
            'label' => 'Page Content',
            'rules' => [
                'string'
            ]
        ]
    ];
    public array $publish_edit = [
        'pub-title' => [
            'label' => 'Publication Title',
            'rules' => [
                'required',
                'string'
            ]
        ],
        'pub-publish-time' => [
            'label' => 'Publication Publish Time',
            'rules' => [
                'required',
                'valid_date'
            ]
        ],
        'pub-is-active' => [
            'label' => 'Is Active',
            'rules' => [
                'required'
            ]
        ]
        // Not necessary need to validate the cover and pdf,
        // since sometimes the user only made change to
        // other inputs
    ];
    public array $publish_add = [
        'pub-title' => [
            'label' => 'Publication Title',
            'rules' => [
                'required',
                'string'
            ]
        ],
        'pub-publish-time' => [
            'label' => 'Publication Publish Time',
            'rules' => [
                'required',
                'valid_date'
            ]
        ],
        'pub-is-active' => [
            'label' => 'Is Active',
            'rules' => [
                'required'
            ]
        ],
        'pub-cover' => [
            'label' => 'Publication Cover',
            'rules' => [
                'max_size[pub-cover,5120]',
                'is_image[pub-cover]'
            ]
        ],
        'pub-file' => [
            'label' => 'Publication File',
            'rules' => [
                'mime_in[pub-file,application/pdf]'
            ]
        ]
    ];

    public array $publish_category = [
        'pc-sc' => [
            'label' => 'Short Code',
            'rules' => [
                'required',
                'string',
                'is_unique[publication_category.shortcode]'
            ],
            'errors' => [
                'is_unique' => 'This short code already exist! Please use other short code.'
            ]
        ],
        'pc-name' => [
            'label' => 'Category Name',
            'rules' => [
                'required',
                'string'
            ]
        ],
        'pc-is-active' => [
            'label' => 'Is Active',
            'rules' => [
                'required',
            ]
        ]
    ];
}
