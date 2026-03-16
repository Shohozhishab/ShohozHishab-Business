<?php
// ADEL CODEIGNITER 4 CRUD GENERATOR

namespace App\Models;
use CodeIgniter\Model;

class Gen_settings_superModel extends Model {

    protected $table = 'gen_settings_super';
    protected $primaryKey = 'settings_id_sup';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['settings_id_sup', 'label','value','createdDtm', 'createdBy', 'updateDtm', 'updatedBy', 'deleted', 'deletedRole'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

}