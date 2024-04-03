<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/newReceipt.latte

use Latte\Runtime as LR;

class Template88a91e1f3a extends Latte\Runtime\Template
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
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 2 */
		$this->createTemplate('headerMaterial.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="content">
<?php
		/* line 4 */
		$this->createTemplate('../components/setOrderForm.latte', ['form' => 'setOrderForm'] + $this->params, "include")->renderToContentType('html');
?>
</div>
<?php
	}

}
