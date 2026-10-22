<?php

namespace App\Forms;

use Nette,
    Nette\Application\UI\Form,
    Contributte\Translation\Translator;
    
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
                ->setHtmlAttribute('placeholder', 'Číslo organizace')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej číslo organizace')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('Firma')
                ->setHtmlAttribute('placeholder', 'Firma, místo')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej firmu / místo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('Mnozstvi', 'Množství')
                ->setHtmlAttribute('placeholder', 'Množství')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej množství')
                ->addRule(Form::INTEGER, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('Nazev', 'Název (výkres)')
                ->setHtmlAttribute('placeholder', 'Název (výkres)')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('text')
                ->setRequired('Zadej název (výkres)')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('VPrumerJadra', 'Průměr jádra')
                ->setHtmlAttribute('placeholder', 'Průměr jádra')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej průměr jádra')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('VPrumerPogumu', 'Průměr pogumu')
                ->setHtmlAttribute('placeholder', 'Průměr pogumu')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej průměr pogumu')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('VDelkaPogumu', 'Délka pogumu')
                ->setHtmlAttribute('placeholder', 'Délka pogumu')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej délku pogumu')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('VDelkaValce', 'Celková délka')
                ->setHtmlAttribute('placeholder', 'Celková délka')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired('Zadej celkovou délku')
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('VCepL', 'Čep levý')
                ->setHtmlAttribute('placeholder', 'Čep levý')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired(false)
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addText('VCepP', 'Čep pravý')
                ->setHtmlAttribute('placeholder', 'Čep pravý')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setType('number')
                ->setRequired(false)
                ->addRule(Form::FLOAT, 'Musí být číslo')
                ->setHtmlAttribute('autocomplete', false);

        $form->addCheckbox('TisknoutKusy', 'Tisknout štítky podle počtu kusů?')
                ->setHtmlAttribute('class', 'form-control form-control-lg checkbox-large');
        
        $form->addTextArea('Poznamka', 'Poznámka')
                ->setHtmlAttribute('class', 'form-control form-control-lg')
                ->setHtmlAttribute('rows', '3')
                ->setRequired(false);

        $form->addSubmit('send', 'form.labels.save')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary');

        $form->getElementPrototype()->autocomplete = 'off';
        
        return $form;
    }

}
