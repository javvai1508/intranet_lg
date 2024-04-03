<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/showReceipt.latte

use Latte\Runtime as LR;

class Template560e763a43 extends Latte\Runtime\Template
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
		if (isset($this->params['receiptItem'])) trigger_error('Variable $receiptItem overwritten in foreach on line 34');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerMaterial.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="content">
            <div class="row delivery-note-header">
                <div class="col-sm-12">
                <div class="float-right delivery-note-status"><span class="<?php
		if ($receipt->Mnozstvi == $receipt->MnozstviDL) {
			?>text-success<?php
		}
		elseif ($receipt->Mnozstvi > $receipt->MnozstviDL) {
			?>text-warning<?php
		}
		else {
			?>text-danger<?php
		}
		?>"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receipt->Mnozstvi, 2)) /* line 6 */ ?></span> / <?php
		echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receipt->MnozstviDL, 2)) /* line 6 */ ?></span></div>
                <h1>DL: <?php echo LR\Filters::escapeHtmlText($receipt->CisloDL) /* line 7 */ ?></h1>
                <?php echo LR\Filters::escapeHtmlText($receipt->Firma) /* line 8 */ ?>

                <strong><?php echo LR\Filters::escapeHtmlText($receipt->DatumDL) /* line 9 */ ?></strong>
                </div>
            </div>
            <div class="row delivery-note-status">
<?php
		if ($receipt->StavRcpExp !== 1) {
?>
                    <div class="col-sm-12">
                        <div class="set-dn-form">
<?php
			/* line 16 */
			$this->createTemplate('../components/readBarCodeForm.latte', ['form' => 'receiptReadBarCodeForm'] + $this->params, "include")->renderToContentType('html');
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
                                <th scope="col">Materiál</th>
                                <th scope="col">DL</th>
                                <th scope="col">Příjem</th>
                                <th scope="col">Zbývá</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
		$iterations = 0;
		foreach ($receiptItems as $receiptItem) {
			?>                                <tr class="clickable-row" data-href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("receiptItemDetail", [$receiptItem->ReceiptItemID])) ?>">
                                    <td class="text-left"><?php echo LR\Filters::escapeHtmlText($receiptItem->Nazev) /* line 36 */ ?><br>Obj: <?php
			echo LR\Filters::escapeHtmlText($receiptItem->Objednavka) /* line 36 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->MnozstviDL, 2)) /* line 37 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->Mnozstvi, 2)) /* line 38 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, ($receiptItem->MnozstviDL - $receiptItem->Mnozstvi), 2)) /* line 39 */ ?></td>
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
		if ($receipt->StavRcpExp == 1) {
			?>                        <span class="text-danger font-weight-bold">Uzavřeno: <?php echo LR\Filters::escapeHtmlText($receipt->DatUzavrenoExp) /* line 49 */ ?></span>
<?php
		}
		else {
			if (($receipt->StavRcpExp == 0)) {
				?>                    <a class="btn btn-success btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("closeReceipt!")) ?>"><i class="far fa-forklift"></i> Uzavřít příjem</a><?php
			}
?>
<br>
<?php
			if (($receipt->StavRcpExp == 0)) {
				?>                    <a class="btn btn-warning btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("closeReceiptQuick!")) ?>"><i class="far fa-fighter-jet"></i> Uzavřít příjem ihned</a>
<?php
			}
		}
?>
                </div>
</div>
<?php
	}

}
