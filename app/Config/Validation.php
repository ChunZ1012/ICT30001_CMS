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
                'required'
            ]
        ]
    ];

    public array $content_upload = [
        'page-title' => [
            'label' => 'Page Title',
            'rules' =>  [ 
                'required',
            ]
        ],
        'page-publish-time' => [
            'label' => 'Page Publish Time',
            'rules' => [
                'required',
                'valid_date'
            ]
        ],
        'page-is-active' => [
            'label' => 'Page Status',
            'rules' => [
                'required'
            ]
        ],
    ];
    public array $publish_add = [
        'pub-title' => [
            'label' => 'Publication Title',
            'rules' => [
                'required',
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
        'pub-category' => [
            'label' => 'publication category',
            'rules' => [
                'required',
                'in_list[INSP,PAST]'
            ],
            'errors' => [
                'in_list' => 'The value {value} for {field} is not valid!'
            ]
        ],
        'pub-cover' => [
            'label' => 'Publication Cover',
            'rules' => [
                'is_image[pub-cover]',
                'mime_in[pub-cover,image/png,image/jpeg,image/jpg,image/gif]',
                'ext_in[pub-cover,png,jpeg,jpg,gif]',
                'max_size[pub-cover,5120]',
            ],
            'errors' => [
                'max_size' => 'The maximum size allowed is less than 5 MB!',
                'is_image' => 'The file uploaded is not an image!',
                'mime_in' => 'The file uploaded is not an image!',
                'ext_in' => 'The file extension is not matched with its MIME!'
            ]
        ],
        'pub-file' => [
            'label' => 'Publication File',
            'rules' => [
                'mime_in[pub-file,application/pdf]',
            ],
            'errors' => [
                'mime_in'=> 'The file uploaded is not a pdf!',
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

    public array $staff_add = [
        'staff-name' => [
            'label' => 'staff name',
            'rules' => [
                'required',
                'string'
            ]
        ],
        'staff-age' => [
            'label' => 'staff age',
            'rules' => [
                'required',
                'less_than[100]',
                'is_natural_no_zero',
            ],
            'errors' => [
                'less_than' => 'The value input is invalid!',
                'is_natural_no_zero' => 'Please enter a number greater than 0!',
            ]
        ],
        'staff-gender' => [
            'label' => 'staff gender',
            'rules' => [
                'required',
                'in_list[M,F]'
            ],
            'errors' => [
                'in_list' => 'The value {value} for {field} is not valid!'
            ]
        ],
        'staff-avatar' => [
            'label' => 'staff avatar',
            'rules' => [
                'uploaded[staff-avatar]',
                'is_image[staff-avatar]',
                'mime_in[staff-avatar,image/png,image/jpeg,image/jpg,image/gif]',
                'ext_in[staff-avatar,.png,.jpeg,.jpg,.gif]'
            ],
            'errors' => [
                'uploaded' => 'Failed to upload the file!',
                'is_image' => 'The file uploaded is not an image!',
                'mime_in' => 'The file uploaded is not an image!',
                'ext_in' => 'The file extension is not matched with its MIME!'
            ]
        ],
        'staff-contact' => [
            'label' => 'staff contact',
            'rules' => [
                'required',
                'max_length[12]'
            ],
            'errors' => [
                'max_length' => 'The value {value} exceeds the maximum length of {param}!'
            ]
        ],
        'staff-email' => [
            'label' => 'staff email',
            'rules' => [
                'required',
                'valid_email',
                'is_unique[staffs.email]'
            ],
            'errors' => [
                'valid_email' => 'The email entered is not a valid email!',
                'is_unique' => 'The email is already existed in the system! Please use a different email!'
            ]
        ],
        'staff-office-contact' => [
            'label' => 'staff office contact',
            'rules' => [
                'required',
                'exact_length[10]'
            ],
            'errors' => [
                'exact_length' => 'Please enter exact {param} characters for {field}!'
            ]
        ],
        'staff-office-fax' => [
            'label' => 'staff office fax',
            'rules' => [
                'required',
                'exact_length[10]'
            ],
            'errors' => [
                'exact_length' => 'Please enter exact {param} characters for {field}!'
            ]
        ],
    ];

    public array $user_add = [
        'user-email' => [
            'label' => 'user email',
            'rules' => [
                'required',
                'is_unique[users.email]',
                'valid_email'
            ],
            'errors' => [
                'is_unique' => 'This email address has been registered!',
                'valid_email' => 'The email address is invalid!'
            ]
        ],
        'user-display-name' => [
            'label' => 'user display name',
            'rules' => [
                'required',
            ]
        ],
        'user-password' => [
            'label' => 'user password',
            'rules' => [
                'required',
                'min_length[8]',
                'alpha_numeric_punct'
            ],
            'errors' => [
                'min_length' => 'The password must contain at least 8 characters!',
                'alpha_numeric_punct' => 'The password must contain alphanumeric, and the special symbol (~!#$%&*-_+=|:.)'
            ]
        ],
        'user-role' => [
            'label' => 'user role',
            'rules' => [
                'required',
                'in_list[1,2]'
            ],
            'errors' => [
                'in_list' => 'The user role value is invalid!'
            ]
        ],
    ];
}
