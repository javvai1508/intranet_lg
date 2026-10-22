<?php

namespace App\Presenters;

use App\Models\MaterialModel,
    App\Models\DeliveryNoteModel,
    App\Forms\SetDeliveryNoteFormFactory,
    App\Forms\SetStockDocFormFactory,
    App\Forms\SetOrderFormFactory,
    App\Forms\ReadBarCodeFormFactory,
    App\Forms\ReceiptReadBarCodeFormFactory,
    App\Forms\SetQuantityFormFactory,
    App\Forms\StartLamInventoryFormFactory,
    App\Forms\SelectInventoryFormFactory,
    App\Forms\StdInventoryBarCodeFormFactory,
    App\Forms\StdInventoryFormFactory,
    Nette\Application\UI\Form,
    Contributte\PdfResponse\PdfResponse;

class MaterialPresenter extends BasePresenter
{

    /** @var MaterialModel @inject */
    public $dataModel;

    /** @var DeliveryNoteModel @inject */
    public $dataModelDN;

    /** @var SetDeliveryNoteFormFactory @inject */
    public $setDeliveryNoteFactory;

    /** @var SetStockDocFormFactory @inject */
    public $setStockDocFactory;

    /** @var SetOrderFormFactory @inject */
    public $setOrderFactory;

    /** @var ReadBarCodeFormFactory @inject */
    public $readBarCodeFactory;

    /** @var ReceiptReadBarCodeFormFactory @inject */
    public $receiptReadBarCodeFactory;

    /** @var SetQuantityFormFactory @inject */
    public $setQuantityFactory;

    /** @var StartLamInventoryFormFactory @inject */
    public $startLamInventoryFactory;

    /** @var SelectInventoryFormFactory @inject */
    public $selectInventoryFactory;

    /** @var StdInventoryBarCodeFormFactory @inject */
    public $stdInventoryBarCodeFactory;

    /** @var StdInventoryFormFactory @inject */
    public $stdInventoryFactory;

    /** @var OrderId */
    private $orderId;

    /** @var OrderItemId */
    private $orderItemId;

    /** @var DispatchId */
    private $dispatchId;

    /** @var ReceiptId */
    private $receiptId;

    /** @var invHdId */
    private $invHdId;

    public function __construct()
    {
        parent::__construct();
    }

    public function startup(): void {
        parent::startup();

        if (!$this->user->isInRole("skladMat")) {
            $this->redirect("Homepage:");
        }
    }

    public function renderDefault()
    {
        if (isset($this->getSession('invHd')->invHdId)) {
            $this->getSession('invHd')->remove();
        }

        // \Tracy\Debugger::barDump($this->showSession());
    }

    /* ============= PŘÍJEMKA ============================================ */

    /**
     * nová příjemka
     */
    public function renderNewReceipt()
    { }

    /**
     * SetOrder form factory.
     * formulář pro zadání objednávky
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSetOrderForm()
    {
        $form = $this->setOrderFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {
            // zkontrolovat objednávku
            $order = $this->dataModel->getOrder($values);
            if ($order) {
                // kontrola stavu
                $orderIdSess = $this->getSession('order');
                $orderIdSess->orderId = $order->ID;
                // $orderIdSess->oxxo = $order->Oxxo;

                if (!isset($this->receiptId)) {
                    // založit příjemku
                    $receiptId = $this->dataModel->createReceipt($order->ID, $values->CisloOrg);

                    if ($receiptId) {
                        $receiptIdSess = $this->getSession('receipt');
                        $receiptIdSess->receiptId = $receiptId;
                    }
                } else {
                    $receiptIdSess = $this->getSession('receipt');
                }

                if (isset($receiptIdSess)) {
                    $this->redirect('Material:showReceipt');
                } else { }
            } else {
                $this->flashMessage('Vybraná objednávka není v evidenci!', 'error');

                $this->redirect('Material:newReceipt');
            }
        };

        return $form;
    }

    /*
     * změna objednávky
     */

    public function actionChangeOrder()
    {
        $this->getSession('order')->remove();
        if (isset($this->getSession('receipt')->receiptId)) {
            $this->receiptId = $this->getSession('receipt')->receiptId;
            $receipt = $this->dataModel->getReceipt($this->receiptId);
            $receiptItems = $this->dataModel->getReceiptItems($this->receiptId);

            $this->template->receiptNr = $receipt->ParovaciZnak;
            $this->template->receipt = $receipt;
            $this->template->receiptItems = $receiptItems;
        }
        \Tracy\Debugger::barDump($this->getSession('receipt')->receiptId);
        // \Tracy\Debugger::barDump($this->getSession('order')->orderId);
        \Tracy\Debugger::barDump($this->template->receipt);
        // \Tracy\Debugger::barDump($this->template->order);
    }

