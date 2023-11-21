<?php

namespace App\Presenters;

use Nette,
    Nette\Utils;

class HomepagePresenter extends BasePresenter {

    public function __construct() {
        parent::__construct();
    }

    public function startup() {
        parent::startup();
    }

    /**
     * 
     */
    public function renderDefault() {
        // \Tracy\Debugger::barDump($sessionDataTyp);
        /*
         *  redirect na presenter podle práv a přednastavení
         * 0 = expedice
         * 1 = prijMatZak - materiál zákazníka
         * 2 = skladMat - sklad materiálu
         */
        if ($this->user->getIdentity()->getData()["vychozi"] !== NULL) {
            if ($this->user->getIdentity()->getData()["vychozi"] == 0 AND $this->user->isInRole("expedice")) {
                $this->redirect('DeliveryNote:default');
            } elseif ($this->user->getIdentity()->getData()["vychozi"] == 1 AND $this->user->isInRole("prijMatZak")) {
                $this->redirect('CustomerMaterial:default');
            } elseif ($this->user->getIdentity()->getData()["vychozi"] == 2 AND $this->user->isInRole("skladMat")) {
                $this->redirect('Material:default');
            } else {
                
            }
        } else {
            
        }
    }

}
