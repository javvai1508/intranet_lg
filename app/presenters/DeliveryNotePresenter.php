<?php

namespace App\Presenters;

use App\Models\DeliveryNoteModel,
    App\Forms\SetDeliveryNoteFormFactory,
    App\Forms\SetLocationFormFactory,
    App\Forms\ReadBarCodeFormFactory,
    App\Forms\SetQuantityFormFactory,
    App\Forms\SetQuantityFromStockFormFactory,
    App\Forms\SetFinalOperationQuantityFormFactory,
    App\Forms\PackageTxtFormFactory,
    App\Forms\CloseDeliveryNoteFormFactory,
    Nette\Application\UI\Form;

class DeliveryNotePresenter extends BasePresenter {

    /** @var DeliveryNoteModel @inject */
    public $dataModel;

    /** @var SetDeliveryNoteFormFactory @inject */
    public $setDeliveryNoteFactory;

    /** @var SetLocationFormFactory @inject */
    public $setLocationFactory;

    /** @var ReadBarCodeFormFactory @inject */
    public $readBarCodeFactory;

    /** @var SetQuantityFormFactory @inject */
    public $setQuantityFactory;

    /** @var SetQuantityFromStockFormFactory @inject */
    public $setQuantityFromStockFactory;

    /** @var SetFinalOperationQuantityFormFactory @inject */
    public $setFinalOperationQuantityFactory;

    /** @var PackageTxtFormFactory @inject */
    public $packageTxtFactory;

    /** @var CloseDeliveryNoteFormFactory @inject */
    public $closeDeliveryNoteFactory;

    /** @var DeliveryNoteId */
    private $deliveryNoteId;

    /** @var PackageNr */
    private $packageNr;

    /** @var packageOpen */
    private $packageOpen;

    public function __construct() {
        parent::__construct();
    }

    public function startup() {
        parent::startup();

        if (!$this->user->isInRole("expedice")) {
            $this->redirect("Homepage:");
        }
    }

    public function renderDefault() {
        if (isset($this->getSession('deliveryNoteId')->deliveryNoteId)) {
            $this->deliveryNoteId = $this->getSession('deliveryNoteId')->deliveryNoteId;
            $this->packageNr = $this->getSession('package')->number;
            $this->packageOpen = $this->getSession('package')->open;

            $deliveryNote = $this->dataModel->getDeliveryNote($this->deliveryNoteId);
            $deliveryNoteItems = $this->dataModel->getDeliveryNoteItems($this->deliveryNoteId);

            $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
            $this->template->deliveryNote = $deliveryNote;
            $this->template->deliveryNoteItems = $deliveryNoteItems;
            $this->template->packageOpen = $this->packageOpen;

            if ($this["setLocationForm"]) {
                $locationDefaults = array('deliveryNoteId' => $this->deliveryNoteId);
                $this["setLocationForm"]->setDefaults($locationDefaults);
            }

            if ($this["readBarCodeForm"]) {
                $readBarCodeDefaults = array('deliveryNoteId' => $this->deliveryNoteId);
                $this["readBarCodeForm"]->setDefaults($readBarCodeDefaults);
            }
            \Tracy\Debugger::barDump($this->template->deliveryNote);
        }

// \Tracy\Debugger::barDump($this->showSession());
    }

    /* ------------- vyhledat DL -------------------------------------- */

    /**
     * SetDeliveryNote form factory.
     * vyhledávní DL 
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSetDeliveryNoteForm() {
        $form = $this->setDeliveryNoteFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {

            $deliveryNoteId = $this->dataModel->checkDeliveryNote($values->ParovaciZnak);
            if (!$deliveryNoteId) {
                $this->flashMessage('Vybraný dodací list není v evidenci!', 'error');

                $this->redirect('DeliveryNote:default');
            } else {

                $deliveryNoteIdSess = $this->getSession('deliveryNoteId');
                $deliveryNoteIdSess->deliveryNoteId = $deliveryNoteId;

                $packageSess = $this->getSession('package');
                $packageSess->open = false;
                $packageSess->number = $this->dataModel->getNextPackage($deliveryNoteId);

                $this->redirect('DeliveryNote:default');
            }
        };

        return $form;
    }

    /* ------------- doplnit umístění -------------------------------------- */

    /**
     * SetLocation form factory.
     * doplnění umístění
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSetLocationForm() {
        $locationArr = $this->dataModel->getLocationArr();

        $form = $this->setLocationFactory->create($locationArr);
        $form->onSuccess[] = function (Form $form, $values) {

            $this->dataModel->setLocation($values);

            $this->redirect('DeliveryNote:default');
        };

        return $form;
    }

    /* ------------- čtení čárového kódu -------------------------------------- */

