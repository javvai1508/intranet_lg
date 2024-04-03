<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\setQuantityForm.latte

use Latte\Runtime as LR;

class Template77890c519c extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		$form = $_form = $this->global->formsStack[] = is_object($form) ? $form : $this->global->uiControl[$form];
		?><form class="text-center"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'class' => NULL,
		), false) ?>>
    <div class="input input-group input-group-sm mr-2">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="far fa-sort-numeric-up"></i></span>
        </div>
        <?php echo end($this->global->formsStack)["quantity"]->getControl() /* line 6 */ ?>

        <div class="input-group-append">
            <?php echo end($this->global->formsStack)["send"]->getControl() /* line 8 */ ?>

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
