<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro vyhledávání DL
 */
class SelectInventoryFormFactory {

    private $dataModel;

    private $translator;

    public function __construct(\App\Models\MaterialModel $materialModel, Translator $translator) {
        $this->dataModel = $materialModel;
        $this->translator = $translator;
    }

    /**
     * @return Form
     * string skupZbo - skupina zboží pro výběr položek
     */
    public function create() {

        $inventoryList = $this->dataModel->getInventoryList();

        $form = new Form;
        $form->setTranslator($this->translator);
        
        $form->addSelect('IDInvHl', 'Vyber inventuru')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setItems($inventoryList)
                ->setPrompt('---')
                ->setAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
