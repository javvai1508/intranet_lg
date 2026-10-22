<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('placeholder', 'Číslo dodacího listu')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo dodacího listu')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
