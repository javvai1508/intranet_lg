<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro nový materiál zákazníka
 */
class StdInventoryBarCodeFormFactory {

    private $dataModel;

    public function __construct(\App\Models\MaterialModel $materialModel) {
        $this->dataModel = $materialModel;
    }

    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        
        /*
        $prumerZao = $this->dataModel->getPrumer1Zao();
        $laminat = $this->dataModel->getLaminat();
         * 
         */
        
        $form->addText('VyrCislo', 'Výrobní číslo')
                ->setHtmlAttribute('placeholder', 'Výrobní číslo')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                // ->setType('number')
                // ->setRequired('Zadej výrobní číslo')
                ->setHtmlAttribute('autocomplete', false);
/*
        $form->addCheckbox('TisknoutStitek', 'Generovat VČ?')
                ->setHtmlAttribute('class', 'form-control form-control-lg checkbox-large');
*/        
        $form->addSubmit('send', 'Další')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