    /**
     * 
     * @param type $deliveryNoteId
     * @param type $serialNumber
     */
    private function processBarCode($deliveryNoteId, $serialNumber) {
        // kontrola existence VČ + kontrola existence v DL
        $packageSess = $this->getSession('package');
        $packageOpen = $packageSess->open;
        $packageNr = $packageSess->number;

        $checkSNRowStock = false;
        $errorMsg = "";
        $errorMsgType = "";
        // nejdřív zkontrolovat, jestli se nejedná o skladovou položku
        $partNumberRow = $this->dataModel->checkPartNumber($deliveryNoteId, $serialNumber);
        if ($partNumberRow) {
            // zpracování skladové položky (_SVyrabetNaSklad) - dotáhnout VČ postupně podle data vložení
            if ($partNumberRow->JeSklad != 1) {
                $errorMsg = "Dílec není nastaven pro použití jako skladová položka";
                $errorMsgType = "error";
                //$this->flashMessage('Dílec není nastaven pro použíti jako skladová položka', 'error');
            } elseif ($partNumberRow->MnozstviPriprava >= $partNumberRow->MnozstviPozadavek) {
                $errorMsg = "Položka je v tomto dodacím listu již připravená";
                $errorMsgType = "error";
                //$this->flashMessage('Položka je v tomto dodacím listu již připravená', 'error');
            } else {
                // serviety - TabKmenZbozi._SVyrabetNaSklad
                $this->redirect('DeliveryNote:setQuantityFromStock', $deliveryNoteId, $serialNumber);
            }
        } else {
            $itemId = null;
            $serialNumberRow = $this->dataModel->checkSerialNumber($deliveryNoteId, $serialNumber);
            if ($serialNumberRow) {
                $itemId = $serialNumberRow->IDPohyb;
            } 
            $resVCSklad = $this->dataModel->checkSNFromStock($deliveryNoteId, $serialNumber);
            if ($resVCSklad && $resVCSklad->IDVyrCP && ($resVCSklad->PozadVCP - $resVCSklad->MnozstviVCP) > 0) {
                // výrobní číslo je na dodacím listu převedené z EP - dodávka ze skladu - xvv210628
                $packageNr = $this->dataModel->updateDeliveryNoteItemFromStock($deliveryNoteId, $resVCSklad->IDPohyb, $resVCSklad->IDVyrCis, $resVCSklad->PozadVCP - $resVCSklad->MnozstviVCP, $packageOpen, $packageNr);
                $packageSess->number = $packageNr;
            } else {
                if (!$serialNumberRow OR ($serialNumberRow AND $serialNumberRow->MnozstviPoPrijmuAVydeji <= 0))
                {
                    // VČ neexistuje - kontrola, jestli jde odvést a případně odvést 
                    $resVyrCis = $this->dataModel->checkFinalOperation($serialNumber);
                    if (!$resVyrCis) {
                        $checkSNRowStock = true;
                        $errorMsg = "Výrobní číslo " . $serialNumber . " vůbec není v evidenci?";
                        $errorMsgType = "error";
                        //$this->flashMessage('Výrobní číslo ' . $serialNumber . ' není v evidenci u žádného výrobního příkazu!', 'error');
                    } elseif ($resVyrCis->res == 1) {
                        // dialog odvedení operace - pouze se odvede požadované množství
                        $errorMsg = "Výrobní číslo " . $serialNumber . " není skladem, ale zkusím ho odvést z výroby!";
                        $errorMsgType = "info";
                        $this->redirect('DeliveryNote:setFinalOperationQuantity', array('deliveryNoteId' => $deliveryNoteId, 'serialNumber' => $serialNumber, 'availableQty' => $resVyrCis->MnKOdvodu));
                        //$this->flashMessage('Výrobní číslo " . $serialNumber . " není skladem, ale lze ho odvést!', 'info');
                    } elseif ($resVyrCis->res == 2) {
                        $checkSNRowStock = true;
                        $errorMsg = "Výrobní číslo " . $serialNumber . " není skladem a není dostatek živých kusů ve výrobě!";
                        $errorMsgType = "error";
                        //$this->flashMessage('Výrobní číslo " . $serialNumber . " není skladem a není dostatek živých kusů ve výrobě!', 'error');
                    } elseif ($resVyrCis->res == 3) {
                        $checkSNRowStock = true;
                        $errorMsg = "Výrobní číslo " . $serialNumber . " není skladem a odpovídající výrobní příkaz není ve stavu Zadáno!";
                        $errorMsgType = "error";
                        //$this->flashMessage('Výrobní číslo ' . $serialNumber . ' není skladem a odpovídající výrobní příkaz není ve stavu Zadáno!', 'error');
                    } else {
                        $checkSNRowStock = true;
                        $errorMsg = "Výrobní číslo " . $serialNumber . " není v evidenci u žádného výrobního příkazu (11)!";
                        $errorMsgType = "error";
                        //$this->flashMessage('Výrobní číslo ' . $serialNumber . ' není v evidenci u žádného výrobního příkazu!', 'error');
                    }
                }
                if ($serialNumberRow AND $serialNumberRow->MnozstviPoPrijmuAVydeji > 0 AND $serialNumberRow->MnozstviZbyva > 0) {
                    // běžný postup - kontrola vztahu EP - VP
                    if (!isset($serialNumberRow->IDPohyb)) {
                        // VČ není na aktuálním dodáku - kontrola EP, jestli je pro stejného odběratele jinde, případně doplnit (kontrola zatím ne!)
                        $checkSNRowStock = true;
                        $errorMsg = "Výrobní číslo " . $serialNumber . " nepatří na aktuální DL nebo už je položka připravená!";
                        $errorMsgType = "danger";
                        //$this->flashMessage('Výrobní číslo ' . $serialNumber . ' nepatří na aktuální DL nebo už je položka připravená!', 'danger');
                    } elseif ($serialNumberRow->MnozstviPoPrijmuAVydeji > 1) {
                        // výrobního čísla je na skladu více než 1 kus - úprava množství podle potřeby - redirect na další formulář
                        $errorMsg = "";
                        $errorMsgType = "";
                        $this->redirect('DeliveryNote:setQuantity', $deliveryNoteId, $serialNumber, true);
                        // $this->flashMessage('Zpracuj část', 'info');
                    } elseif ($serialNumberRow->MnozstviPoPrijmuAVydeji == 1) {
                        // výrobního čísla je na skladu jeden kus - přidat na DL
                        $errorMsg = "";
                        $errorMsgType = "";
                        $packageNr = $this->dataModel->updateDeliveryNoteItem($deliveryNoteId, $serialNumberRow->IDPohyb, $serialNumberRow->IDVyrCis, $serialNumberRow->MnozstviPoPrijmuAVydeji, $packageOpen, $packageNr);
                        $packageSess->number = $packageNr;
                    } else {
                        // sem by se to asi nemělo vůbec dostat
                        $checkSNRowStock = true;
                        $errorMsg = "Proč jsem tady (1)?";
                        $errorMsgType = "info";
                        $this->flashMessage('Proč jsem tady?', 'info');
                    }
                } else {
                    $checkSNRowStock = true;
                    if ($errorMsg == "") {
                        $errorMsg = "VČ nepatří na aktuální DL nebo není na skladu nebo nesouhlasí nastavení výroby na sklad";
                        $errorMsgType = "error";
                    }
                }
                if ($checkSNRowStock) {
                    $serialNumberRowStock = $this->dataModel->checkSerialNumberStock($deliveryNoteId, $serialNumber);
                    if (isset($serialNumberRowStock->IDPohyb)) {
                        $itemId = $serialNumberRowStock->IDPohyb;
                        // nekontrolovat EP-VP (_SNekontrolovatEPVP) - doplnit do všech nesplněných odpovídajících položek (IDZboSklad)
                        if ($serialNumberRowStock->MnozstviPoPrijmuAVydeji > 1) {
                            // výrobního čísla je na skladu více než 1 kus - úprava množství podle potřeby - redirect na další formulář
                            $errorMsg = "";
                            $errorMsgType = "";
                            $this->redirect('DeliveryNote:setQuantity', $deliveryNoteId, $serialNumber, false);
                            // $this->flashMessage('Zpracuj část', 'info');
                        } elseif ($serialNumberRowStock->MnozstviPoPrijmuAVydeji == 1) {
                            // výrobního čísla je na skladu jeden kus - přidat na DL
                            $errorMsg = "";
                            $errorMsgType = "";
                            $packageNr = $this->dataModel->processDeliveryNoteNoCheckRelation($deliveryNoteId, $serialNumberRowStock->IDVyrCis, 1, $packageOpen, $packageNr);
                            $packageSess->number = $packageNr;
                        } else {
                            // sem by se to asi nemělo vůbec dostat
                            $errorMsg = "Výrobní číslo " . $serialNumber . " není na skladu?"; //$errorMsg = "Proč jsem tady (2)?";
                            $errorMsgType = "error";
                            //$this->flashMessage('Proč jsem tady?', 'info');
                        }
                    } else {
                        if ($errorMsg == "") {
                            $errorMsg = "Výrobní číslo " . $serialNumber . " neexistuje(20)?"; 
                            $errorMsgType = "error";
                        }
                    }
                }
            }
        } 
        if ($errorMsg != "") {
            $this->flashMessage($errorMsg, $errorMsgType);
        }
        // \Tracy\Debugger::barDump($serialNumberRow);
        // \Tracy\Debugger::barDump($resVCSklad);
        // \Tracy\Debugger::barDump($res);
        // $this->flashMessage($values->BarCode);
        if (isset($itemId)) {
            $this->redirect('DeliveryNote:itemDetail', $itemId);
        } else {
            $this->redirect('DeliveryNote:default');
        }

    }

