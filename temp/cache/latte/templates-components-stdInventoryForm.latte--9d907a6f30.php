<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\stdInventoryForm.latte

use Latte\Runtime as LR;

class Template9d907a6f30 extends Latte\Runtime\Template
{
	public $blocks = [
		'_material' => 'blockMaterial',
	];

	public $blockTypes = [
		'_material' => 'html',
	];


	function main()
	{
		extract($this->params);
		/* line 1 */
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $this->global->formsStack[] = $this->global->uiControl["stdInventoryForm"], []);
?>

    <div class="form-group row">
        <div class="col-sm-12">
            <h4>VČ: <?php
		if ($newSN == 1) {
			?>Generovat nové<?php
		}
		else {
			echo LR\Filters::escapeHtmlText($sn) /* line 4 */;
		}
?></h4>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><label><?php if ($_label = end($this->global->formsStack)["IDZboSklad"]->getLabel()) echo $_label ?></label></div><div class="col-sm-9"><div id="<?php
		echo htmlSpecialChars($this->global->snippetDriver->getHtmlId('material')) ?>"><?php $this->renderBlock('_material', $this->params) ?></div></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><label><?php if ($_label = end($this->global->formsStack)["Mnozstvi"]->getLabel()) echo $_label ?></label></div><div class="col-sm-6"><?php
		echo end($this->global->formsStack)["Mnozstvi"]->getControl() /* line 11 */ ?></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["send"]->getControl() /* line 14 */ ?></div>
    </div>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack));
?>

<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockMaterial($_args)
	{
		extract($_args);
		$this->global->snippetDriver->enter("material", "static");
		echo end($this->global->formsStack)["IDZboSklad"]->getControl() /* line 8 */;
		$this->global->snippetDriver->leave();
		
	}

}
