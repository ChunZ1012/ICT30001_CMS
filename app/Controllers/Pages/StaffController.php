<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

class StaffController extends BaseController
{
    private $templateData = [
        'brand' => 'CMS',
        'title' => 'Staff Management',
        'description' => 'Here you can manage the staffs information'
    ];
    private $templatePath;
    public function __construct()
    {
        $this->templatePath = 'templates/'.$this->template;
    }
    public function list()
    {
        $this->templateData['content'] = view('pages/staffs/staff_list');
        return view($this->templatePath, $this->templateData);
    }
    public function add()
    {
        $this->templateData['content'] = view('pages/staffs/staff', [
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
    public function edit($id)
    {
        $this->templateData['content'] = view('pages/staffs/staff', [
            'id' => $id,
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
}
