<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Post;
use CodeIgniter\Database\RawSql;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Exception;
use InvalidArgumentException;

class PostAPIController extends BaseController
{
    private $postModel;
    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
    }
    public function list()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);
            // Using RawSql to properly trigger the substring_index function
            $rawSql = new RawSql(
                'id, title, date_format(published_time, \'%Y-%m-%d\') as published_time, is_active, substring_index(cover, \',\', 1) as cover'
            );
            
            $posts = $this->postModel->select($rawSql)->findAll();
            
            $data = [];
            foreach($posts as $d)
            {
                $d['cover'] = isset($d['cover']) && !empty($d['cover']) ? base_url($d['cover']) : '';

                array_push($data, $d);
            }

            array_pop($data);
            // print_r($data);
            $respData['msg'] = "";
            $respData['data'] = $fromUnity ? json_encode($data) : $data;
        }
        catch(Exception $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $respData['msg'] = $e->getMessage();
        }
        // initialize response content
        $p = ['error' => $respCode != Response::HTTP_OK];
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
    }
    public function update($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $postData = $this->getFormData();
            if($id < 0)
            {
                // Loop through each uploaded image to save into the public folder
                foreach($postData['page-covers'] as $c)
                {
                    // Save covers to public folder
                    if(!is_null($c) && !$c->hasMoved() && $c->isValid()) 
                    {
                        $imgRndName = write_file_to_public($c);
                        if(!isset($postData['page-cover'])) $postData['page-cover'] = '';
                        $postData['page-cover'] .= $this->uploads_path.$imgRndName.',';
                    }
                }
                // Remove trailing comma
                if(isset($postData['page-cover'])) $postData['page-cover'] = substr($postData['page-cover'] , 0, strlen($postData['page-cover']) - 1);

                $data = [
                    'title' => $postData['page-title'],
                    'published_time' => $postData['page-publish-time'],
                    'is_active' => $postData['page-is-active'],
                    'content' => $postData['page-content'],
                    'created_by' => get_user_id(session())
                ];
                // Set cover data to post data
                if(isset($postData['page-cover'])) $data['cover'] = $postData['page-cover'];
                // validated, trying to update the data in db
                $r = $this->postModel->insert($data);
                if(!$r) throw new Exception('Error when updating the content info!');
                // Update successfully
                // Set success response message
                else $respData['msg'] = 'Successfully updated!';
            }
            // Edit/ update content
            else
            {
                $post = $this->postModel->find($id);
                // Throw if not exist
                if(is_null($post)) throw new Exception("The selected post is no longer exist!");
                // Split cover's path
                $postCovers = explode(',', $post['cover']);
                // Loop through each uploaded image to save into the public folder
                foreach($postData['page-covers'] as $c)
                {
                    // If the cover uploaded is already existed in db
                    // then skip storing it
                    if(in_array($c, $postCovers)) continue;
                    // If the cover uploaded is no longer inside the db
                    // then store it
                    else 
                    {
                        if(!is_null($c) && !$c->hasMoved() && $c->isValid()) 
                        {
                           $imgRndName = write_file_to_public($c);
                           if(!isset($postData['page-cover'])) $postData['page-cover'] = '';
                           $postData['page-cover'] .= $this->uploads_path.$imgRndName.',';
                        }
                    }
                }
                // Loop through each stored cover, delete the cover if no longer exist in
                // the form request
                foreach($postCovers as $c)
                {
                    if(!in_array($c, $postData['page-covers'])) delete_uploaded_file($c);
                }
                // Remove trailing comma
                if(isset($postData['page-cover'])) $postData['page-cover'] = substr($postData['page-cover'] , 0, strlen($postData['page-cover']) - 1);
                // Store in array (for validation & storing to db purpose)
                $data = [
                    'title' => $postData['page-title'],
                    'published_time' => $postData['page-publish-time'],
                    'is_active' => $postData['page-is-active'],
                    'content' => $postData['page-content'],
                    'cover' => $postData['page-cover'],
                    'modified_by' => get_user_id(session()),
                ];
                // validated, trying to update the data in db
                $r = $this->postModel->update($id, $data);
                if(!$r) throw new Exception('Error when updating the content info!');
                // Update successfully
                // Set success response message
                else $respData['msg'] = 'Successfully updated!';
            }
        }
        catch(ValidationException $e)
        {
            // Return bad request as the form data do not pass the validation
            $respCode = Response::HTTP_BAD_REQUEST;
            $respData['validate_error'] = true;
            $respData['msg'] = json_encode($this->validator->getErrors());
        }
        catch(Exception $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $respData['msg'] = $e->getMessage();
            log_message('error', "Internal error occured!\n{msg}\n{log}", ['msg' => $e->getMessage(), 'log' => $e->getTraceAsString()]);
        }
        // initialize response content
        $p = ['error' => $respCode != Response::HTTP_OK];
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
    }

    public function getFormData()
    {
        $data = $this->request->getPost();
        $covers = [];
        // Throw error if the content(s) is/ are not valid
        if(is_null($data)) throw new InvalidArgumentException('Invalid request body!');
        // validate form data
        if(!$this->validateRequest($data, 'content_upload')) throw new ValidationException  ();
        // If there is/ are any image(s) uploaded
        if($data['page-cover-count'] > 0)
        {
            for($i = 0; $i < $data['page-cover-count']; $i++) array_push($covers,   $this->request->getFile('page-cover-'.$i));
        }
        $data['page-covers'] = $covers;
        // return data
        return $data;
    }
    public function delete($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $post = $this->postModel->find($id);
            if(is_null($post)) throw new InvalidArgumentException("The selected post is no longer exist!");
            // Delete from db
            $r = $this->postModel->delete($id);
            if(!$r) throw new Exception('Error when deleting the content!');
            // Update successfully
            // Set success response message
            else $respData['msg'] = 'Successfully deleted!';
        }
        catch(Exception $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $respData['msg'] = $e->getMessage();
        }
        // initialize response content
        $p = ['error' => $respCode != Response::HTTP_OK];
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
    }
    public function set_post_status($id, $status)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $post = $this->postModel->find($id);
            if(is_null($post)) throw new InvalidArgumentException("The selected post is no longer existed!");

            $data = [
                'is_active' => $status == 1,
                // 'modified_by' => get_user_id($this->session)
                'modified_by' => 1
            ];

            $r = $this->postModel->update($id, $data);
            if(!$r) throw new Exception('Error when '.($status == 1 ? 'activating' : 'deactivating').' the selected post!');
            // Update successfully
            // Set success response message
            else $respData['msg'] = 'Successfully '.($status == 1 ? 'Activated' : 'Deactivated').'!';
        }
        catch(InvalidArgumentException $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_BAD_REQUEST;
            $respData['msg'] = $e->getMessage();
        }
        catch(Exception $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $respData['msg'] = $e->getMessage();
        }
        // initialize response content
        $p = ['error' => $respCode != Response::HTTP_OK];
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
    }
    public function get($id)
    {
        $respData = ['msg' => ""];
        $respCode = Response::HTTP_OK;
        try
        {
            $post = $this->postModel->where('id', $id)->first();
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            if(is_null($post)) throw new InvalidArgumentException('Cannot find the requested post!');
            else $respData['data'] = $fromUnity ? json_encode($post) : $post;
        }
        catch(InvalidArgumentException $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_BAD_REQUEST;
            $respData['msg'] = $e->getMessage();
        }
        catch(Exception $e)
        {
            // Return 500 server error
            $respCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $respData['msg'] = $e->getMessage();
        }
        // initialize response content
        $p = ['error' => $respCode != Response::HTTP_OK];
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
    }
}