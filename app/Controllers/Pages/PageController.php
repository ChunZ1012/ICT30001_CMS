<?php

namespace App\Controllers\Pages;
use App\Controllers\BaseController;

class PageController extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function login()
    {
        // Generate random key
        $rndKey = get_random_string(8);
        // Passing key to the view
        $data['key'] = $rndKey;
        // Save random key into session
        session()->set([
            'token_access_key' => $rndKey
        ]);
        // return view
        return view('pages/login_page', $data);
    }

    public function home()
    {
        $data['brand'] = "CMS";
        $data['title'] = 'Home';
        $data['description'] = 'Hope you have a good day!';
        $data['content'] = '';

        return view('templates/'.$this->template, $data);
    }
    public function menu()
    {
        /// Example of passing argument to page
        // $data['page'] = view('login_page');
        // return view('main', $data);
        $data['brand'] = "CMS";
        $data['title'] = 'Menu Management';
        $data['description'] = 'Here you can manage your menu';
        $data['content'] = view('pages/menu');

        return view('templates/'.$this->template, $data);
    }
}
