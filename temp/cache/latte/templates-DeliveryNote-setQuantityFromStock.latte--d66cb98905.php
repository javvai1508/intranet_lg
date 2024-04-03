<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/setQuantityFromStock.latte

use Latte\Runtime as LR;

class Templated66cb98905 extends Latte\Runtime\Template
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
                <h4>Zadat množství</h4>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th scope="row" class=" text-right">Přidat na DL</th>
                                <td class="text-center"><?php
			/* line 50 */
			$this->createTemplate('../components/setQuantityFromStockForm.latte', ['form' => 'setQuantityFromStockForm'] + $this->params, "include")->renderToContentType('html');
?></td>
                            </tr>
                            <tr>
                                <th scope="row" class=" text-right">Množství skladem</th>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($partNumberRow->MnozstviPoPrijmuAVydeji) /* line 54 */ ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class=" text-right">Množství požadované</th>
                                <td class="text-center"><?php echo LR\Filters::escapeHtmlText($partNumberRow->MnozstviPozadavek) /* line 58 */ ?></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>
<?php
		}
?>
</div>
<?php
	}

}
