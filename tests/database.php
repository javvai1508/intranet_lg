<?php
$container = require __DIR__ . '/bootstrap.php';
$db = $container->getByType(Nette\Database\Connection::class);
$db->query('CREATE TABLE TabCisZam (ID INTEGER, Cislo INTEGER, Jmeno TEXT, Prijmeni TEXT)');
$db->query('CREATE TABLE TabCisZam_EXT (ID INTEGER, _intranet_exp INTEGER, _intranet_prijem_mat INTEGER, _intranet_prijem_matzak INTEGER, _intranet_vychozi TEXT)');
$db->query('INSERT INTO TabCisZam VALUES (?, ?, ?, ?)', 1, 123, "O'Connor", 'Žluťoučký');
$row = $db->query('SELECT * FROM TabCisZam WHERE Cislo = ?', 123)->fetch();
assert($row->Jmeno === "O'Connor");
assert($db->query('SELECT * FROM TabCisZam WHERE Cislo = ?', '123 OR 1=1')->fetch() === null);
$db->beginTransaction();
try {
    $db->query('INSERT INTO TabCisZam VALUES (?, ?, ?, ?)', 2, 456, 'Test', 'Test');
    $db->query('INSERT INTO MissingTable VALUES (?)', 1);
} catch (Throwable $e) {
    $db->rollBack();
}
assert($db->query('SELECT COUNT(*) FROM TabCisZam')->fetchField() === 1);
assert($db->query('SELECT ID, Jmeno FROM TabCisZam')->fetchPairs() === [1 => "O'Connor"]);
echo "PDO parameters, Unicode, fetchPairs and rollback OK\n";
