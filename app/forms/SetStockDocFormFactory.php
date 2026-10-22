<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('placeholder', 'Číslo dokladu')
                ->setHtmlAttribute('class', 'form-control form-control-sm')
                ->setRequired('Zadej číslo dokladu')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setHtmlAttribute('class', 'btn btn-sm btn-secondary');

        return $form;
    }

}
