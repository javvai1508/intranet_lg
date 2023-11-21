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

    public function startup() {
        parent::startup();
    }

    public function renderIn() {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }

    protected function createComponentSignInForm() {
        $form = $this->factory->create();

        $form->onError[] = function ($form) {
            $values = $form->getValues();
            $this->blockedUsers = $this->blockingModel->getBlockedUser($values->username);

            if (!$this->blockedUsers) {
                $this->blockingModel->insertBlockedUser($values->username);
            } else if ($this->blockedUsers['lockedTo'] == NULL) {
                $this->blockingModel->increaseBlockedUser($this->blockedUsers);
            } else if ($this->blockedUsers['lockedTo'] != NULL) {
                $this->blockingModel->increaseBlockedUserTstamp($this->blockedUsers);
            }
        };

        $form->onValidate[] = function($form, $values) {
            /*
            $this->blockedUsers = $this->blockingModel->getBlockedUser($values->username);
            
            if ($this->blockedUsers) {
                $datetime = new \DibiDateTime();

                if ($this->blockedUsers['count'] >= 5 AND $this->blockedUsers['lockedTo'] == NULL) {
                    $this->blockingModel->setTstampLockTo($values->username);
                    $this->flashMessage('You are blocked!');
                    $form->addError('You are blocked!');
                    $time = new \DibiDateTime();
                    $this->redirect('Sign:blocked', $time->setTimestamp(time() + 3600)->__toString());
                } else if ($this->blockedUsers['count'] >= 5 AND $this->blockedUsers['lockedTo'] >= $datetime) {
                    $form->addError('You are blocked!');
                    $this->redirect('Sign:blocked', $this->blockedUsers['lockedTo']->__toString());
                }
            }
             * 
             */
        };

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
        if (new \DibiDateTime() > new\DibiDateTime($time)) {
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

