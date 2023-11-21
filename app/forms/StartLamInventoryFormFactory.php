<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class StartLamInventoryFormFactory {

    private $dataModel;

    private $translator;

    public function __construct(\App\Models\MaterialModel $materialModel, Translator $translator) {
        $this->dataModel = $materialModel;
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create($prumerZao, $laminat) {

        $form = new Form;
        $form->setTranslator($this->translator);
        
        $typLaminatu = $this->dataModel->getTypLaminatu();
        /*
        $prumerZao = $this->dataModel->getPrumer1Zao();
        $laminat = $this->dataModel->getLaminat();
         * 
         */
        
        $form->addText('VyrCislo', 'Výrobní číslo')
                ->setAttribute('placeholder', 'Výrobní číslo')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setAttribute('class', 'form-control form-control-lg no-enter')
                ->setType('number')
                // ->setRequired('Zadej výrobní číslo')
                ->setAttribute('autocomplete', false);

        $form->addSelect('TypLaminatu')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setItems($typLaminatu)
                ->setPrompt('---')
                ->setAttribute('autocomplete', false);

        $form->addSelect('Prumer1', 'Průměr')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setItems($prumerZao)
                ->setPrompt('---')
                ->setAttribute('autocomplete', false);

        $form->addSelect('Nazev', 'Název')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setItems($laminat)
                ->setPrompt('---')
                ->setAttribute('autocomplete', false);

        $form->addCheckbox('TisknoutStitek', 'Generovat VČ a tisknout nový štítek?')
                ->setAttribute('class', 'form-control form-control-lg checkbox-large');
        
        $form->addSubmit('send', 'form.labels.save')
                ->setAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
