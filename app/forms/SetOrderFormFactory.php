<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class SetOrderFormFactory {

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
        
        $form->addText('CisloOrg', 'Odběratel')
                ->setHtmlAttribute('placeholder', 'Číslo organizace')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo organizace')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('Firma')
                ->setHtmlAttribute('placeholder', 'Firma, místo')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej firmu / místo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('ParovaciZnak', 'Číslo objednávky')
                ->setHtmlAttribute('placeholder', 'Číslo objednávky')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo objednávky')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.continue')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
