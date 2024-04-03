<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\components\stdInventoryBarCodeForm.latte

use Latte\Runtime as LR;

class Template313712ad30 extends Latte\Runtime\Template
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
        <div class="col-sm-12">    
            <div class="input input-group mr-2">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup"><i class="far fa-barcode-scan"></i></span>
                </div>
                <?php echo end($this->global->formsStack)["VyrCislo"]->getControl()->addAttributes(['autofocus' => true]) /* line 8 */ ?>

            </div>    
        </div>
    </div>	
    <div class="form-group row">
        <div class="col-sm-12"><?php echo end($this->global->formsStack)["send"]->getControl() /* line 17 */ ?></div>
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
