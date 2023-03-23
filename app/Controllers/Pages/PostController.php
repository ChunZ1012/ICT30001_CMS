<?php

namespace App\Controllers\Pages;
use App\Controllers\BaseController;

class PostController extends BaseController
{
    public function list()
    {
        /// Example of passing argument to page
        // $data['page'] = view('login_page');
        // return view('main', $data);
        $data['brand'] = "CMS";
        $data['title'] = 'Content Management';
        $data['description'] = 'Here you can manage your content';
        $data['content'] = view('pages/content/content_list');
        return view('templates/'.$this->template, $data);
    }
    public function add()
    {
        /// Example of passing argument to page
        // $data['page'] = view('login_page');
        // return view('main', $data);
        $data['brand'] = "CMS";
        $data['title'] = 'New Content';
        $data['description'] = '';
        $data['content'] = view('pages/content/content',[
            'errors' => []
        ]);
        return view('templates/'.$this->template, $data);
    }
    public function edit($id)
    {
        /// Example of passing argument to page
        // $data['page'] = view('login_page');
        // return view('main', $data);
        $data['brand'] = "CMS";
        $data['title'] = 'Content Management';
        $data['description'] = 'Here you can manage your content';
        $data['content'] = view('pages/content/content', [
            'id' => $id,
            'errors' => []
        ]);
        return view('templates/'.$this->template, $data);
    }
    public function view($id)
    {
        $data['brand'] = "CMS";
        $data['title'] = 'Content Preview';
        $data['description'] = 'Here you can preview your content';
        $data['content'] = view('pages/content/content_view', ['id' => $id]);
        
        // return view('templates/'.$this->template, $data);
        return view('pages/content/content_view', ['id' => $id]);
    }
}
