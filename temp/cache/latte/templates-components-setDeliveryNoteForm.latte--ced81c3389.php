<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\setDeliveryNoteForm.latte

use Latte\Runtime as LR;

class Templateced81c3389 extends Latte\Runtime\Template
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
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["ParovaciZnak"]->getLabel()) echo $_label ?></div>
        <div class="col-sm-9">    
            <div class="input input-group input-group-lg mr-2">
                <?php echo end($this->global->formsStack)["ParovaciZnak"]->getControl()->addAttributes(['autofocus' => true]) /* line 6 */ ?>

            </div>    
        </div>
    </div>    
    <div class="form-group row">
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["send"]->getControl() /* line 11 */ ?></div>
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
