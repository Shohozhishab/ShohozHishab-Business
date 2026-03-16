<?php
// ADEL CODEIGNITER 4 CRUD GENERATOR

namespace App\Models;
use CodeIgniter\Model;

class PackageModel extends Model {

    protected $table = 'package';
    protected $primaryKey = 'package_id ';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['package_name','package_all_permission', 'package_admin_permission','status','createdDtm', 'createdBy', 'updateDtm', 'updatedBy', 'deleted', 'deletedRole'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

}