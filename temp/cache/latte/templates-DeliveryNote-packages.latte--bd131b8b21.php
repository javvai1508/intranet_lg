<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/packages.latte

use Latte\Runtime as LR;

class Templatebd131b8b21 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'_packagesTable' => 'blockPackagesTable',
	];

	public $blockTypes = [
		'content' => 'html',
		'_packagesTable' => 'html',
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
		if (isset($this->params['package'])) trigger_error('Variable $package overwritten in foreach on line 24');
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
                <h4>Balení dodacího listu</h4>
<div id="<?php echo htmlSpecialChars($this->global->snippetDriver->getHtmlId('packagesTable')) ?>"><?php
			$this->renderBlock('_packagesTable', $this->params) ?></div>            </div>
        </div>
<?php
		}
?>
</div>
<?php
	}


	function blockPackagesTable($_args)
	{
		extract($_args);
		$this->global->snippetDriver->enter("packagesTable", "static");
?>
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Balík</th>
                                <th scope="col">Zak/pol</th>
                                <th scope="col">Výkres<br>Rozměry</th>
                                <th scope="col">Výr. číslo</th>
                                <th scope="col">Množství v balíku</th>
                                <th scope="col" class="text-right">
                                    <a class="btn btn-sm btn-primary mr-2" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:default")) ?>"><i class="far fa-level-up"></i></a>
                                </th>
                            </tr>
                        </thead>
<?php
		$cisloBal = 0;
?>
                        <tbody>
<?php
		$iterations = 0;
		foreach ($packages as $package) {
?>
                                <tr>
<?php
			if ($package->CisloBal != $cisloBal) {
				?>                                    <td class="text-center" rowspan="<?php echo LR\Filters::escapeHtmlAttr($package->KsVBaliku) /* line 27 */ ?>"><?php
				echo LR\Filters::escapeHtmlText($package->CisloBal) /* line 27 */ ?></td>
<?php
				$cisloBal = $package->CisloBal;
			}
			?>                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($package->Zakazka) /* line 30 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($package->Vykres) /* line 31 */ ?><br><?php
			echo LR\Filters::escapeHtmlText($package->Rozmery) /* line 31 */ ?></td>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($package->Nazev) /* line 32 */ ?></td>
                                    <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText($package->MnozstviBal) /* line 33 */ ?></td>
                                    <td class="text-right text-nowrap">
<?php
			if (($deliveryNote->StavDLExp == 0)) {
				?>                                        <a class="btn btn-sm btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Odebrat položku z balíku" data-nette-confirm-text="Opravu chcete vyjmout položku z balíku?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("removeFromPackage!", [$package->IDBal])) ?>"><i class="far fa-trash-alt"></i></a>
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
