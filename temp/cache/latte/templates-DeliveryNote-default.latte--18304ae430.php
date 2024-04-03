<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/default.latte

use Latte\Runtime as LR;

class Template18304ae430 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'_finalOperation' => 'blockFinalOperation',
	];

	public $blockTypes = [
		'content' => 'html',
		'_finalOperation' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['deliveryNoteItem'])) trigger_error('Variable $deliveryNoteItem overwritten in foreach on line 60');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerDeliveryNote.latte', $this->params, "include")->renderToContentType('html');
		?><div id="<?php echo htmlSpecialChars($this->global->snippetDriver->getHtmlId('finalOperation')) ?>"><?php
		$this->renderBlock('_finalOperation', $this->params) ?></div><div class="content">
<?php
		if (isset($deliveryNote->UmisteniKod)) {
?>
            <div class="row delivery-note-header">
                <div class="col-sm-12">
                <!-- div class="float-right"><a n:href="changeDeliveryNote!" class="btn btn-primary btn-sm"><i class="far fa-exchange-alt"></i></a></div -->
                <h1><?php echo LR\Filters::escapeHtmlText($deliveryNote->Odberatel) /* line 29 */ ?></strong></h1>
                </div>
            </div>
            <div class="row delivery-note-header">
                <div class="col-sm-12">
                <div class="float-right delivery-note-status"><span class="<?php
			if ($deliveryNote->MnozstviVC == $deliveryNote->MnozstviPozad) {
				?>text-success<?php
			}
			elseif ($deliveryNote->MnozstviVC > $deliveryNote->MnozstviPozad) {
				?>text-warning<?php
			}
			else {
				?>text-danger<?php
			}
			?>"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $deliveryNote->MnozstviVC, 2)) /* line 34 */ ?></span> / <?php
			echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $deliveryNote->MnozstviPozad, 2)) /* line 34 */ ?></span></div>
                <strong><?php echo LR\Filters::escapeHtmlText($deliveryNote->DatDodavky) /* line 35 */ ?></strong>
                </div>
            </div>
            <div class="row delivery-note-status">
<?php
			if ($deliveryNote->StavDLExp !== 1) {
?>
                    <div class="col-sm-12">
                        <div class="set-dn-form">
<?php
				/* line 42 */
				$this->createTemplate('../components/readBarCodeForm.latte', ['form' => 'readBarCodeForm'] + $this->params, "include")->renderToContentType('html');
?>
                        </div>
                    </div>
<?php
			}
?>
            </div>
        <div class="delivery-note-content">
            <div class="row delivery-note-items">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Zak/pol/rozměry</th>
                                <th scope="col">Název</th>
                                <th scope="col">Plán</th>
                                <th scope="col">Příprava</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
			$iterations = 0;
			foreach ($deliveryNoteItems as $deliveryNoteItem) {
				?>                                <tr class="clickable-row" data-href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("itemDetail", [$deliveryNoteItem->ID])) ?>">
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Zakazka) /* line 62 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Vykres) /* line 63 */ ?></td>
                                    <td class="text-center font-weight-bold" rowspan="2"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Mnozstvi) /* line 64 */ ?></td>
                                    <td class="text-center font-weight-bold <?php
				if ($deliveryNoteItem->MnozstviVC == $deliveryNoteItem->Mnozstvi) {
					?>text-success<?php
				}
				elseif ($deliveryNoteItem->MnozstviVC > $deliveryNoteItem->Mnozstvi) {
					?>text-warning<?php
				}
				else {
					?>text-danger<?php
				}
				?>" rowspan="2"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->MnozstviVC) /* line 65 */ ?></td>
                                </tr>
                                <tr class="clickable-row" data-href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("itemDetail", [$deliveryNoteItem->ID])) ?>">
                                    <td class="text-center" colspan="2"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Rozmery) /* line 68 */ ?></td>
                                </tr>
<?php
				$iterations++;
			}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                <div class="text-center">
<?php
			if ($deliveryNote->StavDLExp == 1) {
				if (isset($deliveryNote->DatUzavrenoExp)) {
					?>                            <span class="text-danger font-weight-bold">Uzavřeno: <?php echo LR\Filters::escapeHtmlText($deliveryNote->DatUzavrenoExp) /* line 79 */ ?></span>
<?php
				}
				else {
?>
                            <span class="text-danger font-weight-bold">Vyfakturováno</span>
<?php
				}
			}
			else {
				if (($deliveryNote->StavDLExp == 0)) {
					?>                    <a class="btn btn-success btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("closeDeliveryNote!")) ?>"><i class="far fa-forklift"></i> Uzavřít DL</a>
<?php
				}
			}
			?>                    <a class="btn btn-primary btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("changeDeliveryNote!")) ?>"><i class="far fa-exchange-alt"></i> Změnit DL</a>
                </div>
                <div class="text-center">
                    <h5>Balíky</h5>
                    <a class="btn btn-sm btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:packages")) ?>"><i class="far fa-cubes"></i> Přehled</a> 
<?php
			if (($deliveryNote->StavDLExp == 0)) {
				?>                    <a class="btn btn-primary btn-sm m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("openPackage!")) ?>"><i class="far fa-box-open"></i> Nový</a>
<?php
			}
			if (($deliveryNote->StavDLExp == 0)) {
				?>                    <a class="btn btn-primary btn-sm m-1 <?php
				if (!$packageOpen) {
					?>disabled<?php
				}
				?>" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("closePackage!")) ?>"><i class="far fa-box-check"></i> Konec</a>
<?php
			}
?>
                </div>
<?php
		}
		else {
			if (isset($deliveryNote->ID)) {
?>
                        <div class="set-dn-form">
                            <?php
				/* line 97 */
				$this->createTemplate('../../templates/components/setLocationForm.latte', ['form' => 'setLocationForm'] + $this->params, "include")->renderToContentType('html');
?></div>
<?php
			}
			else {
?>
                    <div class="set-dn-form">
<?php
				/* line 100 */
				$this->createTemplate('../../templates/components/setDeliveryNoteForm.latte', ['form' => 'setDeliveryNoteForm'] + $this->params, "include")->renderToContentType('html');
?>
                    </div>
<?php
			}
		}
?>
</div>
<?php
	}


	function blockFinalOperation($_args)
	{
		extract($_args);
		$this->global->snippetDriver->enter("finalOperation", "static");
		if (isset($finalOperation) && $finalOperation) {
?>
        <div id="finalOperation" class='modal fade' tabindex='-1' role='dialog' aria-labelledby='confirmModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content' id='dConfirm'>
                    <div class='modal-header'>
                        <h4 class='modal-title info' id='confirmModalLabel'>Výrobní číslo není na skladě</h4>
                        <button type='button' class='close' class='btn btn-lg' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    </div>
                    <div class='modal-body'>
                        <strong>Je možné zaevidovat odváděcí operaci a výrobní číslo odvést na sklad. Opravdu provést?</strong>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' id='dConfirmCancel' class='btn btn-lg m5 btn-secondary' data-dismiss='modal'>Ne</button>
                        <button type='button' id='dConfirmOk' class='btn btn-lg m5 btn-primary' data-dismiss='modal'>Ano</button>
                    </div>
                </div>
            </div>
        </div>
<?php
		}
		$this->global->snippetDriver->leave();
		
	}

}
