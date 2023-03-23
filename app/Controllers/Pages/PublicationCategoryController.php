<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

class PublicationCategoryController extends BaseController
{
    private $templateData = [
        'brand' => 'CMS',
        'title' => 'Publication Category Management',
        'description' => 'Here you can manage your publication category'
    ];
    private $templatePath;

    public function __construct()
    {
        $this->templatePath = 'templates/'.$this->template;
    }
    public function list()
    {
        $this->templateData['content'] = view('pages/publication/category/publication_category_list');
        return view($this->templatePath, $this->templateData);
    }
    public function edit($id)
    {
        $this->templateData['content'] = view('pages/publication/category/publication_category', [
            'id' => $id,
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
}
