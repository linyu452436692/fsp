<?php
namespace App\User\Model;
use FES\Core\Model;
class test extends Model {
    protected $table = 'test';
    protected $connection = 'user';
    public $timestamps = false;
    
}