<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro nový materiál zákazníka
 */
class StdInventoryFormFactory {

    private $dataModel;

    public function __construct(\App\Models\MaterialModel $materialModel) {
        $this->dataModel = $materialModel;
    }

    /**
     * @return Form
     */
    public function create($skupZbo) {

        $form = new Form;
        
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
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setItems($material)
                ->setPrompt('---')
                ->setHtmlAttribute('autocomplete', false)
                ->setDisabled();

        $form->addText('Mnozstvi', 'Množství')
                ->setHtmlAttribute('placeholder', 'Množství')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setHtmlAttribute('class', 'form-control form-control-lg no-enter')
                ->setRequired('Zadej množství')
                ->addRule(Form::FLOAT, 'Zadej číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'Uložit')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
