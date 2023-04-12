<?php

namespace App\Controllers\API;

use App\Models\User;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Exception;
use InvalidArgumentException;

class UserAPIController extends BaseController
{
    private $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    public function list()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $users = $this->userModel->select(
                'id, email, display_name, role'
            )->findAll();

            $respData['msg'] = '';
            $respData['data'] = $fromUnity ? json_encode($users) : $users;
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
        $respData = [];
        $respCode = Response::HTTP_OK;

        try
        {
            $user = $this->userModel->select(
                'id, email, display_name, role'
            )->find($id);
            if(is_null($user) || empty($user)) throw new InvalidArgumentException("The selected user is no longer exist!");

            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $respData['msg'] = '';
            $respData['data'] = $fromUnity ? json_encode($user) : $user;
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
            log_message('error', "Internal error occured!\n{msg}\n{log}", ['msg' => $e->getMessage(), 'log' => $e->getTraceAsString()]);
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
            // User requests to create new user
            if($id < 0)
            {
                // Validate input, and throw ValidationError if
                // one of rule is not obeyed
                if(!$this->validate('user_add')) throw new ValidationException();
                $d = [
                    'email' => $input['user-email'],
                    'display_name' => $input['user-display-name'],
                    'role' => $input['user-role'],
                    'password' => password_hash($input['user-password'], PASSWORD_BCRYPT)
                ];
                // Insert data into db
                $r = $this->userModel->insert($d);
                // Throw exception if the data are not inserted into db                
                if(!$r) throw new Exception('Error when adding the user information!');                
                // Set success response message
                $respData['msg'] = 'Successfully added!';

            }
            // User requests to modify/ update user
            else
            {
                $user = $this->userModel->find($id);
                if(is_null($user)) throw new InvalidArgumentException("The selected user is no longer exist!");

                $is_password_set = isset($input['user-password']) && !empty($input['user-password']);
                // Always ignore email validation during updating the user info
                $rules_ignore = ['user-email'];
                // If the user requests to set a new password
                if(!$is_password_set)
                {
                    // Tell the validator to ignore the user-password rule
                    array_push($rules_ignore, 'user-password');
                }
                // Validate input, and throw ValidationError if one of rule is not obeyed
                if(!$this->validateRequest($input, 'user_add', $rules_ignore)) throw new ValidationException();

                $d = [
                    'display_name' => $input['user-display-name'],
                    'role' => $input['user-role'],
                ];
                // Update user password
                if($is_password_set)
                {
                    $d['password'] = password_hash($input['user-password'], PASSWORD_BCRYPT);
                }
                // Update data into db
                $r = $this->userModel->update($id, $d);
                // Throw exception if the data are not inserted into db                
                if(!$r) throw new Exception('Error when updating the user information!');                
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
            $user = $this->userModel->find($id);
            if(is_null($user) || empty($user)) throw new InvalidArgumentException("The selected user is no longer exist!");

            $r = $this->userModel->delete($id);
            // Throw exception if the data are not inserted into db
            if(!$r) throw new Exception('Error when deleting the user!');
            // Set success response message
            $respData['msg'] = 'Successfully deleted!';
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

    public function reset_password($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        
        try
        {
            $user = $this->userModel->find($id);
            if(is_null($user)) throw new InvalidArgumentException("The selected user is no longer exist!");

            $input = $this->request->getJSON(true);
            if(is_null($input) || isset($input['new-passwod'])) throw new InvalidArgumentException("Please enter a new password!");

            $d = [
                'password' => password_hash($input['new-password'], PASSWORD_BCRYPT)
            ];

            // Update data into db
            $r = $this->userModel->update($id, $d);
            // Throw exception if the data are not inserted into db                
            if(!$r) throw new Exception('Error when updating the password!!');                
            // Set success response message
            $respData['msg'] = 'Successfully updated!';
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
}
