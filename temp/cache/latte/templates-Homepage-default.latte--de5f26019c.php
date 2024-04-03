<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Homepage/default.latte

use Latte\Runtime as LR;

class Templatede5f26019c extends Latte\Runtime\Template
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
		$this->createTemplate('headerHomepage.latte', $this->params, "include")->renderToContentType('html');
?>
<div class="row content">
<?php
		if ($user->isInRole('expedice')) {
?>        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:default")) ?>"> 
                <i class="far fa-truck fa-2x"></i><br><br><span class="sm">Expedice</span>
            </a>
        </div>
<?php
		}
		if ($user->isInRole('prijMatZak')) {
?>        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("CustomerMaterial:default")) ?>"> 
                <i class="far fa-inbox-in fa-2x"></i><br><br><span class="sm">Materiál zákazníka</span>
            </a>
        </div>
<?php
		}
		if ($user->isInRole('skladMat')) {
?>        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Material:default")) ?>"> 
                <i class="far fa-pallet-alt fa-2x"></i><br><br><span class="sm">Sklad materiálu</span>
            </a>
        </div>
<?php
		}
?>
</div>
<?php
	}

}
