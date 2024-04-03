<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/openDispatchSale.latte

use Latte\Runtime as LR;

class Templateaf00f25a8f extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'_formArea' => 'blockFormArea',
	];

	public $blockTypes = [
		'content' => 'html',
		'_formArea' => 'html',
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
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerMaterial.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="content no-padding-top">
<?php
		$this->renderBlock('_formArea', $this->params);
?>
</div>
<?php
	}


	function blockFormArea($_args)
	{
		extract($_args);
		$this->global->snippetDriver->enter("formArea", "area");
		/* line 5 */
		$this->createTemplate('../../templates/components/setDeliveryNoteForm.latte', ['form' => 'setDeliveryNoteForm'] + $this->params, "include")->renderToContentType('html');
		$this->global->snippetDriver->leave();
		
	}

}
