<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\setOrderForm.latte

use Latte\Runtime as LR;

class Templatec8791ed29c extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		$form = $_form = $this->global->formsStack[] = is_object($form) ? $form : $this->global->uiControl[$form];
		?><form<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		), false) ?>>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["CisloOrg"]->getLabel()) echo $_label ?></div><div class="col-sm-2" odkaz="<?php
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("changeCisloOrg!")) ?>"><?php echo end($this->global->formsStack)["CisloOrg"]->getControl() /* line 3 */ ?></div><div class="col-sm-7 autocomplete" odkaz="<?php
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("autocompleteFirma!")) ?>"><?php echo end($this->global->formsStack)["Firma"]->getControl() /* line 3 */ ?></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["ParovaciZnak"]->getLabel()) echo $_label ?></div><div class="col-sm-4 autocomplete" odkaz="<?php
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("autocompleteParovaciZnak!")) ?>" odkaz-blur="<?php
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("blurParovaciZnak!")) ?>"><?php echo end($this->global->formsStack)["ParovaciZnak"]->getControl() /* line 6 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">	
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["send"]->getControl() /* line 9 */ ?></div>
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
