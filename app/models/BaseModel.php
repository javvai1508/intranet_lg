<?php

namespace App\Models;

use Nette;


class BaseModel
{
    use Nette\SmartObject;
    
     /** @var \Dibi\Connection */
    protected $db;
    
    private $lang;

    public function __construct(\Dibi\Connection $connection) {
        $this->db = $connection;
        $this->lang = 'cs';
    }
    
    public function getDB(){
        return $this->db;
    }
}
