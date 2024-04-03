<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\Material\headerMaterial.latte

use Latte\Runtime as LR;

class Template6cb0d47355 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<div class="row header">
<?php
		if ($user->isLoggedIn()) {
?>
                    <div class="logged">
                        <div class="btn-group">    
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" id="menuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                <i class="far fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="menuButton">
                                <a class="dropdown-item logged-user lg" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:")) ?>"><?php
			echo LR\Filters::escapeHtmlText($user->getIdentity()->getData()['fullname']) /* line 9 */ ?></a>
<?php
			if ($user->isInRole('expedice')) {
				?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:default")) ?>"><i class="far fa-truck"></i> Expedice</a>
<?php
			}
			if ($user->isInRole('prijMatZak')) {
				?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("CustomerMaterial:default")) ?>"><i class="far fa-inbox-in"></i> Materiál zákazníka</a>
<?php
			}
			if ($user->isInRole('skladMat')) {
				?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Material:default")) ?>"><i class="far fa-pallet-alt"></i> Sklad materiálu</a>
<?php
			}
			?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:out")) ?>"><i class="far fa-sign-out"></i> Odhlásit</a>
                            </div>
                        </div>
                    </div>
<?php
		}
		else {
?>
                    <div class="button-sign">
                        <a title="Přihlásit" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:in")) ?>">
                            <i class="far fa-sign-in"></i>
                        </a>
                    </div>
<?php
		}
?>

<?php
		if (isset($receipt->ID)) {
			?>                <h1>Příj: <?php echo LR\Filters::escapeHtmlText($receiptNr) /* line 26 */ ?></h1>
<?php
		}
		elseif (isset($dispatch->ID)) {
			?>                <h1>Výd: <?php echo LR\Filters::escapeHtmlText($dispatchNr) /* line 28 */ ?></h1>
<?php
		}
		elseif (isset($deliveryNote->ID)) {
			?>                <h1>DL: <?php echo LR\Filters::escapeHtmlText($deliveryNoteNr) /* line 30 */ ?></h1>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:NewReceipt")) {
?>
                <h1>Nová příjemka</h1>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:OpenNewDispatch")) {
?>
                <h1>Nová výdejka</h1>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:OpenDispatchSale")) {
?>
                <h1>Prodej laminátu</h1>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:StdInventory") || $presenter->isLinkCurrent("Material:StdInventoryQty")) {
			if (isset($invHdId)) {
				?>                    <h1>Inv: <?php echo LR\Filters::escapeHtmlText($invNazev) /* line 39 */ ?></h1>
<?php
			}
			else {
?>
                    <h1>Inventura</h1>
<?php
			}
		}
		else {
?>
                <h1>Sklad materiálu</h1>
<?php
		}
?>
</div>

    <!-- div class="col-sm-12">
        <div class="row page-header back-receiving-mater">
<?php
		if (isset($receipt->ID)) {
			?>                <div class="col-sm-4"><h3 class="mt-2">Příj: <?php echo LR\Filters::escapeHtmlComment($receiptNr) /* line 51 */ ?></h3></div>
                <div class="col-sm-4"><a n:href="changeStockDoc" class="btn btn-secondary btn-lg m-1"><i class="far fa-exchange-alt"></i> Změnit příjemku</a></div>
<?php
		}
		elseif (isset($dispatch->ID)) {
			?>                <div class="col-sm-4"><h3 class="mt-2">Výd: <?php echo LR\Filters::escapeHtmlComment($dispatchNr) /* line 54 */ ?></h3></div>
                <div class="col-sm-4"><a n:href="changeStockDoc" class="btn btn-secondary btn-lg m-1"><i class="far fa-exchange-alt"></i> Změnit výdejku</a></div>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:NewReceipt")) {
?>
                <div class="col-sm-8"><h3 class="mt-2">Nová příjemka</h3></div>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:OpenNewDispatch")) {
?>
                <div class="col-sm-8"><h3 class="mt-2">Nová výdejka</h3></div>
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:StdInventory") || $presenter->isLinkCurrent("Material:StdInventoryQty")) {
?>
                <div class="col-sm-4"><h3 class="mt-2">Inventura</h3></div>
<?php
			if (isset($invHdId)) {
				?>                    <div class="col-sm-4"><h3 class="mt-2"><?php echo LR\Filters::escapeHtmlComment($invNazev) /* line 63 */ ?></h3></div>
<?php
			}
			else {
?>
                    <div class="col-sm-4">
                        <div class="set-dn-form">
                            <?php
				/* line 67 */
				$this->createTemplate('../../templates/components/selectInventoryForm.latte', ['form' => 'selectInventoryForm'] + $this->params, "include")->renderToContentType('htmlComment');
?></div>
                    </div>
<?php
			}
		}
		else {
?>
                <div class="col-sm-3 novy-doklad"> 
                    <div class="btn-group drop-right">    
                        <button class="btn btn-primary dropdown-toggle" type="button" id="menuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                            Nový doklad
                        </button>
                        <div class="dropdown-menu" aria-labelledby="menuButton">
                            <a class="dropdown-item" n:href="newReceipt" ><i class="far fa-arrow-to-right"></i> Příjemka</a>
                            <a class="dropdown-item" n:href="openNewDispatch" ><i class="far fa-arrow-from-right"></i> Výdejka</a>
                            <a class="dropdown-item" n:href="stdInventory" ><i class="far fa-inventory"></i> Inventura</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5"> 
                    <div class="set-dn-form">
<?php
			/* line 85 */
			$this->createTemplate('../../templates/components/setStockDocForm.latte', ['form' => 'setStockDocForm'] + $this->params, "include")->renderToContentType('htmlComment');
?>
                    </div>
                </div>
<?php
		}
