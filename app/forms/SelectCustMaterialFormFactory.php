<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('placeholder', 'Číslo skladové karty')
                ->setHtmlAttribute('class', 'form-control form-control-sm')
                ->setType('text')
                ->setRequired('Zadej číslo skladové karty')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setHtmlAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
