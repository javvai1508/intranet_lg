<?php

namespace App\Models;

use Nette;

class DeliveryNoteModel extends BaseModel {

    /**
     * kontrola vybraného DL, vrací ID
     * @param type $deliveryNoteNr
     * @return int
     */
    public function checkDeliveryNote($deliveryNoteNr) {

        $deliveryNote = $this->db->query("SELECT "
                        . "do.ID AS ID, "
                        . "do.ParovaciZnak AS ParovaciZnak, "
                        . "CONVERT(NVARCHAR(20), do.DatPovinnostiFa, 104) AS DatVystaveni, "
                        . "CONVERT(NVARCHAR(20), do.Splatnost, 104) AS DatDodavky "
                        . "FROM TabDokladyZbozi do "
                        . "WHERE do.ParovaciZnak = %s", $deliveryNoteNr)->fetch();

        // kontrola existence DL (pozor na prázdné pole)
        if ($deliveryNote) {
            $deliveryNoteId = $deliveryNote->ID;
        } else {
            $deliveryNoteId = false;
        }

        return $deliveryNoteId;
    }

    /**
     * vybraný DL
     * @param type $deliveryNoteId
     * @return type
     */
    public function getDeliveryNote($deliveryNoteId) {
        return $this->db->query("SELECT "
                        . "do.ID AS ID, "
                        . "do.ParovaciZnak AS ParovaciZnak, "
                        . "CONVERT(NVARCHAR(20), do.DatPovinnostiFa, 104) AS DatVystaveni, "
                        . "CONVERT(NVARCHAR(20), ISNULL(do.Splatnost, do.DatPorizeni), 104) AS DatDodavky, "
                        . "co.CisloOrg AS CisloOrg, "
                        . "coe._exp_doplnit_baleni AS DoplnitBaleni, "
                        . "mu.CisloOrg AS CisloMU, "
                        . "COALESCE(mu.CisloOrg, co.CisloOrg) AS CisloOdb,"
                        . "CONCAT(co.Firma, ', ', co.Misto) AS Organizace, "
                        . "CONCAT(mu.Firma, ', ', mu.Misto) AS MistoUrceni,"
                        . "COALESCE(CONCAT(mu.Firma, ', ', mu.Misto), CONCAT(co.Firma, ', ', co.Misto)) AS Odberatel,"
                        . "doe._exp_umisteni AS UmisteniKod, "
                        . "um.Nazev AS UmisteniNazev, "
                        . "LOWER(ume._exp_skupina_tisk) AS SkupinaTisk, "
                        . "CONVERT(NVARCHAR(20), doe._exp_dat_uzavreno, 104) AS DatUzavrenoExp, "
                        // . "0 AS StavDLExp, "
                        . "CASE WHEN do.StavPrevodu IN (1, 2, 3, 4, 5) THEN 1 WHEN EXISTS(SELECT IDDok FROM TabDokumVazba WHERE IdentVazby = 9 AND IDTab = do.ID) THEN 1 WHEN doe._exp_dat_uzavreno IS NOT NULL THEN 1 ELSE 0 END AS StavDLExp, "
                        . "sumy.PocetPozad AS PocetPozad, "
                        . "CONVERT(INT, sumy.MnozstviPozad) AS MnozstviPozad, "
                        . "sumy.PocetVC AS PocetVC, "
                        . "CONVERT(INT, ISNULL(sumy.MnozstviVC, 0)) AS MnozstviVC, "
                        . "ISNULL(baliky.PocetBaliku, 0) AS PocetBaliku, "
                        . "ISNULL(doe._exp_balenitx, '') AS BaleniTx "
                        . "FROM TabDokladyZbozi do "
                        . "LEFT JOIN TabDokladyZbozi_EXT doe ON do.ID = doe.ID "
                        . "LEFT JOIN TabCisOrg co ON do.CisloOrg = co.CisloOrg "
                        . "LEFT JOIN TabCisOrg_EXT coe ON coe.ID = co.ID "
                        . "LEFT JOIN TabCisOrg mu ON do.MistoUrceni = mu.CisloOrg "
                        . "LEFT JOIN TabUmisteni um ON um.Kod = doe._exp_umisteni "
                        . "LEFT JOIN TabUmisteni_EXT ume ON um.ID = ume.ID "
                        . "LEFT JOIN (SELECT "
                        . "po.IDDoklad, "
                        . "COUNT(po.ID) AS PocetPozad, "
                        . "SUM(poe._exp_mnozstvi_pozad_puvodni) AS MnozstviPozad, "
                        . "SUM(CASE WHEN poe._exp_mnozstvi_vc >= po.Mnozstvi THEN 1 ELSE 0 END) AS PocetVC, "
                        . "SUM(poe._exp_mnozstvi_vc) AS MnozstviVC, "
                        . "COUNT(poe._exp_cislo_baliku) AS PocetBaliku "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "WHERE SUBSTRING(po.SkupZbo, 1, 1) = '5' "
                        . "GROUP BY po.IDDoklad) sumy ON do.ID = sumy.IDDoklad "
                        . "LEFT JOIN (SELECT "
                        . "po.IDDoklad, "
                        . "COUNT(DISTINCT bal.CisloBaliku) AS PocetBaliku "
                        . "FROM agrafa_ligum_dl_polozka_balik bal "
                        . "LEFT JOIN TabVyrCP vc ON bal.IDVyrCP = vc.ID "
                        . "LEFT JOIN TabPohybyZbozi po ON vc.IDPolozkaDokladu = po.ID "
                        . "WHERE SUBSTRING(po.SkupZbo, 1, 1) = '5' "
                        . "GROUP BY po.IDDoklad) baliky ON do.ID = baliky.IDDoklad "
                        . "WHERE do.ID = %i", $deliveryNoteId)->fetch();
    }

    /**
     * výběr položek DL
     * @param type $deliveryNoteId
     * @return type
     */
    public function getDeliveryNoteItems($deliveryNoteId) {
        return $this->db->query("SELECT "
                        . "po.ID AS ID, "
                        . "CASE WHEN ISNULL(po.Popis4, '') = '' THEN po.Nazev1 ELSE po.Popis4 END AS Vykres, "
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Rozmery, "
                        . "CONCAT(CONVERT(NVARCHAR(10), kme._VPrumerJadra), '/', CONVERT(NVARCHAR(10), kme._VPrumerPogumu), '/', CONVERT(NVARCHAR(10), kme._VPracDelka), '/', CONVERT(NVARCHAR(10), kme._VDelkaValce)) AS VRozmery, "
                        . "CONVERT(INT, poe._exp_mnozstvi_pozad_puvodni) AS Mnozstvi, "
                        . "ISNULL(CONVERT(INT, poe._exp_mnozstvi_vc), 0) AS MnozstviVC,"
                        . "dox.RadaDokladu + CONVERT(NVARCHAR(6), dox.PoradoveCislo) + '/' +CONVERT(NVARCHAR(10), pox.Poradi) AS Zakazka "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "LEFT JOIN TabStavSkladu skl ON po.IDZboSklad = skl.ID "
                        . "LEFT JOIN TabKmenZbozi km ON skl.IDKmenZbozi = km.ID "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON kme.ID = km.ID "
                        . "LEFT JOIN TabPohybyZbozi pox ON po.IDOldPolozka = pox.ID "
                        . "LEFT JOIN TabDokladyZbozi dox ON pox.IDDoklad = dox.ID "
                        . "WHERE po.IDDoklad = %i AND SUBSTRING(po.SkupZbo, 1, 1) = '5' "
                        . "ORDER BY dox.RadaDokladu, dox.PoradoveCislo, pox.Poradi", $deliveryNoteId)->fetchAll();
    }

