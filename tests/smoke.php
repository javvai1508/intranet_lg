<?php
ob_start();
$container = require __DIR__ . '/bootstrap.php';
foreach (['Sign', 'Homepage', 'DeliveryNote', 'Material', 'CustomerMaterial', 'Error', 'Error4xx'] as $name) {
    $container->getByType(Nette\Application\IPresenterFactory::class)->createPresenter($name);
    echo "Presenter $name OK\n";
}
$translator = $container->getByType(Contributte\Translation\Translator::class);
assert($translator->translate('form.labels.signin') !== 'form.labels.signin');
$latte = $container->getByType(Nette\Bridges\ApplicationLatte\LatteFactory::class)->create();
$latte->addExtension(new Nette\Bridges\ApplicationLatte\UIExtension(null));
$latte->addExtension(new Nette\Bridges\FormsLatte\FormsExtension);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../app/presenters/templates')) as $file) {
    if ($file->getExtension() === 'latte') {
        $latte->compile($file->getPathname());
    }
}
echo "Templates OK\n";
$presenter = $container->getByType(Nette\Application\IPresenterFactory::class)->createPresenter('Sign');
$presenter->autoCanonicalize = false;
$response = $presenter->run(new Nette\Application\Request('Sign', 'GET', ['action' => 'in'], [], [], [Nette\Application\Request::FORWARD => true]));
ob_start();
$response->send($container->getByType(Nette\Http\IRequest::class), $container->getByType(Nette\Http\IResponse::class));
$html = ob_get_clean();
assert(str_contains($html, 'Osobní číslo'));
assert(str_contains($html, 'Přihlásit'));
echo "Sign page render OK\n";
$pdf = new Contributte\PdfResponse\PdfResponse('<h1>Test štítku</h1>');
$pdf->pageFormat = [50, 100];
$pdf->setPageMargins('2, 1, 1, 2, 1, 1');
$pdf->styles = 'h1 { font-size: 12pt; }';
$pdfPath = $pdf->save(sys_get_temp_dir(), 'intranet-lg-pdf-smoke');
assert(str_starts_with(file_get_contents($pdfPath), '%PDF-'));
unlink($pdfPath);
echo "PDF generation OK\n";
