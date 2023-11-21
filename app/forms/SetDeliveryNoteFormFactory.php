<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro vyhledávání DL
 */
class SetDeliveryNoteFormFactory {

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
        
        $form->addText('ParovaciZnak', 'Číslo dodacího listu')
                ->setAttribute('placeholder', 'Číslo dodacího listu')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo dodacího listu')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
