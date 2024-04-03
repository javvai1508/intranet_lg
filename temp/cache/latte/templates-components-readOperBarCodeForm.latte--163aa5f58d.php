<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\readOperBarCodeForm.latte

use Latte\Runtime as LR;

class Template163aa5f58d extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		$form = $_form = $this->global->formsStack[] = is_object($form) ? $form : $this->global->uiControl[$form];
		?><form class="form"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'class' => NULL,
		), false) ?>>
    <div class="form-group row">
            <div class="col-sm-3"><label>První operace</label></div>
            <div class="input input-group mr-2 col-sm-9">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup"><i class="far fa-barcode-scan"></i></span>
                </div>
                <?php echo end($this->global->formsStack)["BarCode"]->getControl()->addAttributes(['autofocus' => true]) /* line 8 */ ?>

            </div>    
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
