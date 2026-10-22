<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro uzavření DL
 */
class CloseDeliveryNoteFormFactory {


    /**
     * @return Form
     */
    public function create() {

        $form = new Form;

        $form->addSubmit('send', 'Opravdu uzavřít?')
                ->setHtmlAttribute('class', 'btn btn-lg btn-success');

        $form->addSubmit('cancel', 'Neuzavírat')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
