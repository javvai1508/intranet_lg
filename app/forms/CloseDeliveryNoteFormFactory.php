<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro uzavření DL
 */
class CloseDeliveryNoteFormFactory {

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

        $form->addSubmit('send', 'Opravdu uzavřít?')
                ->setAttribute('class', 'btn btn-lg btn-success');

        $form->addSubmit('cancel', 'Neuzavírat')
                ->setAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
