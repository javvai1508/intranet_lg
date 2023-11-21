<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class SetStockDocFormFactory {

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
        
        $form->addText('ParovaciZnak')
                ->setAttribute('placeholder', 'Číslo dokladu')
                ->setAttribute('class', 'form-control form-control-sm')
                ->setRequired('Zadej číslo dokladu')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
