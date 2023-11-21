<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro vyhledávání DL
 */
class ReceiptReadBarCodeFormFactory {

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
        
        $form->addHidden('receiptId');
        $form->addText('BarCode')
                ->setAttribute('placeholder', 'Výrobní číslo')
                ->setAttribute('class', 'form-control form-control-sm no-enter')
                ->setType('text')
                ->setRequired('Zadej výrobní číslo')
                ->setAttribute('autocomplete', false);

        return $form;
    }

}
