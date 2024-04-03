<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/setQuantity.latte

use Latte\Runtime as LR;

class Templatec9e088a012 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'_itemSNTable' => 'blockItemSNTable',
	];

	public $blockTypes = [
		'content' => 'html',
		'_itemSNTable' => 'html',
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
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerDeliveryNote.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="content">
<?php
		if (isset($deliveryNote->UmisteniKod)) {
?>
        <div class="row delivery-note-items">
            <div class="col-sm-12">
                <h4>Detail položky dodacího listu</h4>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Zak/pol</th>
                            <th scope="col">Výkres</th>
                            <th scope="col">Rozměry</th>
                            <th scope="col">Požadavek</th>
                            <th scope="col">Připraveno</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
			if (isset($deliveryNoteItem)) {
?>
                            <tr>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Zakazka) /* line 22 */ ?></td>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Vykres) /* line 23 */ ?></td>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Rozmery) /* line 24 */ ?></td>
                                <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Mnozstvi) /* line 25 */ ?></td>
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
				?>"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->MnozstviVC) /* line 26 */ ?></td>
                                <td class="text-right">
<?php
				if (($deliveryNote->StavDLExp == 0)) {
					?>                                    <a class="btn btn-lg btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Zrušení přípravy" data-nette-confirm-text="Opravu chcete zrušit přípravu u vybrané položky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
					echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeDeliveryNoteItem!", [$deliveryNoteItemId])) ?>"><i class="far fa-trash-alt"></i></a>
<?php
				}
?>
                                </td>
                            </tr>
<?php
			}
?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row delivery-note-item-sn">
            <div class="col-sm-12">
                <h4>Nové výrobní číslo na dodací list</h4>
<div id="<?php echo htmlSpecialChars($this->global->snippetDriver->getHtmlId('itemSNTable')) ?>"><?php $this->renderBlock('_itemSNTable', $this->params) ?></div>            </div>
        </div>
<?php
		}
?>
</div>
<?php
	}


	function blockItemSNTable($_args)
	{
		extract($_args);
		$this->global->snippetDriver->enter("itemSNTable", "static");
?>
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class=" text-right">Výrobní číslo</th>
                                <th scope="col"><?php echo LR\Filters::escapeHtmlText($serialNumberRow->BarCode) /* line 44 */ ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row" class=" text-right">Přidat na DL</th>
                                <td class="text-center"><?php
		/* line 50 */
		$this->createTemplate('../components/setQuantityForm.latte', ['form' => 'setQuantityForm'] + $this->params, "include")->renderToContentType('html');
?></td>
                            </tr>
                            <tr>
                                <th scope="row" class=" text-right">Množství skladem</th>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($serialNumberRow->MnozstviPoPrijmuAVydeji) /* line 54 */ ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class=" text-right">Množství požadované</th>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($serialNumberRow->MnozstviPozadavek) /* line 58 */ ?></td>
                            </tr>
                        </tbody>
                    </table>
<?php
		$this->global->snippetDriver->leave();
		
	}

}
