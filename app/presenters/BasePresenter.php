<?php

namespace App\Presenters;

use Nette;

class BasePresenter extends Nette\Application\UI\Presenter {

    const LANG = ['en', 'cs'];

    private $session;

    /** @persistent */
    public $locale;

    /** @inject @var \Kdyby\Translation\Translator */
    public $translator;

    public function startup() {
        Parent::startup();

        $sessionLang = $this->getSession()->getSection('lang')->lang;
        if (isset($sessionLang) && $this->locale != $sessionLang) {
            $this->locale = $this->getSession()->getSection('lang')->lang;
        } else {
            $this->getSession()->getSection('lang')->lang = $this->locale;
        }
        /*
        $this->template->lang = $this->locale;
        $this->template->languages = $this->userManager->getLanguages();
        */
        /*
          $httpRequest = $this->getHttpRequest();
          $ipHigh = ip2long('192.168.2.254');
          $ipLow = ip2long('192.168.2.1');
          $ip = ip2long($httpRequest->getRemoteAddress());
          if (($ip > $ipHigh || $ip < $ipLow) && $this->getPresenter()->name != 'Sign'){
          }
         * 
         */
        if (!$this->user->isLoggedIn() && $this->getPresenter()->name != 'Sign') {
            $this->getSession()->destroy();
            $this->redirect('Sign:in');
        }
        // \Tracy\Debugger::barDump($this->getPresenter()->name);
        
    }

    protected function showSession() {
        $session = $this->getSession();
        \Tracy\Debugger::barDump($session->getSection('lang')->lang);
        \Tracy\Debugger::barDump($session->getSection('deliveryNote')->deliveryNote);
        \Tracy\Debugger::barDump($session->getSection('package')->number);
        \Tracy\Debugger::barDump($session->getSection('package')->open);
    }

    /**
     * změna jazyka
     * @param type $lang
     */
    public function handleLang($lang) {
        $this->locale = $lang;
        $this->getSession()->getSection('lang')->lang = $lang;
        $this->getHttpResponse()->setCookie('lang', $lang, '100 days');
        // $sectionBc = $this->session->getSection('breadCrumb');
        // $sectionBc->bc = $this->breadCrumbModel->getMenuLang($lang, $this->user->id, $this->projectId);
        $this->redirect('this');
    }

    /**
     * úprava formuláře pro bootstrap
     * @param type $form
     * @param type $ajax
     * @return type
     */
    protected function twitterBootstrapCompatibility($form, $ajax = false) {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div ';
        $renderer->wrappers['label']['container'] = 'div class="control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        // make form and controls compatible with Twitter Bootstrap

        if ($ajax) {
            $form->getElementPrototype()->class('form-horizontal ajax');
        } else {
            $form->getElementPrototype()->class('form-horizontal');
        }

        foreach ($form->getControls() as $control) {
            if ($control instanceof Controls\Button) {
                $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
                $usedPrimary = TRUE;
            } elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
                $control->getControlPrototype()->addClass('form-control');
            } elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
                $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
            }
        }

        return $form;
    }

}