    /**
     * dohledání a dotažení položek DL s rozdíly proti požadavku - nedodané zboží
     * zohlednit zboží ze skladu!!!
     * @param type $deliveryNoteId
     */
    public function getDeliveryNoteDiffItems($deliveryNoteId) {
        return $this->db->query("SELECT "
                        . "po.ID AS DeliveryNoteItemID, "
                        . "CASE WHEN ISNULL(po.Popis4, '') = '' THEN po.Nazev1 ELSE po.Popis4 END AS Vykres, "
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Rozmery, "
                        . "po.MJ AS MJ, "
                        . "poe._exp_mnozstvi_vc AS Mnozstvi, "
                        . "poe._exp_mnozstvi_pozad_puvodni AS Pozadavek, "
                        . "dox.RadaDokladu + CONVERT(NVARCHAR(6), dox.PoradoveCislo) + '/' +CONVERT(NVARCHAR(10), pox.Poradi) AS Zakazka "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON poe.ID = po.ID "
                        . "LEFT JOIN TabStavSkladu skl ON skl.ID = po.IDZboSklad "
                        . "LEFT JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON kme.ID = km.ID "
                        . "LEFT JOIN TabPohybyZbozi pox ON po.IDOldPolozka = pox.ID "
                        . "LEFT JOIN TabDokladyZbozi dox ON pox.IDDoklad = dox.ID "
                        . "WHERE po.IDDoklad = %i AND ISNULL(poe._exp_mnozstvi_vc, 0) <> poe._exp_mnozstvi_pozad_puvodni", $deliveryNoteId)->fetchAll();
    }

    public function getResponsibleMail($deliveryNoteId) {
        return $this->db->query("SELECT "
                        . "ko.Spojeni AS Mail "
                        . "FROM TabDokladyZbozi do "
                        . "JOIN TabCisOrg co ON co.CisloOrg = do.CisloOrg "
                        . "JOIN TabCisZam zam ON co.OdpOs = zam.ID "
                        . "JOIN TabKontakty ko ON zam.ID = ko.IDCisZam "
                        . "WHERE do.ID = %i AND ko.Druh = 6 AND zam.ID IS NOT NULL", $deliveryNoteId)->fetch()->Mail;
    }

    /**
     * vrátí pole pro výběr umístění
     * @return type
     */
    public function getLocationArr() {
        return $this->db->query("SELECT Kod, Nazev FROM TabUmisteni WHERE IDSklad = '10000700'")->fetchPairs();
    }

    /**
     * uloží umístění do hlavičky dokladu
     * @param type $deliveryNoteId
     * @param type $locationKod
     */
    public function setLocation($values) {
        return $this->db->query("UPDATE TabDokladyZbozi_EXT SET _exp_umisteni = %s WHERE ID = %i", $values->UmisteniKod, $values->deliveryNoteId);
    }

    /**
     * dohledání položky podle čísla karty - skladová položka
     * @param type $deliveryNoteId
     * @param type $serialNumber
     * @return type
     */
    public function checkPartNumber($deliveryNoteId, $partNumber) {
        return $this->db->query("SELECT "
                        . "skl.ID AS IDZboSklad,"
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Rozmery, "
                        . "SUM(CONVERT(INT, skl.Mnozstvi)) AS Mnozstvi, "
                        . "SUM(CONVERT(INT, skl.Mnozstvi + skl.MnozstviKPrijmu - skl.MnozstviKVydeji)) AS MnozstviPoPrijmuAVydeji, "
                        . "SUM(poe._exp_mnozstvi_pozad_puvodni) AS MnozstviPozadavek, "
                        . "SUM(ISNULL(poe._exp_mnozstvi_vc, 0)) AS MnozstviPriprava, "
                        . "MAX(CONVERT(INT, kme._SVyrabetNaSklad)) AS JeSklad "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "JOIN TabStavSkladu skl ON po.IDZboSklad = skl.ID "
                        . "JOIN TabKmenZbozi km ON skl.IDKmenZbozi = km.ID "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "WHERE po.IDDoklad = %i AND (km.SkupZbo + km.RegCis) = %s "
                        . "GROUP BY skl.ID, CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END ", $deliveryNoteId, $partNumber)->fetch();
    }

    /**
     * kontrola zadaného výrobního čísla - kontrola na vztah mezi EP a VP
     * @param type $deliveryNoteId
     * @param type $serialNumber
     * @return type
     */
    public function checkSerialNumber($deliveryNoteId, $serialNumber) {
        return $this->db->query("SELECT "
                        . "vc.ID AS IDVyrCis, "
                        . "vc.IDStavSkladu AS IDSZboSklad, "
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Rozmery, "
                        . "vc.Nazev1 AS BarCode, "
                        . "pozdr.ID AS IDZdrojVC, "
                        . "podl.ID AS IDPohyb, "
                        . "podl.IDZboSklad AS IDZboSklad,"
                        . "CONVERT(INT, vc.Mnozstvi) AS Mnozstvi, "
                        . "CONVERT(INT, vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji, "
                        . "podle._exp_mnozstvi_pozad_puvodni AS MnozstviPozadavek, "
                        . "ISNULL(podle._exp_mnozstvi_vc, 0) AS MnozstviPriprava, "
                        . "podle._exp_mnozstvi_pozad_puvodni - ISNULL(podle._exp_mnozstvi_vc, 0) AS MnozstviZbyva, "
                        . "kme._SVyrabetNaSklad AS SVyrabetNaSklad, "
                        . "kme._SNekontrolovatVPEP AS _SNekontrolovatVPEP "
                        . "FROM TabVyrCS vc "
                        . "JOIN TabStavSkladu skl ON skl.ID = vc.IDStavSkladu "
                        . "JOIN TabKmenZbozi km ON skl.IDKmenZbozi = km.ID "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON kme.ID = km.ID "
                        . "LEFT JOIN TabVyrCisPrikaz vcpr ON vc.Nazev1 = vcpr.VyrCislo "
                        . "LEFT JOIN TabPrikaz pr ON pr.ID = vcpr.IDPrikaz "
                        . "LEFT JOIN TabPrikaz_EXT pre ON pr.ID = pre.ID "
                        . "LEFT JOIN TabPohybyZbozi pozdr ON pozdr.ID = ISNULL(pr.IDRezervace, pre._prikaz_idrezervace_oprava) "
                        . "LEFT JOIN TabPohybyZbozi podl ON pozdr.ID = podl.IDOldPolozka AND podl.IDDoklad = %i "
                        . "LEFT JOIN TabPohybyZbozi_EXT podle ON podle.ID = podl.ID "
                        . "WHERE vc.Nazev1 = %s", $deliveryNoteId, $serialNumber)->fetch();
    }

