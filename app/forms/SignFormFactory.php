<?php

namespace App\Forms;

use Nette,
    //Lang,
    Nette\Application\UI\Form,
    Nette\Security\User;

/**
 * formulář pro přihlášení
 */
class SignFormFactory{

    use Nette\SmartObject;
    
    /** @var User */
    private $user;
    public $vaiale;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create() {
        $form = new Form;
        $form->addText('username', 'Osobní číslo')
                ->setRequired('Zadejte osobní číslo')
                ->setHtmlAttribute('class', 'form-control')
                ->setType('number')
                ->setHtmlAttribute('placeholder', 'Osobní číslo');

        $form->addCheckbox('remember', 'Zapamatovat si mě?');
        
        // $form->addCheckbox('onlySign', 'Příště ihned na přihlášení');

        $form->addSubmit('send', 'Přihlásit')
                ->setHtmlAttribute('class', 'btn btn-lg btn-primary col-xs-12');

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
            // Nette forwards only explicitly supplied arguments to Authenticator.
            $this->user->login($values->username, '');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}
