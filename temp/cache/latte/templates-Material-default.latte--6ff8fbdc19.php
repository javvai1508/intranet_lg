<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/default.latte

use Latte\Runtime as LR;

class Template6ff8fbdc19 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
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
		if (isset($this->params['deliveryNoteItem'])) trigger_error('Variable $deliveryNoteItem overwritten in foreach on line 52');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerMaterial.latte', $this->params, "include")->renderToContentType('html');
?>
                <div class="col-sm-5"> 
                    <div class="set-dn-form">
<?php
		/* line 5 */
		$this->createTemplate('../../templates/components/setStockDocForm.latte', ['form' => 'setStockDocForm'] + $this->params, "include")->renderToContentType('html');
?>
                    </div>
                </div>
<div class="row content">
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("newReceipt")) ?>"> 
                <i class="far fa-arrow-to-right fa-2x"></i><br><br><span class="sm">Příjemka</span>
            </a>
        </div>
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("openNewDispatch")) ?>"> 
                <i class="far fa-arrow-from-left fa-2x"></i><br><br><span class="sm">Výdejka</span>
            </a>
        </div>
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("stdInventory")) ?>"> 
                <i class="far fa-inventory fa-2x"></i><br><br><span class="sm">Inventura</span>
            </a>
        </div>

<?php
		if (isset($order->orderId)) {
?>
        <div class="row delivery-note-header">
            <div class="col-sm-3">
                <a class="btn btn-primary btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("changeOrder!")) ?>"><i class="far fa-exchange-alt"></i> Změnit VObj</a>
            </div>
            <div class="col-sm-9 text-right">
<?php
			if (($deliveryNote->StavDLExp == 0)) {
				?>                <a class="btn btn-success btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("closeReceipt!")) ?>"><i class="far fa-hand-receiving"></i> Uzavřít příjemku</a>
<?php
			}
?>
            </div>
        </div>
        <div class="delivery-note-content">
            <div class="row delivery-note-items">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Zak/pol</th>
                                <th scope="col">Výkres</th>
                                <th scope="col">Rozměry</th>
                                <th scope="col">Plán</th>
                                <th scope="col">Příprava</th>
                                <th scope="col" class="text-right">
                                    <a class="btn btn-lg btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:packages")) ?>"><i class="far fa-cubes"></i></a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
<?php
			$iterations = 0;
			foreach ($deliveryNoteItems as $deliveryNoteItem) {
?>
                                <tr>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Zakazka) /* line 54 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Vykres) /* line 55 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Rozmery) /* line 56 */ ?></td>
                                    <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Mnozstvi) /* line 57 */ ?></td>
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
				?>"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->MnozstviVC) /* line 58 */ ?></td>
                                    <td class="text-right text-nowrap">
                                        <a class="btn btn-lg btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("itemDetail", [$deliveryNoteItem->ID])) ?>"><i class="far fa-info"></i></a>
<?php
				if (($deliveryNote->StavDLExp == 0)) {
					?>                                        <a class="btn btn-lg btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Zrušení přípravy" data-nette-confirm-text="Opravu chcete zrušit přípravu u vybrané položky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
					echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeDeliveryNoteItem!", [$deliveryNoteItem->ID])) ?>"><i class="far fa-trash-alt"></i></a>
<?php
				}
?>
                                    </td>
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
<?php
		}
		elseif ($presenter->isLinkCurrent("Material:NewReceipt")) {
			/* line 71 */
			$this->createTemplate('../components/setOrderForm.latte', ['form' => 'setOrderForm'] + $this->params, "include")->renderToContentType('html');
		}
?>
</div>
<?php
	}

}
