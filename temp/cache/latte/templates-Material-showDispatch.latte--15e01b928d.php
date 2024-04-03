<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/showDispatch.latte

use Latte\Runtime as LR;

class Template15e01b928d extends Latte\Runtime\Template
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
		if (isset($this->params['dispatchItem'])) trigger_error('Variable $dispatchItem overwritten in foreach on line 43');
		if (isset($this->params['requestItem'])) trigger_error('Variable $requestItem overwritten in foreach on line 70');
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
		if (isset($dispatch->ID)) {
?>
            <div class="row delivery-note-header">
                <div class="col-sm-12">
                <div class="float-right delivery-note-status"><span class="font-weight-bold"><span class="<?php
			if ($dispatch->MnozstviVC == $dispatch->MnozstviPozad) {
				?>text-success<?php
			}
			elseif ($dispatch->MnozstviVC > $sumaRequests) {
				?>text-warning<?php
			}
			else {
				?>text-danger<?php
			}
			?>"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $dispatch->MnozstviVC, 2)) /* line 7 */ ?></span> / <?php
			echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $sumaRequests, 2)) /* line 7 */ ?></span></div>
<?php
			if ($dispatchType == 'own') {
?>
                    <h1>Vlastní spotřeba</h1> 
<?php
			}
			elseif ($dispatchType == 'def') {
?>
                    <h1>Zmetky</h1> 
<?php
			}
			elseif ($dispatchType == 'undo') {
?>
                    <h1>Storno výdejky</h1> 
<?php
			}
			else {
				if (isset($dispatch->RadaPrikaz)) {
					?>                        <h1>Přík <?php echo LR\Filters::escapeHtmlText($dispatch->Prikaz) /* line 16 */ ?> (<?php
					echo LR\Filters::escapeHtmlText($dispatch->Alias) /* line 16 */ ?>)</h1> 
<?php
				}
				else {
					?>                        <h1>Přík <?php echo LR\Filters::escapeHtmlText($dispatch->SdruzPrik) /* line 18 */ ?></strong></h1>
<?php
				}
			}
?>
                </div>
            </div>
            <div class="row delivery-note-status">
                <div class="col-sm-12">
<?php
			if ($dispatch->StavDispExp !== 1) {
?>
                        <div class="set-dn-form">
<?php
				/* line 27 */
				$this->createTemplate('../../templates/components/readBarCodeForm.latte', ['form' => 'readBarCodeForm'] + $this->params, "include")->renderToContentType('html');
?>
                        </div>
<?php
			}
?>
                </div>
            </div>
        <div class="delivery-note-content">
            <div class="row delivery-note-items">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Výdejka</th>
                                <th scope="col">Připraveno</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
			$iterations = 0;
			foreach ($dispatchItems as $dispatchItem) {
				?>                                <tr class="clickable-row" data-href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("dispatchItemDetail", [$dispatchItem->DispatchItemID])) ?>">
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($dispatchItem->Nazev) /* line 45 */ ?></td>
                                    <td class="text-center font-weight-bold <?php
				if ($dispatchItem->MnozstviVC == $dispatchItem->Mnozstvi) {
					?>text-success<?php
				}
				elseif ($dispatchItem->MnozstviVC > $dispatchItem->Mnozstvi) {
					?>text-warning<?php
				}
				else {
					?>text-danger<?php
				}
				?>"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $dispatchItem->MnozstviVC, 2)) /* line 46 */ ?></td>
                                    <!-- td class="text-right text-nowrap">
                                        <a n:href="dispatchItemDetail $dispatchItem->DispatchItemID" class="btn btn-lg btn-primary mr-2"><i class="far fa-info"></i></a>
                                        <a n:href="removeDispatchItem! $dispatchItem->DispatchItemID" n:if="($dispatch->StavDispExp == 0)" class="btn btn-lg btn-danger mr-2 ajax" data-nette-confirm="modal" data-nette-confirm-title="Zrušení výdeje položky" data-nette-confirm-text="Opravu chcete zrušit výdej u vybrané položky?" data-nette-confirm-ok-class="btn-danger" data-nette-confirm-ok-text="Ano" data-nette-confirm-cancel-class="btn-secondary" data-nette-confirm-cancel-text="Ne"><i class="far fa-trash-alt"></i></a>
                                    </td -->
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
			if (isset($requestItems)) {
?>
        <div class="delivery-note-content">
            <div class="row delivery-note-items">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Materiál / délka [mm]</th>
                                <th scope="col">Požadavek</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
				$iterations = 0;
				foreach ($requestItems as $requestItem) {
?>
                                <tr>
                                    <td class="text-center"><?php echo LR\Filters::escapeHtmlText($requestItem->Nazev) /* line 72 */ ?><br><strong><?php
					echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $requestItem->KsZadane, 0)) /* line 72 */ ?> x <?php
					echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $requestItem->DelkaSleeve, 0)) /* line 72 */ ?></strong></td>
                                    <td class="text-center font-weight-bold"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $requestItem->MnozZadane, 2)) /* line 73 */ ?></td>
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
?>
                <div class="text-center">
<?php
			if ($dispatch->StavDispExp == 1) {
				?>                        <span class="text-danger font-weight-bold">Uzavřeno: <?php echo LR\Filters::escapeHtmlText($dispatch->DatUzavrenoExp) /* line 84 */ ?></span>
<?php
			}
			else {
				if (($dispatch->StavDispExp == 0)) {
					?>                    <a class="btn btn-success btn-lg m-1" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("closeDispatch!")) ?>"><i class="far fa-forklift"></i> Uzavřít výdej</a>
<?php
				}
			}
?>
                </div>
<?php
		}
?>
</div>
<?php
	}

}
