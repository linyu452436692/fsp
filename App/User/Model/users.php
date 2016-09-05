<?php
namespace App\User\Model;
class users extends \FES\Core\Model {
    protected $table = 'users';
    protected $connection = 'user';
    public $timestamps = false;
    
}