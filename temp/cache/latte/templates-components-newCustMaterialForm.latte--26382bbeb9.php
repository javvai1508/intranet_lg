<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\newCustMaterialForm.latte

use Latte\Runtime as LR;

class Template26382bbeb9 extends Latte\Runtime\Template
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
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["Mnozstvi"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["Mnozstvi"]->getControl() /* line 6 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["Nazev"]->getLabel()) echo $_label ?></div><div class="col-sm-9"><?php
		echo end($this->global->formsStack)["Nazev"]->getControl() /* line 9 */ ?></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["VPrumerJadra"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["VPrumerJadra"]->getControl() /* line 12 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["VPrumerPogumu"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["VPrumerPogumu"]->getControl() /* line 15 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["VDelkaPogumu"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["VDelkaPogumu"]->getControl() /* line 18 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["VDelkaValce"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["VDelkaValce"]->getControl() /* line 21 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["VCepL"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["VCepL"]->getControl() /* line 24 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["VCepP"]->getLabel()) echo $_label ?></div><div class="col-sm-4"><?php
		echo end($this->global->formsStack)["VCepP"]->getControl() /* line 27 */ ?></div><div class="col-sm-5"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["TisknoutKusy"]->getLabelPart("")) echo $_label ?></div><div class="col-sm-1"><?php
		echo end($this->global->formsStack)["TisknoutKusy"]->getControlPart("") /* line 30 */ ?></div><div class="col-sm-8"></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><?php if ($_label = end($this->global->formsStack)["Poznamka"]->getLabel()) echo $_label ?></div><div class="col-sm-9"><?php
		echo end($this->global->formsStack)["Poznamka"]->getControl() /* line 33 */ ?></div>
    </div>
    <div class="form-group row">	
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["send"]->getControl() /* line 36 */ ?></div>
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
