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
                'id, image, name, contact, email, position, location'
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
                'id, image, name, contact, email, position, location'
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
            $input = $this->request->getJSON(true);
            // Throw if the request body is empty or not set
            if(is_null($input) || empty($input)) throw new InvalidArgumentException("Please fill in all required fields!");
            // User requests to add staff information
            if($id < 0)
            {
                // Validate input, and throw ValidationError if
                // one of rule is not obeyed
                if(!$this->validate('staff_add')) throw new ValidationException();
                $d = [
                    'image' => $input['staff-image'],
                    'name' => $input['staff-name'],
                    'contact' => $input['staff-contact'],
                    'email' => $input['staff-email'],
                    'position' => $input['staff-position'],
                    'location' => $input['staff-location'],
                    'created_by' => get_user_id(session())
                ];
                // Insert data into db
                $r = $this->staffModel->insert($d);
                // Throw exception if the data are not inserted into db                
                if(!$r) throw new Exception('Error when adding the staff information!');                
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
                // Validate input, and throw ValidationError if one of rule is not obeyed
                if(!$this->validateRequest($input, 'staff_add', $rules_ignore)) throw new ValidationException();
                $d = [
                    'image' => $input['staff-image'],
                    'name' => $input['staff-name'],
                    'contact' => $input['staff-contact'],
                    'email' => $input['staff-email'],
                    'position' => $input['staff-position'],
                    'location' => $input['staff-location'],
                    'modified_by' => get_user_id(session())
                ];

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