    /**
     * kontrola zadaného výrobního čísla - bez kontroly na vztah mezi EP a VP, tj. pouze jestli existuje na DL stejná karta
     * @param type $deliveryNoteId
     * @param type $serialNumber
     * @return type
     */
    public function checkSerialNumberStock($deliveryNoteId, $serialNumber) {
        return $this->db->query("SELECT "
                        . "vc.ID AS IDVyrCis, "
                        . "vc.IDStavSkladu AS IDSZboSklad, "
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Rozmery, "
                        . "vc.Nazev1 AS BarCode, "
                        . "NULL AS IDZdrojVC, "
                        . "pos.ID AS IDPohyb, " 
                        . "pos.IDZboSklad AS IDZboSklad, "
                        . "CONVERT(INT, vc.Mnozstvi) AS Mnozstvi, " 
                        . "CONVERT(INT, vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji, " 
                        . "pose._exp_mnozstvi_pozad_puvodni AS MnozstviPozadavek, " 
                        . "ISNULL(pose._exp_mnozstvi_vc, 0) AS MnozstviPriprava, "
                        . "pose._exp_mnozstvi_pozad_puvodni - ISNULL(pose._exp_mnozstvi_vc, 0) AS MnozstviZbyva "
                        . "FROM TabVyrCS vc "
                        . "JOIN TabStavSkladu skl ON skl.ID = vc.IDStavSkladu "
                        . "LEFT JOIN TabPohybyZbozi pos ON pos.IDZboSklad = skl.ID AND pos.IDDoklad = %i "
                        . "LEFT JOIN TabPohybyZbozi_EXT pose ON pose.ID = pos.ID "
                        . "LEFT JOIN TabPohybyZbozi pox ON pos.IDOldPolozka = pox.ID "
                        . "LEFT JOIN TabDokladyZbozi dox ON pox.IDDoklad = dox.ID "
                        . "LEFT JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "WHERE vc.Nazev1 = %s AND pose._exp_mnozstvi_pozad_puvodni - ISNULL(pose._exp_mnozstvi_vc, 0) > 0 AND ISNULL(kme._SNekontrolovatVPEP, 0) = 1 "
                        . "ORDER BY dox.RadaDokladu, dox.PoradoveCislo, pox.Poradi", $deliveryNoteId, $serialNumber)->fetch();
    }

    /**
     * kontrola zadaného výrobního čísla ze skladu - VČ je už zadané na DL (převedené z EP)
     * @param type $deliveryNoteId
     * @param type $serialNumber
     * @return type
     */
    public function checkSNFromStock($deliveryNoteId, $serialNumber) {
        return $this->db->query("SELECT "
                        . "po.ID AS IDPohyb, "
                        . "po.IDZboSklad AS IDZboSklad, "
                        . "vcs.ID AS IDVyrCis, "
                        . "CONVERT(INT, vcs.Mnozstvi) AS Mnozstvi, "
                        . "CONVERT(INT, vcs.Mnozstvi + vcs.MnozstviKPrijmu - vcs.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji, "
                        . "vcp.ID AS IDVyrCP, "
                        . "vcp.Mnozstvi AS MnozstviVCP, "
                        . "vcpe._exp_mnozstvi_vc_pozad AS PozadVCP "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabVyrCP vcp ON vcp.IDPolozkaDokladu = po.ID "
                        . "LEFT JOIN TabVyrCS vcs ON vcp.IDVyrCis = vcs.ID "
                        . "LEFT JOIN TabVyrCP_EXT vcpe ON vcp.ID = vcpe.ID "
                        . "WHERE po.IDDoklad = %i AND vcs.Nazev1 = %s", $deliveryNoteId, $serialNumber)->fetch();
    }

    /**
     * kontrola, jestli lze zaevidovat odváděcí operaci
     * @param type $serialNumber
     * @return int
     */
    public function checkFinalOperation($serialNumber) {
        /*
         * 1 = OK lze odvést, 2 = nedostatek živých kusů, 3 = přikaz není ve stavu Zadáno
         */
        return $this->db->query("SELECT "
                        . "(CONVERT(NUMERIC(19,6), CASE WHEN (vcpr.Mnozstvi - vcpr.Odvedeno - vcpr.Zmetky - CONVERT(numeric(19,6), (SELECT ISNULL(SUM(CP.Mnozstvi * PZ.PrepMnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabVyrCP CP ON (CP.IDPolozkaDokladu = PZ.ID) INNER JOIN TabVyrCS CS ON (CS.ID = CP.IDVyrCis AND CS.Nazev1 = vcpr.VyrCislo) WHERE PZ.TypVyrobnihoDokladu=0 AND PZ.IDPrikaz = vcpr.IDPrikaz AND PZ.DruhPohybuZbo=0 AND PZ.SkutecneDatReal IS NULL))) < 0 THEN 0 ELSE (vcpr.Mnozstvi - vcpr.Odvedeno - vcpr.Zmetky - CONVERT(numeric(19,6), (SELECT ISNULL(SUM(CP.Mnozstvi * PZ.PrepMnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabVyrCP CP ON (CP.IDPolozkaDokladu = PZ.ID) INNER JOIN TabVyrCS CS ON (CS.ID = CP.IDVyrCis AND CS.Nazev1 = vcpr.VyrCislo) WHERE PZ.TypVyrobnihoDokladu=0 AND PZ.IDPrikaz = vcpr.IDPrikaz AND PZ.DruhPohybuZbo=0 AND PZ.SkutecneDatReal IS NULL))) END, 0)) AS MnKOdvodu, "
                        . "CASE WHEN (CONVERT(NUMERIC(19,6), CASE WHEN (vcpr.Mnozstvi - vcpr.Odvedeno - vcpr.Zmetky - CONVERT(numeric(19,6), (SELECT ISNULL(SUM(CP.Mnozstvi * PZ.PrepMnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabVyrCP CP ON (CP.IDPolozkaDokladu = PZ.ID) INNER JOIN TabVyrCS CS ON (CS.ID = CP.IDVyrCis AND CS.Nazev1 = vcpr.VyrCislo) WHERE PZ.TypVyrobnihoDokladu=0 AND PZ.IDPrikaz = vcpr.IDPrikaz AND PZ.DruhPohybuZbo=0 AND PZ.SkutecneDatReal IS NULL))) < 0 THEN 0 ELSE (vcpr.Mnozstvi - vcpr.Odvedeno - vcpr.Zmetky - CONVERT(numeric(19,6), (SELECT ISNULL(SUM(CP.Mnozstvi * PZ.PrepMnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabVyrCP CP ON (CP.IDPolozkaDokladu = PZ.ID) INNER JOIN TabVyrCS CS ON (CS.ID = CP.IDVyrCis AND CS.Nazev1 = vcpr.VyrCislo) WHERE PZ.TypVyrobnihoDokladu=0 AND PZ.IDPrikaz = vcpr.IDPrikaz AND PZ.DruhPohybuZbo=0 AND PZ.SkutecneDatReal IS NULL))) END, 0)) <= 0 THEN 2 WHEN pr.StavPrikazu <> 30 THEN 3 ELSE 1 END res "
                        . "FROM TabVyrCisPrikaz vcpr "
                        . "JOIN TabPrikaz pr ON pr.ID = vcpr.IDPrikaz "
                        . "WHERE (CONVERT(NUMERIC(19,6), CASE WHEN (vcpr.Mnozstvi - vcpr.Odvedeno - vcpr.Zmetky - CONVERT(numeric(19,6), (SELECT ISNULL(SUM(CP.Mnozstvi * PZ.PrepMnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabVyrCP CP ON (CP.IDPolozkaDokladu = PZ.ID) INNER JOIN TabVyrCS CS ON (CS.ID = CP.IDVyrCis AND CS.Nazev1 = vcpr.VyrCislo) WHERE PZ.TypVyrobnihoDokladu=0 AND PZ.IDPrikaz = vcpr.IDPrikaz AND PZ.DruhPohybuZbo=0 AND PZ.SkutecneDatReal IS NULL))) < 0 THEN 0 ELSE (vcpr.Mnozstvi - vcpr.Odvedeno - vcpr.Zmetky - CONVERT(numeric(19,6), (SELECT ISNULL(SUM(CP.Mnozstvi * PZ.PrepMnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabVyrCP CP ON (CP.IDPolozkaDokladu = PZ.ID) INNER JOIN TabVyrCS CS ON (CS.ID = CP.IDVyrCis AND CS.Nazev1 = vcpr.VyrCislo) WHERE PZ.TypVyrobnihoDokladu=0 AND PZ.IDPrikaz = vcpr.IDPrikaz AND PZ.DruhPohybuZbo=0 AND PZ.SkutecneDatReal IS NULL))) END, 0)) > 0 AND  "
                        . "vcpr.VyrCislo = %s", $serialNumber)->fetch();
    }