    /**
     * ReadBarCode form factory.
     * načtení výrobního čísla čárovým kódem a zpracování
     * přečte čárový kód
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentReadBarCodeForm() {
        $form = $this->readBarCodeFactory->create();
        /*
          $form->onValidate[] = function (Form $form) {
          $values = $form->values;

          };
         * 
         */
        $form->onSuccess[] = function (Form $form, $values) {
// kontrola existence VČ + kontrola existence v DL
            $this->processBarCode($values->deliveryNoteId, $values->BarCode);
        };

        return $form;
    }

    /**
     * evidence odváděcí operace
     * @param type $serialNumber
     */
    public function actionFinalizeOperation($deliveryNoteId, $serialNumber, $quantity) {
        $res = $this->dataModel->finalizeOperation($this->user->getIdentity()->getData()['username'], $serialNumber, $quantity);
        if ($res <> "") {
            $this->flashMessage($res, 'error');
        } else {
// znovu pustit zpracování výrobního čísla
            $this->processBarCode($deliveryNoteId, $serialNumber);
        }
    }

    /**
     * úprava množství výrobního čísla při odvádění operace se šarží
     * @param int $deliveryNoteId
     * @param string $serialNumber
     */
    public function renderSetFinalOperationQuantity($deliveryNoteId, $serialNumber, $availableQty) {
        $deliveryNote = $this->dataModel->getDeliveryNote($deliveryNoteId);
// kontrola, kdy to můžu udělat (co musí být v DL vyplněné)

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;
        $this->packageNr = $this->getSession('package')->number;
        $this->packageOpen = $this->getSession('package')->open;

        $serialNumberRow = $this->dataModel->checkSerialNumber($deliveryNoteId, $serialNumber);
        $this->template->deliveryNoteId = $deliveryNoteId;
        $this->template->serialNumberRow = $serialNumberRow;
        $this->template->packageOpen = $this->packageOpen;
        $this->template->serialNumber = $serialNumber;

        \Tracy\Debugger::barDump($serialNumberRow);

        $setQuantityDefaults = array('deliveryNoteId' => $deliveryNote->ID, 'serialNumber' => $serialNumber, 'quantity' => $availableQty);
        $this["setFinalOperationQuantityForm"]->setDefaults($setQuantityDefaults);
    }

    /**
     * formulář pro úpravu množství při odvádění operace se šarží
     * @return type
     */
    public function createComponentSetFinalOperationQuantityForm() {
        $form = $this->setFinalOperationQuantityFactory->create();

// $form->onValidate[] = array($this, "validateEditQuantityForm");

        $form->onSuccess[] = function (Form $form, $values) {

            if ($form['send']->isSubmittedBy()) {
                $this->actionFinalizeOperation($values->deliveryNoteId, $values->serialNumber, $values->quantity);
            }


            $this->redirect('DeliveryNote:default');
        };

        return $form;
    }

    /**
     * úprava množství výrobního čísla při zadání čárovým kódem
     * @param type $serialNumberRow
     */
    public function renderSetQuantity($deliveryNoteId, $serialNumber, $checkRelation) {
        $deliveryNote = $this->dataModel->getDeliveryNote($deliveryNoteId);
// kontrola, kdy to můžu udělat (co musí být v DL vyplněné)

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;
        $this->packageNr = $this->getSession('package')->number;
        $this->packageOpen = $this->getSession('package')->open;

        if ($checkRelation) {
            $serialNumberRow = $this->dataModel->checkSerialNumber($deliveryNoteId, $serialNumber);
        } else {
            $serialNumberRow = $this->dataModel->checkSerialNumberStock($deliveryNoteId, $serialNumber);
        }
        $this->template->deliveryNoteId = $deliveryNoteId;
        $this->template->serialNumberRow = $serialNumberRow;
        $this->template->packageOpen = $this->packageOpen;

        \Tracy\Debugger::barDump($serialNumberRow);

        $setQuantityDefaults = array('deliveryNoteId' => $deliveryNote->ID, 'idPohyb' => $serialNumberRow->IDPohyb, 'idVyrCis' => $serialNumberRow->IDVyrCis, 'quantity' => ($serialNumberRow->MnozstviPoPrijmuAVydeji > ($serialNumberRow->MnozstviPozadavek - $serialNumberRow->MnozstviPriprava)) ? $serialNumberRow->MnozstviPozadavek - $serialNumberRow->MnozstviPriprava : $serialNumberRow->MnozstviPoPrijmuAVydeji, 'checkRelation' => $checkRelation);
        $this["setQuantityForm"]->setDefaults($setQuantityDefaults);
    }

    /**
     * formulář pro úpravu množství při zadání čárovým kódem
     * @return type
     */
    public function createComponentSetQuantityForm() {
        $form = $this->setQuantityFactory->create();

// $form->onValidate[] = array($this, "validateEditQuantityForm");

        $form->onSuccess[] = function (Form $form, $values) {

            $packageSess = $this->getSession('package');
            $packageOpen = $packageSess->open;
            $packageNr = $packageSess->number;

            \Tracy\Debugger::barDump($values);
            if ($values->checkRelation) {
                $packageNr = $this->dataModel->updateDeliveryNoteItem($values->deliveryNoteId, $values->idPohyb, $values->idVyrCis, $values->quantity, $packageOpen, $packageNr);
            } else {
                $packageNr = $this->dataModel->processDeliveryNoteNoCheckRelation($values->deliveryNoteId, $values->idVyrCis, $values->quantity, $packageOpen, $packageNr);
            }
            $packageSess->number = $packageNr;

            $this->redirect('DeliveryNote:itemDetail', $values->idPohyb);
        };

        return $form;
    }

    /**
     * úprava množství výrobního čísla při zadání skladové položky (serviety)
     * @param type $serialNumberRow
     */
    public function renderSetQuantityFromStock($deliveryNoteId, $partNumber) {
        $deliveryNote = $this->dataModel->getDeliveryNote($deliveryNoteId);
// kontrola, kdy to můžu udělat (co musí být v DL vyplněné)

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;
        $this->packageNr = $this->getSession('package')->number;
        $this->packageOpen = $this->getSession('package')->open;

        $partNumberRow = $this->dataModel->checkPartNumber($deliveryNoteId, $partNumber);
        $this->template->deliveryNoteId = $deliveryNoteId;
        $this->template->partNumberRow = $partNumberRow;
        $this->template->packageOpen = $this->packageOpen;

        \Tracy\Debugger::barDump($partNumberRow);

        $setQuantityDefaults = array('deliveryNoteId' => $deliveryNote->ID, 'idZboSklad' => $partNumberRow->IDZboSklad);
        $this["setQuantityFromStockForm"]->setDefaults($setQuantityDefaults);
    }

    /**
     * formulář pro úpravu množství při zadání skladové položky
     * @return type
     */
    public function createComponentSetQuantityFromStockForm() {
        $form = $this->setQuantityFromStockFactory->create();

// $form->onValidate[] = array($this, "validateEditQuantityForm");

        $form->onSuccess[] = function (Form $form, $values) {

            $packageSess = $this->getSession('package');
            $packageOpen = $packageSess->open;
            $packageNr = $packageSess->number;

            $res = $this->dataModel->processDeliveryNoteFromStock($values->deliveryNoteId, $values->idZboSklad, $values->quantity, $packageOpen, $packageNr);
            if ($res["res"] > 0) {
                // $this->flashMessage($res["msg"], 'error');
            } else {
                /*$packageSess->number = $res["packageNr"];
                if ($res["mnozstviPriprava"] <> 0) {
                    $this->flashMessage('Všechny požadované kusy nejsou k dipozici, zbylo ještě ' . $res["mnozstviPriprava"], 'info');
                }*/
                
            }
            
            $this->redirect('DeliveryNote:default');
        };

        return $form;
    }

    /**
     * nový výběr dodacího listu
     */
    public function handleChangeDeliveryNote() {
        $this->getSession('deliveryNoteId')->remove();
        $this->redirect('DeliveryNote:default');
    }

    /**
     * generování PDF s rozdíly v DL - neodeslané zboží
     */
    public function createDeliveryNoteErrorPDF($deliveryNoteId) {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . "/templates/DeliveryNote/deliveryNoteErrorPDF.latte");
        $deliveryNote = $this->dataModel->getDeliveryNote($deliveryNoteId);
        $deliveryNoteItems = $this->dataModel->getDeliveryNoteDiffItems($deliveryNoteId);
        $template->deliveryNote = $deliveryNote;
        $template->deliveryNoteItems = $deliveryNoteItems;
        
        $pdf = new \Joseki\Application\Responses\PdfResponse($template);

        $pdf->pageFormat = "A4";
        $pdf->setPageMargins("10, 10, 10, 10, 1, 1");
        $pdf->styles = file_get_contents("__DIR__" . "\\..\\..\\www\\css\\pdf-style.css");
        //$pdf->styles = file_get_contents("c:\\inetpub\\wwwroot\\www\\css\\pdf-style.css");
        $pdf->getMPDF()->setFooter("Ligum Intranet");

        /* server */
        // dev $pdf->save("c:\\fs\\data_helios\\TBLT_EXPED_NH\\",  "dl" . $deliveryNote->ParovaciZnak);        
        $pdf->save("e:\\fs\\data_helios\\TBLT_EXPED_NH\\",  "dl" . $deliveryNote->ParovaciZnak);        
        // \Tracy\Debugger::barDump($printCmd);

    }

    /**
     * tisk uzavřeného dodacího listu
     */
    public function handlePrintDeliveryNote() {
        $deliveryNote = $this->dataModel->getDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);
        // zrušit příznak tisku v DL
        $this->dataModel->reprintDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);

        $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_" . $deliveryNote->SkupinaTisk . "\\" . $this->getSession('deliveryNoteId')->deliveryNoteId . ".txt", "w");
        fwrite($f, $this->getSession('deliveryNoteId')->deliveryNoteId);
        $f = fopen("e:\\fs\\data_helios\\batch_" . $deliveryNote->SkupinaTisk . "\\" . $this->getSession('deliveryNoteId')->deliveryNoteId . ".txt", "w");
        fwrite($f, $this->getSession('deliveryNoteId')->deliveryNoteId);

    }

    /**
     * doplnit balení k dodacímu listu
     */
    public function renderAddPackageDeliveryNote() {
        $deliveryNote = $this->dataModel->getDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;

        //\Tracy\Debugger::barDump($deliveryNote);

        $packageTxtDefaults = array('deliveryNoteId' => $deliveryNote->ID, 'BaleniTx' => $deliveryNote->BaleniTx);
        $this["packageTxtForm"]->setDefaults($packageTxtDefaults);
    }

    /**
     * uzavření dodacího listu
     */
    private function closeDeliveryNote() {
        $deliveryNote = $this->dataModel->getDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);

        // \Tracy\Debugger::barDump($deliveryNote);
        // $this->redirect('DeliveryNote:default');

        $jeAtt = 0;
        if ($deliveryNote->SkupinaTisk == 'nh') {

            $isPdf = $this->dataModel->checkDeliveryNoteBeforeClosing($this->getSession('deliveryNoteId')->deliveryNoteId);
            if ($isPdf > 0) {
                // generovat PDF
                $this->createDeliveryNoteErrorPDF($this->getSession('deliveryNoteId')->deliveryNoteId);

                $jeAtt = 1;

                /* možno zrušit
                $responsibleMail = $this->dataModel->getResponsibleMail($this->getSession('deliveryNoteId')->deliveryNoteId) . ";vinkler@agrafa.cz";
                $f = fopen("e:\\fs\\data_helios\\TBLT_EXPED_NH\\" . "dl" . $deliveryNote->ParovaciZnak . ".pdf.to", "w");
                fwrite($f, $responsibleMail);
                fclose($f);*/

            }
        }


        $res = $this->dataModel->closeDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);
        if (!$res) {
            $this->flashMessage('Dodací list se nepodařilo uzavřít!', 'danger');
        } else {
        // odeslat mail odpovědné osobě - v každém případě s textem balení
            if ($deliveryNote->SkupinaTisk == 'nh') {

/*                $jeAtt = 0;
                $res = $this->dataModel->checkDeliveryNoteBeforeClosing($this->getSession('deliveryNoteId')->deliveryNoteId);
                if ($res > 0) {
                    // generovat PDF
                    $this->createDeliveryNoteErrorPDF($this->getSession('deliveryNoteId')->deliveryNoteId);

                    $jeAtt = 1;
                }*/

                $this->dataModel->sendMailAfterDeliveryNoteClose($this->getSession('deliveryNoteId')->deliveryNoteId, $jeAtt);

                /*
                $res = $this->dataModel->checkDeliveryNoteBeforeClosing($this->getSession('deliveryNoteId')->deliveryNoteId);
                // $this->flashMessage($res);
                // odeslání zprávy o nedodaném zboží - zpráva vždy od 24. 9. 2024
                // if ($res > 0) {
                    // generovat PDF
                    $this->createDeliveryNoteErrorPDF($this->getSession('deliveryNoteId')->deliveryNoteId);
                    // generovat soubor s mailem, kam to poslat
                    $deliveryNote = $this->dataModel->getDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);
                    $responsibleMail = $this->dataModel->getResponsibleMail($this->getSession('deliveryNoteId')->deliveryNoteId);
                    $f = fopen("e:\\fs\\data_helios\\TBLT_EXPED_NH\\" . "dl" . $deliveryNote->ParovaciZnak . ".pdf.to", "w");
                    fwrite($f, $responsibleMail);
                    fclose($f);
                //}*/
            }
        // vytvořit soubor v adresáři pro tisk DL
            $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_" . $deliveryNote->SkupinaTisk . "\\" . $this->getSession('deliveryNoteId')->deliveryNoteId . ".txt", "w");
            fwrite($f, $this->getSession('deliveryNoteId')->deliveryNoteId);
            $f = fopen("e:\\fs\\data_helios\\batch_" . $deliveryNote->SkupinaTisk . "\\" . $this->getSession('deliveryNoteId')->deliveryNoteId . ".txt", "w");
            fwrite($f, $this->getSession('deliveryNoteId')->deliveryNoteId);
        
            $this->getSession('deliveryNoteId')->remove();
            $this->getSession('package')->remove();
            $this->redirect('DeliveryNote:default');
        }
    }

    /**
     * uloží balení k dodacímu listu
     */
    private function savePackageTxt($baleniTx) {
        $deliveryNote = $this->dataModel->getDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);

        $res = $this->dataModel->savePackageTxt($this->getSession('deliveryNoteId')->deliveryNoteId, $baleniTx);

        $this->dataModel->sendMailAfterAddPackageTxtClose($this->getSession('deliveryNoteId')->deliveryNoteId);

        $this->getSession('deliveryNoteId')->remove();
        $this->getSession('package')->remove();
        $this->redirect('DeliveryNote:default');
}

    /**
     * PackageTxt form factory.
     * načtení výrobního čísla čárovým kódem a zpracování
     * přečte čárový kód
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentPackageTxtForm() {
        $form = $this->packageTxtFactory->create();

        /**
         * vybrat tlačítko:
         * odeslat - spustit uzavření DL
         * storno - vrátit zpět 
         */
        $form->onSuccess[] = function (Form $form, $values) {
            if ($form['send']->isSubmittedBy()) {

                //$this->dataModel->updateDocRow($data);
                $this->savePackageTxt($values->BaleniTx);
                //$this->closeDeliveryNote($values->BaleniTx);
                /*$this->flashMessage('Uzavřít');
                $this->redirect('DeliveryNote:default');*/

            } else {
                $this->redirect('DeliveryNote:default');
            }
            /*
            if ($data->doctype == 0) {
                $this->redirect('Fakturoid:showInvoices');
            } elseif ($data->doctype == 1) {
                $this->redirect('Fakturoid:showExpenses');
            } else {
                $this->redirect('Admin:default');
            }
                */


        };

        return $form;
    }

    /**
     * CloseDeliveryNote form factory.
     * načtení výrobního čísla čárovým kódem a zpracování
     * přečte čárový kód
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentCloseDeliveryNoteForm() {
        $form = $this->closeDeliveryNoteFactory->create();

        /**
         * vybrat tlačítko:
         * odeslat - spustit uzavření DL
         * storno - vrátit zpět 
         */
        $form->onSuccess[] = function (Form $form, $values) {
            if ($form['send']->isSubmittedBy()) {

                //$this->dataModel->updateDocRow($data);
                $this->closeDeliveryNote();
                /*$this->flashMessage('Uzavřít');
                $this->redirect('DeliveryNote:default');*/

            } else {
                $this->flashMessage('Neuzavřeno');
                $this->redirect('DeliveryNote:default');
            }
            /*
            if ($data->doctype == 0) {
                $this->redirect('Fakturoid:showInvoices');
            } elseif ($data->doctype == 1) {
                $this->redirect('Fakturoid:showExpenses');
            } else {
                $this->redirect('Admin:default');
            }
                */


        };

        return $form;
    }

    /**
     * 
     */
    public function renderCloseDeliveryNote() {
        $deliveryNote = $this->dataModel->getDeliveryNote($this->getSession('deliveryNoteId')->deliveryNoteId);

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;

        \Tracy\Debugger::barDump($deliveryNote);

/*        $packageTxtDefaults = array('deliveryNoteId' => $deliveryNote->ID, 'jeReq' => $deliveryNote->DoplnitBaleni);
        $this["closeDeliveryNoteForm"]->setDefaults($closeDeliveryNoteDefaults);*/
    }

    /**
     * otevření nového balíku
     */
    public function handleOpenPackage() {
// $deliveryNoteId = $this->getSession('deliveryNoteId')->deliveryNoteId;
        $packageSess = $this->getSession('package');
        if ($packageSess->open == true) {
            $packageSess->number = $this->dataModel->getNextPackage($this->getSession('deliveryNoteId')->deliveryNoteId);
        }
        $packageSess->open = true;

        $this->redirect('DeliveryNote:default');
    }

    /**
     * uzavření balíku
     */
    public function handleClosePackage() {
        $packageSess = $this->getSession('package');
        if ($packageSess->open == true) {
            $packageSess->number = $this->dataModel->getNextPackage($this->getSession('deliveryNoteId')->deliveryNoteId);
            $packageSess->open = false;
        }

        \Tracy\Debugger::barDump($packageSess->packageNr);
        $this->redirect('DeliveryNote:default');
    }

    /**
     * smazání přípravy celé vybrané položky na dodacím listu
     * @param type $itemId
     */
    public function handleRemoveDeliveryNoteItem($itemId) {
        $this->dataModel->removeDeliveryNoteItem($itemId);
        //$this->flashMessage('Jsem tu', 'danger');

        $this->redirect('DeliveryNote:default');
    }

    /**
     * zobrazení detailu položky dodacího listu (rozpis výrobních čísel)
     * @param type $itemId
     */
    public function renderItemDetail($itemId) {
        $this->deliveryNoteId = $this->getSession('deliveryNoteId')->deliveryNoteId;
        $deliveryNote = $this->dataModel->getDeliveryNote($this->deliveryNoteId);
// kontrola, kdy to můžu udělat (co musí být v DL vyplněné)

        $deliveryNoteItem = $this->dataModel->getDeliveryNoteItem($itemId);
        $deliveryNoteItemSN = $this->dataModel->getDeliveryNoteItemSN($itemId);
        $this->packageNr = $this->getSession('package')->number;
        $this->packageOpen = $this->getSession('package')->open;

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;
        $this->template->deliveryNoteItemId = $itemId;
        $this->template->deliveryNoteItem = $deliveryNoteItem;
        $this->template->deliveryNoteItemSNs = $deliveryNoteItemSN;
        $this->template->packageOpen = $this->packageOpen;

        if ($this["readBarCodeForm"]) {
            $readBarCodeDefaults = array('deliveryNoteId' => $this->deliveryNoteId);
            $this["readBarCodeForm"]->setDefaults($readBarCodeDefaults);
        }

        \Tracy\Debugger::barDump($deliveryNoteItem);
    }

    /**
     * úprava množství na výrobním čísle - inline v přehledu
     * @return \Nette\Application\UI\Multiplier
     */
    protected function createComponentEditQuantityForm() {
        return new \Nette\Application\UI\Multiplier(function($id) {
            $form = new Form;
            $form->addHidden('itemSNId', $id);
            $form->addText('quantity')
                    ->setType('number')
                    ->setAttribute('class', 'form-control form-control-lg text-center ajax quantity-form-input');

            $form->onValidate[] = array($this, "validateEditQuantityForm");

            return $form;
        });
    }

    /**
     * validace změny množství na výrobním čísle
     * @param type $form
     * @param type $values
     */
    public function validateEditQuantityForm($form, $values) {
// $values = $form->getHttpData();
        \Tracy\Debugger::barDump($values);

        $res = $this->dataModel->updateQuantitySN($values);

        switch ($res) {
            case 0:
                break;
            case 1:
                $form->addError('Neplatný kód výrobního čísla!');
                $this->flashMessage('Neplatný kód výrobního čísla!');
                break;
            case 2:
                $form->addError('Nedostatek výrobního čísla skladem. Nelze vydat!');
                $this->flashMessage('Nedostatek výrobního čísla skladem. Nelze vydat!');
                break;
            case 3:
                $form->addError('Překročeno požadované množství. Nelze vydat!');
                $this->flashMessage('Překročeno požadované množství. Nelze vydat!');
                break;
            case 9:
                $form->addError('Chyba SQL');
                $this->flashMessage('Chyba SQL');
                break;
            default:
                $form->addError('Chyba?');
                $this->flashMessage('Chyba?');
                break;
        }

        $this->redirect('DeliveryNote:itemDetail', $this->presenter->getParameter("itemId"));
    }

    /**
     * smazání přípravy vybraného výrobního čísla
     * @param type $itemId
     * @param type $itemSNId
     */
    public function handleRemoveDeliveryNoteItemSN($itemId, $itemSNId) {
        $this->dataModel->removeDeliveryNoteItemSN($itemSNId);

        $this->redirect('DeliveryNote:itemDetail', $itemId);
    }

    /**
     * zobrazení detailu položky dodacího listu (rozpis výrobních čísel)
     * @param type $deliveryNoteId
     */
    public function renderPackages() {
        $this->deliveryNoteId = $this->getSession('deliveryNoteId')->deliveryNoteId;
        $deliveryNote = $this->dataModel->getDeliveryNote($this->deliveryNoteId);
// kontrola, kdy to můžu udělat (co musí být v DL vyplněné)
// $deliveryNoteItem = $this->dataModel->getDeliveryNoteItem($itemId);
        $deliveryNotePackages = $this->dataModel->getDeliveryNotePackages($this->deliveryNoteId);
        $this->packageNr = $this->getSession('package')->number;
        $this->packageOpen = $this->getSession('package')->open;

        $this->template->deliveryNoteNr = $deliveryNote->ParovaciZnak;
        $this->template->deliveryNote = $deliveryNote;
// $this->template->deliveryNoteItemId = $itemId;
// $this->template->deliveryNoteItem = $deliveryNoteItem;
        $this->template->packages = $deliveryNotePackages;
        $this->template->packageOpen = $this->packageOpen;

        if ($this["readBarCodeForm"]) {
            $readBarCodeDefaults = array('deliveryNoteId' => $this->deliveryNoteId);
            $this["readBarCodeForm"]->setDefaults($readBarCodeDefaults);
        }

        \Tracy\Debugger::barDump($deliveryNotePackages);
    }

    /**
     * smazání položky z balíku
     * @param type $packageId
     */
    public function handleRemoveFromPackage($packageId) {
        $this->dataModel->removeFromPackage($packageId);

        $this->redirect('DeliveryNote:packages');
    }

}
