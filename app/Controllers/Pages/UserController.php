<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

class UserController extends BaseController
{
    private $templateData = [
        'brand' => 'CMS',
        'title' => 'User Management',
        'description' => 'Here you can manage the user information'
    ];
    private $templatePath;
    public function __construct()
    {
        $this->templatePath = 'templates/'.$this->template;
    }
    public function list()
    {
        $this->templateData['content'] = view('pages/users/user_list');
        return view($this->templatePath, $this->templateData);
    }
    public function add()
    {
        $this->templateData['content'] = view('pages/users/user', [
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
    public function edit($id)
    {
        $this->templateData['content'] = view('pages/users/user', [
            'id' => $id,
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
}
