<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Post;
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
            $data = $this->postModel->select(
                'id, title, date_format(published_time, \'%Y-%m-%d\') as published_time, is_active'
            )->findAll();
            $total = $this->postModel->selectCount("id");

            $d = [
                'data' => $data,
                'total' => $total,
            ];

            $respData['msg'] = $d;
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
    public function add()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        $input = $this->request->getJSON(true);

        try
        {
            if(is_null($input) || empty($input['data'])) throw new InvalidArgumentException("Invalid request body!");
            // Get form data
            $data = $input['data'];
            // Store in array (for validation & storing to db purpose)
            $post = [
                'title' => $data['page-title'],
                'published_time' => $data['page-publish-time'],
                'is_active' => $data['page-is-active'],
                'content' => $data['page-content'],
                'created_by' => get_user_id(session()),
            ];
            // validate form data
            if(!$this->validateRequest($post, 'content_upload')) throw new ValidationException();

            // validated, trying to update the data in db
            $r = $this->postModel->insert($post);
            if(!$r) throw new Exception('Error when updating the content info!');
            // Update successfully
            // Set success response message
            else $respData['msg'] = 'Successfully updated!';
        }
        catch(ValidationException $e)
        {
            return $this->getResponse([
                'error' => true,
                'validate_error' => true,
                'msg' => json_encode($this->validator->getErrors())
            ], Response::HTTP_BAD_REQUEST);
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
    public function update($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        $input = $this->request->getJSON(true);

        try
        {
            if(is_null($input) || empty($input['data'])) throw new InvalidArgumentException("Invalid request body!");
            // Get form data
            $data = $input['data'];
            // Store in array (for validation & storing to db purpose)
            $post = [
                'title' => $data['page-title'],
                'published_time' => $data['page-publish-time'],
                'is_active' => $data['page-is-active'],
                'content' => $data['page-content'],
                'modified_by' => get_user_id(session()),
            ];
            // validate form data
            if(!$this->validateRequest($post, 'content_upload')) throw new ValidationException();
            // validated, trying to update the data in db
            $r = $this->postModel->update($id, $post);
            if(!$r) throw new Exception('Error when updating the content info!');
            // Update successfully
            // Set success response message
            else $respData['msg'] = 'Successfully updated!';
        }
        catch(ValidationException $e)
        {
            // Return bad request as the form data do not pass the validation
            $respCode = Response::HTTP_BAD_REQUEST;
            $respData['validate_error'] = true;
            $respData['msg'] = json_encode($this->validator->getErrors()); 
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
        log_message('info', 'response: '.$respCode);
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
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
        try
        {
            $post = $this->postModel->where('id', $id)->first();

            if(is_null($post))
            {
                return $this->getResponse([
                    'error' => true,
                    'msg' => 'Cannot find the requested post!'
                ]);
            }
            else
            {
                return $this->getResponse([
                    'error' => false,
                    'msg' => json_encode($post)
                ]);
            }
        }
        catch(Exception $e)
        {
            return $this->getResponse([
                'error' => true,
                'msg' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}