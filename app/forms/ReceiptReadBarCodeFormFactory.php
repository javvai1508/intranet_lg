<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('placeholder', 'Výrobní číslo')
                ->setHtmlAttribute('class', 'form-control form-control-sm no-enter')
                ->setType('text')
                ->setRequired('Zadej výrobní číslo')
                ->setHtmlAttribute('autocomplete', false);

        return $form;
    }

}
