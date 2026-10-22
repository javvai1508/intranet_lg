# Upgrade na PHP 8.2 a PDO

Aplikace používá nejnovější stabilní balíčky slučitelné s PHP 8.2 podle
přiloženého composer.lock: Nette Application 3.2.12, Database 3.2.9,
Latte 3.1.7. Composer má cílovou platformu nastavenou na PHP 8.2.0,
aby aktualizace na novějším vývojovém PHP nepřidala nekompatibilní balíčky.

## Nasazení na testovací IIS

1. Zálohujte aplikaci a její neveřejný `app/config/config.local.neon`.
2. Připravte PHP 8.2 a rozšíření `pdo_sqlsrv`, `mbstring`, `gd`, `dom`, `xml`, `intl`.
   Ovladač PDO_SQLSRV musí odpovídat PHP 8.2, architektuře a TS/NTS sestavení.
   Na serveru musí být nainstalovaný Microsoft ODBC Driver pro SQL Server.
3. Nahraďte konfiguraci `dibi:` konfigurací `database:` podle
   `app/config/config.local.neon.example`. Zachovejte své přihlašovací údaje.
   Pro Windows autentizaci vynechte `user` a `password`; přístup musí mít
   identita aplikačního poolu a použité prostředí musí Windows autentizaci podporovat.
   Příklad vyžaduje důvěryhodný certifikát SQL Serveru. Pro interní server
   se self-signed certifikátem lze po posouzení nastavit `TrustServerCertificate=yes`.
4. Ve složce aplikace odstraňte starý `vendor` a spusťte:

   ```console
   composer install --no-dev --prefer-dist --optimize-autoloader
   composer check-platform-reqs --no-dev
   ```

   Composer lze spustit na jiném Windows počítači se stejným PHP a rozšířeními
   a výsledný `vendor` přenést. Závislosti se již neukládají do Gitu.
5. Vymažte obsah `temp/cache`, zachovejte zapisovatelná oprávnění pro `temp` a `log`.
   Webový kořen směrujte na `www`. Debug režim je ve výchozím stavu vypnutý.
6. Zkontrolujte existenci a oprávnění výstupních adresářů PDF na `e:\fs\data_helios`.
   Jejich dosavadní názvy a tiskový postup zůstaly zachovány.
7. Otestujte přihlášení osobním číslem, oprávnění a odhlášení i vypršení relace.
   V testovací databázi ověřte příjem a výdej materiálu, expedici,
   přidání/odebrání výrobního čísla, balíky, uzavření dokladů, inventury
   a vytvoření a následný tisk PDF včetně rozměrů štítků.

## Změny

- Dibi je odstraněné; modely používají Nette Database Connection nad PDO_SQLSRV.
- Dotazy používají parametry `?`; skalární výsledek se načítá přes `fetchField()`.
- Transakce používají `beginTransaction()` a zachycují také databázové výjimky.
- INSERT a načtení `SCOPE_IDENTITY()` běží v jednom SQL batchi.
- Opravené tři nesoulady argumentů dotazů: dvě kontroly před uzavřením dokladu
  a výběr výrobního čísla při validaci množství.
- Kdyby Translation nahrazuje Contributte Translation, Joseki PDF nahrazuje
  Contributte PDF s aktuálním mPDF. Slovníky a veřejné chování zůstávají zachovány.
- Přizpůsobené rozhraní autentizace, formuláře, mapování presenterů a syntaxe Latte 3.
- Odstraněné nefunkční callbacky přihlášení odkazující na neexistující blockingModel.

## Automatické kontroly

```console
composer validate --strict
php -d zend.assertions=1 -d assert.exception=1 tests/smoke.php
php -d zend.assertions=1 -d assert.exception=1 tests/database.php
```

Smoke test sestaví DI kontejner, vytvoří všechny presentery, přeloží všechny
Latte šablony, vykreslí přihlášení a vytvoří PDF. Databázový test používá
SQLite a ověřuje PDO parametry, Unicode, fetchPairs a rollback.
GitHub Actions spouští kontroly na PHP 8.2. Testy nepřistupují do Heliosu.
SQLite nenahrazuje ověření SQL Server procedur, triggerů a tisku na cílovém serveru.
