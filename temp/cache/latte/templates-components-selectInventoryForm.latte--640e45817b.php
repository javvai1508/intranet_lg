<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\selectInventoryForm.latte

use Latte\Runtime as LR;

class Template640e45817b extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		$form = $_form = $this->global->formsStack[] = is_object($form) ? $form : $this->global->uiControl[$form];
		?><form<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		), false) ?>>
    <div class="form-group row">
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["IDInvHl"]->getControl() /* line 3 */ ?></div>
    </div>
    <div class="form-group row">	
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["send"]->getControl() /* line 6 */ ?></div>
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
