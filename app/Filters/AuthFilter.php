<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET');
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        if(!empty($header))
        {
            if(preg_match('/Bearer\s(\S+)/', $header, $matches))
            {
                $token = $matches[1];
            }
        }

        if(is_null($token) || empty($token))
        {
            $response = service('response');
            $response->setBody('Access denied!');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

            return $response;
        }

        $session = session()->get('token');
        if(is_null($session))
        {
            $response = service('response');
            $response->setBody('Access denied!');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

            return $response;
        }

        try
        {
            JWT::decode($token, new Key($key, 'HS256'));
        }
        catch(Exception $e)
        {
            $response = service('response');
            $response->setBody('Access denied!');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
