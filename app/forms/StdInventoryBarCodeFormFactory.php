<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class StdInventoryBarCodeFormFactory {

    private $dataModel;

    private $translator;

    public function __construct(\App\Models\MaterialModel $materialModel, Translator $translator) {
        $this->dataModel = $materialModel;
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        $form->setTranslator($this->translator);
        
        /*
        $prumerZao = $this->dataModel->getPrumer1Zao();
        $laminat = $this->dataModel->getLaminat();
         * 
         */
        
        $form->addText('VyrCislo', 'Výrobní číslo')
                ->setHtmlAttribute('placeholder', 'Výrobní číslo')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                // ->setType('number')
                // ->setRequired('Zadej výrobní číslo')
                ->setHtmlAttribute('autocomplete', false);
/*
        $form->addCheckbox('TisknoutStitek', 'Generovat VČ?')
                ->setHtmlAttribute('class', 'form-control form-control-lg checkbox-large');
*/        
        $form->addSubmit('send', 'form.labels.next')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