?>
            <div class="col-sm-4">
<?php
		if ($user->isLoggedIn()) {
?>
                    <div class="logged">
                        <div class="btn-group dropleft">    
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="menuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                <?php echo LR\Filters::escapeHtmlComment($user->getIdentity()->getData()['fullname']) /* line 94 */ ?>

                            </button>
                            <div class="dropdown-menu" aria-labelledby="menuButton">
                                <a class="dropdown-item" n:if="$user->isInRole('expedice')" n:href="DeliveryNote:default">Expedice</a>
                                <a class="dropdown-item" n:if="$user->isInRole('prijMatZak')" n:href="CustomerMaterial:default">Materiál zákazníka</a>
                                <a class="dropdown-item" n:if="$user->isInRole('skladMat')" n:href="Material:default">Sklad materiálu</a>
                            </div>
                        </div>
                        <a class="btn btn-light btn-lg ml-3" n:href="Sign:out">
                            <i class="far fa-sign-out"></i>
                        </a>
                    </div>
<?php
		}
		else {
?>
                    <div class="button-sign">
                        <a n:href="Sign:in" title="Přihlásit">
                            <i class="far fa-sign-in"></i> Přihlásit
                        </a>
                    </div>
<?php
		}
?>
            </div>
        </div>
<?php
		if (isset($receipt)) {
?>
            <div class="row delivery-note-header">
                <div class="col-sm-6 delivery-note-header-text nowrap">
                    <strong><?php echo LR\Filters::escapeHtmlComment($receipt->Firma) /* line 118 */ ?></strong>
                </div>
                <div class="col-sm-3 delivery-note-header-text">
                    <strong>DL: <?php echo LR\Filters::escapeHtmlComment($receipt->CisloDL) /* line 121 */ ?></strong>
                </div>
                <div class="col-sm-3 delivery-note-header-text">
                    <strong><?php echo LR\Filters::escapeHtmlComment($receipt->DatumDL) /* line 124 */ ?></strong>
                </div>
            </div>
            <div class="row delivery-note-status">
<?php
			if ($receipt->StavRcpExp == 1) {
?>
                    <div class="col-sm-12 text-right">
                        <span class="text-danger font-weight-bold">Uzavřeno: <?php echo LR\Filters::escapeHtmlComment($receipt->DatUzavrenoExp) /* line 130 */ ?></span>
                    </div>
<?php
			}
			else {
?>
                    <div class="col-sm-4">
                        <div class="set-dn-form">
<?php
				/* line 135 */
				$this->createTemplate('../components/readBarCodeForm.latte', ['form' => 'receiptReadBarCodeForm'] + $this->params, "include")->renderToContentType('htmlComment');
?>
                        </div>
                    </div>
                    <div class="col-sm-4 text-center">
                        Ks: <span class="font-weight-bold"><span class="<?php
				if ($receipt->Mnozstvi == $receipt->MnozstviDL) {
					?>text-success<?php
				}
				elseif ($receipt->Mnozstvi > $receipt->MnozstviDL) {
					?>text-warning<?php
				}
				else {
					?>text-danger<?php
				}
				?>"><?php echo LR\Filters::escapeHtmlComment(call_user_func($this->filters->number, $receipt->Mnozstvi, 2)) /* line 139 */ ?></span> / <?php
				echo LR\Filters::escapeHtmlComment(call_user_func($this->filters->number, $receipt->MnozstviDL, 2)) /* line 139 */ ?></span>
                    </div>
                    <div class="col-sm-4 text-right">
                        <a n:href="closeReceipt!" class="btn btn-success btn-lg m-1"><i class="far fa-forklift"></i> Uzavřít příjem</a>
                    </div>
<?php
			}
?>
            </div>
<?php
		}
?>
    </div -->

<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
