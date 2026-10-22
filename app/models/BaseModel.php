<?php

namespace App\Models;

use Nette;


class BaseModel
{
    use Nette\SmartObject;
    
     /** @var \Nette\Database\Connection */
    protected $db;
    

    public function __construct(\Nette\Database\Connection $connection) {
        $this->db = $connection;
    }
    
    protected function containsPattern(string $value): string
    {
        return '%' . strtr($value, ['[' => '[[]', '%' => '[%]', '_' => '[_]']) . '%';
    }

    public function getDB(){
        return $this->db;
    }
}
