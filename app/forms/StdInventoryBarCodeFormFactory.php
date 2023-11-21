<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
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
                ->setAttribute('placeholder', 'Výrobní číslo')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setAttribute('class', 'form-control form-control-lg')
                // ->setType('number')
                // ->setRequired('Zadej výrobní číslo')
                ->setAttribute('autocomplete', false);
/*
        $form->addCheckbox('TisknoutStitek', 'Generovat VČ?')
                ->setAttribute('class', 'form-control form-control-lg checkbox-large');
*/        
        $form->addSubmit('send', 'form.labels.next')
                ->setAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
