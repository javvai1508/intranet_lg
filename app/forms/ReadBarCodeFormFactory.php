<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
/**
 * formulář pro vyhledávání DL
 */
class ReadBarCodeFormFactory {

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
        
        $form->addHidden('deliveryNoteId');
        $form->addHidden('dispatchId');
        $form->addHidden('receiptId');
        $form->addHidden('itemId');
        $form->addText('BarCode')
                ->setHtmlAttribute('placeholder', 'Čárový kód')
                ->setHtmlAttribute('class', 'form-control form-control-sm')
                ->setType('text')
                ->setRequired('Zadej kód výrobku')
                ->setHtmlAttribute('autocomplete', false);

        /*
        $form->addCheckbox('AddOne', 'Po jednom kusu?')
                ->setHtmlAttribute('class', 'form-control form-control-lg checkbox-large');
         * 
         */
        
        return $form;
    }

}