    /**
     * zaevidování odváděcí operace
     * @param type $persNumber
     * @param type $serialNumber
     * @return int
     */
    public function finalizeOperation($persNumber, $serialNumber, $quantity) {
        $res = $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @Res NVARCHAR(200) "
                        . "EXEC hpx_agrafa_exp_odvedeni_vyrobku @OsC = %i, @VyrCislo = %s, @Mnozstvi = %f, @Res = @Res OUTPUT "
                        . "SELECT @Res", $persNumber, $serialNumber, $quantity)->fetchSingle();
        return $res;
    }

    /**
     * 
     * @param type $deliveryNoteId
     * @param type $serialNumber
     * @return boolean
     */
    public function checkItemOnOtherOrders($deliveryNoteId, $serialNumber) {
        $deliveryNote = $this->getDeliveryNote($deliveryNoteId);
        $this->db->query("SELECT "
                . "ep.ID AS IDDoklad, "
                . "ep.ParovaciZnak AS CisloEP, "
                . "vc.ID AS IDVyrCis, "
                . "vc.Nazev1 AS BarCode, "
                . "CONVERT(INT, vc.Mnozstvi) AS Mnozstvi, "
                . "CONVERT(INT, vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji, "
                . "vc.IDStavSkladu AS IDSZboSklad, "
                . "po.ID AS IDPohyb, "
                . "CONVERT(INT, poe._exp_mnozstvi_pozad_puvodni) AS MnozstviPozadavek "
                . "FROM TabVyrCS vc "
                . "JOIN TabPohybyZbozi po ON po.IDZboSklad = vc.IDStavSkladu "
                . "LEFT JOIN TabPohybyZbozi_EXT ON po.ID = poe.ID "
                . "JOIN TabDokladyZbozi ep ON ep.ID = po.IDDoklad "
                . "WHERE vc.Nazev1 = %s", $serialNumber)->fetch();

        /**
         * 
         SELECT
          vc.ID AS IDVyrCis,
          vc.Nazev1 AS BarCode,
          CONVERT(INT, vc.Mnozstvi) AS Mnozstvi,
          CONVERT(INT, vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji,
          vc.IDStavSkladu AS IDSZboSklad,
          ep.ID AS IDDoklad,
          ep.ParovaciZnak AS CisloEP,
          eppo.ID AS IDPohyb,
          CONVERT(INT, eppo.Mnozstvi) AS MnozstviEP
          FROM TabVyrCS vc
          JOIN TabPohybyZbozi eppo ON eppo.IDZboSklad = vc.IDStavSkladu
          JOIN TabDokladyZbozi ep ON ep.ID = eppo.IDDoklad AND ep.DruhPohybuZbo = 9
          WHERE ep.IDSklad = '10000700'
          ORDER BY vc.Nazev1
          -- WHERE vc.Nazev1 = '' AND
         */
        return true;
    }

    private function updateDeliveryNoteItemQuantity($itemId) {
        // aktualizace TabPohybyZbozi.Mnozstvi
        $this->db->query("UPDATE TabPohybyZbozi "
                . "SET Mnozstvi = ISNULL(poe._exp_mnozstvi_vc, 0) "
                . "FROM TabPohybyZbozi_EXT poe "
                . "WHERE TabPohybyZbozi.ID = poe.ID AND TabPohybyZbozi.ID = %i AND SUBSTRING(TabPohybyZbozi.SkupZbo, 1, 1) = '5'", $itemId);

        $deliveryNoteId = $this->db->query("SELECT IDDoklad FROM TabPohybyZbozi WHERE ID = %i", $itemId)->fetch()->IDDoklad;
        $this->db->query("EXEC dbo.hp_VypCenOZPolozek_IDDokladu @IDDoklad = %i, @AktualizaceSlev = 0", $deliveryNoteId);
        $this->db->query("EXEC dbo.hpx_agrafa_exp_aktualizuj_odebrane_mnozstvi_dl_ep @ID = %i", $deliveryNoteId);
    }

    /**
     * doplní výrobní číslo do dokladu
     * @param type $itemId
     * @param type $vyrCisloId
     * @param type $mnozstviPriprava
     * @return type
     */
    public function updateDeliveryNoteItem($deliveryNoteId, $pohybId, $vyrCisloId, $mnozstviPriprava, $packageOpen, $packageNr) {
        // transakce
        $this->db->begin();
        try {
            $vyrCislo = $this->db->query('SELECT Nazev FROM TabVyrCS WHERE ID = %i', $vyrCisloId)->fetch()->Nazev;
            $itemRows = $this->db->query("SELECT "
                            . "po.ID AS ItemId,"
                            . "CONVERT(INT, poe._exp_mnozstvi_pozad_puvodni) AS MnozstviPozadavek, "
                            . "CONVERT(INT, poe._exp_mnozstvi_vc) AS MnozstviPriprava "
                            . "FROM TabPohybyZbozi po "
                            . "LEFT JOIN TabPohybyZbozi_EXT poe ON poe.ID = po.ID "
                            . "LEFT JOIN TabVyrCP vcp ON po.ID = vcp.IDPolozkaDokladu "
                            . "WHERE po.ID = %i "
                            . "ORDER BY po.Poradi", $pohybId)->fetchAll();
            foreach ($itemRows as $itemRow) {
                $itemId = $itemRow->ItemId;
                if ($mnozstviPriprava > 0) {
                    $mnozstviUloz = $itemRow->MnozstviPozadavek - $itemRow->MnozstviPriprava;
                    if ($mnozstviUloz > $mnozstviPriprava) {
                        $mnozstviUloz = $mnozstviPriprava;
                    }
                    if ($mnozstviUloz > 0) {
                        $itemSNRow = $this->db->query("SELECT "
                                        . "ID AS ItemId, "
                                        . "Mnozstvi AS MnozstviSN "
                                        . "FROM TabVyrCP "
                                        . "WHERE IDVyrCis = %i AND IDPolozkaDokladu = %i", $vyrCisloId, $itemId)->fetch();

                        if ($itemSNRow) {
                            $this->db->query("UPDATE TabVyrCP "
                                    . "SET Mnozstvi = Mnozstvi + %f "
                                    . "WHERE ID = %i", $mnozstviUloz, $itemSNRow->ItemId);
                            $itemSNId = $itemSNRow->ItemId;
                        } else {
                            $this->db->query("INSERT INTO TabVyrCP (IDPolozkaDokladu, IDVyrCis, Nazev, Vznik, Mnozstvi) "
                                    . "VALUES (%i, %i, '', 0, %f) ", $itemId, $vyrCisloId, $mnozstviUloz);
                            $itemSNId = $this->db->query("SELECT SCOPE_IDENTITY()")->fetchSingle();
                        }

                        // balíky
                        if ($packageOpen) {
                            $this->db->query("INSERT INTO agrafa_ligum_dl_polozka_balik (IDVyrCP, CisloBaliku, Mnozstvi) "
                                    . "VALUES (%i, %i, %f)", $itemSNId, $packageNr, $mnozstviUloz);
                        } else {
                            for ($ii = 1; $ii <= $mnozstviUloz; $ii++) {
                                $this->db->query("INSERT INTO agrafa_ligum_dl_polozka_balik (IDVyrCP, CisloBaliku, Mnozstvi) "
                                        . "VALUES (%i, %i, %f)", $itemSNId, $packageNr, 1);
                                $packageNr = $this->getNextPackage($deliveryNoteId);
                            }
                        }
                        // upravit tak, aby se vždy přepočítal stav z výrobních čísel položky
                        $ext = $this->db->query("SELECT ID FROM TabPohybyZbozi_EXT WHERE ID = %i", $itemId)->fetch();
                        if ($ext) {
                            $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                                    . "SET _exp_mnozstvi_vc = s.Mnozstvi "
                                    . "FROM (SELECT IDPolozkaDokladu, SUM(Mnozstvi) AS Mnozstvi FROM TabVyrCP GROUP BY IDPolozkaDokladu) s "
                                    . "WHERE s.IDPolozkaDokladu = TabPohybyZbozi_EXT.ID AND TabPohybyZbozi_EXT.ID = %i", $itemId);
                            /*
                              $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                              . "SET _exp_mnozstvi_vc = ISNULL(_exp_mnozstvi_vc, 0) + %f "
                              . "WHERE ID = %i", $mnozstviPriprava, $itemId);
                             * 
                             */
                        } else {
                            $res = $this->db->query("INSERT INTO TabPohybyZbozi_EXT (ID, _exp_mnozstvi_vc) "
                                    . "SELECT "
                                    . "IDPolozkaDokladu, "
                                    . "SUM(Mnozstvi) "
                                    . "FROM TabVyrCP "
                                    . "WHERE IDPolozkaDokladu = %i "
                                    . "GROUP BY IDPolozkaDokladu", $itemId);
                            /*
                              $res = $this->db->query("INSERT INTO TabPohybyZbozi_EXT (ID, _exp_mnozstvi_vc)"
                              . "VALUES (%i, %f)", $itemId, $mnozstviPriprava);
                             * 
                             */
                        }
                        $mnozstviPriprava = $mnozstviPriprava - $mnozstviUloz;
                    }
                    $this->writeExpLog($deliveryNoteId, 2, $pohybId, $vyrCisloId, null, $vyrCislo, $mnozstviPriprava, $mnozstviUloz, 0, '');
                }
                // aktualizace TabPohybyZbozi.Mnozstvi
                $this->updateDeliveryNoteItemQuantity($itemId);
            }
            $this->db->commit();
            if ($res) {
                $res = $packageNr;
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * doplní výrobní číslo do dokladu - při expedici ze skladu
     * @param type $itemId
     * @param type $vyrCisloId
     * @param type $mnozstviPriprava
     * @return type
     */
    public function updateDeliveryNoteItemFromStock($deliveryNoteId, $pohybId, $vyrCisloId, $mnozstviPriprava, $packageOpen, $packageNr) {
        // transakce
        $this->db->begin();
        try {
            $itemRow = $this->db->query("SELECT "
                            . "po.ID AS ItemId,"
                            . "CONVERT(INT, poe._exp_mnozstvi_pozad_puvodni) AS MnozstviPozadavek, "
                            . "CONVERT(INT, poe._exp_mnozstvi_vc) AS MnozstviPriprava,"
                            . "vcp.ID AS IDVyrCP "
                            . "FROM TabPohybyZbozi po "
                            . "LEFT JOIN TabPohybyZbozi_EXT poe ON poe.ID = po.ID "
                            . "JOIN TabVyrCP vcp ON po.ID = vcp.IDPolozkaDokladu "
                            . "WHERE po.ID = %i AND vcp.IDVyrCis = %i "
                            . "ORDER BY po.Poradi", $pohybId, $vyrCisloId)->fetch();

            if ($itemRow) {
                \Tracy\Debugger::barDump($itemRow);
                $itemId = $itemRow["ItemId"];
                $this->db->query("UPDATE TabVyrCP "
                        . "SET Mnozstvi = Mnozstvi + %f "
                        . "WHERE ID = %i", $mnozstviPriprava, $itemRow["IDVyrCP"]);

                // balíky
                if ($packageOpen) {
                    $this->db->query("INSERT INTO agrafa_ligum_dl_polozka_balik (IDVyrCP, CisloBaliku, Mnozstvi) "
                            . "VALUES (%i, %i, %f)", $itemRow["IDVyrCP"], $packageNr, $mnozstviPriprava);
                } else {
                    for ($ii = 1; $ii <= $mnozstviPriprava; $ii++) {
                        $this->db->query("INSERT INTO agrafa_ligum_dl_polozka_balik (IDVyrCP, CisloBaliku, Mnozstvi) "
                                . "VALUES (%i, %i, %f)", $itemRow["IDVyrCP"], $packageNr, $mnozstviPriprava);
                        $packageNr = $this->getNextPackage($deliveryNoteId);
                    }
                }
                // upravit tak, aby se vždy přepočítal stav z výrobních čísel položky
                $ext = $this->db->query("SELECT ID FROM TabPohybyZbozi_EXT WHERE ID = %i", $itemId)->fetch();
                if ($ext) {
                    $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                            . "SET _exp_mnozstvi_vc = s.Mnozstvi "
                            . "FROM (SELECT IDPolozkaDokladu, SUM(Mnozstvi) AS Mnozstvi FROM TabVyrCP GROUP BY IDPolozkaDokladu) s "
                            . "WHERE s.IDPolozkaDokladu = TabPohybyZbozi_EXT.ID AND TabPohybyZbozi_EXT.ID = %i", $itemId);
                    /*
                      $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                      . "SET _exp_mnozstvi_vc = ISNULL(_exp_mnozstvi_vc, 0) + %f "
                      . "WHERE ID = %i", $mnozstviPriprava, $itemId);
                     * 
                     */
                } else {
                    $res = $this->db->query("INSERT INTO TabPohybyZbozi_EXT (ID, _exp_mnozstvi_vc) "
                            . "SELECT "
                            . "IDPolozkaDokladu, "
                            . "SUM(Mnozstvi) "
                            . "FROM TabVyrCP "
                            . "WHERE IDPolozkaDokladu = %i "
                            . "GROUP BY IDPolozkaDokladu", $itemId);
                    /*
                      $res = $this->db->query("INSERT INTO TabPohybyZbozi_EXT (ID, _exp_mnozstvi_vc)"
                      . "VALUES (%i, %f)", $itemId, $mnozstviPriprava);
                     * 
                     */
                }
                // aktualizace TabPohybyZbozi.Mnozstvi
                $this->updateDeliveryNoteItemQuantity($itemId);
            }
            $this->db->commit();
            if ($res) {
                $res = $packageNr;
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * doplní postupně podle zadaného množství ke všem shodným položkám do naplnění - nekontroluje se vztah EP-VP
     * @param type $itemId
     * @param type $vyrCisloId
     * @param type $mnozstviPriprava
     * @return type
     */
    public function processDeliveryNoteNoCheckRelation($deliveryNoteId, $vyrCisId, $mnozstviPriprava, $packageOpen, $packageNr) {
        // transakce
        //$res = NULL;
        $resmsg = "";
        $this->db->begin();
        try {
            $this->db->query("EXEC dbo.hpx_agrafa_exp_sleeve_dopln_noepvp @IDDoklad = %i, @IDVyrCis = %i, @MnozPridat = %f", $deliveryNoteId, $vyrCisId, $mnozstviPriprava);

            $this->db->commit();
            if (isset($res)) {
                $resArr["res"] = $res;
                $resArr["packageNr"] = $packageNr;
                $resArr["mnozstviPriprava"] = $mnozstviPriprava;
                $resArr["msg"] = $resmsg;
            } else {
                $resArr["res"] = 1;
            }
            return $resArr;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * doplní výrobní číslo do dokladu - při expedici ze skladu
     * @param type $itemId
     * @param type $vyrCisloId
     * @param type $mnozstviPriprava
     * @return type
     */
    public function processDeliveryNoteFromStock($deliveryNoteId, $zboSkladId, $mnozstviPriprava, $packageOpen, $packageNr) {
        // transakce
        //$res = NULL;
        $resmsg = "";
        $this->db->begin();
        try {
            $this->db->query("EXEC dbo.hpx_agrafa_exp_sleeve_pridej_skl_pol @IDDoklad = %i, @IDZboSklad = %i, @MnozPridat = %f", $deliveryNoteId, $zboSkladId, $mnozstviPriprava);

            $this->db->commit();
            if (isset($res)) {
                $resArr["res"] = $res;
                $resArr["packageNr"] = $packageNr;
                $resArr["mnozstviPriprava"] = $mnozstviPriprava;
                $resArr["msg"] = $resmsg;
            } else {
                $resArr["res"] = 1;
            }
            return $resArr;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * 
     * @param type $itemId
     */
    public function removeDeliveryNoteItem($itemId) {
        // transakce
        $this->db->begin();
        try {
            $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                    . "SET _exp_mnozstvi_vc = 0 "
                    . "WHERE ID = %i", $itemId);
            if ($res) {
                $res = $this->db->query("DELETE FROM agrafa_ligum_dl_polozka_balik "
                        . "WHERE id IN (SELECT bal.id FROM agrafa_ligum_dl_polozka_balik bal JOIN TabVyrCP vc ON vc.ID = bal.IDVyrCP WHERE vc.IDPolozkaDokladu = %i)", $itemId);
                // zkontrolovat a opravit mazání balíků vvx-220407 if ($res) {
                    $resDel = $this->db->query("DELETE FROM TabVyrCP WHERE IDPolozkaDokladu = %i AND NOT(EXISTS(SELECT ID FROM TabVyrCP_EXT WHERE TabVyrCP_EXT.ID = TabVyrCP.ID AND ISNULL(TabVyrCP_EXT._exp_mnozstvi_vc_pozad, 0) > 0))", $itemId);
                    $resUpd = $this->db->query("UPDATE TabVyrCP SET Mnozstvi = 0 WHERE IDPolozkaDokladu = %i AND EXISTS(SELECT ID FROM TabVyrCP_EXT WHERE TabVyrCP_EXT.ID = TabVyrCP.ID AND ISNULL(TabVyrCP_EXT._exp_mnozstvi_vc_pozad, 0) > 0)", $itemId);
                        if ($resDel || $resUpd) {
                            $res = true;
                        } else {
                            $res = false;
                        }
                    // aktualizace TabPohybyZbozi.Mnozstvi
                    $this->updateDeliveryNoteItemQuantity($itemId);
                //}
            }
            if ($res) {
                $this->db->commit();
            } else {
                $this->db->rollback();
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * výběr položky DL
     * @param type $itemId
     * @return type
     */
    public function getDeliveryNoteItem($itemId) {
        return $this->db->query("SELECT "
                        . "po.ID AS ID, "
                        . "CASE WHEN ISNULL(po.Popis4, '') = '' THEN po.Nazev1 ELSE po.POpis4 END AS Vykres, "
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Rozmery, "
                        . "CONCAT(CONVERT(NVARCHAR(10), kme._VPrumerJadra), '/', CONVERT(NVARCHAR(10), kme._VPrumerPogumu), '/', CONVERT(NVARCHAR(10), kme._VPracDelka), '/', CONVERT(NVARCHAR(10), kme._VDelkaValce)) AS VRozmery, "
                        . "CONVERT(INT, poe._exp_mnozstvi_pozad_puvodni) AS Mnozstvi, "
                        . "ISNULL(CONVERT(INT, poe._exp_mnozstvi_vc), 0) AS MnozstviVC,"
                        . "dox.RadaDokladu + CONVERT(NVARCHAR(6), dox.PoradoveCislo) + '/' +CONVERT(NVARCHAR(10), pox.Poradi) AS Zakazka "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "LEFT JOIN TabStavSkladu skl ON po.IDZboSklad = skl.ID "
                        . "LEFT JOIN TabKmenZbozi km ON skl.IDKmenZbozi = km.ID "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON kme.ID = km.ID "
                        . "LEFT JOIN TabPohybyZbozi pox ON po.IDOldPolozka = pox.ID "
                        . "LEFT JOIN TabDokladyZbozi dox ON pox.IDDoklad = dox.ID "
                        . "WHERE po.ID = %i", $itemId)->fetch();
    }

    /**
     * výběr výrobních čísel k položce DL
     * @param type $itemId
     * @return type
     */
    public function getDeliveryNoteItemSN($itemId) {
        return $this->db->query("SELECT "
                        . "cp.ID AS IDVyrCP, "
                        . "vc.ID AS IDVyrCS, "
                        . "vc.Nazev1 AS Nazev, "
                        . "CONVERT(INT, ISNULL(vc.Mnozstvi, 0)) AS Mnozstvi, "
                        . "CONVERT(INT, ISNULL(vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji, 0)) AS MnozstviPoPrijmuAVydeji, "
                        . "vc.IDStavSkladu AS IDSZboSklad, "
                        . "CONVERT(INT, ISNULL(cp.Mnozstvi, 0)) AS MnozstviCP "
                        . "FROM TabVyrCP cp "
                        . "LEFT JOIN TabVyrCS vc ON cp.IDVyrCis = vc.ID "
                        . "WHERE cp.IDPolozkaDokladu = %i", $itemId)->fetchAll();
    }

    /**
     * 
     * @param type $itemSNId
     */
    public function removeDeliveryNoteItemSN($itemSNId) {
        // transakce 
        $this->db->begin();
        try {
            $res = true;
            $itemSNRow = $this->db->query("SELECT "
                            . "IDPolozkaDokladu AS ItemId, "
                            . "Mnozstvi AS MnozstviSN "
                            . "FROM TabVyrCP "
                            . "WHERE ID = %i", $itemSNId)->fetch();

            if ($itemSNRow) {
                $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                        . "SET _exp_mnozstvi_vc = _exp_mnozstvi_vc - %f "
                        . "WHERE ID = %i", $itemSNRow->MnozstviSN, $itemSNRow->ItemId);

                if ($res) {
                    $res = $this->db->query("DELETE FROM agrafa_ligum_dl_polozka_balik WHERE IDVyrCP = %i", $itemSNId);
                    // zkontrolovat a opravit mazání balíků vvx-220407 if ($res) {
                        $resDel = $this->db->query("DELETE FROM TabVyrCP WHERE ID = %i AND NOT(EXISTS(SELECT ID FROM TabVyrCP_EXT WHERE TabVyrCP_EXT.ID = TabVyrCP.ID AND ISNULL(TabVyrCP_EXT._exp_mnozstvi_vc_pozad, 0) > 0))", $itemSNId);
                        $resUpd = $this->db->query("UPDATE TabVyrCP SET Mnozstvi = 0 WHERE ID = %i AND EXISTS(SELECT ID FROM TabVyrCP_EXT WHERE TabVyrCP_EXT.ID = TabVyrCP.ID AND ISNULL(TabVyrCP_EXT._exp_mnozstvi_vc_pozad, 0) > 0)", $itemSNId);
                        // aktualizace TabPohybyZbozi.Mnozstvi
                        if ($resDel || $resUpd) {
                            $res = true;
                        } else {
                            $res = false;
                        }
                        $this->updateDeliveryNoteItemQuantity($itemSNRow->ItemId);
                    //}
                }
            } else {
                $res = false;
            }
            if ($res) {
                $this->db->commit();
            } else {
                $this->db->rollback();
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * 
     * @param type $values
     * @return type
     */
    public function updateQuantitySN($values) {
        // kontrola množství na sklad
        // transakce
        $this->db->begin();
        try {
            $itemSNRow = $this->db->query("SELECT "
                            . "cp.IDPolozkaDokladu AS ItemId, "
                            . "CONVERT(INT, ISNULL(cp.Mnozstvi, 0)) AS MnozstviSN, "
                            . "CONVERT(INT, ISNULL(cs.Mnozstvi + cs.MnozstviKPrijmu - cs.MnozstviKVydeji, 0)) AS MnozstviSkladem, "
                            . "CASE WHEN (ISNULL(poe._exp_mnozstvi_vc - cp.Mnozstvi, 0) + %f) > ISNULL(poe._exp_mnozstvi_pozad_puvodni, 0) THEN 0 ELSE 1 END AS ErrPozad, "
                            . "CASE WHEN ISNULL(cs.Mnozstvi + cs.MnozstviKPrijmu - cs.MnozstviKVydeji, 0) + ISNULL(cp.Mnozstvi, 0) >= %f THEN 1 ELSE 0 END AS ErrSklad "
                            . "FROM TabVyrCP cp "
                            . "LEFT JOIN TabVyrCS cs ON cp.IDVyrCis = cs.ID "
                            . "LEFT JOIN TabPohybyZbozi po ON po.ID = cp.IDPolozkaDokladu "
                            . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                            . "WHERE cp.ID = %i", $values->quantity, $values->quantity, $values->itemSNId)->fetch();

            \Tracy\Debugger::barDump($itemSNRow);

            if ($itemSNRow) {
                if ($itemSNRow->ErrSklad != 1) {
                    $res = 2;
                } elseif ($itemSNRow->ErrPozad != 1) {
                    $res = 3;
                } else {
                    $this->db->query("UPDATE TabVyrCP "
                            . "SET Mnozstvi = %f "
                            . "WHERE ID = %i", $values->quantity, $values->itemSNId);

                    $mnozVC = $this->db->query("SELECT Mnozstvi FROM TabVyrCP WHERE ID = %i", $values->itemSNId)->fetch()->Mnozstvi;

                    if ($mnozVC == 0) {
                        $this->db->query("DELETE FROM TabVyrCP WHERE ID = %i", $values->itemSNId);
                    }

                    $sqRes = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                            . "SET _exp_mnozstvi_vc = _exp_mnozstvi_vc - %f + %f "
                            . "WHERE ID = %i", $itemSNRow->MnozstviSN, $values->quantity, $itemSNRow->ItemId);
                    // aktualizace TabPohybyZbozi.Mnozstvi
                    $this->updateDeliveryNoteItemQuantity($itemSNRow->ItemId);
                    if ($sqRes) {
                        $res = 0;
                    } else {
                        $res = 9;
                    }
                }
            } else {
                $res = 1;
            }
            if ($res == 0) {
                $this->db->commit();
            } else {
                $this->db->rollback();
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * 
     * @param type $itemSNId
     * @param type $quantity
     * @return boolean
     */
    public function validateQuantity($itemSNId, $quantity) {
        // transakce
        $itemSNRow = $this->db->query("SELECT "
                        . "cp.IDPolozkaDokladu AS ItemId, "
                        . "CONVERT(INT, ISNULL(cp.Mnozstvi, 0)) AS MnozstviSN, "
                        . "CONVERT(INT, ISNULL(cs.Mnozstvi + cs.MnozstviKPrijmu - cs.MnozstviKVydeji, 0)) AS MnozstviSkladem, "
                        . "CONVERT(INT, ISNULL(poe._exp_mnozstvi_pozad_puvodni, 0)) AS MnozstviPozad "
                        . "FROM TabVyrCP cp "
                        . "LEFT JOIN TabPohybyZbozi po ON po.ID = cp.IDPolozkaDokladu "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON poe.ID = po.ID "
                        . "LEFT JOIN TabVyrCS cs ON cp.IDVyrCis = cs.ID "
                        . "WHERE cp.ID = %i", $quantity, $itemSNId)->fetch();


        if ($itemSNRow) {
            if ($itemSNRow->MnozstviSN > $itemSNRow->MnozstviSkladem) {
                return array('res' => false, 'type' => 'danger', 'message' => 'Nedostatek výrobního čísla na skladu. Nelze vydat!');
            } elseif ($itemSNRow->MnozstviSN > $itemSNRow->MnozstviPozad) {
                return array('res' => false, 'type' => 'warning', 'message' => 'Překročeno požadované množství.');
            } else {
                return array('res' => true, 'type' => 'success', 'message' => '');
            }
        } else {
            return array('res' => false, 'type' => 'danger', 'message' => 'Neexistující výrobní číslo!');
        }
    }

    /**
     * výběr balíků k položce DL
     * @param type $deliveryNoteId
     * @return type
     */
    public function getDeliveryNotePackages($deliveryNoteId) {
        return $this->db->query("SELECT "
                        . "bal.id AS IDBal, "
                        . "bal.IDVyrCP AS IDVyrCP, "
                        . "ksbal.ks AS KsVBaliku, "
                        . "bal.CisloBaliku AS CisloBal, "
                        . "CONVERT(INT, ISNULL(bal.Mnozstvi, 0)) AS MnozstviBal, "
                        . "vc.Nazev1 AS Nazev, "
                        . "CONVERT(INT, ISNULL(vc.Mnozstvi, 0)) AS Mnozstvi, "
                        . "CONVERT(INT, ISNULL(vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji, 0)) AS MnozstviPoPrijmuAVydeji, "
                        . "vc.IDStavSkladu AS IDSZboSklad, "
                        . "CONVERT(INT, ISNULL(vcp.Mnozstvi, 0)) AS MnozstviCP, "
                        . "po.ID AS ID, "
                        . "po.Nazev1 AS Vykres, "
                        . "CONCAT(CONVERT(NVARCHAR(10), kme._VPrumerJadra), '/', CONVERT(NVARCHAR(10), kme._VPrumerPogumu), '/', CONVERT(NVARCHAR(10), kme._VPracDelka), '/', CONVERT(NVARCHAR(10), kme._VDelkaValce)) AS Rozmery, "
                        . "CONVERT(INT, poe._exp_mnozstvi_pozad_puvodni) AS Mnozstvi, "
                        . "ISNULL(CONVERT(INT, poe._exp_mnozstvi_vc), 0) AS MnozstviVC, "
                        . "dox.RadaDokladu + CONVERT(NVARCHAR(6), dox.PoradoveCislo) + '/' +CONVERT(NVARCHAR(10), pox.Poradi) AS Zakazka "
                        . "FROM agrafa_ligum_dl_polozka_balik bal "
                        . "LEFT JOIN TabVyrCP vcp ON vcp.ID = bal.IDVyrCP "
                        . "LEFT JOIN TabVyrCS vc ON vc.ID = vcp.IDVyrCis "
                        . "LEFT JOIN TabPohybyZbozi po ON po.ID = vcp.IDPolozkaDokladu "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "LEFT JOIN TabStavSkladu skl ON skl.ID = po.IDZboSklad "
                        . "LEFT JOIN TabKmenZbozi km ON skl.IDKmenZbozi = km.ID "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON kme.ID = km.ID "
                        . "LEFT JOIN TabPohybyZbozi pox ON po.IDOldPolozka = pox.ID "
                        . "LEFT JOIN TabDokladyZbozi dox ON pox.IDDoklad = dox.ID "
                        . "LEFT JOIN (SELECT IDVyrCP AS IDVyrCP, CisloBaliku AS CisloBal, COUNT(id) AS ks FROM agrafa_ligum_dl_polozka_balik GROUP BY IDVyrCP, CisloBaliku) ksbal ON bal.IDVyrCP = ksbal.IDVyrCP AND bal.CisloBaliku = ksbal.CisloBal "
                        . "WHERE po.IDDoklad = %i "
                        . "ORDER BY bal.CisloBaliku, vc.Nazev1 ", $deliveryNoteId)->fetchAll();
    }

    /**
     * 
     * @param type $packageId
     */
    public function removeFromPackage($packageId) {
        // transakce 
        $this->db->begin();
        try {
            $res = true;
            $packageRow = $this->db->query("SELECT "
                            . "bal.IDVyrCP AS IDVyrCP, "
                            . "bal.CisloBaliku AS CisloBaliku, "
                            . "bal.Mnozstvi AS Mnozstvi,"
                            . "cp.IDPolozkaDokladu AS IDPohybu "
                            . "FROM agrafa_ligum_dl_polozka_balik bal "
                            . "JOIN TabVyrCP cp ON cp.ID = bal.IDVyrCP "
                            . "WHERE bal.id = %i", $packageId)->fetch();

            if ($packageRow) {
                $res = $this->db->query("UPDATE TabVyrCP "
                        . "SET Mnozstvi = Mnozstvi - %f "
                        . "WHERE ID = %i", $packageRow->Mnozstvi, $packageRow->IDVyrCP);
                if ($res) {
                    $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                            . "SET _exp_mnozstvi_vc = _exp_mnozstvi_vc - %f "
                            . "WHERE ID = %i", $packageRow->Mnozstvi, $packageRow->IDPohybu);
                    // aktualizace TabPohybyZbozi.Mnozstvi
                    $this->updateDeliveryNoteItemQuantity($packageRow->IDPohybu);
                    if ($res) {
                        $res = $this->db->query("DELETE FROM agrafa_ligum_dl_polozka_balik WHERE id = %i", $packageId);
                    }
                }
            } else {
                $res = false;
            }

            if ($res) {
                $this->db->commit();
            } else {
                $this->db->rollback();
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * uzavření dodacího listu
     * @param type $deliveryNoteId
     * @return boolean
     */
    public function checkDeliveryNoteBeforeClosing($deliveryNoteId) {
        // kontrola na splnění požadavků
        $res = 0;
        $chyby = $this->db->query("SELECT SUM(CASE WHEN ISNULL(poe._exp_mnozstvi_vc, 0) <> poe._exp_mnozstvi_pozad_puvodni THEN 1 ELSE 0 END) AS chyby "
                    . "FROM TabPohybyZbozi po "
                    . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID " 
                    , "WHERE po.IDDoklad = %i", $deliveryNoteId)->fetch()->chyby;
        if ($chyby > 0) {
            $res = 1;
        }
        return $res;

    }

    /**
     * odeslání mailu po uzavření DL na NH
     */
    public function sendMailAfterDeliveryNoteClose($deliveryNoteId, $jeAtt) {
        $this->db->query("EXEC dbo.hpx_agrafa_exp_uzavdl_mail @JeAtt = %i, @IDDoklad = %i", $jeAtt, $deliveryNoteId);
        return true;
    }

    /**
     * odeslání mailu po doplnění balení k DL na NH
     */
    public function sendMailAfterAddPackageTxtClose($deliveryNoteId) {
        $this->db->query("EXEC dbo.hpx_agrafa_exp_vlozit_baleni_dl_mail @IDDoklad = %i", $deliveryNoteId);
        return true;
    }

    /**
     * nový tisk DL - zrušit příznak tisku
     */
    public function reprintDeliveryNote($deliveryNoteId) {
        $this->db->query("UPDATE TabDokladyZbozi_EXT SET _exp_dat_tisku = NULL WHERE ID = %i", $deliveryNoteId);
    }

    /**
     * uzavření dodacího listu
     * @param type $deliveryNoteId
     * @return boolean
     */
    public function closeDeliveryNote($deliveryNoteId) {
        $this->db->query("EXEC dbo.hpx_agrafa_exp_uzavrit_dl @ID = %i", $deliveryNoteId);
        return true;
    }

    /**
     * uloži balení k dodacímu listu
     * @param type $deliveryNoteId
     * @return boolean
     */
    public function savePackageTxt($deliveryNoteId, $baleniTx) {
        $this->db->query("EXEC dbo.hpx_agrafa_exp_vlozit_baleni_dl @BaleniTx = %s, @ID = %i", $baleniTx, $deliveryNoteId);
        return true;
    }

    /**
     * získání náledujícího čísla balíku
     * @param type $deliveryNoteId
     * @return int
     */
    public function getNextPackage($deliveryNoteId) {
        $nextPack = $this->db->query("SELECT TOP 1 CisloBaliku AS packNr "
                        . "FROM (SELECT bal1.packNr + 1 AS CisloBaliku "
                        . "FROM (SELECT DISTINCT po.IDDoklad, bal.CisloBaliku AS packNr "
                        . "FROM agrafa_ligum_dl_polozka_balik bal "
                        . "LEFT JOIN TabVyrCP vc ON vc.ID = bal.IDVyrCP "
                        . "LEFT JOIN TabPohybyZbozi po ON po.ID = vc.IDPolozkaDokladu "
                        . "WHERE po.IDDoklad = %i) bal1 "
                        . "WHERE NOT EXISTS(SELECT * FROM (SELECT DISTINCT po.IDDoklad, bal.CisloBaliku AS packNr FROM agrafa_ligum_dl_polozka_balik bal LEFT JOIN TabVyrCP vc ON vc.ID = bal.IDVyrCP LEFT JOIN TabPohybyZbozi po ON po.ID = vc.IDPolozkaDokladu WHERE po.IDDoklad = %i) bal2 WHERE bal2.packNr = bal1.packNr + 1 ) "
                        . "UNION "
                        . "SELECT 1 AS CisloBaliku "
                        . "WHERE NOT EXISTS (SELECT * FROM (SELECT DISTINCT po.IDDoklad, bal.CisloBaliku AS packNr FROM agrafa_ligum_dl_polozka_balik bal LEFT JOIN TabVyrCP vc ON vc.ID = bal.IDVyrCP LEFT JOIN TabPohybyZbozi po ON po.ID = vc.IDPolozkaDokladu WHERE po.IDDoklad = %i) bal3 WHERE bal3.packNr = 1)) ot "
                        . "ORDER BY 1", $deliveryNoteId, $deliveryNoteId, $deliveryNoteId)->fetch()->packNr;
        /*
          $nextPack = $this->db->query("SELECT "
          . "ISNULL(MAX(bal.CisloBaliku) + 1, 1) AS packNr "
          . "FROM agrafa_ligum_dl_polozka_balik bal "
          . "LEFT JOIN TabVyrCP vc ON vc.ID = bal.IDVyrCP "
          . "LEFT JOIN TabPohybyZbozi po ON po.ID = vc.IDPolozkaDokladu "
          . "WHERE po.IDDoklad = %i", $deliveryNoteId)->fetch()->packNr;
         * 
         */
        return $nextPack;
    }

    /**
     * zápis do logu expedice
     */
    public function writeExpLog($deliveryNoteId, $action, $itemId, $snId, $partNumber, $barCode, $quantitySet, $quantity, $result, $errMsg) {
        $res = $this->db->query("INSERT INTO eTab_LogExpNH (IDDoklad, Akce, IDPohyb, IDVyrCis, RegCis, VyrCislo, MnozstviZad, Mnozstvi, Vysledek, Chyba, DatZaznamu)"
                        . "VALUES (%i, %i, %i, %i, %s, %s, %f, %f, %i, %s, GETDATE())", $deliveryNoteId, $action, $itemId, $snId, $partNumber, $barCode, $quantitySet, $quantity, $result, $errMsg);

        return $res;
    }
}
