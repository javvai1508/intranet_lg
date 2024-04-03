<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/dispatchItemDetail.latte

use Latte\Runtime as LR;

class Templatec6e662a4df extends Latte\Runtime\Template
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
		if (isset($this->params['dispatchItemSN'])) trigger_error('Variable $dispatchItemSN overwritten in foreach on line 53');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerMaterial.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="content">
    <div class="row delivery-note-items">
        <div class="col-sm-12">
            <h5>Detail položky výdejky</h5>
            <table class="table table-bordered table-striped table-sm">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Materiál</th>
                        <th scope="col">Vydáno</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
<?php
		if (isset($dispatchItem)) {
?>
                        <tr>
                            <td class="text-center"><?php echo LR\Filters::escapeHtmlText($dispatchItem->Nazev) /* line 18 */ ?></td>
                            <td class="text-center"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $dispatchItem->MnozstviVC, 2)) /* line 19 */ ?></td>
                            <td class="text-right text-nowrap">
<?php
			if (($dispatch->StavDispExp == 0)) {
				?>                                <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Zrušení příjmu" data-nette-confirm-text="Opravu chcete zrušit příjem u vybrané položky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeDispatchItem!", [$dispatchItem->DispatchItemID])) ?>"><i class="far fa-trash-alt"></i></a>
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
            <div class="row">
                <div class="col-sm-12"><h5>Výrobní čísla položky</h5></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="set-dn-form">
                    </div>
                </div>
            </div>
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
                            <th scope="col">Vydáno</th>
                            <th scope="col" class="text-right">
                                <a class="btn btn-sm btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Material:showDispatch")) ?>"><i class="far fa-level-up"></i></a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
<?php
		$iterations = 0;
		foreach ($dispatchItemSNs as $dispatchItemSN) {
?>
                            <tr>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($dispatchItemSN->Nazev) /* line 55 */ ?></td>
                                <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $dispatchItemSN->Mnozstvi, 2)) /* line 56 */ ?></td>
                                <td class="text-right text-nowrap">
<?php
			if (($dispatch->StavDispExp == 0)) {
				?>                                    <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Odebrat výrobní číslo z výdejky" data-nette-confirm-text="Opravdu chcete zrušit výrobní číslo z výdejky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeDispatchItemSN!", [$dispatchItemSN->DispatchItemID, $dispatchItemSN->IDVyrCP])) ?>"><i class="far fa-trash-alt"></i></a>
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
