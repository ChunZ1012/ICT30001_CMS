<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Publication;
use CodeIgniter\Database\RawSql;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Exception;
use InvalidArgumentException;

class PublicationAPIController extends BaseController
{
    private $pubModel;
    public function __construct()
    {
        parent::__construct();
        $this->pubModel = new Publication();
    }
    public function list()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $data = $this->pubModel->select(
                'id, title, is_active, date_format(published_time, "%Y-%m-%d") as published_time'
            )
            // Only returning active publication if the request is from unity
            ->where($fromUnity ? 'is_active' : 'id >=' , 1)
            ->findAll();

            if(!$fromUnity)
            {
                $data['data'] = $data;
                $data['total'] = $this->pubModel->selectCount('id')->first()['id'];
            }

            $respData['data'] = $fromUnity ? json_encode($data) : $data;
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
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $rSql = new RawSql(
                'id, title, category, is_active, CONCAT(\''.base_url(getenv("PUBLIC_UPLOAD_PATH")).'\', cover) as cover, CONCAT(\''.base_url(getenv("PUBLIC_UPLOAD_PATH")).'\', pdf) as pdf,  published_time'
            );
            $pub = $this->pubModel
                ->select($rSql)
                ->where('id', $id)
                // Only return the publication detail if the post is active and requested from unity
                ->where($fromUnity ? 'is_active' : 'id >=', 1)
                ->first();
            // Throw if the id is not exist in db
            if(is_null($pub)) throw new InvalidArgumentException("The selected publication is no longer exist!");
            // Else return the correspinding data
            $respData['msg'] = $fromUnity ? json_encode($pub) : $pub;
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
    
    public function upload($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            // Get the uploaded file
            $img = $this->request->getFile('pub-cover');
            $file = $this->request->getFile('pub-file');
            // Get form data
            $postData = $this->request->getPost();
            // If the id passed in is larger then 0 (mean the user requests for an upadte of the current information)
            // Then we need to check if that id is exist in the db
            if($id > 0)
            {
                $pub = $this->pubModel->find($id);
                // Throw exception if the id is not in the system
                if(is_null($pub)) throw new Exception('The selected publication is no longer exist!');

                // Use different set of validation rule,
                // as the user might only made change to
                // other inputs, rather than cover and pdf
                if(!$this->validate('publish_edit')) throw new ValidationException();
                // Write cover file to public folder
                if(!is_null($img) && !$img->hasMoved() && $img->isValid()) 
                {
                    $imgRndName = write_file_to_public($img);
                    // Remove uploaded cover
                    delete_uploaded_file($pub['cover']);
                    // Update cover path
                    $pub['cover'] = $imgRndName;
                }
                // Get pdf file from form data
                $file = $this->request->getFile('pub-file');
                // Write cover file to public folder
                if(!is_null($file) && !$file->hasMoved() && $file->isValid()) 
                {
                    $fileRndName = write_file_to_public($file);
                    // Remove uploaded pdf file
                    delete_uploaded_file($pub['pdf']);
                    // Update pdf file path
                    $pub['pdf'] = $fileRndName;
                }
                // Update object data
                $pub['title'] = $postData['pub-title'];
                $pub['published_time'] = $postData['pub-publish-time'];
                $pub['is_active'] = $postData['pub-is-active'];
                // Update the data into db
                $r = $this->pubModel->update($id, $pub);
                // TODO: Category the insertion error to different exception class
                // Throw exception if the data are not inserted into db
                if(!$r) throw new Exception('Error when updating the publication info!');
                // Update successfully
                // Set success response message
                $respData['msg'] = 'Successfully updated!';
            }
            // Below section is used by new publication 
            else
            {
                // Validate input, and throw ValidationError if
                // one of rule is not obeyed
                if(!$this->validate('publish_add')) throw new ValidationException();
                // Cover file name
                $imgFileName = '';
                // Get pdf file name
                $fileFileName = '';
                // Write cover file to public folder
                if(!is_null($img) && !$img->hasMoved() && $img->isValid()) $imgFileName = write_file_to_public($img);
                // Write file to public folder
                if(!is_null($file) && !$file->hasMoved() && $file->isValid()) $fileFileName = write_file_to_public($file);
                $d = [
                    'title' => $postData['pub-title'],
                    // TODO: Get category from form data
                    // 'category' => $data['pub-category'],
                    'category' => 'SC',
                    'published_time' => $postData['pub-publish-time'],
                    'is_active' => $postData['pub-is-active'],
                    'cover' => ($imgFileName),
                    'pdf' => ($fileFileName),
                    'created_by' => get_user_id(session())
                    // 'created_by' => 1
                ];
                // Insert data into db
                $r = $this->pubModel->insert($d);
                // Throw exception if the data are not inserted into db
                if(!$r) throw new Exception('Error when adding the publication!');
                // Set success response message
                $respData['msg'] = 'Successfully added!';
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

    public function set_pub_status($id, $status)
    {
        $this->pubModel = new Publication();
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            // Get the selected publication
            $pub = $this->pubModel->find($id);
            // Throw error if the id is not exist in the db
            if(is_null($pub)) throw new InvalidArgumentException("The selected publication is no longer exist!");
            // Set the status
            $data = [
                'is_active' => $status == 1,
                'modified_by' => get_user_id(session())
            ];
            // Update the status to db
            $r = $this->pubModel->update($id, $data);
            if($r)
            {
                $respData['msg'] = 'Successfully '.($status == 1 ? 'activated' : 'deactivated').'!';
            }
            else throw new Exception('Error when '.($status == 1 ? 'activated' : 'deactivated').' the new publication!');
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

    public function delete($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            $pub = $this->pubModel->find($id);
            if(is_null($pub)) throw new InvalidArgumentException("The selected publication is no longer exist!");

            $r = $this->pubModel->delete($id);
            if($r)
            {
                // Remove uploaded cover
                delete_uploaded_file($pub['cover']);
                // Remove uploaded pdf file
                delete_uploaded_file($pub['pdf']);
                // Set response message
                $respData['msg'] = 'Successfully deleted!';
            }
            else throw new Exception('Error when deleting the new publication!');
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
}