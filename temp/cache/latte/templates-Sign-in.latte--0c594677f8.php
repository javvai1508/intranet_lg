<?php
// source: C:\Users\vinkler\Documents\Git\ligum_intranet\app\presenters/templates/Sign/in.latte

use Latte\Runtime as LR;

class Template0c594677f8 extends Latte\Runtime\Template
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
		if (isset($this->params['input'])) trigger_error('Variable $input overwritten in foreach on line 8');
		if (isset($this->params['error'])) trigger_error('Variable $error overwritten in foreach on line 28');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		$_translator = Kdyby\Translation\PrefixedTranslator::register($this, "sign");
?>
<div class="row content sign">
    <div class="col-sm-4 offset-sm-4 col-lg-2 offset-lg-5 text-center">
        <h3>Přihlášení</h3>
        <?php
		/* line 6 */
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $this->global->formsStack[] = $this->global->uiControl["signInForm"], []);
?>


<?php
		$iterations = 0;
		foreach ($form->controls as $input) {
			if ($input->name == "remember") {
?>
                    <div class="form-check mb-2">
                        <input<?php
				$_input = end($this->global->formsStack)["remember"];
				echo $_input->getControlPart()->attributes() ?>>
                        <label><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "sign.remember")) ?></label>
                    </div>
<?php
			}
			elseif ($input->name == "send") {
				?>                    <?php
				$_input = is_object($input) ? $input : end($this->global->formsStack)[$input];
				echo $_input->getControl() /* line 15 */ ?>

<?php
			}
			else {
?>
                    <div class="input-group mb-2">	
                        <?php
				$_input = is_object($input) ? $input : end($this->global->formsStack)[$input];
				echo $_input->getControl() /* line 18 */ ?>

<?php
				if ($input->name == "username") {
?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="far fa-user"></i></span>
                            </div>
<?php
				}
?>
                    </div>	
<?php
			}
			$iterations++;
		}
		if ($form->ownErrors) {
?>            <ul class=error>
<?php
			$iterations = 0;
			foreach ($form->ownErrors as $error) {
				?>                <li><?php echo LR\Filters::escapeHtmlText($error) /* line 28 */ ?></li>
<?php
				$iterations++;
			}
?>
            </ul>
<?php
		}
		?>        <?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack));
?>

        <div class="clearfix"></div>
    </div>
</div>

<?php
	}

}
