<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/setFinalOperationQuantity.latte

use Latte\Runtime as LR;

class Templatef733d5f84c extends Latte\Runtime\Template
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
        <div class="row delivery-note-item-sn">
            <div class="col-sm-12">
                <h2>Odvádění výrobního čísla z výroby</h2>
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
                                <th scope="col" class=" text-right">Výrobní číslo</th>
                                <th scope="col"><?php echo LR\Filters::escapeHtmlText($serialNumber) /* line 13 */ ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row" class=" text-right">Odvést z výroby</th>
                                <td class="text-center"><?php
		/* line 19 */
		$this->createTemplate('../components/setFinalOperationQuantityForm.latte', ['form' => 'setFinalOperationQuantityForm'] + $this->params, "include")->renderToContentType('html');
?></td>
                            </tr>
                        </tbody>
                    </table>
<?php
		$this->global->snippetDriver->leave();
		
	}

}
