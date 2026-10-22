<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro nový materiál zákazníka
 */
class SetStockDocFormFactory {


    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        
        $form->addText('ParovaciZnak')
                ->setHtmlAttribute('placeholder', 'Číslo dokladu')
                ->setHtmlAttribute('class', 'form-control form-control-sm')
                ->setRequired('Zadej číslo dokladu')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'Vybrat')
                ->setHtmlAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
