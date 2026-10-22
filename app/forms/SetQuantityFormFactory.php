<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro vyhledávání DL
 */
class SetQuantityFormFactory {


    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        
        $form->addHidden('deliveryNoteId');
        $form->addHidden('dispatchId');
        $form->addHidden('idPohyb');
        $form->addHidden('checkRelation');
        $form->addHidden('idVyrCis');
        $form->addHidden('vyrCis');
        $form->addText('quantity')
                ->setHtmlAttribute('placeholder', 'Množství')
                ->setHtmlAttribute('class', 'form-control form-control-sm text-center ajax')
                ->setType('number')
                ->setRequired('Zadej množství')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'OK')
                ->setHtmlAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
