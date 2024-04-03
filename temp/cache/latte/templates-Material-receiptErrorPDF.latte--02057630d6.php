<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/Material/receiptErrorPDF.latte

use Latte\Runtime as LR;

class Template02057630d6 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<div class="pdf-header">
    <img class="logo" src="images/logo.jpg">
    <h1 class="text-right">Chyby v příjmu DL č.: <?php echo LR\Filters::escapeHtmlText($receipt->CisloDL) /* line 3 */ ?></h1>
    <h2 class="text-right">Příjemka č.: <?php echo LR\Filters::escapeHtmlText($receipt->ParovaciZnak) /* line 4 */ ?></h2>
</div>
<div class="clearer"></div>
<?php
		if (count($receiptItems) > 0) {
?>
<div class="content">
    <h3>Seznam položek s rozdíly</h3>
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Materiál</th>
                                <th scope="col">Objednávka</th>
                                <th scope="col">Dodací list</th>
                                <th scope="col">Přijato</th>
                                <th scope="col">Rozdíl (nedodáno)</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
			$iterations = 0;
			foreach ($receiptItems as $receiptItem) {
?>
                                <tr>
                                    <td class="text-left"><?php echo LR\Filters::escapeHtmlText($receiptItem->Nazev) /* line 23 */ ?></td>
                                    <td class="text-left"><?php echo LR\Filters::escapeHtmlText($receiptItem->Objednavka) /* line 24 */ ?></td>
                                    <td class="text-right"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->MnozstviDL, 2)) /* line 25 */ ?></td>
                                    <td class="text-right"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receiptItem->Mnozstvi, 2)) /* line 26 */ ?></td>
                                    <td class="text-right"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, ($receiptItem->MnozstviDL - $receiptItem->Mnozstvi), 2)) /* line 27 */ ?></td>
                                </tr>
<?php
				$iterations++;
			}
?>
                        </tbody>
                    </table>
</div>
<?php
		}
		if (isset($receipt->Text2)) {
?>
<div class="err-text">
    <h3>Seznam položek, které se z dodacího listu nenačetly</h3>
    <p>Pravděpodobně chybná vazba na řádek objednávky.</p>
    <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->breaklines, $receipt->Text2)) /* line 38 */ ?>

</div>
<?php
		}
?>
<div class="err-text">
    <p>Vygenerováno: <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->date, "now", 'j.n.Y H:i:s')) /* line 42 */ ?></p>
</div>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['receiptItem'])) trigger_error('Variable $receiptItem overwritten in foreach on line 21');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
