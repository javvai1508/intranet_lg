<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\readBarCodeForm.latte

use Latte\Runtime as LR;

class Template60af05993e extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		$form = $_form = $this->global->formsStack[] = is_object($form) ? $form : $this->global->uiControl[$form];
		?><form class=""<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'class' => NULL,
		), false) ?>>
    <div class="input input-group input-group-lg mr-2">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="far fa-barcode-scan"></i></span>
        </div>
        <?php echo end($this->global->formsStack)["BarCode"]->getControl()->addAttributes(['autofocus' => true]) /* line 6 */ ?>

    </div>    
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), false);
?></form>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
