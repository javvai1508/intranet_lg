<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/DeliveryNote/deliveryNoteErrorPDF.latte

use Latte\Runtime as LR;

class Templateeb1b62d8a8 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<div class="pdf-header">
    <img class="logo" src="images/logo.jpg">
    <h1 class="text-right">Nedodané zboží DL č.: <?php echo LR\Filters::escapeHtmlText($deliveryNote->ParovaciZnak) /* line 3 */ ?></h1>
    <h2 class="text-right">Uzavřeno: <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->date, "now", 'j.n.Y H:i:s')) /* line 4 */ ?></h2>
</div>
<div class="clearer"></div>
<?php
		if (count($deliveryNoteItems) > 0) {
?>
<div class="content">
    <h3>Seznam položek s rozdíly</h3>
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Zak/pol</th>
                                <th scope="col">Název</th>
                                <th scope="col">Rozměry</th>
                                <th scope="col">Plán</th>
                                <th scope="col">Příprava</th>
                                <th scope="col">Rozdíl (nedodáno)</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
			$iterations = 0;
			foreach ($deliveryNoteItems as $deliveryNoteItem) {
?>
                                <tr>
                                    <td class="text-left"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Zakazka) /* line 24 */ ?></td>
                                    <td class="text-left"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Vykres) /* line 25 */ ?></td>
                                    <td class="text-left"><?php echo LR\Filters::escapeHtmlText($deliveryNoteItem->Rozmery) /* line 26 */ ?></td>
                                    <td class="text-right"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $deliveryNoteItem->Pozadavek, 2)) /* line 27 */ ?></td>
                                    <td class="text-right"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $deliveryNoteItem->Mnozstvi, 2)) /* line 28 */ ?></td>
                                    <td class="text-right"><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, ($deliveryNoteItem->Pozadavek - $deliveryNoteItem->Mnozstvi), 2)) /* line 29 */ ?></td>
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
?>
<div class="err-text">
    <p>Vygenerováno: <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->date, "now", 'j.n.Y H:i:s')) /* line 37 */ ?></p>
</div>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['deliveryNoteItem'])) trigger_error('Variable $deliveryNoteItem overwritten in foreach on line 22');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
