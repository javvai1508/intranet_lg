<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro nový materiál zákazníka
 */
class SetOrderFormFactory {


    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        
        $form->addText('CisloOrg', 'Odběratel')
                ->setHtmlAttribute('placeholder', 'Číslo organizace')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo organizace')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('Firma')
                ->setHtmlAttribute('placeholder', 'Firma, místo')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej firmu / místo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('ParovaciZnak', 'Číslo objednávky')
                ->setHtmlAttribute('placeholder', 'Číslo objednávky')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo objednávky')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'Pokračovat')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
