<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('class', 'btn btn-lg btn-success');

        $form->addSubmit('cancel', 'Neuzavírat')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
