<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro zadání čísla skladové karty
 */
class SelectCustMaterialFormFactory {


    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        
        $form->addText('CisloKarty')
                ->setHtmlAttribute('placeholder', 'Číslo skladové karty')
                ->setHtmlAttribute('class', 'form-control form-control-sm')
                ->setType('text')
                ->setRequired('Zadej číslo skladové karty')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'Vybrat')
                ->setHtmlAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
