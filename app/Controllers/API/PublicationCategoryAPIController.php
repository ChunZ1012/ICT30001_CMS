<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\PublicationCategory;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Exception;
use InvalidArgumentException;

class PublicationCategoryAPIController extends BaseController
{
    private $catModel;
    public function __construct()
    {
        parent::__construct();
        $this->catModel = new PublicationCategory();
    }

    public function list()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $data = [
                'data' => $this->catModel->select(
                    'id, shortcode, name, is_active'
                )->findAll(),
                'total' => $this->catModel->selectCount('id')->first()['id']
            ];

            $respData['msg'] = $data;
        }
        catch(Exception $e)
        {
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
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            $cat = $this->catModel->select(
                'id, shortcode, name, is_active'
            )->where('id', $id)->first();
            // Throw if the id is not exist in db
            if(is_null($cat)) throw new InvalidArgumentException("The selected publication category is no longer exist!");
            // Else return the correspinding data
            $respData['msg'] = json_encode($cat);
        }
        catch(InvalidArgumentException $e)
        {
            $respCode = Response::HTTP_BAD_REQUEST;
            $respData['msg'] = $e->getMessage();
        }
        catch(Exception $e)
        {
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
            // validate form data
            if(!$this->validateRequest($data, 'publish_category')) throw new ValidationException();

            $cat = [
                'shortcode' => $data['pc-sc'],
                'name' => $data['pc-name'],
                'is_active' => $data['pc-is-active'],
                // TODO: Revert comment
                // 'created_by' => get_user_id(session()),
                'created_by' => 1,
            ];
            // validated, trying to update the data in db
            $r = $this->catModel->insert($cat);
            if(!$r) throw new Exception('Error when adding the publication category!');
            // Update successfully
            // Set success response message
            else $respData['msg'] = 'Successfully added!';
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

    public function edit($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        $input = $this->request->getJSON(true);

        try
        {
            if(is_null($input) || empty($input['data'])) throw new InvalidArgumentException("Invalid request body!");
            // Get form data
            $data = $input['data'];
            // validate form data
            if(!$this->validateRequest($data, 'publish_category')) throw new ValidationException();

            $cat = [
                'shortcode' => $data['pc-sc'],
                'name' => $data['pc-name'],
                'is_active' => $data['pc-is-active'],
                // TODO: Revert comment
                // 'created_by' => get_user_id(session()),
                'created_by' => 1,
            ];
            // validated, trying to update the data in db
            $r = $this->catModel->update($id, $cat);
            if(!$r) throw new Exception('Error when editing the publication category!');
            // Update successfully
            // Set success response message
            else $respData['msg'] = 'Successfully added!';
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

    public function set_cate_status($id, $status)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $post = $this->catModel->find($id);
            if(is_null($post)) throw new InvalidArgumentException("The selected publication category is no longer existed!");

            $data = [
                'is_active' => $status == 1,
                // 'modified_by' => get_user_id($this->session)
                'modified_by' => 1
            ];

            $r = $this->catModel->update($id, $data);
            if(!$r) throw new Exception('Error when '.($status == 1 ? 'activating' : 'deactivating').' the selected publication category!');
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
}
