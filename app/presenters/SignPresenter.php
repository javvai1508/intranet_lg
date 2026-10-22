<?php

namespace   App\Presenters;

use         App\Forms\SignFormFactory;

class SignPresenter extends BasePresenter
{
    private $blockedUsers;
    
    /** @var SignFormFactory @inject */
    public $factory;
    
    public function __construct() {
        parent::__construct();
    }

    public function startup(): void {
        parent::startup();
    }

    public function renderIn() {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }

    protected function createComponentSignInForm() {
        $form = $this->factory->create();

        $form->onSuccess[] = function ($form, $values) {
                // $this->userManager->insertUsersLog($this->user->id, $this->user->identity->data['project_id'], 'login');
        };
        return $form;
    }

    public function actionOut() {
        $this->getUser()->logout(TRUE);
        $session = $this->getSession();
        $session->destroy();
        $this->redirect('in');
    }

    public function renderBlocked($time) {
        if (new \DateTimeImmutable() > new \DateTimeImmutable($time)) {
            $this->redirect('Sign:in');
        } else {
            $this->template->time = $time;
        }
    }

    public function renderDefault() {
        if($this->getHttpRequest()->getCookie('onlySign')){
            $this->redirect('Sign:in');
        }
    }
}

