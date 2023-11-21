<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class SetOrderFormFactory {

    private $translator;

    public function __construct(Translator $translator) {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        $form->setTranslator($this->translator);
        
        $form->addText('CisloOrg', 'Odběratel')
                ->setAttribute('placeholder', 'Číslo organizace')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo organizace')
                ->setAttribute('autocomplete', false);

        $form->addText('Firma')
                ->setAttribute('placeholder', 'Firma, místo')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej firmu / místo')
                ->setAttribute('autocomplete', false);

        $form->addText('ParovaciZnak', 'Číslo objednávky')
                ->setAttribute('placeholder', 'Číslo objednávky')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo objednávky')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.continue')
                ->setAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
