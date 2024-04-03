<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/firstOperationLabelPDF5x10.latte

use Latte\Runtime as LR;

class Template419d0879e7 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<div class="barcode">
    <barcode code="<?php echo LR\Filters::escapeHtmlAttr($dispatch->CarovyKod) /* line 2 */ ?>" type="C128A" class="barcode" size="0.7"></barcode> 
    <p><?php echo LR\Filters::escapeHtmlText($dispatch->CarovyKod) /* line 3 */ ?></p>
</div>
<p class="pdf-label-bottom-text line0"><?php echo LR\Filters::escapeHtmlText($dispatch->Prikaz) /* line 5 */ ?></p>
<p class="pdf-label-text line0"><?php echo LR\Filters::escapeHtmlText($dispatch->VyrCislo) /* line 6 */ ?></p>
<p class="pdf-label-text line1"><?php echo LR\Filters::escapeHtmlText($dispatch->Nazev) /* line 7 */ ?></p>
<p class="pdf-label-text line2"><?php echo LR\Filters::escapeHtmlText($dispatch->Odberatel) /* line 8 */ ?></p>
<p class="pdf-label-text line3"><?php echo LR\Filters::escapeHtmlText($dispatch->Dilec) /* line 9 */ ?></p>
<p class="pdf-label-bottom-text line4"><?php echo LR\Filters::escapeHtmlText(date("d.m.Y")) /* line 10 */ ?></p>
<p class="pdf-label-text line4"><?php echo LR\Filters::escapeHtmlText($dispatch->CisloEP) /* line 11 */ ?> / <?php
		echo LR\Filters::escapeHtmlText($dispatch->PoradiEP) /* line 11 */ ?></p><?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
