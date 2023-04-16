<?php

namespace App\Models;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Model;

class PostImage extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'post_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'post_id',
        'path',
        'description',
        'content',
        'created_by',
        'modified_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'modified_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getImages($post_id, $withContent = true, $limit = PHP_INT_MAX)
    {
        $rawSql = new RawSql(
            'CONCAT(\''.base_url(getenv("PUBLIC_UPLOAD_PATH")).'\', path) as path '.($withContent ? ', id, description, content' : '')
        );
        $postImages = $this->select($rawSql)
            ->where('post_id', $post_id)
            // Limit the number of images return, only get the first image/ cover of    the post
            ->limit($limit, 0)
            ->get()
            ->getResultArray($this->returnType);

        return $postImages;
    }
}
