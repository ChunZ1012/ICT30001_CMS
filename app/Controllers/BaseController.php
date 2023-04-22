<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['Util', 'form', 'file'];
    protected string $uploads_path;
    protected string $template = 'template-v2';
    function __construct()
    {
        $this->uploads_path = getenv('PUBLIC_UPLOAD_PATH');
    }
    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        // Preload any models, libraries, etc, here.
    }
    public function getResponse(array $responseBody, int $code = ResponseInterface::HTTP_OK) {
        return $this
            ->response
            ->setStatusCode($code)
            ->setJSON($responseBody);
    }
    public function getRequestInput(IncomingRequest $request){
        $input = $request->getPost();

        if (empty($input)) {
            //convert request body to associative array
            $input = json_decode($request->getBody(), true);
        }
        return $input;
    }
    public function validateRequest($input, $rules, $rules_ignore = [], $messages =[]){
        $this->validator = Services::Validation();
        // If you replace the $rules array with the name of the group
        if (is_string($rules)) {
            $validation = config('Validation');
    
            // If the rule wasn't found in the \Config\Validation, we
            // should throw an exception so the developer can find it.
            if (!isset($validation->$rules)) {
                throw ValidationException::forRuleNotFound($rules);
            }
    
            // If no error message is defined, use the error message in the Config\Validation file
            if (!$messages) {
                $errorName = $rules . '_Errors';
                $messages = $validation->$errorName ?? [];
            }
    
            $rules = $validation->$rules;
            if(isset($rules_ignore) && count($rules_ignore) > 0)
            {
                for($i = 0; $i < count($rules_ignore); $i++)
                {
                    $isKeyExist = array_key_exists($rules_ignore[$i], $rules);
                    if($isKeyExist) 
                    {
                        $index = array_search($rules_ignore[$i], array_keys($rules));
                        array_splice($rules, $index, 1);
                    }
                }
            }
        }
        return $this->validator->setRules($rules, $messages)->run($input);
    } 
}
