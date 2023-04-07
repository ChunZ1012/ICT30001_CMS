<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

class FrontendPageController extends BaseController
{
    public function list($page_index)
    {
        if(!isset($page_index)) $page_index = 1;
        $data['page_index'] = $page_index;
        $data['page_limit'] = 5;
        return view('pages/frontend/content_list', $data);
    }
}
