<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

class PublicationController extends BaseController
{
    private $templateData = [
        'brand' => 'CMS',
        'title' => 'Publication Management',
        'description' => 'Here you can manage your publication'
    ];
    private $templatePath;

    public function __construct()
    {
        $this->templatePath = 'templates/'.$this->template;
    }

    public function list()
    {
        $this->templateData['content'] = view('pages/publication/publication_list');
        return view($this->templatePath, $this->templateData);
    }
    public function add()
    {
        $this->templateData['content'] = view('pages/publication/publication', [
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
    public function edit($id)
    {
        $this->templateData['content'] = view('pages/publication/publication', [
            'id' => $id,
            'errors' => []
        ]);
        return view($this->templatePath, $this->templateData);
    }
    // public function upload($id)
    // {
    //     $data['brand'] = "CMS";
    //     $data['title'] = 'Publication Management';
    //     $data['description'] = 'Here you can manage your publication';

    //     $pubModel = new Publication();
    //     $pageData = [
    //         'error' => false
    //     ];

    //     try
    //     {
    //         // Get the uploaded file
    //         $img = $this->request->getFile('pub-cover');
    //         $file = $this->request->getFile('pub-file');
    //         // Get form data
    //         $postData = $this->request->getPost();
    //         // If the id passed in is larger then 0
    //         // Then we need to check if that id is exist in the db
    //         if($id > 0)
    //         {
    //             $pub = $pubModel->find($id);
    //             // Throw exception if the id is not in the system
    //             if(is_null($pub)) throw new Exception('The selected publication is no longer exist!');
    //             else
    //             {
    //                 // Use different set of validation rule,
    //                 // as the user might only made change to
    //                 // other inputs, rather than cover and pdf
    //                 if(!$this->validate('publish_edit')) throw new ValidationException();
    //                 // Get cover from form data
    //                 $img = $this->request->getFile('pub-cover');
    //                 // Write cover file to public folder
    //                 if(!is_null($img) && !$img->hasMoved()) 
    //                 {
    //                     $imgRndName = write_file_to_public($img);
    //                     // Remove uploaded cover
    //                     delete_uploaded_file($pub['cover']);
    //                     // Update cover path
    //                     $pub['cover'] = $this->uploads_path.$imgRndName;
    //                 }
    //                 // Get pdf file from form data
    //                 $file = $this->request->getFile('pub-file');
    //                 // Write cover file to public folder
    //                 if(!is_null($file) && !$file->hasMoved()) 
    //                 {
    //                     $fileRndName = write_file_to_public($file);
    //                     // Remove uploaded pdf file
    //                     delete_uploaded_file($pub['pdf']);
    //                     // Update pdf file path
    //                     $pub['cover'] = $this->uploads_path.$fileRndName;
    //                 }
    //                 // Update object data
    //                 $pub['title'] = $postData['pub-title'];
    //                 $pub['published_time'] = $postData['pub-publish-time'];
    //                 $pub['is_active'] = $postData['pub-is-active'];

    //                 $r = $pubModel->update($id, $pub);
    //                 if($r)
    //                 {
    //                     return $this->getResponse([
    //                         'error' => false,
    //                         'msg' => 'Successfully Updated!'
    //                     ]);
    //                 }
    //                 else
    //                 {
    //                     return $this->getResponse([
    //                         'error' => true,
    //                         'msg' => 'Error when updating the publication info!'
    //                     ]); 
    //                 }
    //             }
    //         }
    //         // Below section is used by new publication
    //         else
    //         {
    //             // Validate input, and throw ValidationError if
    //             // one of rule is not obeyed
    //             if(!$this->validate('publish_add')) throw new ValidationException();
    //             // Continue to get the form data
    //             $img = $this->request->getFile('pub-cover');
    //             $imgFileName = write_file_to_public($img);
            
    //             $file = $this->request->getFile('pub-file');
    //             $fileFileName = write_file_to_public($file);
            
    //             $d = [
    //                 'title' => $postData['pub-title'],
    //                 // 'category' => $data['pub-category'],
    //                 'category' => 'SC',
    //                 'published_time' => $postData['pub-publish-time'],
    //                 'is_active' => $postData['pub-is-active'],
    //                 'cover' => ($this->uploads_path.$imgFileName),
    //                 'pdf' => ($this->uploads_path.$fileFileName),
    //                 // 'created_by' => get_user_id(session())
    //                 'created_by' => 1
    //             ];
            
    //             $r = $pubModel->insert($d);
    //             if($r) $pageData['msg'] = 'Successfully Added!';
    //             else throw new Exception('Error when adding the publication!');
    //         }
    //     }
    //     // Validate before checking for the publication's existance in db
    //     catch(ValidationException $e)
    //     {
    //         $pageData['validate_error'] = true;
    //         $pageData['errors'] = $this->validator->getErrors();
    //     }
    //     catch(Exception $e)
    //     {
    //         $pageData['error'] = true;
    //         $pageData['msg'] = $e->getMessage();
    //     }
        
    //     $data['content'] = view('pages/publication/publication', $pageData);
    //     return view('templates/'.$this->template, $data);
    // }
}