    /**
     * autocomplete formulářového pole firma
     * 
     * @param type $term
     */
    public function handleAutocompleteFirma($term)
    {
        if ($this->isAjax()) {
            $autocomplete = $this->dataModel->getAutocompleteFirma($term);
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse($autocomplete));
        }
    }

    /**
     * dotažení firmy po změně čísla organizace
     * 
     * @param type $cisloOrg
     */
    public function handleChangeCisloOrg($cisloOrg)
    {
        if ($this->isAjax()) {
            $firma = $this->dataModel->getFirma($cisloOrg)["Firma"];
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse($firma));
        }
    }

    /**
     * autocomplete formulářového párovací znak
     * @param type $cisloOrg
     * @param type $term
     */
    public function handleAutocompleteParovaciZnak($cisloOrg, $term)
    {
        if ($this->isAjax()) {
            $autocomplete = $this->dataModel->getAutocompleteParovaciZnak($cisloOrg, $term);
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse($autocomplete));
        }
    }

    /**
     * dotažení údajů o firmě podle čísla objednávky
     * @param type $cisloOrg
     * @param type $term
     */
    public function handleBlurParovaciZnak($parovaciZnak)
    {
        if ($this->isAjax()) {
            $blur = $this->dataModel->getBlurParovaciZnak($parovaciZnak);
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse($blur));
        }
    }

    /**
     * zobrazení příjemky
     */
    public function renderShowReceipt()
    {
        if (isset($this->getSession('receipt')->receiptId)) {
            $this->receiptId = $this->getSession('receipt')->receiptId;
            $receipt = $this->dataModel->getReceipt($this->receiptId);
            $receiptItems = $this->dataModel->getReceiptItems($this->receiptId);

            $this->template->receiptNr = $receipt->ParovaciZnak;
            $this->template->receipt = $receipt;
            $this->template->receiptItems = $receiptItems;
            $this->template->oxxo = $this->getSession('receipt')->oxxo;

            if ($this["receiptReadBarCodeForm"]) {
                $barCodeDefaults = array('receiptId' => $this->receiptId);
                $this["receiptReadBarCodeForm"]->setDefaults($barCodeDefaults);
            }
        }
        \Tracy\Debugger::barDump('OXXO: ' . $this->getSession('receipt')->oxxo);
        // \Tracy\Debugger::barDump($this->getSession('order')->orderId);
        \Tracy\Debugger::barDump($this->template->receipt);
        // \Tracy\Debugger::barDump($this->template->order);
    }

    /**
     * zobrazení detailu položky příjemky s objednávkou
     * @param type $itemId
     */
    public function renderReceiptItemDetail($itemId)
    {
        $this->receiptId = $this->getSession('receipt')->receiptId;
        $receipt = $this->dataModel->getReceipt($this->receiptId);

        $receiptItem = $this->dataModel->getReceiptItem($itemId, $this->receiptId);
        $oxxo = $this->getSession('receipt')->oxxo;

        $receiptItemSNs = $this->dataModel->getReceiptItemSN($receiptItem["ReceiptItemID"]);

        // kontrola, kdy to můžu udělat 
        $this->template->receiptNr = $receipt->ParovaciZnak;
        $this->template->receipt = $receipt;
        $this->template->receiptItem = $receiptItem;
        $this->template->receiptItemSNs = $receiptItemSNs;
        $this->template->oxxo = $oxxo;

        if ($this["receiptReadBarCodeForm"]) {
            $barCodeDefaults = array('receiptId' => $this->receiptId, 'itemId' => $receiptItem["ReceiptItemID"]);
            $this["receiptReadBarCodeForm"]->setDefaults($barCodeDefaults);
        }
        // \Tracy\Debugger::barDump($orderItem);
    }

    /* ------------- čtení čárového kódu -------------------------------------- */

    /**
     * ReceiptReadBarCode form factory.
     * načtení výrobního čísla čárovým kódem a zpracování pro příjemku
     * přečte čárový kód
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentReceiptReadBarCodeForm()
    {
        $form = $this->readBarCodeFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {

            $itemId = null;
            if ($this->getSession('receipt')->oxxo == 1) {
                $snItem = $this->dataModel->checkReceiptItemSN($values);
                if (!$snItem) {
                    $this->flashMessage('Výrobní číslo nebylo nalezeno na DL!', 'error');
                } else {
                    $itemId = $snItem->PrijRdId;
                    if ($snItem->PrijemVC >= $snItem->DodanoVC) {
                        $this->flashMessage('Překročeno množství na DL!', 'error');
                    } else {
                        $res = $this->dataModel->updateReceiptItemSN($values);
                    }
                }
                // $this->flashMessage('OXXO');
            } else {
                // je id položky (rozbalená) - rovnou přiřadit k položce, jinak výběr položky z příjemky a zadání množství - podobně jako u inventury laminátů???
                if ($values->itemId) {
                    $this->dataModel->addSNToReceiptId($values->receiptId, $values->BarCode, $values->itemId, 1);
                    $this->redirect('Material:receiptItemDetail', $values->itemId);

                    // $this->flashMessage('hopla');
                } else {
                    $this->flashMessage('vedle');
                }
            }

            // \Tracy\Debugger::barDump($itemsIds);
            // $this->flashMessage($values->BarCode);
            if ($itemId) {
                $this->redirect('Material:receiptItemDetail', $itemId);
            } else {
                $this->redirect('Material:showReceipt');
            }
        };

        return $form;
    }

    /**
     * smazání celé vybrané položky z příjemky
     * @param type $itemId
     */
    public function handleRemoveReceiptItem($itemId)
    {
        $this->dataModel->removeReceiptItem($itemId);

        $this->redirect('Material:showReceipt');
    }

    /**
     * smazání přípravy vybraného výrobního čísla
     * @param type $itemId
     * @param type $itemSNId
     */
    public function handleRemoveReceiptItemSN($itemId, $itemSNId)
    {
        $this->dataModel->removeReceiptItemSN($itemSNId);
        $this->redirect('Material:receiptItemDetail', $itemSNId);
    }

    /**
     * generování výrobního čísla
     * @param type $itemId
     */
    public function handleGenerateBarCode($itemId)
    {
        if ($this->isAjax()) {
            $generate = $this->dataModel->generateSN($itemId);
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse($generate));
        }
    }

    /**
     * úprava množství na výrobním čísle - inline v přehledu
     * @return \Nette\Application\UI\Multiplier
     */
    protected function createComponentEditQuantityForm()
    {
        return new \Nette\Application\UI\Multiplier(function ($id) {
            $form = new Form;
            $form->addHidden('itemSNId', $id);
            $form->addText('quantity')
                ->setType('number')
                ->setHtmlAttribute('class', 'form-control form-control-lg text-center ajax quantity-form-input');

            return $form;
        });
    }

    /* --------------------- Uzavření příjemky ------------------------ */
    /**
     * 
     */
    public function createReceiptErrorPDF($receiptId)
    {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . "/templates/Material/receiptErrorPDF.latte");
        $receipt = $this->dataModel->getReceipt($receiptId);
        $receiptItems = $this->dataModel->getReceiptDiffItems($receiptId);
        $template->receipt = $receipt;
        $template->receiptItems = $receiptItems;

        $pdf = new \Contributte\PdfResponse\PdfResponse($template);

        $pdf->pageFormat = "A4";
        $pdf->setPageMargins("10, 10, 10, 10, 1, 1");
        $pdf->styles = file_get_contents(__DIR__ . '/../../www/css/pdf-style.css');
        $pdf->getMPDF()->setFooter("Ligum Intranet");

        /* server */
        $pdf->save("e:\\fs\\data_helios\\TBLT_PRIJEM_OXXO\\",  "pr" . $receipt->ParovaciZnak);
        // \Tracy\Debugger::barDump($printCmd);

    }

    /**
     * uzavření příjemky
     */
    public function handleCloseReceipt()
    {
        if ($this->getSession('receipt')->oxxo) {
            $res = $this->dataModel->checkReceiptBeforeClosing($this->getSession('receipt')->receiptId);
            // $this->flashMessage($res);
            if ($res > 0) {
                // generovat PDF
                $this->createReceiptErrorPDF($this->getSession('receipt')->receiptId);
            }
        }
        // $this->redirect('Material:default');

        $res = $this->dataModel->closeReceipt($this->getSession('receipt')->receiptId);
        if (!$res) {
            $this->flashMessage('Příjemku se nepodařilo uzavřít!', 'danger');
        } else {
            $this->getSession('receipt')->remove();
            $this->getSession('order')->remove();
            $this->redirect('Material:default');
        }
    }

    /**
     * uzavření příjemky bez načítání položek
     */
    public function handleCloseReceiptQuick()
    {
        $res = $this->dataModel->closeReceiptQuick($this->getSession('receipt')->receiptId);
        if (!$res) {
            $this->flashMessage('Příjemku se nepodařilo uzavřít!', 'danger');
        } else {
            $this->getSession('receipt')->remove();
            $this->getSession('order')->remove();
            $this->redirect('Material:default');
        }
    }

    /* ------------- změna - vyhledání dokladu - příjemka i výdejka ------------------------------ */

    /**
     * 
     */
    public function actionChangeStockDoc()
    {
        $this->getSession('receipt')->remove();
        $this->getSession('order')->remove();
        $this->getSession('dispatch')->remove();
        $this->redirect("Material:default");
    }

    /**
     * SetStockDoc form factory.
     * vyhledání příjemky
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSetStockDocForm()
    {
        $form = $this->setStockDocFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {

            $this->getSession('receipt')->remove();
            $this->getSession('order')->remove();

            $doc = $this->dataModel->getStockDocPZ($values->ParovaciZnak);
            if (!$doc) {
                $this->flashMessage('Vybraný doklad není v evidenci!', 'error');

                $this->redirect('Material:default');
            } else {

                // podle typu dokladu pokračovat jako příjemka nebo výdejka
                if ($doc->DruhPohybuZbo == 0) {
                    if ($doc->Pripraveno != 1) {
                        $this->flashMessage('Příjemka není připravená ke zpracování!', 'error');

                        $this->redirect('Material:default');
                    } else {
                        $receiptIdSess = $this->getSession('receipt');
                        $receiptIdSess->receiptId = $doc->ID;
                        $receiptIdSess->oxxo = $doc->Oxxo;
                    }

                    if (isset($receiptIdSess)) {
                        $this->redirect('Material:showReceipt');
                    }
                } elseif ($doc->DruhPohybuZbo == 2) {
                    $dispatchIdSess = $this->getSession('dispatch');
                    $dispatchIdSess->dispatchId = $doc->ID;

                    if (isset($dispatchIdSess)) {
                        $this->redirect('Material:showDispatch');
                    }
                } elseif ($doc->DruhPohybuZbo == 4) {
                    $dispatchIdSess = $this->getSession('dispatch');
                    $dispatchIdSess->dispatchId = $doc->ID;

                    if (isset($dispatchIdSess)) {
                        $this->redirect('Material:showDispatch');
                    }
                } else {
                    $this->redirect('Material:default');
                }
            }
        };

        return $form;
    }

    /* ============================= VÝDEJKA =================================== */

    /**
     * nová výdejka
     */
    public function renderOpenNewDispatch()
    { }

    /**
     * nová výdejka - vlastní spotřeba
     */
    public function handleNewDispatchOwn()
    { 
        $res = $this->dataModel->openNewDispatch('own');
        if ($res["IDDoklad"]) {
            $dispatchId = $res["IDDoklad"];

            if ($dispatchId) {
                // založit novou výdejku  a doplnit řádky
                $dispatchIdSess = $this->getSession('dispatch');
                $dispatchIdSess->dispatchId = $dispatchId;
                $dispatchIdSess->dispatchType = 'own';

                $this->redirect("Material:showDispatch");
            } else {
                $this->flashMessage("Chyba při založení výdejky");
                $this->redirect("Material:openNewDispatch");
            }
        }         
    }

    /**
     * nová výdejka - zmetek
     */
    public function handleNewDispatchDefect()
    { 
        $res = $this->dataModel->openNewDispatch('def');
        if ($res["IDDoklad"]) {
            $dispatchId = $res["IDDoklad"];

            if ($dispatchId) {
                // založit novou výdejku  a doplnit řádky
                $dispatchIdSess = $this->getSession('dispatch');
                $dispatchIdSess->dispatchId = $dispatchId;
                $dispatchIdSess->dispatchType = 'def';

                $this->redirect("Material:showDispatch");
            } else {
                $this->flashMessage("Chyba při založení výdejky");
                $this->redirect("Material:openNewDispatch");
            }
        }         

    }

    /**
     * storno výdejky
     */
    public function handleUndoDispatch()
    { 
        $res = $this->dataModel->openNewDispatch('undo');
        if ($res["IDDoklad"]) {
            $dispatchId = $res["IDDoklad"];

            if ($dispatchId) {
                // založit novou výdejku  a doplnit řádky
                $dispatchIdSess = $this->getSession('dispatch');
                $dispatchIdSess->dispatchId = $dispatchId;
                $dispatchIdSess->dispatchType = 'undo';

                $this->redirect("Material:showDispatch");
            } else {
                $this->flashMessage("Chyba při založení výdejky");
                $this->redirect("Material:openNewDispatch");
            }
        }         
    }

    /**
     * zobrazení výdejky
     */
    public function renderShowDispatch()
    {
        if (isset($this->getSession('dispatch')->dispatchId)) {
            $this->dispatchId = $this->getSession('dispatch')->dispatchId;
            $dispatchType = $this->getSession('dispatch')->dispatchType;
            $dispatch = $this->dataModel->getDispatch($this->dispatchId);
            $dispatchItems = $this->dataModel->getDispatchItems($this->dispatchId);
            $requestItems = $this->dataModel->getRequestItems($dispatch->BarCodeOper);
            // spočítat množství požadované v hlavičce podle součtu z řádků 
            $sumaRequests = 0;
            $sumaLength = 0;
            foreach ($requestItems as $requestItem) {
                $sumaRequests += $requestItem->MnozPozad;
                $sumaLength += $requestItem->KsZadane * $requestItem->DelkaSleeve;
            }

            $this->template->dispatchNr = $dispatch->ParovaciZnak;
            $this->template->dispatchType = $dispatchType;
            $this->template->dispatch = $dispatch;
            $this->template->dispatchItems = $dispatchItems;
            $this->template->requestItems = $requestItems;
            $this->template->sumaRequests = $sumaRequests;
            $this->template->sumaLength = $sumaLength;

            if ($this["readBarCodeForm"]) {
                $readBarCodeDefaults = array('dispatchId' => $this->dispatchId);
                $this["readBarCodeForm"]->setDefaults($readBarCodeDefaults);
            }

            \Tracy\Debugger::barDump($dispatch);
        }
        \Tracy\Debugger::barDump($this->getSession('dispatch')->dispatchId);
    }

    /* ------------- čtení čárového kódu -------------------------------------- */

    /**
     * ReadBarCode form factory.
     * načtení čísla operace pro výběr materiálu do výdejky
     * přečte čárový kód
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentReadOperBarCodeForm()
    {
        $form = $this->readBarCodeFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {
            $firstOper = $values->BarCode;

            $res = $this->dataModel->openNewDispatchFromOper($firstOper);
            if ($res["Stav"] >= 0) {
                $dispatchId = $res["IDDoklad"];

                if ($dispatchId) {
                    // založit novou výdejku  a doplnit řádky
                    $dispatchIdSess = $this->getSession('dispatch');
                    $dispatchIdSess->dispatchId = $dispatchId;
                    $dispatchIdSess->dispatchType = '';
                    if ($res["Stav"] == 3) {
                        $this->flashMessage("Výdejka je již otevřená!");
                    }

                    $this->redirect("Material:showDispatch");
                } else {
                    $this->flashMessage("Chyba při založení výdejky");
                    $this->redirect("Material:openNewDispatch");
                }
            } else {
                switch ($res["Stav"]) {
                    case -1:
                        $this->flashMessage("Nebyla nalezena požadovaná operace!");
                        break;
                    case -2:
                        $this->flashMessage("Chybná identifikace výrobku!");
                        break;
                    case -3:
                        $this->flashMessage("K vybrané operaci není žádný požadavek na materiál!");
                        break;
                }
                $this->redirect("Material:openNewDispatch");
            }

            \Tracy\Debugger::barDump(substr($values->BarCode, 0, strpos(strtoupper($values->barCode), 'B') + 1));
        };

        return $form;
    }

    /**
     * ReadBarCode form factory.
     * načtení výrobního čísla materiálu
     * přečte čárový kód
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentReadBarCodeForm()
    {
        $form = $this->readBarCodeFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {
            $dispatchType = $this->getSession('dispatch')->dispatchType;
            if ($dispatchType == '') {
                // kontrola existence VČ + kontrola existence v DL
                $serialNumber = $values->BarCode;
                $serialNumberRow = $this->dataModel->checkSerialNumber($values->dispatchId, $serialNumber);
                if (!($serialNumberRow["IDVyrCis"])) {
                    // VČ neexistuje - výpis chyby
                    $this->flashMessage('Výrobní číslo ' . $values->BarCode . ' není v evidenci!', 'error');
                } else {
                    $res = $this->dataModel->updateDispatchItem($values->dispatchId, $serialNumberRow->IDZboSklad, $serialNumberRow["IDVyrCis"], 1);
                }
            } else {
                $serialNumber = $values->BarCode;
                $serialNumberRow = $this->dataModel->checkSerialNumberOther($values->dispatchId, $serialNumber);
                if (!($serialNumberRow["IDVyrCis"])) {
                    // VČ neexistuje - výpis chyby
                    $this->flashMessage('Výrobní číslo ' . $values->BarCode . ' není v evidenci!', 'error');
                } else {
                    $res = $this->dataModel->updateDispatchItemOther($values->dispatchId, $serialNumberRow->IDZboSklad, $serialNumberRow["IDVyrCis"], 1);
                }
            }

            \Tracy\Debugger::barDump($serialNumberRow);
            // $this->flashMessage($values->BarCode);

            $this->redirect('Material:showDispatch');
        };

        return $form;
    }

    /**
     * zobrazení detailu položky výdejky
     * @param type $itemId
     */
    public function renderDispatchItemDetail($itemId)
    {
        $this->dispatchId = $this->getSession('dispatch')->dispatchId;
        $dispatch = $this->dataModel->getDispatch($this->dispatchId);
        $dispatchItems = $this->dataModel->getDispatchItems($this->dispatchId);
        $dispatchItem = $this->dataModel->getDispatchItem($itemId);
        $requestItems = $this->dataModel->getRequestItems($dispatch->BarCodeOper);
        // spočítat množství požadované v hlavičce podle součtu z řádků 
        $sumaRequests = 0;
        $sumaLength = 0;
        foreach ($requestItems as $requestItem) {
            $sumaRequests += $requestItem->MnozPozad;
            $sumaLength += $requestItem->KsZadane * $requestItem->DelkaSleeve;
        }

        // upravit množství požadované v hlavičce podle součtu z řádků (bez vydaného)
        foreach ($dispatchItems as $dispatchItem) {
            if ($dispatchItem->Mnozstvi < 1) {
                $dispatch->MnozstviPozad = $dispatch->MnozstviPozad - $dispatchItem->MnozVydane;
            }
        }

        $dispatchItemSNs = $this->dataModel->getDispatchItemSN($itemId);

        // kontrola, kdy to můžu udělat 
        $this->template->dispatchNr = $dispatch->ParovaciZnak;
        $this->template->dispatch = $dispatch;
        $this->template->dispatchItem = $dispatchItem;
        $this->template->dispatchItemSNs = $dispatchItemSNs;
        $this->template->sumaRequests = $sumaRequests;

        if ($this["readBarCodeForm"]) {
            $readBarCodeDefaults = array('dispatchId' => $this->dispatchId);
            $this["readBarCodeForm"]->setDefaults($readBarCodeDefaults);
        }

        \Tracy\Debugger::barDump($dispatchItem);
    }

    /**
     * úprava množství výrobního čísla při zadání čárovým kódem
     * @param type $serialNumberRow
     */
    public function renderSetQuantity($dispatchId, $serialNumber)
    {
        $dispatch = $this->dataModel->getDispatch($dispatchId);
        // kontrola, kdy to můžu udělat (co musí být v DL vyplněné)

        $this->template->dispatchNr = $dispatch->ParovaciZnak;
        $this->template->dispatch = $dispatch;

        $serialNumberRow = $this->dataModel->checkSerialNumber($dispatchId, $serialNumber);
        $this->template->dispatchId = $dispatchId;
        $this->template->serialNumberRow = $serialNumberRow;

        \Tracy\Debugger::barDump($serialNumberRow);

        $setQuantityDefaults = array('dispatchId' => $dispatch->ID, 'idZboSklad' => $serialNumberRow->IDZboSklad, 'idVyrCis' => $serialNumberRow["IDVyrCis"], 'quantity' => ($serialNumberRow->MnozstviPoPrijmuAVydeji > ($serialNumberRow->MnozstviPozadavek - $serialNumberRow->MnozstviPriprava)) ? $serialNumberRow->MnozstviPozadavek - $serialNumberRow->MnozstviPriprava : $serialNumberRow->MnozstviPoPrijmuAVydeji);
        $this["setQuantityForm"]->setDefaults($setQuantityDefaults);
    }

    /**
     * formulář pro úpravu množství při zadání čárovým kódem
     * @return type
     */
    public function createComponentSetQuantityForm()
    {
        $form = $this->setQuantityFactory->create();

        // $form->onValidate[] = array($this, "validateEditQuantityForm");

        $form->onSuccess[] = function (Form $form, $values) {

            $itemId = $this->dataModel->updateDispatchItem($values->dispatchId, $values->idZboSklad, $values->idVyrCis, $values->quantity);

            $this->redirect('Material:showDispatch');
        };

        return $form;
    }

    public function handleRemoveDispatchItem($itemId)
    {
        $this->dataModel->removeDispatchItem($itemId, $this->getSession('dispatch')->dispatchType);

        $this->redirect('Material:showDispatch');
    }

    public function handleRemoveDispatchItemSN($itemId, $itemSNId)
    {
        $this->dataModel->removeDispatchItemSN($itemSNId, $this->getSession('dispatch')->dispatchType);

        $this->redirect('Material:dispatchItemDetail', $itemId);
    }

    /**
     * generování štítku do PDF
     */
    public function handleFirstOperationLabelPDF($dispatchId)
    {
        $template = $this->createTemplate();
        // $template->setFile(__DIR__ . "/templates/Material/firstOperationLabelPDF.latte");
        $template->setFile(__DIR__ . "/templates/Material/firstOperationLabelPDF5x10.latte");
        $dispatch = $this->dataModel->getFirstOperationData($dispatchId);

        $ii = 0;
        foreach ($dispatch as $dispatchItem) {
            $ii++;
            $template->dispatch = $dispatchItem;

            $pdf = new \Contributte\PdfResponse\PdfResponse($template);

            // $pdf->pageFormat = array(50, 30);
            $pdf->pageFormat = array(50, 100);
            $pdf->setPageMargins("0, 1, 1, 2, 1, 1");
            // $pdf->styles = file_get_contents(__DIR__ . '/../../www/css/pdf-label-style-3x5.css');
            $pdf->styles = file_get_contents(__DIR__ . '/../../www/css/pdf-label-style.css');

            $numberOfCopies = "1x";
            $printSettings = "noscale, " . $numberOfCopies;

            /* localhost */
            // $printCmd = 'cmd /c c:\\fs\\data_helios\\script\\sumatrapdf.exe -print-to "OKI MC352(PCL)" -print-settings "' . $printSettings . '" c:\\fs\\data_helios\\batch_mater\\' . $dispatch->ID . '.pdf';
            // $pdf->save("c:\\fs\\data_helios\\batch_mater",  $dispatch->ID);        

            /* server */
            $pdf->save("e:\\fs\\data_helios\\tblt_vydej_lam",  $dispatchId . '-' . $ii);
            $f = fopen("e:\\fs\\data_helios\\tblt_vydej_lam\\" . $dispatchId . '-' . $ii . ".pdf.settings", "w");
            fwrite($f, $printSettings);
        }

        $this->dataModel->dispatchLabelPrintOK($dispatchId);
    }

    /**
     * uzavření výdejky
     */
    public function handleCloseDispatch()
    {
        $this->dispatchId = $this->getSession('dispatch')->dispatchId;
        $res = $this->dataModel->closeDispatch($this->dispatchId);
        if (!$res) {
            $this->flashMessage('Výdejku se nepodařilo uzavřít!', 'danger');
        } else {
            // generovat PDF štítky podle počtu kusů - pouze u příkazů s VČ
            $dispatch = $this->dataModel->getDispatch($this->dispatchId);

            // $this->handleFirstOperationLabelPDF($this->dispatchId);

            if (isset($dispatch->DruhPruvodky) && $dispatch->DruhPruvodky == 0 && $dispatch->StitkyHotovo == 0) {
                $this->handleFirstOperationLabelPDF($this->dispatchId);
            }

            $this->getSession('dispatchId')->remove();
            $this->redirect('Material:default');
        }
    }

    /* ------------- prodej laminátů - podobně jako v expedici --------------------------------- */
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

            $deliveryNoteId = $this->dataModelDN->checkDeliveryNote($values->ParovaciZnak);
            
            if (!$deliveryNoteId) {
                $this->flashMessage('Vybraný dodací list není v evidenci!', 'error');

                $this->redirect('DeliveryNote:default');
            } else {

                $deliveryNoteIdSess = $this->getSession('deliveryNoteId');
                $deliveryNoteIdSess->deliveryNoteId = $deliveryNoteId;

                $packageSess = $this->getSession('package');
                $packageSess->open = false;
                $packageSess->number = $this->dataModelDN->getNextPackage($deliveryNoteId);

                $this->redirect('DeliveryNote:default');
            }
        };

        return $form;
    }


    /* ------------- inventura laminátu - počáteční verze -------------------------------------- */
    /**
     * zadávání inventury - počáteční zpracování laminátu
     * - nejprve v HeO založit příjemku na inventuru - do Popisu dodávky zadat něco jako ID inventury
     * - načtení VČ
     * - výběr položky z kmene
     * - uložení - zaměnit zadané VČ s univerzálním
     * - pokud není univerzální VČ na skladě - přidat do příjemky - a asi ohlásit
     * - ohlásit výledek
     */
    public function renderLamInventory()
    { }

    /**
     * formulář pro startovací inventuru
     * @return type
     */
    protected function createComponentStartLamInventoryForm()
    {
        $prumerZao = $this->dataModel->getPrumer1Zao();
        $nazevLaminatu = $this->dataModel->getLaminat();
        $form = $this->startLamInventoryFactory->create($prumerZao, $nazevLaminatu);
        $form->onSuccess[] = function (Form $form, $values) {
            /*
             * pokud je požadavek na generování VČ, vygenerovat nové 
             * kontrola - otevřené doklady, existence VČ ve skladu a stav
             * přidat na příjemku 1 ks VČ
             * přidat na výdejku (pokud to jde) 1 ks se startovacím VČ
             */
            if ((!isset($values->VyrCislo) || $values->VyrCislo == '') && $values->TisknoutStitek) {
                // generování VČ
                $sn = $this->dataModel->generateSNKmen($values->Nazev);
                if (isset($sn) && $sn != '') {
                    $values->VyrCislo = $sn;
                }
                \Tracy\Debugger::barDump($sn);
            }

            $res = $this->dataModel->processLamInventorySN($values->VyrCislo, $values->Nazev, $values->TisknoutStitek);

            if ($res == 1) {
                $this->flashMessage('Není zadané výrobní číslo!', 'danger');
            } elseif ($res == 2) {
                $this->flashMessage('Chybně zadaná kmenová karta!', 'danger');
            } elseif ($res == 3) {
                $this->flashMessage('Výrobní číslo již je na skladě!', 'danger');
            } elseif ($res == 99) {
                $this->flashMessage('Chyba při generování skladové položky (TabStavSkladu)!', 'danger');
            } else {
                $this->flashMessage('Výrobní číslo bylo zaevidováno!', 'info');
                if ($values->TisknoutStitek) {
                    $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_material\\vc_stitek_" . $values->VyrCislo . ".txt", "w");
                    fwrite($f, $values->VyrCislo);
                }
            }
            \Tracy\Debugger::barDump($values);

            $this->redirect('Material:LamInventory');
        };

        return $form;
    }

    /**
     * 
     */
    public function handleChangeVyrCislo()
    { }

    public function handleChangeTypLaminatu($typLaminatu)
    {
        if ($this->isAjax() && $typLaminatu != "") {
            $prumerZao = $this->dataModel->getPrumer1ZaoByTyp($typLaminatu);
            $nazevLaminatu = $this->dataModel->getLaminatByTypPrumer($typLaminatu, null);
            $this['startLamInventoryForm']['Prumer1']
                ->setItems($prumerZao, false);
            $this['startLamInventoryForm']['Nazev']
                ->setItems($nazevLaminatu);
            $this->redrawControl('prumerLaminatu');
            $this->redrawControl('nazevLaminatu');
            $this->redrawControl('formArea');
        }
    }

    public function handleChangePrumerLaminatu($typLaminatu, $prumerLaminatu)
    {
        if ($this->isAjax() && $typLaminatu != "" && $prumerLaminatu != "") {
            $nazevLaminatu = $this->dataModel->getLaminatByTypPrumer($typLaminatu, $prumerLaminatu);
            $this['startLamInventoryForm']['Nazev']
                ->setItems($nazevLaminatu);
            $this->redrawControl('nazevLaminatu');
            $this->redrawControl('formArea');
        }
    }

    /* ------------- inventura - standardní  verze -------------------------------------- */
    /**
     * zadávání inventury
     * - nejprve v HeO založit  inventuru - včetně VČ a nulových stavů
     * - 1. form: načtení VČ
     * - 2. form: výběr položky z kmene, zadání množství - u laminátů natvrdo 1 (podle SkupZbo)
     * - uložení - do řádků inventury
     * - pokud není VČ v evidenci, přidat i do inventury
     * - ohlásit výsledek
     */
    public function renderStdInventory()
    {
        if (isset($this->getSession('invHd')->invHdId)) {
            $this->invHdId = $this->getSession('invHd')->invHdId;

            $this->template->invHdId = $this->invHdId;

            $invHdRec = $this->dataModel->getInventoryHd($this->invHdId);
            if ($invHdRec) {
                $this->template->invNazev = $invHdRec["Nazev"];
                $this->template->skupZbo = $this->getSession('invHd')->invSkupZbo;
            }

            \Tracy\Debugger::barDump($this->invHdId);
        }
    }

    public function renderStdInventoryQty($sn, $idZboSklad, $generSN)
    {
        if (isset($this->getSession('invHd')->invHdId)) {
            $this->invHdId = $this->getSession('invHd')->invHdId;

            $this->template->invHdId = $this->invHdId;

            $invHdRec = $this->dataModel->getInventoryHd($this->invHdId);
            if ($invHdRec) {
                $this->template->invNazev = $invHdRec["Nazev"];
            }

            \Tracy\Debugger::barDump($this->template->invNazev);
        }

        $this->template->sn = $sn;
        $this->template->newSN = $generSN;

        $itemInfo = $this->dataModel->getInvItemInfo($idZboSklad);

        $invItemDefaults = ['NewSN' => $generSN];
        $invItemDefaults += ['VyrCislo' => $sn];
        if ($idZboSklad != -1) {
            $invItemDefaults += array('IDZboSklad' => $idZboSklad);
            $invItemDefaults += array('IDZboSkladOri' => $idZboSklad);
        }
        if ($this->getSession('invHd')->invSkupZbo == '151') {
            $invItemDefaults += ['Mnozstvi' => 1];
        }
        $this["stdInventoryForm"]->setDefaults($invItemDefaults);
        \Tracy\Debugger::barDump($this->template);
    }

    /**
     * SelectInventory form factory.
     * výběr inventury
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSelectInventoryForm()
    {

        $form = $this->selectInventoryFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {

            $invHdSess = $this->getSession('invHd');
            $invHdSess->invHdId = $values->IDInvHl;
            $invHdRec = $this->dataModel->getInventoryHd($values->IDInvHl);
            if ($invHdRec) {
                $invHdSess->invSkupZbo = $invHdRec->InvSkupZbo;
            }

            $this->redirect('Material:StdInventory');
        };

        return $form;
    }

    /**
     * formulář pro inventuru - načtení VČ, případně generování 
     * @return type
     */
    protected function createComponentStdInventoryBarCodeForm()
    {
        $form = $this->stdInventoryBarCodeFactory->create($this->getSession('invHd')->invSkupZbo);
        $form->onSuccess[] = function (Form $form, $values) { 
            /*
             * kontrola - otevřené doklady, existence VČ ve skladu a stav
             * přidat na příjemku 1 ks VČ
             * přidat na výdejku (pokud to jde) 1 ks se startovacím VČ
             */
            /*
            if ((!isset($values->VyrCislo) || $values->VyrCislo == '') && $values->TisknoutStitek) {
                // generování VČ
                $sn = $this->dataModel->generateSNKmen($values->Nazev);
                if (isset($sn) && $sn != '') {
                    $values->VyrCislo = $sn;
                }
                \Tracy\Debugger::barDump($sn);
                
            }
            */

            $idZboSklad = $this->dataModel->checkStdInventorySN($values->VyrCislo);

            if (isset($idZboSklad) && $this->getSession('invHd')->invSkupZbo == '151') {
                $res = $this->dataModel->processStdInventorySN($this->getSession('invHd')->invHdId, $values->VyrCislo, $idZboSklad, 1, false);

                if ($res == 1) {
                    $this->flashMessage('Není zadané výrobní číslo!', 'danger');
                } elseif ($res == 2) {
                    $this->flashMessage('Chybně zadaná skladová karta!', 'danger');
                } elseif ($res == 3) {
                    $this->flashMessage('Vybraná inventura je již uzavřená!', 'danger');
                } elseif ($res == 4) {
                    $this->flashMessage('Chybně zadaný sklad!', 'danger');
                } elseif ($res == 5) {
                    $this->flashMessage('Výrobní číslo ' . $values->VyrCislo . ' je už zaevidované!', 'danger');
                } elseif ($res == 99) {
                    $this->flashMessage('Chyba při úpravě položky inventury!', 'danger');
                } else {
                    // $this->flashMessage('Výrobní číslo bylo zaevidováno!', 'info');
                    /*
                    if ($values->TisknoutStitek) {
                        $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_material\\vc_stitek_" . $values->VyrCislo . ".txt", "w");
                        fwrite($f, $values->VyrCislo);
                    }
                    */
                }
    
                $this->redirect('Material:StdInventory');
    
            } else {
                if (isset($idZboSklad) && $idZboSklad > -1) {
                    $this->redirect('Material:StdInventoryQty', $values->VyrCislo, $idZboSklad, false); //$values->TisknoutStitek
                } else {
                    $this->flashMessage('VČ není v evidenci nebo chybně zadaná skladová karta!', 'danger');
                    $this->redirect('Material:StdInventory');
                }
            }
            /*
            if ($res == -1) {
                $this->flashMessage('Výrobní číslo nebylo nalezeno! Vybrat materiál', 'danger');
            } else {
                $this->flashMessage('Pokračovat!', 'info');
                $this->redirect('Material:StdInventoryQty', $res, $values->TisknoutStitek);
                if ($values->TisknoutStitek) {
                    $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_material\\vc_stitek_" . $values->VyrCislo . ".txt", "w");
                    fwrite($f, $values->VyrCislo);
                }
            }
            */
            \Tracy\Debugger::barDump($values);
        };

        return $form;
    }

    /**
     * formulář pro inventuru - zpracování dat po načtení VČ - dohledání v kmeni, doplnění množství
     * @return type
     */
    protected function createComponentStdInventoryForm()
    {
        $prumerZao = $this->dataModel->getPrumer1Zao();
        $nazevLaminatu = $this->dataModel->getLaminat();
        $form = $this->stdInventoryFactory->create($this->getSession('invHd')->invSkupZbo);
        $form->onSuccess[] = function (Form $form, $values) {
            /*
             * pokud je požadavek na generování VČ, vygenerovat nové 
             * kontrola - otevřené doklady, existence VČ ve skladu a stav
             * přidat na příjemku 1 ks VČ
             * přidat na výdejku (pokud to jde) 1 ks se startovacím VČ
             */

            if ($values->NewSN == 1) {
                // generování VČ
                $sn = $this->dataModel->generateSNSklad($values->IDZboSkladOri);
                if (isset($sn) && $sn != '') {
                    $values->VyrCislo = $sn;
                }
                \Tracy\Debugger::barDump($sn);
            }

            $res = $this->dataModel->processStdInventorySN($this->getSession('invHd')->invHdId, $values->VyrCislo, $values->IDZboSkladOri, $values->Mnozstvi, $values->NewSN);

            if ($res == 1) {
                $this->flashMessage('Není zadané výrobní číslo!', 'danger');
            } elseif ($res == 2) {
                $this->flashMessage('Chybně zadaná skladová karta!', 'danger');
            } elseif ($res == 3) {
                $this->flashMessage('Vybraná inventura je již uzavřená!', 'danger');
            } elseif ($res == 4) {
                $this->flashMessage('Chybně zadaný sklad!', 'danger');
            } elseif ($res == 99) {
                $this->flashMessage('Chyba při úpravě položky inventury!', 'danger');
            } else {
                $this->flashMessage('Výrobní číslo bylo zaevidováno!', 'info');
                /*
                if ($values->TisknoutStitek) {
                    $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_material\\vc_stitek_" . $values->VyrCislo . ".txt", "w");
                    fwrite($f, $values->VyrCislo);
                }
                */
            }

            \Tracy\Debugger::barDump($values);

            $this->redirect('Material:StdInventory');
        };

        return $form;
    }
}
