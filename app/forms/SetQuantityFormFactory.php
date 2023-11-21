<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro vyhledávání DL
 */
class SetQuantityFormFactory {

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
        
        $form->addHidden('deliveryNoteId');
        $form->addHidden('dispatchId');
        $form->addHidden('idPohyb');
        $form->addHidden('idVyrCis');
        $form->addText('quantity')
                ->setAttribute('placeholder', 'Množství')
                ->setAttribute('class', 'form-control form-control-sm text-center ajax')
                ->setType('number')
                ->setRequired('Zadej množství')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'OK')
                ->setAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
