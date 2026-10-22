<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro vyhledávání DL
 */
class SelectInventoryFormFactory {

    private $dataModel;

    public function __construct(\App\Models\MaterialModel $materialModel) {
        $this->dataModel = $materialModel;
    }

    /**
     * @return Form
     * string skupZbo - skupina zboží pro výběr položek
     */
    public function create() {

        $inventoryList = $this->dataModel->getInventoryList();

        $form = new Form;
        
        $form->addSelect('IDInvHl', 'Vyber inventuru')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setItems($inventoryList)
                ->setPrompt('---')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'Vybrat')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
