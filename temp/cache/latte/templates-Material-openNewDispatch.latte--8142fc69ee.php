<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/openNewDispatch.latte

use Latte\Runtime as LR;

class Template8142fc69ee extends Latte\Runtime\Template
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
		$this->createTemplate('../components/readOperBarCodeForm.latte', ['form' => 'readOperBarCodeForm'] + $this->params, "include")->renderToContentType('html');
?>
    <div class="row">
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("newDispatchOwn!")) ?>"> 
                <i class="far fa-warehouse fa-2x"></i><br><br><span class="sm">Vlastní spotřeba</span>
            </a>
        </div>
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("openDispatchSale")) ?>"> 
                <i class="far fa-truck fa-2x"></i><br><br><span class="sm">Prodej</span>
            </a>
        </div>
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("newDispatchDefect!")) ?>"> 
                <i class="far fa-unlink fa-2x"></i><br><br><span class="sm">Zmetek</span>
            </a>
        </div>
        <div class="select-app">
            <a class="" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("undoDispatch!")) ?>"> 
                <i class="far fa-undo fa-2x"></i><br><br><span class="sm">Storno výdejky</span>
            </a>
        </div>
    </div>
</div>
<?php
	}

}
