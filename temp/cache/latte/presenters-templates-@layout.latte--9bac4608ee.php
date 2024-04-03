<?php
// source: C:\inetpub\wwwroot\app\presenters/templates/@layout.latte

use Latte\Runtime as LR;

class Template9bac4608ee extends Latte\Runtime\Template
{
	public $blocks = [
		'scripts' => 'blockScripts',
	];

	public $blockTypes = [
		'scripts' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width" initial-scale="1">

        <title>LIGUM s.r.o. - intranet</title>
        <!-- bootstrap - latest compiled and minified CSS -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 9 */ ?>/css/bootstrap.min.css" crossorigin="anonymous">        
        <!-- link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous" -->        
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 11 */ ?>/css/bootstrap-datepicker3.css">
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 12 */ ?>/css/jquery-ui.min.css">
        <!-- font awesome css -->
        <link href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 14 */ ?>/css/fontawesome.min.css" rel="stylesheet">
        <link href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 15 */ ?>/css/fa-regular.min.css" rel="stylesheet">
        <!-- script defer src="<?php echo LR\Filters::escapeHtmlComment($basePath) /* line 16 */ ?>/js/fa-regular.min.js"></script -->
        <!-- link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous" -->

        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 19 */ ?>/css/style.css">
    </head>

    <body>
<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>        <div class="flash <?php echo LR\Filters::escapeHtmlAttr($flash->type) /* line 23 */ ?>" data-message-type="<?php
			echo LR\Filters::escapeHtmlAttr($flash->type) /* line 23 */ ?>" data-message="<?php echo LR\Filters::escapeHtmlAttr($flash->message) /* line 23 */ ?>"></div>
<?php
			$iterations++;
		}
?>

        <div class="container-fluid">
<?php
		$this->renderBlock('content', $this->params, 'html');
?>
        </div>

        <div class="row footer">
            <div class="col-sm-1 brand"><img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 30 */ ?>/images/logo.jpg"></div>
            <div class="col-sm-9"></div>
            <div class="col-sm-2">Intranet&nbsp;20240403</div>
        </div>
<?php
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('scripts', get_defined_vars());
?>
    </body>
</html>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 23');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockScripts($_args)
	{
		extract($_args);
		?>        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 35 */ ?>/js/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <!-- script src="<?php echo LR\Filters::escapeHtmlComment($basePath) /* line 36 */ ?>/js/jquery-3.4.1.min.js" crossorigin="anonymous"></script -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 37 */ ?>/js/popper.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 38 */ ?>/js/bootstrap.min.js" crossorigin="anonymous"></script>
        <!-- script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 40 */ ?>/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 41 */ ?>/js/bootstrap-datepicker.cs.min.js"></script>

        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 43 */ ?>/js/jquery-ui.min.js"></script>

        <!-- nette -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 46 */ ?>/js/nette.ajax.js"></script>
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 47 */ ?>/js/netteForms.min.js"></script>

        <!-- script src="/www/js/dropzone.js"></script>
        <script src="/www/js/dropzoneUploader.js"></script -->

        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 52 */ ?>/js/delete.ajax.js"></script>

        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 54 */ ?>/js/main.js"></script>
<?php
	}

}
