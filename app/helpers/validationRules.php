<?php
namespace App\Helpers;

use Nette;

class ValidationRules {
    
    /** @var DataModel @inject */
    public $dataModel;

    const QUANTITY = 'ValidationRules::validateQuantity';
    
    public static function validateQuantity(IControl $control) {
        
    }
    
}

