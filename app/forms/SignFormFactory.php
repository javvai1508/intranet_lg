<?php

namespace App\Forms;

use Nette,
    //Lang,
    Nette\Application\UI\Form,
    Nette\Security\User,
    Kdyby\Translation\Translator;

/**
 * formulář pro přihlášení
 */
class SignFormFactory{

    use Nette\SmartObject;
    
    /** @var User */
    private $user;
    private $translator;
    public $vaiale;

    public function __construct(User $user, Translator $translator) {
        $this->user = $user;
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create() {
        $form = new Form;
        $form->setTranslator($this->translator);
        $form->addText('username', 'Osobní číslo')
                ->setRequired('Zadejte osobní číslo')
                ->setAttribute('class', 'form-control')
                ->setType('number')
                ->setAttribute('placeholder', 'Osobní číslo');

        $form->addCheckbox('remember', 'form.labels.remember');
        
        // $form->addCheckbox('onlySign', 'form.labels.nexttimesignin');

        $form->addSubmit('send', 'form.labels.signin')
                ->setAttribute('class', 'btn btn-lg btn-primary col-xs-12');

        $form->onSuccess[] = [$this, 'formSucceeded'];
        return $form;
    }
    
    /**
     * zpracování formuláře
     * @param type $form
     * @param type $values
     */
    public function formSucceeded($form, $values) {
        if ($values->remember) {
            $this->user->setExpiration('14 days', FALSE);
        } else {
            $this->user->setExpiration('2 hours', TRUE);
        }

        try {
            $this->user->login($values->username);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}
