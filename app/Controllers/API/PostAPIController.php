<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Post;
use App\Models\PostImage;
use CodeIgniter\Database\RawSql;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Exception;
use InvalidArgumentException;

class PostAPIController extends BaseController
{
    private $postModel;
    private $postImageModel;
    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
        $this->postImageModel = new PostImage();
    }
    public function list()
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);
            // Using RawSql to properly trigger the substring_index function
            $rawSql = new RawSql(
                'posts.id, title, date_format(published_time, \'%Y-%m-%d\') as published_time, display_name as created_by, date_format(posts.created_at, \'%Y-%m-%d\') as created_at'. ($fromUnity ? '' : ', is_active')
            );
            
            $posts = $this->postModel
                ->select($rawSql)
                ->join('users', 'users.id = posts.created_by')
                ->where($fromUnity ? 'is_active' : 'posts.id >=', 1)
                ->get()
                ->getResultArray($this->postModel->returnType);

            if($fromUnity)
            {
                // Use for loop to add images key to posts, `foreach` loop only allow to read/ modify the existing content
                for($i = 0; $i < count($posts); $i++)
                {
                    $postImages = $this->postImageModel->getImages($posts[$i]['id'], false, 1);
                    // Get the first image
                    $posts[$i]['image'] = !is_null($postImages) && count($postImages) > 0 ? $postImages[0]['path'] : '';
                }
            }

            // print_r($data);
            $respData['msg'] = "";
            $respData['data'] = $fromUnity ? json_encode($posts) : $posts;
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
            $postData = $this->getFormData();
            if($id < 0)
            {
                $data = [
                    'title' => $postData['page-title'],
                    'published_time' => $postData['page-publish-time'],
                    'is_active' => $postData['page-is-active'] == '1',
                    'content' => '<div>'.$postData['page-content'].'</div>',
                    'created_by' => get_user_id(session())
                ];
                // validated, trying to update the data in db
                $r = $this->postModel->insert($data);
                if(!$r) throw new Exception('Error when updating the content info!');
                // Insert successfully
                else 
                {
                    $id = $this->postModel->getInsertID();
                    // Loop through each uploaded image to save into the public folder
                    foreach($postData['page-images'] as $c)
                    {
                        $image = $c['page-image'];
                        // Save covers to public folder
                        if(is_uploaded_file_valid($image)) 
                        {
                            $imgRndName = write_file_to_public($image, 'posts');
                            $postImageData = [
                                'post_id' => $id,
                                'path' => $imgRndName,
                                'description' => $c['page-image-alt-text'],
                                'content' => '<div>'.$c['page-image-content'].'</div>',
                                'created_by' => get_user_id(session())
                            ];
                            $r = $this->postImageModel->insert($postImageData);
                        }   
                    }   

                    $respData['msg'] = 'Successfully updated!';
                }
            }
            // Edit/ update content
            else
            {
                $post = $this->postModel->find($id);
                // Throw if not exist
                if(is_null($post)) throw new Exception("The selected post is no longer exist!");
                
                $storedImages = $this->postImageModel
                    ->select('path')
                    ->where('post_id', $post['id'])
                    ->get()
                    // This method will return 2 dimentional array, thus array_map will be used to get 1d array
                    ->getResultArray($this->postImageModel->returnType);
                // Get the columns data into single dimension array 
                $storedImages = array_map(function($v){
                    return $v['path'];
                }, $storedImages);

                $uploadedImages = [];
                // Extract page image from uploaded content
                foreach($postData['page-images'] as $p) array_push($uploadedImages, $p['page-image']->getClientName());
                // Loop through each stored cover, delete the cover if no longer exist in the form request
                for($x = 0; $x < count($storedImages); $x++)
                {
                    $storedImage = $storedImages[$x];
                    if(!in_array($storedImage, $uploadedImages)) 
                    {
                        // Remove from local storage
                        delete_uploaded_file($storedImage, 'posts');
                        // Remove image from db
                        $this->postImageModel
                            ->where('path', $storedImage)
                            ->delete();
                        // Remove from list
                        array_splice($storedImages, $x, 1);
                    }
                }
                // Loop through each uploaded image to save into the public folder
                foreach($postData['page-images'] as $coverMeta)
                {
                    $img = $coverMeta['page-image'];
                    $postImageData = [];
                    // If the cover uploaded is already existed in db, update the description and content into database
                    if(in_array($img->getClientName(), $storedImages)) $postImageData['modified_by'] = get_user_id(session());
                    // If the cover uploaded is not inside the db, then store it
                    else 
                    {
                        if(is_uploaded_file_valid($img)) 
                        {
                           $imgRndName = write_file_to_public($img, 'posts');
                           $postImageData['post_id'] = $post['id'];
                           $postImageData['path'] = $imgRndName;
                           $postImageData['created_by'] = get_user_id(session());
                        }
                    }
                    $postImageData['description'] = $coverMeta['page-image-alt-text'];
                    // append and prepend content with div tag, used by the front end parser
                    $postImageData['content'] = '<div>'.$coverMeta['page-image-content'].'</div>';
                    
                    // Get the image content id
                    $imageId = $this->postImageModel
                        ->select('id')
                        ->where('path', $img->getClientName())
                        ->first();
                        
                    // Update the content if ID found
                    if(!is_null($imageId) && isset($imageId) && $imageId > 0) $this->postImageModel->update($imageId, $postImageData);
                    // Insert the content into db if ID not found
                    else $this->postImageModel->insert($postImageData);
                }
                // Store in array (for validation & storing to db purpose)
                $data = [
                    'title' => $postData['page-title'],
                    'published_time' => $postData['page-publish-time'],
                    'content' => '<div>'.$postData['page-content'].'</div>',
                    'is_active' => $postData['page-is-active'] == '1',
                    'modified_by' => get_user_id(session()),
                ];
                // validated, trying to update the data in db
                $r = $this->postModel->update($id, $data);
                if(!$r) throw new Exception('Error when updating the content info!');
                // Update successfully
                // Set success response message
                else $respData['msg'] = 'Successfully updated!';
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

    public function getFormData()
    {
        $data = $this->request->getPost();
        $postMeta = [];
        $coverData = [];
        // Throw error if the content(s) is/ are not valid
        if(is_null($data)) throw new InvalidArgumentException('Invalid request body!');
        // validate form data
        if(!$this->validateRequest($data, 'content_upload')) throw new ValidationException();
        // If there is/ are any image(s) uploaded
        if($data['page-cover-count'] > 0)
        {
            for($i = 0; $i < $data['page-cover-count']; $i++) 
            {
                $coverMeta = json_decode($data['page-cover-meta-'.$i], true);
                $img = $this->request->getFile('page-cover-'.$i);
                $coverMeta['page-image'] = $img;

                array_push($coverData, $coverMeta);
            }
        }

        $postMeta['page-title'] = $data['page-title'];
        $postMeta['page-publish-time'] = $data['page-publish-time'];
        $postMeta['page-is-active'] = $data['page-is-active'];
        $postMeta['page-content'] = $data['page-content'];
        $postMeta['page-images'] = $coverData;

        return $postMeta;
    }
    public function delete($id)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $post = $this->postModel->find($id);
            if(is_null($post)) throw new InvalidArgumentException("The selected post is no longer exist!");
            // Check for user role
            // If the user is not an admin then throw error
            // Else continue the process
            if(get_user_id(session()) == '1')
            {
                // Delete from db
                $r = $this->postModel->delete($id);
                if(!$r) throw new Exception('Error when deleting the content!');
                // Update successfully
                // Set success response message
                else $respData['msg'] = 'Successfully deleted!';
            }
            else throw new InvalidArgumentException('You do not have permission to delete the content!');
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
    public function set_post_status($id, $status)
    {
        $respData = [];
        $respCode = Response::HTTP_OK;
        try
        {
            $post = $this->postModel->find($id);
            if(is_null($post)) throw new InvalidArgumentException("The selected post is no longer existed!");
            // Check for user role
            // If the user is not an admin then throw error
            // Else continue the process
            if(get_user_role(session()) == '1')
            {
                $data = [
                    'is_active' => $status == 1,
                    'modified_by' => get_user_id(session())
                ];
    
                $r = $this->postModel->update($id, $data);
                if(!$r) throw new Exception('Error when '.($status == 1 ? 'activating' : 'deactivating').' the selected post!');
                // Update successfully
                // Set success response message
                else $respData['msg'] = 'Successfully '.($status == 1 ? 'Activated' : 'Deactivated').'!';
            }
            else throw new InvalidArgumentException('You do not have permission to update the content status!');
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
    public function get($id)
    {
        $respData = ['msg' => ""];
        $respCode = Response::HTTP_OK;
        try
        {
            $fromUnity = filter_var($this->request->getHeaderLine('X-Unity-Req'), FILTER_VALIDATE_BOOL);

            $post = $this->postModel
                ->select(
                    'title, date_format(published_time, \'%Y-%m-%d\') as published_time, content'.($fromUnity ? '' : ', is_active')
                )
                ->where('id', $id)
                ->where($fromUnity ? 'is_active' : 'id >=', 1)
                ->first();

            if(is_null($post)) throw new InvalidArgumentException('The selected post is no longer exist!');

            if($fromUnity)
            {
                $postImages = $this->postImageModel->getImages($id);
                $post['images'] = $postImages;
            }
            // set return data
            $respData['data'] = $fromUnity ? json_encode($post) : $post;
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