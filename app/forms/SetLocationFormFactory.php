<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
/**
 * formulář pro vyhledávání DL
 */
class SetLocationFormFactory {

    private $translator;

    public function __construct(Translator $translator) {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create($locationArr) {

        $form = new Form;
        $form->setTranslator($this->translator);
        
        $form->addHidden('deliveryNoteId');
        $form->addSelect('UmisteniKod', 'Umístění')
                ->setItems($locationArr)
                ->setPrompt('---')
                ->setHtmlAttribute('placeholder', 'vyber umístění')
                ->setHtmlAttribute('class', 'form-control form-control-lg');

        $form->addSubmit('send', 'OK')
                ->setHtmlAttribute('class', 'btn btn-lg btn-secondary');

        return $form;
    }

}
