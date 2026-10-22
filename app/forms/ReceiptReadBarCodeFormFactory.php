<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form;
    
/**
 * formulář pro vyhledávání DL
 */
class ReceiptReadBarCodeFormFactory {


    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        
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
