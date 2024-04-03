<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/itemDetail.latte

use Latte\Runtime as LR;

class Templated0bfd05817 extends Latte\Runtime\Template
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
		if (isset($this->params['deliveryNoteItemSN'])) trigger_error('Variable $deliveryNoteItemSN overwritten in foreach on line 63');
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
            <div class="row delivery-note-status">
<?php
			if ($deliveryNote->StavDLExp !== 1) {
?>
                    <div class="col-sm-12">
                        <div class="set-dn-form">
<?php
				/* line 9 */
				$this->createTemplate('../components/readBarCodeForm.latte', ['form' => 'readBarCodeForm'] + $this->params, "include")->renderToContentType('html');
?>
                        </div>
                    </div>
<?php
			}
?>
            </div>
        <div class="row delivery-note-items">
            <div class="col-sm-12">
                <h2>Detail položky dodacího listu</h2>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Zak/Náz/Rozm</th>
                            <th scope="col">Pož</th>
                            <th scope="col">Připr</th>
                                <th scope="col" class="text-right">
                                    <a class="btn btn-sm btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:default")) ?>"><i class="far fa-level-up"></i></a>
                                </th>
                        </tr>
                    </thead>
                    <tbody>
<?php
			if (isset($deliveryNoteItem)) {
?>
                            <tr>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Zakazka) /* line 31 */ ?></td>
                                <td class="text-center font-weight-bold" rowspan="2"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Mnozstvi) /* line 32 */ ?></td>
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
				?>" rowspan="2"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->MnozstviVC) /* line 33 */ ?></td>
                                <td class="text-right" rowspan="2">
<?php
				if (($deliveryNote->StavDLExp == 0)) {
					?>                                    <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Zrušení přípravy" data-nette-confirm-text="Opravu chcete zrušit přípravu u vybrané položky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
					echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeDeliveryNoteItem!", [$deliveryNoteItemId])) ?>"><i class="far fa-trash-alt"></i></a>
<?php
				}
?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Vykres) /* line 39 */ ?></td>
                            </tr>
                            <tr>
                                <td class="text-center" colspan="4"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Rozmery) /* line 42 */ ?></td>
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
                <h2>Výrobní čísla položky dodacího listu</h2>
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
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Výrobní číslo</th>
                                <th scope="col">Skladem</th>
                                <th scope="col">Na DL</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
<?php
		$iterations = 0;
		foreach ($deliveryNoteItemSNs as $deliveryNoteItemSN) {
?>
                                <tr>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItemSN->Nazev) /* line 65 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItemSN->MnozstviPoPrijmuAVydeji) /* line 66 */ ?></td>
                                    <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItemSN->MnozstviCP) /* line 67 */ ?>

                                    </td>
                                    <td class="text-right text-nowrap">
<?php
			if (($deliveryNote->StavDLExp == 0)) {
				?>                                        <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Odebrat výrobní číslo z dodacího listu" data-nette-confirm-text="Opravdu chcete zrušit výrobní číslo z dodacího listu?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeDeliveryNoteItemSN!", [$deliveryNoteItem->ID, $deliveryNoteItemSN->IDVyrCP])) ?>"><i class="far fa-trash-alt"></i></a>
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
<?php
		$this->global->snippetDriver->leave();
		
	}

}
