<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro zadání čísla skladové karty
 */
class SelectCustMaterialFormFactory {

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
        
        $form->addText('CisloKarty')
                ->setAttribute('placeholder', 'Číslo skladové karty')
                ->setAttribute('class', 'form-control form-control-sm')
                ->setType('text')
                ->setRequired('Zadej číslo skladové karty')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
