<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Staff;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Exception;
use InvalidArgumentException;

class StaffAPIController extends BaseController
{
    private $staffModel;
    public function __construct()
    {
        parent::__construct();
        $this->staffModel = new Staff();
    }

    public function list()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $staffs = $this->staffModel->select(
                'id, name, contact, office_contact, office_fax, CONCAT(\''.get_staff_avatar_public_path().'\', avatar) as image'
            )->findAll();

            $respData['msg'] = '';
            $respData['data'] = $fromUnity ? json_encode($staffs) : $staffs;
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
        $respData = ['msg' => ''];
        $respCode = Response::HTTP_OK;

        try
        {
            $staff = $this->staffModel->select(
                'id, name, gender, avatar, contact, email, office_contact, office_fax'
            )->find($id);
            // Throw if the staff information is not existed in the db
            if(is_null($staff)) throw new InvalidArgumentException('The selected staff is no longer exist!');
            
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $respData['data'] = $fromUnity ? json_encode($staff) : $staff;
        }
        catch(InvalidArgumentException $e)
        {
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
        
        try
        {
            // Get image data
            $avatar = $this->request->getFile('staff-avatar');
            $input = $this->request->getPost();
            // User requests to add staff information
            if($id < 0)
            {
                $rndName = '';
                // Validate input, and throw ValidationError if
                // one of rule is not obeyed
                if(!$this->validate('staff_add')) throw new ValidationException();
                $d = [
                    'name' => $input['staff-name'],
                    'age' => $input['staff-age'],
                    'gender' => $input['staff-gender'],
                    'contact' => $input['staff-contact'],
                    'email' => $input['staff-email'],
                    'office_contact' => $input['staff-office-contact'],
                    'office_fax' => $input['staff-office-fax'],
                    'created_by' => get_user_id(session())
                ];
                // Try to save the image/ avatar to public assets folder
                if(is_uploaded_file_valid($avatar)) 
                {
                    $rndName = write_file_to_public($avatar, 'avatars');
                    $d['avatar'] = $rndName;
                }
                else throw new InvalidArgumentException("The avatar uploaded is not valid!");
                // Insert data into db
                $r = $this->staffModel->insert($d);
                // Throw exception if the data are not inserted into db                
                if(!$r) 
                {
                    // Delete uploaded file if insertion is failed
                    delete_uploaded_file($rndName, 'avatars');
                    throw new Exception('Error when adding the staff information!');
                }          
                // Set success response message
                $respData['msg'] = 'Successfully added!';
            }
            // User requests to update staff information
            else
            {
                $staff = $this->staffModel->find($id);
                // Throw if the staff information is not existed in the db
                if(is_null($staff)) throw new InvalidArgumentException('The selected staff is no longer exist!');
                // Always ignore the email validation during editing
                $rules_ignore = ['staff-email'];
                // Ignore the staff avatar if the user did not upload it
                if(!is_uploaded_file_valid($avatar)) array_push($rules_ignore, 'staff-avatar');
                log_message('debug', $avatar->getMimeType());
                // Validate input, and throw ValidationError if one of rule is not obeyed
                if(!$this->validateRequest($input, 'staff_add', $rules_ignore)) throw new ValidationException();

                $d = [
                    'name' => $input['staff-name'],
                    'age' => $input['staff-age'],
                    'gender' => $input['staff-gender'],
                    'contact' => $input['staff-contact'],
                    'office_contact' => $input['staff-office-contact'],
                    'office_fax' => $input['staff-office-fax'],
                    'modified_by' => get_user_id(session())
                ];

                if(is_uploaded_file_valid($avatar)) 
                {
                    $rndName = write_file_to_public($avatar, 'avatars');
                    // Remove uploaded avatar
                    delete_uploaded_file($staff['avatar'], 'avatars');
                    // Update avatar path
                    $d['avatar'] = $rndName;
                }

                // Update data into db
                $r = $this->staffModel->update($id, $d);
                // Throw exception if the data are not inserted into db
                if(!$r) throw new Exception('Error when updating the staff information!');
                // Set success response message
                $respData['msg'] = 'Successfully updated!';
            }
        }
        catch(InvalidArgumentException $e)
        {
            $respCode = Response::HTTP_BAD_REQUEST;
            $respData['msg'] = $e->getMessage();
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

    public function delete($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            $staff = $this->staffModel->find($id);
            // Throw if the staff information is not existed in the db
            if(is_null($staff)) throw new InvalidArgumentException('The selected staff is no longer exist!');

            $r = $this->staffModel->delete($id);
            // Throw exception if the data are not inserted into db
            if(!$r) throw new Exception('Error when deleting the staff information!');
            // Set success response message
            $respData['msg'] = 'Successfully deleted!';
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
