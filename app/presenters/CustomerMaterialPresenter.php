<?php

namespace App\Presenters;

use App\Models\CustMaterialModel,
    App\Forms\SelectCustMaterialFormFactory,
    App\Forms\NewCustMaterialFormFactory,
    Nette\Application\UI\Form,
    Joseki\Application\Responses\PdfResponse;

class CustomerMaterialPresenter extends BasePresenter {

    /** @var CustMaterialModel @inject */
    public $dataModel;

    /** @var SelectCustMaterialFormFactory @inject */
    public $selectCustMaterialFactory;

    /** @var NewCustMaterialFormFactory @inject */
    public $newCustMaterialFactory;

    /** @var CustMaterId */
    private $custMaterId;

    public function __construct() {
        parent::__construct();
    }

    public function startup() {
        parent::startup();
        
        if (!$this->user->isInRole("prijMatZak")) {
            $this->redirect("Homepage:");
        }
    }
    
    /**
     * 
     */
    public function renderDefault() {
        if (isset($this->getSession('custMaterId')->custMaterId)) {
            $this->custMaterId = $this->getSession('custMaterId')->custMaterId;

            $custMater = $this->custMaterModel->getCustMater($this->custMaterId);

            $this->template->custMaterNr = $custMater->CisloKarty;
            $this->template->custMater = $custMater;

            if ($this["selectCustMaterForm"]) {
                $custMaterDefaults = array('custMaterId' => $this->custMaterId);
                $this["selectCustMaterForm"]->setDefaults($custMaterDefaults);
            }

            \Tracy\Debugger::barDump($this->template->custMater);
        }

        // \Tracy\Debugger::barDump($this->showSession());
        
    }

    /* ------------- vyhledat skladovou kartu -------------------------------------- */

    /**
     * SelectCustMaterial form factory.
     * vyhledání skladové karty - zatím nepoužito
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSelectCustMaterialForm() {
        $form = $this->selectCustMaterialFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {
        };

        return $form;
    }

    /* ------------- nový materiál -------------------------------------- */

    /**
     * NewCustMaterial form factory.
     * nový materiál
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentNewCustMaterialForm() {
        $form = $this->newCustMaterialFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {

            $custMaterId = $this->dataModel->saveNewCustomerMaterial($values);

            // původní postup - tisk Robotaskem z HeO
            $f = fopen("c:\\inetpub\\wwwroot\\www\\batch_matzak\\" . $custMaterId . ".txt", "w");
            fwrite($f, $custMaterId);

            // nový postup - generování PDF a tisk Robotaskem pomocí SumatraPDF
            $receiptItem = $this->dataModel->getReceiptItem($custMaterId);
            $this->handleCustMatLabelPDF($receiptItem);
            // \Tracy\Debugger::barDump($custMaterId);
            // system("(echo ".$custMaterId.") >\\\\vv_nb\\MC352");

            $this->redirect("CustomerMaterial:default");
        };

        return $form;
    }

    /**
     * autocomplete formulářového pole firma
     * 
     * @param type $term
     */
    public function handleAutocompleteFirma($term) {
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
    public function handleChangeCisloOrg($cisloOrg) {
        if ($this->isAjax()) {
            $firma = $this->dataModel->getFirma($cisloOrg)["Firma"];
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse($firma));
        }
        
    }

    /**
     * Handle - vytvoření štítku v PDF
     */
    public function handleCustMatLabelPdf($receiptItem) {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . "/templates/CustomerMaterial/custMatLabelPDF5x10.latte");
        $template->receiptItem = $receiptItem;
        
        $pdf = new \Joseki\Application\Responses\PdfResponse($template);

        $pdf->pageFormat = array(50, 100);
        // $pdf->pageFormat = "A4-L";
        $pdf->setPageMargins("2, 1, 1, 2, 1, 1");
        $pdf->styles = file_get_contents("c:\\inetpub\\wwwroot\\www\\css\\pdf-label-style.css");
        // $pdf->getMPDF()->setFooter("");
        $numberOfCopies = intval($receiptItem->Mnozstvi) . "x";
        $printSettings = "noscale, " . $numberOfCopies;

        /* localhost */
        // $printCmd = 'cmd /c c:\\fs\\data_helios\\script\\sumatrapdf.exe -print-to "OKI MC352(PCL)" -print-settings "' . $printSettings . '" c:\\fs\\data_helios\\batch_matzak\\' . $receiptItem->ID . '.pdf';
        // $pdf->save("c:\\fs\\data_helios\\batch_matzak",  $receiptItem->ID);        

        /* server */
        $pdf->save("e:\\fs\\data_helios\\batch_matzak",  $receiptItem->ID);        
        $f = fopen("e:\\fs\\data_helios\\batch_matzak\\" . $receiptItem->ID . ".pdf.settings", "w");
        fwrite($f, $printSettings);
        // $printCmd = 'cmd /c e:\\fs\\data_helios\\script\\sumatrapdf.exe -print-to "CITIZEN 06" -print-settings ' . $printSettings . '" e:\\fs\\data_helios\\batch_matzak\\' . $receiptItem->ID . '.pdf';
        // $pdf->save("e:\\fs\\data_helios\\batch_matzak",  $receiptItem->ID);        

        // exec($printCmd, $result);

        /* localhost */
        // unlink("c:\\fs\\data_helios\\batch_matzak\\" . $receiptItem->ID . ".pdf");

        /* server */
        // unlink("e:\\fs\\data_helios\\batch_matzak\\" . $receiptItem->ID . ".pdf");

        /* tisk pomocí HeO
        $f = fopen("c:\\fs\\data_helios\\batch_matzak\\" . $receiptItem->ID . ".pdf.settings", "w");
        fwrite($f, $printSettings);
        */

        // \Tracy\Debugger::barDump($printCmd);

    }

}
