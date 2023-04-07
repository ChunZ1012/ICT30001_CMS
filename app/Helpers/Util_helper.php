<?php
    use CodeIgniter\Files\File;
    use CodeIgniter\HTTP\Files\UploadedFile;
    use Firebase\JWT\JWT;
    
    if(!function_exists('generate_jwt_token'))
    {
        function generate_jwt_token($payload_claims)
        {
            $key = getenv('JWT_SECRET');
            $keyId = getenv('JWT_ENC_DEC_KEY');
            $iat = time();

            $payload = [
                'iss' => 'Born2Code',
                'aud' => 'Born2Code',
                'sub' => 'Born2Code',
                'iat' => $iat,
                'exp' => $iat + getenv('JWT_EXPIRY') 
            ];

            array_push($payload, $payload_claims);
            $token = JWT::encode($payload, $key, $keyId);

            return $token;
        }
    }

    if(!function_exists('verify_jwt_token'))
    {
        function verify_jwt_token($token)
        {
            $key = getenv('JWT_SECRET');
            $keyId = getenv('JWT_ENC_DEC_KEY');

            try
            {
                JWT::decode($token, new \Firebase\JWT\Key($key, $keyId));
            }
            catch(Exception $e)
            {
                return false;
            }
            
            return true;
        }
    }

    if(!function_exists('get_user_id'))
    {
        function get_user_id($session)
        {
            if(is_null($session) || empty($session->get('id'))) return null;
            else return $session->get('id');
        }
    }

    if(!function_exists('write_file_to_public'))
    {
        function write_file_to_public(UploadedFile $file)
        {
            $public_uploads_path = getenv('PUBLIC_UPLOAD_PATH');
            try
            {
                // Create file object
                $fileObj = new File(WRITEPATH.'uploads/'.$file->store());
                // Get random name of file
                $fileRndName = $fileObj->getRandomName();
                // Move file to public folder
                $fileObj->move($public_uploads_path, $fileRndName, true);
                // Delete temporary file
                if(file_exists($fileObj->getRealPath())) unlink($fileObj->getRealPath());
                // log
                log_message("info", $fileRndName.' has been saved to public folder');
            }
            catch(Exception $e)
            {
                log_message("wrror", 'write_file_to_public: {msg}\n{trace}', [
                    'msg' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            // return file random name
            return $fileRndName;
        }
    }
    
    if(!function_exists('delete_uploaded_file'))
    {
        function delete_uploaded_file($filePath)
        {
            $baseDir = getcwd();
            $fileRealPath = $baseDir.'\\'.$filePath;
            if(file_exists($fileRealPath) && is_file($fileRealPath)) 
            {
                unlink($fileRealPath);
                log_message('info', $filePath.' has been removed');
            }
            else log_message('warning', 'The file path: '.$fileRealPath.' is not exist! Delete aborted');
        }
    }

    if(!function_exists('get_random_string'))
    {
        function get_random_string($c)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randStr = '';

            for($i = 0; $i < $c; $i++)
            {
                $idx = rand(0, strlen($characters) - 1);
                $randStr .= $characters[$idx];
            }

            return $randStr;
        }
    }
?>