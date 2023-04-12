<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\Response;
use Exception;
use InvalidArgumentException;

class AuthController extends BaseController
{
    private $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    public function login()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        $input = $this->request->getJSON(true);

        try{
            // if the input is empty | the user submit empty credentials
            if(is_null($input) || empty($input))  throw new InvalidArgumentException("Please fill in the login credential!");
            if(!$this->validate('user_login')) throw new InvalidArgumentException('Please fill in the login credential!');
            // Get keyed credentials
            $email = $input['email'];
            $password = $input['password'];
            // Search for the appropriate user
            $user = $this->userModel->where('email', $email)->first();

            if(is_null($user)) throw new InvalidArgumentException("Invalid email or password!");
            // Password verify
            $pwd_verify = password_verify($password, $user['password']);
            if(!$pwd_verify) throw new InvalidArgumentException("Invalid email or password!");
            // Generate jwt payload
            $payload = generate_jwt_token(['email' => $email]);
            // Set session
            session()->set([
                'token' => $payload,
                'id' => $user['id'],
                'role' => $user['role']
            ]);
            // set token access key (if not exist)
            if(is_null(session()->get('token_access_key')))
            {
                $rndKey = get_random_string(8);
                session()->set('token_access_key', $rndKey);
                $respData['key'] = $rndKey;
            }
            // Return response
            $respData['msg'] = $payload;
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
    public function register()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        $input = $this->request->getJSON(true);

        try
        {
            if(!$this->validate('user_registration')) throw new InvalidArgumentException();

            $email = $input['email'];
            $password = $input['password'];
            $dName = $input['display-name'];

            $data = [
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'display_name' => $dName
            ];
            // save user data
            $r = $this->userModel->save($data);
            // fail to save
            if(!$r) throw new Exception('Error when saving the user credential!');
            // successfully saved
            $respData['msg'] = 'Successfully Registered!';
        }
        catch(InvalidArgumentException $e)
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
        }
        // initialize response content
        $p = ['error' => $respCode != Response::HTTP_OK];
        foreach($respData as $k => $v) $p[$k] = $v;
        // Return response
        return $this->getResponse($p, $respCode);
    }

    public function auth()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        $input = $this->request->getJSON(true);
        
        try
        {
            // Throw error if there is no token passed in
            if(is_null($input) || empty($input['token'])) throw new Exception("Please login before you proceed!");
            // Verify the token
            $auth = verify_jwt_token($input['token']);
            // Throw error if the token is no longer valid
            if(!$auth) throw new Exception("Please login before you proceed!");
            // Verify success
            $respData['msg'] = 'Authorized!';
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
    public function logout()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            session()->remove(['token', 'id', 'token_access_key']);
            $respData['msg'] = 'Successfully logged out!';
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
