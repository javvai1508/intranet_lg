<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
/**
 * formulář pro zadání balení při uzavření DL
 */
class PackageTxtFormFactory {

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

        $form->addHidden('jeReq');
        
        $form->addTextArea('BaleniTx', 'Balení')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setHtmlAttribute('rows', '3')/*
                ->addConditionOn($form['jeReq'], $form::EQUAL, true)
                    ->setRequired('Vyplňte údaje o balení')*/;

        $form->addSubmit('send', 'Uložit')
                ->setHtmlAttribute('class', 'btn btn-lg btn-success');

        $form->addSubmit('cancel', 'Zpět')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
