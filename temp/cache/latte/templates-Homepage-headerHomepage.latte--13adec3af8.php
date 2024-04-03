<?php
// source: C:\inetpub\wwwroot\app\presenters\templates\Homepage\headerHomepage.latte

use Latte\Runtime as LR;

class Template13adec3af8 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<div class="row header">
<?php
		if ($user->isLoggedIn()) {
?>
                    <div class="logged">
                        <div class="btn-group">    
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" id="menuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                <i class="far fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="menuButton">
                                <a class="dropdown-item logged-user lg" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:")) ?>"><?php
			echo LR\Filters::escapeHtmlText($user->getIdentity()->getData()['fullname']) /* line 9 */ ?></a>
<?php
			if ($user->isInRole('expedice')) {
				?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeliveryNote:default")) ?>"><i class="far fa-truck"></i> Expedice</a>
<?php
			}
			if ($user->isInRole('prijMatZak')) {
				?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("CustomerMaterial:default")) ?>"><i class="far fa-inbox-in"></i> Materiál zákazníka</a>
<?php
			}
			if ($user->isInRole('skladMat')) {
				?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Material:default")) ?>"><i class="far fa-pallet-alt"></i> Sklad materiálu</a>
<?php
			}
			?>                                <a class="dropdown-item" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:out")) ?>"><i class="far fa-sign-out"></i> Odhlásit</a>
                            </div>
                        </div>
                    </div>
<?php
		}
		else {
?>
                    <div class="button-sign">
                        <a title="Přihlásit" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:in")) ?>">
                            <i class="far fa-sign-in"></i>
                        </a>
                    </div>
<?php
		}
?>
</div>

<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
