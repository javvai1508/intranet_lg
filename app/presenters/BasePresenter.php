<?php

namespace App\Presenters;

use Nette;
use Nette\Forms\Controls;

class BasePresenter extends Nette\Application\UI\Presenter {

    public function startup(): void {
        Parent::startup();

        if (!$this->user->isLoggedIn() && $this->getPresenter()->getName() != 'Sign') {
            $this->redirect('Sign:in');
        }
        // \Tracy\Debugger::barDump($this->getPresenter()->getName());
        
    }

    protected function showSession() {
        $session = $this->getSession();
        \Tracy\Debugger::barDump($session->getSection('deliveryNote')->deliveryNote);
        \Tracy\Debugger::barDump($session->getSection('package')->number);
        \Tracy\Debugger::barDump($session->getSection('package')->open);
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
