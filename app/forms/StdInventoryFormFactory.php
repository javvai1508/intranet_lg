<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class StdInventoryFormFactory {

    private $dataModel;

    private $translator;

    public function __construct(\App\Models\MaterialModel $materialModel, Translator $translator) {
        $this->dataModel = $materialModel;
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create($skupZbo) {

        $form = new Form;
        $form->setTranslator($this->translator);
        
        $material = $this->dataModel->getMaterialInv($skupZbo);
        /*
        $prumerZao = $this->dataModel->getPrumer1Zao();
        $laminat = $this->dataModel->getLaminat();
         * 
         */
        
        $form->addHidden('IDZboSkladOri');
        $form->addHidden('NewSN');
        $form->addHidden('VyrCislo');

        $form->addSelect('IDZboSklad', 'Název')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setItems($material)
                ->setPrompt('---')
                ->setAttribute('autocomplete', false)
                ->setDisabled();

        $form->addText('Mnozstvi', 'Množství')
                ->setAttribute('placeholder', 'Množství')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setAttribute('class', 'form-control form-control-lg no-enter')
                ->setRequired('Zadej množství')
                ->addRule(Form::FLOAT, 'Zadej číslo')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.save')
                ->setAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
