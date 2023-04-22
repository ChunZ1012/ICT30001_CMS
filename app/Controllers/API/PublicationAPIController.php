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
                'publications.id, title, is_active, date_format(published_time, "%Y-%m-%d") as published_time, date_format(publications.created_at, "%Y-%m-%d") as created_at, display_name as created_by'
            )
            ->join('users', 'users.id = publications.created_by')
            // Only returning active publication if the request is from unity
            ->where($fromUnity ? 'is_active' : 'publications.id >=' , 1)
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
                // Define the rules to be ignored by the validator
                $rules_ignore = [];
                // If the user did not upload any publication cover, then we will tell the validator to skip validate it
                if(!is_uploaded_file_valid($img)) array_push($rules_ignore, 'pub-cover');
                // If the user did not upload any publication file, then we will tell the validator to skip validate it
                if(!is_uploaded_file_valid($file)) array_push($rules_ignore, 'pub-file');
                // Throw error if the validation failed
                if(!$this->validateRequest($postData, 'publish_add', $rules_ignore)) throw new ValidationException();
                // Write cover file to public folder
                if(is_uploaded_file($img)) 
                {
                    $imgRndName = write_file_to_public($img, 'pubs');
                    // Remove uploaded cover
                    delete_uploaded_file($pub['cover'], 'pubs');
                    // Update cover path
                    $pub['cover'] = $imgRndName;
                }
                // Get pdf file from form data
                $file = $this->request->getFile('pub-file');
                // Write cover file to public folder
                if(is_uploaded_file($file)) 
                {
                    $fileRndName = write_file_to_public($file, 'pubs');
                    // Remove uploaded pdf file
                    delete_uploaded_file($pub['pdf'], 'pubs');
                    // Update pdf file path
                    $pub['pdf'] = $fileRndName;
                }
                // Update object data
                $pub['title'] = $postData['pub-title'];
                $pub['published_time'] = $postData['pub-publish-time'];
                // Only admin has the role to set the active status
                $pub['is_active'] = get_user_role(session()) == '1' ? $postData['pub-is-active'] : 0;
                $pub['modified_by'] = get_user_id(session());
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
                if(!$this->validateRequest($postData, 'publish_add')) throw new ValidationException();
                // Cover file name
                $imgFileName = '';
                // Get pdf file name
                $fileFileName = '';
                // Write cover file to public folder
                if(is_uploaded_file_valid($img)) $imgFileName = write_file_to_public($img, 'pubs');
                // Write file to public folder
                if(is_uploaded_file_valid($file)) $fileFileName = write_file_to_public($file, 'pubs');
                $d = [
                    'title' => $postData['pub-title'],
                    // TODO: Get category from form data
                    // 'category' => $data['pub-category'],
                    'published_time' => $postData['pub-publish-time'],
                    'is_active' => (get_user_role(session()) == '1' ? $postData['pub-is-active'] : 0),
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
            // Check for user role
            // If the user is not an admin then throw error
            // Else continue the process
            if(get_user_role(session()) == '1')
            {
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
            else throw new InvalidArgumentException('You do not have permission to update the publication status!');
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
            // Check for user role
            // If the user is not an admin then throw error
            // Else continue the process
            if(get_user_role(session()) == '1')
            {
                $r = $this->pubModel->delete($id);
                if($r)
                {
                    // Remove uploaded cover
                    delete_uploaded_file($pub['cover'], 'pubs');
                    // Remove uploaded pdf file
                    delete_uploaded_file($pub['pdf'], 'pubs');
                    // Set response message
                    $respData['msg'] = 'Successfully deleted!';
                }
                else throw new Exception('Error when deleting the new publication!');
            }
            else throw new InvalidArgumentException('You do not have permission to delete the publication!');
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