<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setItems($inventoryList)
                ->setPrompt('---')
                ->setHtmlAttribute('autocomplete', false);

        $form->addSubmit('send', 'form.labels.select')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
