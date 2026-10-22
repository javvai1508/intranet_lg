<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class StartInventoryFormFactory {

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
                ->setHtmlAttribute('placeholder', 'Výrobní číslo')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setHtmlAttribute('class', 'form-control form-control-lg no-enter')
                ->setType('number')
                // ->setRequired('Zadej výrobní číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSelect('TypLaminatu')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setItems($typLaminatu)
                ->setPrompt('---')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSelect('Prumer1', 'Průměr')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setItems($prumerZao)
                ->setPrompt('---')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSelect('Nazev', 'Název')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setItems($laminat)
                ->setPrompt('---')
                ->setHtmlAttribute('autocomplete', false);

        $form->addCheckbox('TisknoutStitek', 'Generovat VČ a tisknout nový štítek?')
                ->setHtmlAttribute('class', 'form-control form-control-lg checkbox-large');
        
        $form->addSubmit('send', 'form.labels.save')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
