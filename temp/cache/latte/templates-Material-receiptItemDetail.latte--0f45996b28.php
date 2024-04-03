<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/receiptItemDetail.latte

use Latte\Runtime as LR;

class Template0f45996b28 extends Latte\Runtime\Template
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
		if (isset($this->params['receiptItemSN'])) trigger_error('Variable $receiptItemSN overwritten in foreach on line 60');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerMaterial.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="content">
<?php
		if ($receipt->StavRcpExp !== 1) {
?>
            <div class="row delivery-note-status">
                    <div class="col-sm-12">
                        <div class="set-dn-form">
<?php
			/* line 8 */
			$this->createTemplate('../components/readBarCodeForm.latte', ['form' => 'receiptReadBarCodeForm'] + $this->params, "include")->renderToContentType('html');
?>
                        </div>
                    </div>
            </div>
<?php
		}
?>
    <div class="row delivery-note-items">
        <div class="col-sm-12">
            <h2>Detail položky příjemky</h2>
            <table class="table table-bordered table-striped table-sm">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" colspan="3">Materiál</th>
                        <th scope="col" rowspan="2" class="text-right text-nowrap">
                            <a class="btn btn-sm btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Material:showReceipt")) ?>"><i class="far fa-level-up"></i></a>
                        </th>
                    </tr>
                    <tr>
                        <th scope="col">DL</th>
                        <th scope="col">Příjem</th>
                        <th scope="col">Zbývá</th>
                    </tr>
                </thead>
                <tbody>
<?php
		if (isset($receiptItem)) {
?>
                        <tr>
                            <td class="text-center font-weight-bold" colspan="3"><?php echo LR\Filters::escapeHtmlText($receiptItem->Nazev) /* line 33 */ ?></td>
                            <td class="text-right text-nowrap" rowspan="2">
<?php
			if (($receipt->StavRcpExp == 0)) {
				?>                                <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Zrušení příjmu" data-nette-confirm-text="Opravu chcete zrušit příjem u vybrané položky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeReceiptItem!", [$receiptItem->ReceiptItemID])) ?>"><i class="far fa-trash-alt"></i></a>
<?php
			}
?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->MnozstviDL, 2)) /* line 39 */ ?></td>
                            <td class="text-center font-weight-bold <?php
			if ($receiptItem->Mnozstvi == $receiptItem->MnozstviDL) {
				?>text-success<?php
			}
			elseif ($receiptItem->Mnozstvi > $receiptItem->MnozstviDL) {
				?>text-warning<?php
			}
			else {
				?>text-danger<?php
			}
			?>"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->Mnozstvi, 2)) /* line 40 */ ?></td>
                            <td class="text-center"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->MnozstviDL - $receiptItem->Mnozstvi, 2)) /* line 41 */ ?></td>
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
<div id="<?php echo htmlSpecialChars($this->global->snippetDriver->getHtmlId('itemSNTable')) ?>"><?php $this->renderBlock('_itemSNTable', $this->params) ?></div>        </div>
    </div>
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
                            <th scope="col">Příjato</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
		$iterations = 0;
		foreach ($receiptItemSNs as $receiptItemSN) {
?>
                            <tr>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($receiptItemSN->Nazev) /* line 62 */ ?></td>
                                <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItemSN->Mnozstvi, 2)) /* line 63 */ ?></td>
                                <td class="text-right text-nowrap">
<?php
			if (($receipt->StavRcpExp == 0)) {
				?>                                    <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Odebrat výrobní číslo z příjemky" data-nette-confirm-text="Opravdu chcete zrušit výrobní číslo z příjemky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeReceiptItemSN!", [$receiptItemSN->ReceiptItemID, $receiptItemSN->IDVyrCP])) ?>"><i class="far fa-trash-alt"></i></a>
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
