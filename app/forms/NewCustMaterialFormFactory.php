<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Kdyby\Translation\Translator;
    
/**
 * formulář pro nový materiál zákazníka
 */
class NewCustMaterialFormFactory {

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
        
        $form->addText('CisloOrg', 'Odběratel')
                ->setAttribute('placeholder', 'Číslo organizace')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo organizace')
                ->setAttribute('autocomplete', false);

        $form->addText('Firma')
                ->setAttribute('placeholder', 'Firma, místo')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej firmu / místo')
                ->setAttribute('autocomplete', false);

        $form->addText('Mnozstvi', 'Množství')
                ->setAttribute('placeholder', 'Množství')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej množství')
                ->addRule(Form::INTEGER, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addText('Nazev', 'Název (výkres)')
                ->setAttribute('placeholder', 'Název (výkres)')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej název (výkres)')
                ->setAttribute('autocomplete', false);

        $form->addText('VPrumerJadra', 'Průměr jádra')
                ->setAttribute('placeholder', 'Průměr jádra')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej průměr jádra')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addText('VPrumerPogumu', 'Průměr pogumu')
                ->setAttribute('placeholder', 'Průměr pogumu')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej průměr pogumu')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addText('VDelkaPogumu', 'Délka pogumu')
                ->setAttribute('placeholder', 'Délka pogumu')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej délku pogumu')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addText('VDelkaValce', 'Celková délka')
                ->setAttribute('placeholder', 'Celková délka')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej celkovou délku')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addText('VCepL', 'Čep levý')
                ->setAttribute('placeholder', 'Čep levý')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired(false)
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addText('VCepP', 'Čep pravý')
                ->setAttribute('placeholder', 'Čep pravý')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired(false)
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setAttribute('autocomplete', false);

        $form->addCheckbox('TisknoutKusy', 'Tisknout štítky podle počtu kusů?')
                ->setAttribute('class', 'form-control form-control-lg checkbox-large');
        
        $form->addTextArea('Poznamka', 'Poznámka')
                ->setAttribute('class', 'form-control form-control-lg')
                ->setAttribute('rows', '3')
                ->setRequired(false);

        $form->addSubmit('send', 'form.labels.save')
                ->setAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
