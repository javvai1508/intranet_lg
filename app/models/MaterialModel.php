<?php

namespace App\Models;

use Nette;

class MaterialModel extends BaseModel {

    /**
     * našeptávač firma
     * @param type $subStr
     * @return type
     */
    public function getAutocompleteFirma($subStr) {
        return $this->db->query("SELECT Firma + ', ' + Misto + ' (' + CONVERT(NVARCHAR(10), CisloOrg) + ')' AS Firma FROM TabCisOrg WHERE (Firma LIKE %~like~ OR Misto LIKE %~like~) AND ISNULL(Fakturacni, 0) = 1 AND Stav = 0 AND ISNULL(JeDodavatel, 0) = 1 ORDER BY Firma + ', ' + Misto + ' (' + CONVERT(NVARCHAR(10), CisloOrg) + ')'", $subStr, $subStr)->fetchAll();
    }

    /**
     * našeptávač vydaná objednávka - pokud je vybraná firma, nabízí její objednávky, jinak nic
     * @param type $cisloOrg
     * @param type $subStr
     * @return type
     */
    public function getAutocompleteParovaciZnak($cisloOrg, $subStr) {
        if (!($cisloOrg === "")) {
            return $this->db->query("SELECT ParovaciZnak AS ParovaciZnak FROM TabDokladyZbozi WHERE ParovaciZnak LIKE %~like~ AND DruhPohybuZbo = 6 AND IDSklad = '10000100' AND CisloOrg = %i ORDER BY ParovaciZnak", $subStr, $cisloOrg)->fetchAll();
        } else {
            return NULL;
        }
    }

    /**
     * dotažení firmy podle čísla objednávky
     * @param type $parovaciZnak
     * @return type
     */
    public function getBlurParovaciZnak($parovaciZnak) {
        return $this->db->query("SELECT co.CisloOrg, co.Firma + ', ' + co.Misto + ' (' + CONVERT(NVARCHAR(10), co.CisloOrg) + ')' AS Firma FROM TabDokladyZbozi do, TabCisOrg co WHERE do.ParovaciZnak = %s AND do.CisloOrg = co.CisloOrg", $parovaciZnak)->fetchAll();
    }

    /**
     * dohledání firmy podle čísla organizace
     * @param type $cisloOrg
     * @return type
     */
    public function getFirma($cisloOrg) {
        return $this->db->query("SELECT Firma + ', ' + Misto + ' (' + CONVERT(NVARCHAR(10), CisloOrg) + ')' AS Firma FROM TabCisOrg WHERE CisloOrg = %i", $cisloOrg)->fetch();
    }

    /**
     * dohledání a dotažení objednávky
     * @param type $values
     */
    public function getOrder($values) {
        return $this->db->query("SELECT do.ID, "
                        . "do.RadaDokladu, "
                        . "do.PoradoveCislo, "
                        . "do.CisloOrg, "
                        . "do.DatPorizeni, "
                        . "do.StavRezervace, "
                        . "do.Splneno, "
                        . "do.ParovaciZnak, "
                        . "do.IDSklad, "
                        . "co.Firma + ', ' + co.Misto AS Firma "
                        . "FROM TabDokladyZbozi do "
                        . "LEFT JOIN TabCisOrg co ON co.CisloOrg = do.CisloOrg "
                        . "LEFT JOIN TabCisOrg_EXT coe ON co.ID = coe.ID "
                        . "WHERE do.ParovaciZnak = %s AND do.DruhPohybuZbo = 6 AND do.IDSklad = '10000100' AND do.CisloOrg = %i", $values->ParovaciZnak, $values->CisloOrg)->fetch();
    }

    /**
     * dohledání a dotažení objednávky
     * @param type $orderId
     */
    public function getOrderId($orderId) {
        return $this->db->query("SELECT do.ID, "
                        . "do.RadaDokladu, "
                        . "do.PoradoveCislo, "
                        . "do.CisloOrg, "
                        . "do.DatPorizeni, "
                        . "do.StavRezervace, "
                        . "do.Splneno, "
                        . "do.ParovaciZnak, "
                        . "do.IDSklad, "
                        . "co.Firma + ', ' + co.Misto AS Firma, "
                        . "ISNULL(coe._prijem_oxxo, 0) AS Oxxo "
                        . "FROM TabDokladyZbozi do "
                        . "LEFT JOIN TabCisOrg co ON co.CisloOrg = do.CisloOrg "
                        . "LEFT JOIN TabCisOrg_EXT coe ON co.ID = coe.ID "
                        . "WHERE do.ID = %i", $orderId)->fetch();
    }

    /**
     * dohledání a dotažení položek objednávky v kombinaci s aktuální příjemkou
     * @param type $orderId
     * @param type $receiptId
     */
    public function getOrderItems($orderId, $receiptId) {
        return $this->db->query("SELECT vo.ID AS OrderItemID, "
                        . "pr.ID AS ReceiptItemID, "
                        . "vo.Nazev1 + ' (' + vo.SkupZbo + '/' + vo.RegCis + ')' AS Nazev, "
                        . "vo.MJ AS MJ, "
                        . "vo.Mnozstvi AS MnozstviObj, "
                        . "vo.MnozOdebrane AS MnozstviOdebr, "
                        . "vo.Mnozstvi - vo.MnozOdebrane AS MnozstviZbyva, "
                        . "pr.Mnozstvi AS MnozstviPrij "
                        . "FROM TabPohybyZbozi vo "
                        . "LEFT JOIN TabPohybyZbozi pr ON pr.IDOldPolozka = vo.ID AND pr.IDDoklad = %i "
                        . "WHERE vo.IDDoklad = %i ", $receiptId, $orderId)->fetchAll();
    }

    /**
     * výběr položky k editaci
     * @param type $itemId
     */
    public function getOrderItem($itemId, $receiptId) {
        return $this->db->query("SELECT "
                        . "vo.ID AS OrderItemID, "
                        . "pr.ID AS ReceiptItemID, "
                        . "vo.Nazev1 + ' (' + vo.SkupZbo + '/' + vo.RegCis + ')' AS Nazev, "
                        . "vo.MJ AS MJ, "
                        . "vo.Mnozstvi AS MnozstviObj, "
                        . "vo.MnozOdebrane AS MnozstviOdebr, "
                        . "vo.Mnozstvi - vo.MnozOdebrane AS MnozstviZbyva, "
                        . "pr.Mnozstvi AS MnozstviPrij "
                        . "FROM TabPohybyZbozi vo "
                        . "LEFT JOIN TabPohybyZbozi pr ON pr.IDOldPolozka = vo.ID AND pr.IDDoklad = %i "
                        . "WHERE vo.ID = %i ", $receiptId, $itemId)->fetch();
    }

    /**
     * založení příjemky
     * 
     * @param type $cisloOrg
     * @return type
     */
    public function createReceipt($orderId, $cisloOrg) {

        return $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @IDDoklad INT "
                        . "EXEC hpx_agrafa_sklad_zaloz_prij_tablet @CisloOrg = %i, @IDDoklad = @IDDoklad OUTPUT "
                        . "SELECT @IDDoklad", $cisloOrg)->fetchSingle(); // @VObjID = %i, , $orderId
    }

    /**
     * dohledání a dotažení příjemky
     * @param type $receiptId
     */
    public function getReceipt($receiptId) {
        return $this->db->query("SELECT do.ID, "
                        . "do.RadaDokladu, "
                        . "do.PoradoveCislo, "
                        . "do.CisloOrg, "
                        . "do.DatPorizeni, "
                        . "do.ParovaciZnak, "
                        . "do.Text2, " 
                        . "do.IDSklad, "
                        . "CASE WHEN doe._exp_dat_uzavreno IS NOT NULL OR do.DatRealizace IS NOT NULL THEN 1 ELSE 0 END AS StavRcpExp, "
                        . "co.Firma AS Firma, "
                        . "do.NavaznaObjednavka AS CisloDL, "
                        . "CONVERT(NVARCHAR(20), do.DatPovinnostiFa, 104) AS DatumDL, "
                        . "CONVERT(NVARCHAR(20), doe._exp_dat_uzavreno, 104) AS DatUzavrenoExp, "
                        . "ISNULL(doe._skl_import_oxxo, 0) AS Oxxo, "
                        . "sumy.Mnozstvi AS Mnozstvi, "
                        . "sumy.MnozstviDL AS MnozstviDL "
                        . "FROM TabDokladyZbozi do "
                        . "LEFT JOIN TabDokladyZbozi_EXT doe ON do.ID = doe.ID "
                        . "LEFT JOIN TabCisOrg co ON co.CisloOrg = do.CisloOrg "
                        . "LEFT JOIN (SELECT "
                        . "po.IDDoklad, "
                        . "COUNT(po.ID) AS PocetPozad, "
                        . "SUM(ISNULL(poe._prijem_dle_dl, 0)) AS MnozstviDL, "
                        . "SUM(ISNULL(po.Mnozstvi, 0)) AS Mnozstvi "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "GROUP BY po.IDDoklad) sumy ON do.ID = sumy.IDDoklad "
                        . "WHERE do.ID = %i", $receiptId)->fetch();
    }

    /**
     * dohledání a dotažení položek příjemky
     * @param type $receiptId
     */
    public function getReceiptItems($receiptId) {
        return $this->db->query("SELECT "
                        . "pr.ID AS ReceiptItemID, "
                        . "pr.Nazev1 AS Nazev, "
                        . "pr.MJ AS MJ, "
                        . "pr.Mnozstvi AS Mnozstvi, "
                        . "pre._prijem_dle_dl AS MnozstviDL, "
                        . "vod.ParovaciZnak AS Objednavka "
                        . "FROM TabPohybyZbozi pr "
                        . "LEFT JOIN TabPohybyZbozi_EXT pre ON pre.ID = pr.ID "
                        . "LEFT JOIN TabPohybyZbozi vo ON pr.IDOldPolozka = vo.ID "
                        . "LEFT JOIN TabDokladyZbozi vod ON vod.ID = vo.IDDoklad "
                        . "WHERE pr.IDDoklad = %i", $receiptId)->fetchAll();
    }

    /**
     * dohledání a dotažení položek příjemky s rozdíly proti DL
     * @param type $receiptId
     */
    public function getReceiptDiffItems($receiptId) {
        return $this->db->query("SELECT "
                        . "pr.ID AS ReceiptItemID, "
                        . "pr.Nazev1 AS Nazev, "
                        . "pr.MJ AS MJ, "
                        . "pr.Mnozstvi AS Mnozstvi, "
                        . "pre._prijem_dle_dl AS MnozstviDL, "
                        . "vod.ParovaciZnak AS Objednavka "
                        . "FROM TabPohybyZbozi pr "
                        . "LEFT JOIN TabPohybyZbozi_EXT pre ON pre.ID = pr.ID "
                        . "LEFT JOIN TabPohybyZbozi vo ON pr.IDOldPolozka = vo.ID "
                        . "LEFT JOIN TabDokladyZbozi vod ON vod.ID = vo.IDDoklad "
                        . "WHERE pr.IDDoklad = %i AND pr.Mnozstvi <> pre._prijem_dle_dl", $receiptId)->fetchAll();
    }

    /**
     * dohledání a dotažení položky příjemky
     * @param type $receiptId
     */
    public function getReceiptItem($itemId, $receiptId) {
        return $this->db->query("SELECT "
                        . "pr.ID AS ReceiptItemID, "
                        . "pr.Nazev1 AS Nazev, "
                        . "pr.MJ AS MJ, "
                        . "pr.Mnozstvi AS Mnozstvi, "
                        . "pre._prijem_dle_dl AS MnozstviDL, "
                        . "vod.ParovaciZnak AS Objednavka "
                        . "FROM TabPohybyZbozi pr "
                        . "LEFT JOIN TabPohybyZbozi_EXT pre ON pre.ID = pr.ID "
                        . "LEFT JOIN TabPohybyZbozi vo ON pr.IDOldPolozka = vo.ID "
                        . "LEFT JOIN TabDokladyZbozi vod ON vod.ID = vo.IDDoklad "
                        . "WHERE pr.IDDoklad = %i AND pr.ID = %i", $receiptId, $itemId)->fetch();
    }

    /**
     * dohledání a dotažení položek příjemky
     * @param type $itemId
     */
    public function getReceiptItemSN($itemId) {
        return $this->db->query("SELECT pr.ID AS ReceiptItemID, "
                        . "vc.ID AS IDVyrCP, "
                        . "vc.Nazev AS Nazev, "
                        . "vc.Mnozstvi AS Mnozstvi "
                        . "FROM TabVyrCP vc "
                        . "JOIN TabPohybyZbozi pr ON vc.IDPolozkaDokladu = pr.ID "
                        . "WHERE pr.ID = %i ", $itemId)->fetchAll();
    }

    /**
     * dohledání a dotažení skladového dokladu podle čísla (ParovaciZnak)
     * @param type $parovaciZnak
     */
    public function getStockDocPZ($parovaciZnak) {
        $row = $this->db->query("SELECT do.ID, "
                        . "do.RadaDokladu, "
                        . "do.PoradoveCislo, "
                        . "do.CisloOrg, "
                        . "do.DatPorizeni, "
                        . "do.ParovaciZnak, "
                        . "do.DruhPohybuZbo, "
                        . "do.IDSklad, "
                        . "CASE WHEN doe._exp_dat_uzavreno IS NOT NULL THEN 1 ELSE 0 END AS StavDLExp, "
                        . "ISNULL(doe._skl_import_oxxo, 0) AS Oxxo, "
                        . "ISNULL(doe._skl_prijem_priprava, 0) AS Pripraveno "
                        . "FROM TabDokladyZbozi do "
                        . "LEFT JOIN TabDokladyZbozi_EXT doe ON do.ID = doe.ID "
                        . "WHERE do.ParovaciZnak = %s AND do.IDSklad = '10000100'", $parovaciZnak)->fetch();
        if (!$row) {
            $row = $this->db->query("SELECT do.ID, "
                            . "do.RadaDokladu, "
                            . "do.PoradoveCislo, "
                            . "do.CisloOrg, "
                            . "do.DatPorizeni, "
                            . "do.ParovaciZnak, "
                            . "do.DruhPohybuZbo, "
                            . "do.IDSklad, "
                            . "CASE WHEN doe._exp_dat_uzavreno IS NOT NULL THEN 1 ELSE 0 END AS StavDLExp, "
                            . "ISNULL(doe._skl_import_oxxo, 0) AS Oxxo, "
                            . "ISNULL(doe._skl_prijem_priprava, 0) AS Pripraveno "
                            . "FROM TabDokladyZbozi do "
                            . "LEFT JOIN TabDokladyZbozi_EXT doe ON do.ID = doe.ID "
                            . "WHERE do.NavaznaObjednavka = %s AND do.IDSklad = '10000100'", $parovaciZnak)->fetch();
        }
        return $row;
    }

    /**
     * kontrola VČ položky na příjemce - OXXO
     * @param type $values
     * @return type
     */
    public function checkReceiptItemSN($values) {
        return $this->db->query("SELECT "
                        . "po.ID AS PrijRdId, "
                        . "vcp.ID AS IDVyrCP,"
                        . "vcp.Mnozstvi AS PrijemVC, "
                        . "vcpe._prijem_vc_dle_dl AS DodanoVC "
                        . "FROM TabVyrCP vcp "
                        . "JOIN TabVyrCS vcs ON vcp.IDVyrCis = vcs.ID "
                        . "JOIN TabPohybyZbozi po ON vcp.IDPolozkaDOkladu = po.ID "
                        . "LEFT JOIN TabVyrCP_EXT vcpe ON vcp.ID = vcpe.ID "
                        . "WHERE po.IDDoklad = %i AND vcs.Nazev1 = %s", $values->receiptId, $values->BarCode)->fetch();
    }

    /**
     * aktualizace položky příjemky a VČ položky na příjemce - OXXO
     * @param type $values
     * @return type
     */
    public function updateReceiptItemSN($values) {
        $res = 0;
        $item = $this->db->query("SELECT "
                        . "po.ID AS PrijRdId, "
                        . "vcp.ID AS IDVyrCP,"
                        . "vcp.Mnozstvi AS PrijemVC, "
                        . "vcpe._prijem_vc_dle_dl AS DodanoVC "
                        . "FROM TabVyrCP vcp "
                        . "JOIN TabVyrCS vcs ON vcp.IDVyrCis = vcs.ID "
                        . "JOIN TabPohybyZbozi po ON vcp.IDPolozkaDOkladu = po.ID "
                        . "LEFT JOIN TabVyrCP_EXT vcpe ON vcp.ID = vcpe.ID "
                        . "WHERE po.IDDoklad = %i AND vcs.Nazev1 = %s", $values->receiptId, $values->BarCode)->fetch();
        $idVyrCP = $item->IDVyrCP;
        $idPohyb = $item->PrijRdId;
        $mnozstvi = $item->DodanoVC;
        if ($idVyrCP) {
            $sqres = $this->db->query("UPDATE TabVyrCP "
                    . "SET Mnozstvi = Mnozstvi + %f "
                    . "WHERE TabVyrCP.ID = %i", $mnozstvi, $idVyrCP);
            if ($sqres && $idPohyb) {
                $sqres = $this->db->query("UPDATE TabPohybyZbozi "
                        . "SET Mnozstvi = Mnozstvi + %f "
                        . "WHERE ID = %i", $mnozstvi, $idPohyb);
            }
        }
        return $res;
    }

    /**
     * kontrola, případně založení nového VČ, přidat na řádek dokladu (viz výše) - akce v HeO, mimo OXXO
     */
    public function addSNToReceiptId($receiptId, $sn, $itemId, $qty) {
        $this->db->query("EXEC dbo.hpx_agrafa_sklad_nove_vc_prijem_tablet @IDPohyb = %i, @BarCode = %s, @Mnozstvi = %f, @IDDoklad = %i", $itemId, $sn, $qty, $receiptId);

        return 1;
    }

    /**
     * 
     * @param type $itemId
     */
    public function removeReceiptItem($itemId) {
        // transakce
        $this->db->begin();
        try {
            $res = $this->db->query("UPDATE TabPohybyZbozi "
                    . "SET Mnozstvi = 0 "
                    . "WHERE ID = %i", $itemId);
            if ($res) {
                $res = $this->db->query("UPDATE TabVyrCP SET Mnozstvi = 0 WHERE IDPolozkaDokladu = %i", $itemId);
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
     * @param type $itemSNId
     */
    public function removeReceiptItemSN($itemSNId) {
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
                $res = $this->db->query("UPDATE TabVyrCP SET Mnozstvi = 0 WHERE ID = %i", $itemSNId);

                $res = $this->db->query("UPDATE TabPohybyZbozi "
                        . "SET Mnozstvi = Mnozstvi - %f "
                        . "WHERE ID = %i", $itemSNRow->MnozstviSN, $itemSNRow->ItemId);
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
     * kontrola a výběr pro novou položku přijemky
     * @param type $values
     * @return type
     */
    public function checkNewReceiptItem($values) {
        return $this->db->query("SELECT "
                        . "obj.ID AS ObjRdId, "
                        . "prij.ID AS PrijRdId, "
                        . "obj.IDZboSklad "
                        . "FROM TabPohybyZbozi obj "
                        . "LEFT JOIN TabPohybyZbozi prij ON obj.IDZboSklad = prij.IDZboSklad AND prij.IDDoklad = %i AND prij.IDOldPolozka = obj.ID "
                        . "WHERE obj.ID = %i", $values->receiptId, $values->orderItemId)->fetch();
    }

    /**
     * přidání položky do příjemky
     * @param type $orderId
     * @param type $orderItemId
     * @param type $receiptId
     * @param type $receiptItemId
     * @param type $idZboSklad
     * @param type $sn
     * @param type $mnozstvi
     * @return type
     */
    public function addReceiptItem($orderId, $orderItemId, $receiptId, $receiptItemId, $idZboSklad, $sn, $mnozstvi) {

        return $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @IDPrijRd INT "
                        . "SET @IDPrijRd = %i "
                        . "EXEC hpx_agrafa_sklad_prijem_radek_tablet @IDPrijemka = %i, @IDPrijRd = @IDPrijRd OUTPUT, @IDObj = %i, @IDObjRd = %i, @IDZboSklad = %i, @BarCode = %s, @Mnozstvi = %f "
                        . "SELECT @IDPrijRd", $receiptItemId, $receiptId, $orderId, $orderItemId, $idZboSklad, $sn, $mnozstvi)->fetchSingle();
    }

    /**
     * 
     */
    public function checkReceiptBeforeClosing($receiptId) {
        // kontrola na menší dodávku - porovnání TabPohybyZbozi.Mnozstvi a TabPohybyZbozi_EXT._prijem_dle_dl
        $res = 0;
        $chyby = $this->db->query("SELECT SUM(CASE WHEN po.Mnozstvi < ISNULL(poe._prijem_dle_dl, 0) THEN 1 ELSE 0 END) AS chyby "
                    . "FROM TabPohybyZbozi po "
                    . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID " 
                    , "WHERE po.IDDoklad = %i", $receiptId)->fetch()->chyby;
        if ($chyby > 0) {
            $res = 1;
        }
        // kontrola TabDokladyZbozi.Text2
        $pozn = $this->db->query("SELECT Text2 FROM TabDokladyZbozi WHERE ID = %i", $receiptId)->fetch()->Text2;
        if ($pozn) {
            $res = $res + 10;
        }
        return $res;

    }

    /**
     * uzavření příjemky
     * @param type $receiptId
     * @return boolean
     */
    public function closeReceipt($receiptId) {
        $this->db->query("EXEC dbo.hpx_agrafa_sklad_uzavrit_prijem @IDDoklad = %i", $receiptId);
        /*
        $this->db->query("UPDATE TabDokladyZbozi_EXT "
                . "SET _exp_dat_uzavreno = GETDATE(), "
                . "_exp_dat_ktisku = GETDATE(), "
                . "_exp_dat_tisku = NULL "
                . "WHERE ID = %i", $receiptId);
                */
        return true;
    }

    /**
     * uzavření příjemky  rychlé - bez načítání položek
     * @param type $receiptId
     * @return boolean
     */
    public function closeReceiptQuick($receiptId) {
        $this->db->query("EXEC dbo.hpx_agrafa_sklad_uzavrit_prijem_ihned @IDDoklad = %i", $receiptId);
        return true;
    }

    /**
     * generování výrobního čísla - IDKmenZbozi (6) + pořadové číslo (4)
     * @param type $itemId
     * @return type
     */
    public function generateSN($itemId) {
        $stockId = $this->db->query("SELECT IDZboSklad FROM TabPohybyZbozi WHERE ID = %i", $itemId)->fetchSingle();
        $sn = $this->db->query("SELECT "
                        . "RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) + RIGHT('0000' +CONVERT(NVARCHAR(4), MAX(CONVERT(INT, SUBSTRING(vcs.Nazev1, 8, 4))) + 1), 4) "
                        . "FROM TabVyrCS vcs  "
                        . "JOIN TabStavSkladu skl ON skl.ID = vcs.IDStavSkladu "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "WHERE vcs.IDStavSkladu = %i AND SUBSTRING(vcs.Nazev1, 1, 6) = RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) "
                        . "GROUP BY vcs.IDStavSkladu, RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6)", $stockId)->fetchSingle();
        if (!$sn) {
            $sn = $this->db->query("SELECT "
                            . "RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) + '0001' "
                            . "FROM TabKmenZbozi km "
                            . "JOIN TabStavSkladu skl ON km.ID = skl.IDKmenZbozi "
                            . "WHERE skl.ID = %i ", $stockId)->fetchSingle();
        }
        return $sn;
    }

    /**
     * dohledání a dotažení výdejky
     * @param type $dispatchId
     */
    public function getDispatch($dispatchId) {
        return $this->db->query("SELECT do.ID, "
                        . "do.RadaDokladu, "
                        . "do.PoradoveCislo, "
                        . "do.DatPorizeni, "
                        . "do.ParovaciZnak, "
                        . "do.IDSklad, "
                        . "CONVERT(NVARCHAR(20), doe._exp_dat_uzavreno, 104) AS DatUzavrenoExp, "
                        . "CASE WHEN doe._exp_dat_uzavreno IS NOT NULL OR do.DatRealizace IS NOT NULL THEN 1 ELSE 0 END AS StavDispExp, "
                        . "sumy.PocetPozad AS PocetPozad, "
                        . "sumy.MnozstviPozad AS MnozstviPozad, "
                        . "sumy.PocetVC AS PocetVC, "
                        . "ISNULL(sumy.MnozstviVC, 0) AS MnozstviVC, "
                        . "pr.RadaPrikaz, "
                        . "pr.Prikaz, "
                        . "pr.ID AS IDPrikaz, "
                        . "odb.Firma, "
                        . "odb.Alias, "
                        . "doe._skl_barcode_oper AS BarCodeOper, "
                        . "doe._skl_sdruz_oper_prik AS SdruzPrik, "
                        . "ISNULL(kme._vyr_druh_pruvodky, 0) AS DruhPruvodky, "
                        . "ISNULL(pre._prikaz_skl_tisk_vydej_laminat, 0) AS StitkyHotovo "
                        . "FROM TabDokladyZbozi do "
                        . "LEFT JOIN TabDokladyZbozi_EXT doe ON do.ID = doe.ID "
                        . "LEFT JOIN TabPrikaz pr ON pr.ID = do.IDPrikaz "
                        . "LEFT JOIN TabPrikaz_EXT pre ON pr.ID = pre.ID "
                        . "LEFT JOIN TabKmenZbozi km ON km.ID = pr.IDTabKmen "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "LEFT JOIN (SELECT pr.ID, org.Firma, orge._alias AS Alias FROM TabCisOrg org LEFT JOIN TabCisOrg_EXT orge ON org.ID = orge.ID JOIN TabDokladyZbozi do ON org.CisloOrg = do.CisloOrg JOIN TabPohybyZbozi po ON po.IDDoklad = do.ID JOIN TabPrikaz pr ON pr.IDRezervace = po.ID) odb ON odb.ID = pr.ID "
                        . "LEFT JOIN (SELECT "
                        . "po.IDDoklad, "
                        . "COUNT(po.ID) AS PocetPozad, "
                        . "SUM(po.Mnozstvi) AS MnozstviPozad, "
                        . "SUM(CASE WHEN poe._exp_mnozstvi_vc >= po.Mnozstvi THEN 1 ELSE 0 END) AS PocetVC, "
                        . "SUM(poe._exp_mnozstvi_vc) AS MnozstviVC, "
                        . "COUNT(poe._exp_cislo_baliku) AS PocetBaliku "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "WHERE po.SkupZbo IN ('151', '350') "
                        . "GROUP BY po.IDDoklad) sumy ON do.ID = sumy.IDDoklad "
                        . "WHERE do.ID = %i", $dispatchId)->fetch();
    }

    /**
     * dohledání a dotažení požadavků na výdej podle pracovního kusovníku
     * @param type $barCodeOper
     * @return type
     */
    public function getRequestItems($barCodeOper) {
        // dohledat, jestli se jedná o sdruženou 
        $sdruz = $this->db->query("SELECT ID FROM TabSdruzVyrOperace WHERE BarCode = %s", $barCodeOper)->fetch();
        if (isset($sdruz->ID)) {
            // sdružená operace 
            $requests = $this->db->query("SELECT "
                            . "pkv.ID AS IDPrKVazby, "
                            . "km.ID AS IDKmen, "
                            . "km.Nazev1 AS Nazev, "
                            . "km.MJEvidence AS MJ, "
                            . "kmve._SCelkovaDelkaSleeve AS DelkaSleeve, "
                            . "pkv.mnoz_pozadovane - pkv.Mnoz_skut_realizovane - (SELECT ISNULL(SUM(PZ.prepmnozstvi * PZ.mnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabStavSkladu SS ON (SS.ID = PZ.IDZboSklad) WHERE PZ.druhPohybuZbo IN (2, 4) AND PZ.SkutecneDatReal IS NULL AND PZ.TypVyrobnihoDokladu = 1 AND PZ.IDPrikaz = pkv.IDPrikaz AND PZ.DokladPrikazu = opr.Doklad AND SS.IDKmenZbozi = pkv.nizsi) AS MnozPozad, "
                            . "pkv.mnoz_zad AS MnozZadane, "
                            . "pkv.mnozstvi AS MnozKs, "
                            . "pr.ID AS IDPrikaz,"
                            . "pr.Kusy_zad_puvodni AS KsZadane, "
                            . "1 AS Sdruz "
                            . "FROM TabPrKVazby pkv  "
                            . "JOIN TabKmenZbozi km ON km.ID = pkv.nizsi  "
                            . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                            . "JOIN TabSdruzVyrOperaceR opr ON opr.IDprikaz = pkv.IDPrikaz "
                            . "JOIN TabSdruzVyrOperace op ON opr.IDSdruzVyrOperace = op.ID "
                            . "JOIN TabPrikaz pr ON pr.ID = pkv.IDPrikaz "
                            . "JOIN TabKmenZbozi kmv ON kmv.ID = pkv.vyssi "
                            . "JOIN TabKmenZbozi_EXT kmve ON kmv.ID = kmve.ID "
                            . "WHERE pkv.IDOdchylkyDo IS NULL  "
                            . "AND dbo.hf_GetPrPDokladForPrKV(pkv.ID) > ISNULL((SELECT TOP 1 Doklad FROM TabPrPostup OUTER APPLY (SELECT TOP 1 _TiskKodu, dilec, Operace FROM TabPostup LEFT JOIN TabPostup_EXT ON TabPostup.ID = TabPostup_EXT.ID WHERE dilec = TabPrPostup.dilec AND Operace = TabPrPostup.Operace AND IDPrikaz = pkv.IDPrikaz ORDER BY TabPostup.DatPorizeni DESC) AS tp WHERE IDPrikaz = pkv.IDPrikaz AND Doklad < opr.Doklad AND ISNULL(tp._TiskKodu, 0) = 0 ORDER BY Doklad DESC), 0)  "
                            . "AND dbo.hf_GetPrPDokladForPrKV(pkv.ID) <= opr.Doklad "
                            . "AND pkv.prednastaveno = 1  "
                            . "AND km.SkupZbo IN ('151') "
                            . "AND op.BarCode = %s", $barCodeOper)->fetchAll();
        } else {
            // jednotlivá operace
            $requests = $this->db->query("SELECT "
                            . "pkv.ID AS IDPrKVazby, "
                            . "km.ID AS IDKmen, "
                            . "km.Nazev1 AS Nazev, "
                            . "km.MJEvidence AS MJ, "
                            . "kmve._SCelkovaDelkaSleeve AS DelkaSleeve, "
                            . "pkv.mnoz_pozadovane - pkv.Mnoz_skut_realizovane - (SELECT ISNULL(SUM(PZ.prepmnozstvi * PZ.mnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabStavSkladu SS ON (SS.ID = PZ.IDZboSklad) WHERE PZ.druhPohybuZbo IN (2, 4) AND PZ.SkutecneDatReal IS NULL AND PZ.TypVyrobnihoDokladu = 1 AND PZ.IDPrikaz = pkv.IDPrikaz AND PZ.DokladPrikazu = prp.Doklad AND SS.IDKmenZbozi = pkv.nizsi) AS MnozPozad, "
                            . "pkv.mnoz_zad AS MnozZadane, "
                            . "pkv.mnozstvi AS MnozKs, "
                            . "pr.ID AS IDPrikaz,"
                            . "pr.Kusy_zad_puvodni AS KsZadane, "
                            . "0 AS Sdruz "
                            . "FROM TabPrKVazby pkv "
                            . "JOIN TabKmenZbozi km ON km.ID = pkv.nizsi "
                            . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                            . "JOIN TabPrPostup prp ON prp.IDprikaz = pkv.IDPrikaz "
                            . "JOIN TabPrikaz pr ON pr.ID = pkv.IDPrikaz "
                            . "JOIN TabKmenZbozi kmv ON kmv.ID = pkv.vyssi "
                            . "JOIN TabKmenZbozi_EXT kmve ON kmv.ID = kmve.ID "
                            . "WHERE pkv.IDOdchylkyDo IS NULL "
                            . "AND dbo.hf_GetPrPDokladForPrKV(pkv.ID) > ISNULL((SELECT TOP 1 Doklad FROM TabPrPostup OUTER APPLY (SELECT TOP 1 _TiskKodu, dilec, Operace FROM TabPostup LEFT JOIN TabPostup_EXT ON TabPostup.ID = TabPostup_EXT.ID WHERE dilec = TabPrPostup.dilec AND Operace = TabPrPostup.Operace AND IDPrikaz = pkv.IDPrikaz ORDER BY TabPostup.DatPorizeni DESC) AS tp WHERE IDPrikaz = pkv.IDPrikaz AND Doklad < prp.Doklad AND ISNULL(tp._TiskKodu, 0) = 0 ORDER BY Doklad DESC), 0) "
                            . "AND dbo.hf_GetPrPDokladForPrKV(pkv.ID) <= prp.Doklad "
                            . "AND pkv.prednastaveno = 1 "
                            . "AND prp.BarCode = %s "
                            . "AND km.SkupZbo IN ('151', '350')", $barCodeOper)->fetchAll();
        }
        return $requests;
    }

    /**
     * dohledání a dotažení položek výdejky
     * @param type $dispatchId
     */
    public function getDispatchItems($dispatchId) {
        return $this->db->query("SELECT "
                        . "vy.ID AS DispatchItemID, "
                        . "vy.Nazev1 AS Nazev, "
                        . "vy.MJ AS MJ, "
                        . "vy.Mnozstvi AS Mnozstvi, "
                        . "ISNULL(vye._exp_mnozstvi_vc, 0) AS MnozstviVC, "
                        . "kv.mnoz_zad AS MnozZadane, "
                        . "kv.mnozstvi AS MnozKs, "
                        . "kv.Mnoz_skut_realizovane + (SELECT ISNULL(SUM(PZ.prepmnozstvi * PZ.mnozstvi), 0.0) FROM TabPohybyZbozi PZ INNER JOIN TabStavSkladu SS ON (SS.ID = PZ.IDZboSklad) WHERE PZ.druhPohybuZbo IN (2, 4) AND PZ.SkutecneDatReal IS NULL AND PZ.TypVyrobnihoDokladu = 1 AND PZ.IDPrikaz = kv.IDPrikaz AND PZ.DokladPrikazu = kv.Doklad AND SS.IDKmenZbozi = kv.nizsi AND PZ.IDDoklad <> vy.IDDoklad) AS MnozVydane, "
                        . "kme._SCelkovaDelkaSleeve AS DelkaSleeve, "
                        . "pr.Kusy_zad_puvodni AS KsZadane "
                        . "FROM TabPohybyZbozi vy "
                        . "LEFT JOIN TabPohybyZbozi_EXT vye ON vye.ID = vy.ID "
                        . "LEFT JOIN TabPrKVazby kv ON kv.IDPrikaz = vy.IDPrikaz AND kv.Doklad = vy.DokladPrikazu AND kv.IDOdchylkyDo IS NULL "
                        . "LEFT JOIN TabKmenZbozi km ON km.ID = kv.vyssi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "LEFT JOIN TabPrikaz pr ON pr.ID = kv.IDPrikaz "
                        . "WHERE vy.IDDoklad = %i", $dispatchId)->fetchAll();
    }

    /**
     * dohledání položky výdejky
     * @param type $itemId
     */
    public function getDispatchItem($itemId) {
        return $this->db->query("SELECT "
                        . "pr.ID AS DispatchItemID, "
                        . "pr.Nazev1 AS Nazev, "
                        . "pr.MJ AS MJ, "
                        . "pr.Mnozstvi AS Mnozstvi, "
                        . "ISNULL(pre._exp_mnozstvi_vc, 0) AS MnozstviVC, "
                        . "vod.ParovaciZnak AS Objednavka "
                        . "FROM TabPohybyZbozi pr "
                        . "LEFT JOIN TabPohybyZbozi_EXT pre ON pre.ID = pr.ID "
                        . "LEFT JOIN TabPohybyZbozi vo ON pr.IDOldPolozka = vo.ID "
                        . "LEFT JOIN TabDokladyZbozi vod ON vod.ID = vo.IDDoklad "
                        . "WHERE pr.ID = %i", $itemId)->fetch();
    }

    /**
     * dohledání a dotažení seriových čísel položek výdejky
     * @param type $itemId
     */
    public function getDispatchItemSN($itemId) {
        return $this->db->query("SELECT pr.ID AS DispatchItemID, "
                        . "vc.ID AS IDVyrCP, "
                        . "vcs.Nazev1 AS Nazev, "
                        . "vc.Mnozstvi AS Mnozstvi "
                        . "FROM TabVyrCP vc "
                        . "JOIN TabVyrCS vcs ON vcs.ID = vc.IDVyrCis "
                        . "JOIN TabPohybyZbozi pr ON vc.IDPolozkaDokladu = pr.ID "
                        . "WHERE pr.ID = %i ", $itemId)->fetchAll();
    }

    /**
     * dohledání materiálu k operaci, založení výdejky, včetně řádků
     * @param type $operBarCode
     * @return type
     */
    public function openNewDispatchFromOper($operBarCode) {
        // kontrola operace
        if (substr(strtoupper($operBarCode), 0, 1) == 'A') {
            $operBarCode = substr($operBarCode, 0, strpos(strtoupper($operBarCode), 'B') + 1);
        } elseif (substr(strtoupper($operBarCode), 0, 1) == 'B') {
            // sdružená operace 
        } else {
            // nesmysl
        }
        // 0 = válec, 1 = sleeve, 2 = sdružená operace, 3 = otevřená výdejka, -1 = není operace, -2 = chybný výrobek, -3 = není materiál

        $token = uniqid();
        // pokud je válec - založit pouze hlavičku
        $res["Stav"] = $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @Stav INT "
                        . "EXEC hpx_agrafa_sklad_ko_vydej_tablet @OperBarCode = %s, @Token = %s, @Stav = @Stav OUTPUT "
                        . "SELECT @Stav", $operBarCode, $token)->fetchSingle();
        if ($res["Stav"] == 0) { // válec
        } elseif ($res["Stav"] == 1 OR $res["Stav"] == 2) { // sleeve
            $this->db->query("SET NOCOUNT ON "
                    . "EXEC hpx_agrafa_sklad_zaloz_vydej_hl_tablet @OperBarCode = %s, @Token = %s ", $operBarCode, $token);
            $res["IDDoklad"] = $this->db->query("SELECT ID FROM TabDokladyZbozi_EXT WHERE _skl_token = %s", $token)->fetchSingle();
        } elseif ($res["Stav"] == 3) { // otevřená výdejka
            $res["IDDoklad"] = $this->db->query("SELECT ID FROM TabDokladyZbozi_EXT WHERE _skl_token = %s", $token)->fetchSingle();
        } else {
            
        }
        return $res;
    }

    /**
     * založení výdejky mimo operace (vlastní spotřeba, zmetek, prodej)
     * @return type
     */
    public function openNewDispatch($dispatchType) {
        $token = uniqid();
        // založit pouze hlavičku
        $this->db->query("SET NOCOUNT ON "
                . "EXEC hpx_agrafa_sklad_zaloz_vydej_ost_hl_tablet @TypVydejky = %s, @Token = %s ", $dispatchType, $token);
        $res["IDDoklad"] = $this->db->query("SELECT ID FROM TabDokladyZbozi_EXT WHERE _skl_token = %s", $token)->fetchSingle();
        return $res;
    }

    /**
     * kontrola zadaného výrobního čísla - výdej na operaci
     * @param type $dispatchId
     * @param type $serialNumber
     * @return type
     */
    public function checkSerialNumber($dispatchId, $serialNumber) {
        $this->db->query("SET NOCOUNT ON "
                . "EXEC hpx_agrafa_sklad_ko_sn_vydej_tablet @IDDoklad = %i, @VyrCNazev = %s ", $dispatchId, $serialNumber);

        return $this->db->query("SELECT "
                        . "vc.ID AS IDVyrCis, "
                        . "vc.Nazev1 AS BarCode, "
                        . "CONVERT(INT, vc.Mnozstvi) AS Mnozstvi, "
                        . "CONVERT(INT, vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji, "
                        . "vc.IDStavSkladu AS IDSZboSklad, "
                        . "po.IDZboSklad AS IDZboSklad, "
                        . "CONVERT(INT, po.Mnozstvi) AS MnozstviPozadavek, "
                        . "CONVERT(INT, po._exp_mnozstvi_vc) AS MnozstviPriprava "
                        . "FROM TabVyrCS vc "
                        . "LEFT JOIN ("
                        . "SELECT "
                        . "po.IDZboSklad, "
                        . "po.IDDoklad, "
                        . "SUM(po.Mnozstvi) AS Mnozstvi,"
                        . "SUM(poe._exp_mnozstvi_vc) AS _exp_mnozstvi_vc "
                        . "FROM TabPohybyZbozi po "
                        . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                        . "GROUP BY po.IDDoklad, po.IDZboSklad "
                        . ") po ON po.IDZboSklad = vc.IDStavSkladu AND po.IDDoklad = %i "
                        . "WHERE vc.Nazev1 = %s", $dispatchId, $serialNumber)->fetch();
    }

    /**
     * doplní výrobní číslo do dokladu - výdej na operaci
     * @param type $serialNumber
     * @param type $mnozstviPriprava
     * @return type
     */
    public function updateDispatchItem($dispatchId, $zboSkladId, $vyrCisloId, $mnozstviPriprava) {
        // transakce
        $res = -9;
        $this->db->begin();
        try {
            $mnozstviPripravaInit = $mnozstviPriprava;
            $itemSums = $this->db->query("SELECT "
                            . "po.IDZboSklad AS IdZboSklad,"
                            . "SUM(ISNULL(poe._exp_mnozstvi_pozad_puvodni, 0)) AS MnozstviPozadavek "
                            . "FROM TabPohybyZbozi po "
                            . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                            . "WHERE po.IDDoklad = %i AND po.IDZboSklad = %i "
                            . "GROUP BY po.IDZboSklad ", $dispatchId, $zboSkladId)->fetch();
            $itemRows = $this->db->query("SELECT "
                            . "po.ID AS ItemId,"
                            . "ISNULL(poe._exp_mnozstvi_pozad_puvodni, 0) AS MnozstviPozadavek, "
                            . "ISNULL(vcp.MnozstviPriprava, 0) AS MnozstviPriprava "
                            . "FROM TabPohybyZbozi po "
                            . "LEFT JOIN TabPohybyZbozi_EXT poe ON po.ID = poe.ID "
                            . "LEFT JOIN ("
                            . "SELECT "
                            . "IDPolozkaDokladu, "
                            . "SUM(Mnozstvi) AS MnozstviPriprava "
                            . "FROM TabVyrCP "
                            . "GROUP BY IDPolozkaDokladu) vcp ON po.ID = vcp.IDPolozkaDokladu "
                            . "WHERE po.IDDoklad = %i AND po.IDZboSklad = %i "
                            . "ORDER BY po.Poradi", $dispatchId, $zboSkladId)->fetchAll();
            foreach ($itemRows as $itemRow) {
                if ($mnozstviPriprava > 0) {
                    // $mnozstviUloz = $itemRow->MnozstviPozadavek - $itemRow->MnozstviPriprava; 
                    $mnozstviUloz = $itemRow->MnozstviPozadavek / $itemSums->MnozstviPozadavek * $mnozstviPripravaInit;
                    if ($mnozstviUloz > $mnozstviPriprava) {
                        $mnozstviUloz = $mnozstviPriprava;
                    }
                    $itemId = $itemRow->ItemId;
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
            }
            $this->db->commit();
            if ($res) {
                $res = $itemId;
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * kontrola zadaného výrobního čísla kromě výdejek na operaci
     * @param type $dispatchId
     * @param type $serialNumber
     * @return type
     */
    public function checkSerialNumberOther($dispatchId, $serialNumber) {
        return $this->db->query("SELECT "
                        . "vc.ID AS IDVyrCis, "
                        . "vc.Nazev1 AS BarCode, "
                        . "CONVERT(INT, vc.Mnozstvi) AS Mnozstvi, "
                        . "CONVERT(INT, vc.Mnozstvi + vc.MnozstviKPrijmu - vc.MnozstviKVydeji) AS MnozstviPoPrijmuAVydeji, "
                        . "vc.IDStavSkladu AS IDZboSklad "
                        . "FROM TabVyrCS vc "
                        . "WHERE vc.Nazev1 = %s", $serialNumber)->fetch();
    }

    /**
     * doplní výrobní číslo do dokladu kromě výdejek na operaci
     * @param type $serialNumber
     * @param type $mnozstviPriprava
     * @return type
     */
    public function updateDispatchItemOther($dispatchId, $zboSkladId, $vyrCisloId, $mnozstviPriprava) {
        // transakce
        $res = -9;
        $this->db->begin();
        try {
            // spustit akci na přidání položky do výdejky
            $itemId = $this->db->query("SET NOCOUNT ON "
            . "DECLARE @IDPohyb INT "
            . "EXEC hpx_agrafa_sklad_vydej_pol_ost_tablet @IDPohyb = @IDPohyb OUTPUT, @IDZboSklad = %i, @IDVyrCS = %i, @Mnozstvi = %f, @IDDoklad = %i "
            . "SELECT @IDPohyb", $zboSkladId, $vyrCisloId, $mnozstviPriprava, $dispatchId)->fetchSingle();

            $this->db->commit();
            if ($res) {
                $res = $itemId;
            }
            return $res;
        } catch (DibiException $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * data pro generování štítků pro první operaci
     */
    public function getFirstOperationData($dispatchId) {
        $barCode = $this->db->query("SELECT _skl_barcode_oper FROM TabDokladyZbozi_EXT WHERE ID = %i", $dispatchId)->fetchSingle();
        return $this->db->query("SELECT "
                        . "pr.ID, "
                        . "pr.Prikaz, "
                        . "prp.ID, "
                        . "prp.BarCode, "
                        . "prp.BarCode + CASE WHEN CHARINDEX('-', ISNULL(vcp.VyrCislo,'')) > 0 THEN SUBSTRING(vcp.VyrCislo, CHARINDEX('-', ISNULL(vcp.VyrCislo,'')) + 1, 3) ELSE ISNULL(vcp.VyrCislo,'') END AS CarovyKod, "
                        . "prp.Nazev, "
                        . "vcp.VyrCislo, "
                        . "COALESCE(coop.Firma, cop.Firma, 'SKLAD') AS Odberatel, "
                        . "CASE WHEN SUBSTRING(km.SkupZbo, 1, 2) = '51' THEN ISNULL(CONVERT(NVARCHAR(20), kme._SPrumerJadra), '-') + '/U' + ISNULL(CONVERT(NVARCHAR(20), kme._STiskovyObvod), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SPracovniDelkaSleeve), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._SCelkovaDelkaSleeve), '-') WHEN SUBSTRING(km.SkupZbo, 1, 2) = '55' OR SUBSTRING(km.SkupZbo, 1, 2) = '35' THEN ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') ELSE '' END AS Dilec, "
                        . "COALESCE(doop.ParovaciZnak, do.ParovaciZnak, '') AS CisloEP, "
                        . "COALESCE(poop.Poradi, po.Poradi) AS PoradiEP "
                        . "FROM TabPrPostup prp "
                        . "JOIN TabPrikaz pr ON pr.ID = prp.IDPrikaz "
                        . "LEFT JOIN TabPrikaz_EXT pre ON pr.ID = pre.ID "
                        . "JOIN TabKmenZbozi km ON km.ID = pr.IDTabKmen "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "LEFT JOIN TabCisOrg coop ON pre._prikaz_cisloorg_oprava = coop.CisloOrg "
                        . "LEFT JOIN TabDokladyZbozi doop ON pre._prikaz_idep_oprava = doop.ID "
                        . "LEFT JOIN TabPohybyZbozi poop ON pre._prikaz_idrezervace_oprava = poop.ID "
                        . "LEFT JOIN TabPohybyZbozi po ON po.ID = pr.IDRezervace "
                        . "LEFT JOIN TabDokladyZbozi do ON do.ID = po.IDDoklad "
                        . "LEFT JOIN TabCisOrg cop ON cop.CisloOrg = do.CisloOrg "
                        . "JOIN TabVyrCisPrikaz vcp ON vcp.IDPrikaz = pr.ID "
                        . "WHERE prp.BarCode = %s", $barCode)->fetchAll();
    }

    /**
    * uzavření tísku štítků
    */
    public function dispatchLabelPrintOK($dispatchId) {
        $dispatch = $this->getDispatch($dispatchId);
        $this->db->query("UPDATE TabPrikaz_EXT SET _prikaz_skl_tisk_vydej_laminat = 1 WHERE ID = %i", $dispatch->IDPrikaz);
    }

    /**
     * uzavření výdejky
     * @param type $dispatchId
     * @return boolean
     */
    public function closeDispatch($dispatchId) {

        return $this->db->query("EXEC dbo.hpx_agrafa_sklad_uzavrit_vydej @ID = %i", $dispatchId);
    }

    /**
     * doplní položku do pracovního kusovníku TabPrKVazby
     * @param type $sn
     * @return int
     */
    public function addBOM($sn, $dispatchId) {
        $res = 0;
        $this->db->query("EXEC dbo.hpx_agrafa_sklad_dopln_kvazby_tablet @VyrCNazev = %s, @IDDoklad = %i", $sn, $dispatchId);
        return $res;
    }

    /**
     * 
     * @param type $itemId
     */
    public function removeDispatchItem($itemId, $dispatchType) {
        // transakce
        $this->db->begin();
        try {
            if ($dispatchType == 'def' || $dispatchType == 'own') {
                $res = $this->db->query("UPDATE TabPohybyZbozi "
                        . "SET Mnozstvi = 0 "
                        . "WHERE ID = %i", $itemId);
            } 
            $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                        . "SET _exp_mnozstvi_vc = 0 "
                        . "WHERE ID = %i", $itemId);
            if ($res) {
                $res = $this->db->query("DELETE FROM TabVyrCP WHERE IDPolozkaDokladu = %i", $itemId);
                // aktualizace TabPohybyZbozi.Mnozstvi
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
     * @param type $itemSNId
     */
    public function removeDispatchItemSN($itemSNId, $dispatchType) {
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
                if ($dispatchType == 'def' || $dispatchType == 'own') {
                    $res = $this->db->query("UPDATE TabPohybyZbozi "
                        . "SET Mnozstvi = Mnozstvi - %f "
                        . "WHERE ID = %i", $itemSNRow->MnozstviSN, $itemSNRow->ItemId);
                }
                $res = $this->db->query("UPDATE TabPohybyZbozi_EXT "
                        . "SET _exp_mnozstvi_vc = _exp_mnozstvi_vc - %f "
                        . "WHERE ID = %i", $itemSNRow->MnozstviSN, $itemSNRow->ItemId);
                if ($res) {
                    $res = $this->db->query("DELETE FROM TabVyrCP WHERE ID = %i", $itemSNId);
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

    /* ------------------------------ inventura ------------------------------------------- */

    /**
     * 
     * @return type
     */
    public function getTypLaminatu() {
        return $this->db->query("SELECT "
                        . "kme._typ_laminatu_id, "
                        . "clam.typ "
                        . "FROM TabStavSkladu skl "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "LEFT JOIN hvw_agrafa_cis_laminat clam ON clam.ID = kme._typ_laminatu_id "
                        . "WHERE km.SkupZbo = '151' "
                        . "ORDER BY clam.typ")->fetchPairs();
    }

    /**
     * 
     * @return type
     */
    public function getPrumer1Zao() {
        return $this->db->query("SELECT "
                        . "DISTINCT FLOOR(kme._Prumer1), FLOOR(kme._Prumer1) "
                        . "FROM TabStavSkladu skl "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "WHERE km.SkupZbo = '151' AND kme._Prumer1 IS NOT NULL AND kme._Prumer1 > 0 "
                        . "ORDER BY FLOOR(kme._Prumer1)")->fetchPairs();
    }

    /**
     * 
     * @param type $typLaminatu
     * @return type
     */
    public function getPrumer1ZaoByTyp($typLaminatu) {
        return $this->db->query("SELECT "
                        . "DISTINCT FLOOR(kme._Prumer1), FLOOR(kme._Prumer1) "
                        . "FROM TabStavSkladu skl "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "WHERE km.SkupZbo = '151' AND kme._Prumer1 IS NOT NULL AND kme._Prumer1 > 0 AND kme._typ_laminatu_id = %i "
                        . "ORDER BY FLOOR(kme._Prumer1)", $typLaminatu)->fetchPairs();
    }

    /**
     * 
     * @return type
     */
    public function getLaminat() {
        return $this->db->query("SELECT "
                        . "km.ID, "
                        . "REPLACE(clam.Typ, ' ', '') + ' ' + REPLACE(CONVERT(NVARCHAR(10), CONVERT(DECIMAL(11, 3), kme._Prumer1)), '.', ',') + CASE WHEN ISNULL(kme._Prumer2, 0) <> 0 THEN '/' + REPLACE(CONVERT(NVARCHAR(10), CONVERT(DECIMAL(11, 3), kme._Prumer2)), '.', ',') ELSE '' END + ' x ' + CONVERT(NVARCHAR(10), kme._DelkaLaminatu) "
                        . "FROM TabStavSkladu skl "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                        . "LEFT JOIN hvw_agrafa_cis_laminat clam ON clam.ID = kme._typ_laminatu_id "
                        . "WHERE km.SkupZbo = '151' "
                        . "ORDER BY Nazev1")->fetchPairs();
    }

    /**
     * 
     * @param type $typLaminatu
     * @param type $prumerZao
     * @return type
     */
    public function getLaminatByTypPrumer($typLaminatu, $prumerZao) {
        $where = "";
        if (isset($prumerZao) && $prumerZao > 0) {
            return $this->db->query("SELECT "
                            . "km.ID, "
                            . "REPLACE(clam.Typ, ' ', '') + ' ' + REPLACE(CONVERT(NVARCHAR(10), CONVERT(DECIMAL(11, 3), kme._Prumer1)), '.', ',') + CASE WHEN ISNULL(kme._Prumer2, 0) <> 0 THEN '/' + REPLACE(CONVERT(NVARCHAR(10), CONVERT(DECIMAL(11, 3), kme._Prumer2)), '.', ',') ELSE '' END + ' x ' + CONVERT(NVARCHAR(10), kme._DelkaLaminatu) "
                            . "FROM TabStavSkladu skl "
                            . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                            . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                            . "LEFT JOIN hvw_agrafa_cis_laminat clam ON clam.ID = kme._typ_laminatu_id "
                            . "WHERE km.SkupZbo = '151' AND kme._typ_laminatu_id = %i AND FLOOR(kme._Prumer1) = %i "
                            . "ORDER BY REPLACE(clam.Typ, ' ', ''), kme._Prumer1, kme._Prumer2, kme._DelkaLaminatu", $typLaminatu, $prumerZao)->fetchPairs();
        } else {
            return $this->db->query("SELECT "
                            . "km.ID, "
                            . "REPLACE(clam.Typ, ' ', '') + ' ' + REPLACE(CONVERT(NVARCHAR(10), CONVERT(DECIMAL(11, 3), kme._Prumer1)), '.', ',') + CASE WHEN ISNULL(kme._Prumer2, 0) <> 0 THEN '/' + REPLACE(CONVERT(NVARCHAR(10), CONVERT(DECIMAL(11, 3), kme._Prumer2)), '.', ',') ELSE '' END + ' x ' + CONVERT(NVARCHAR(10), kme._DelkaLaminatu) "
                            . "FROM TabStavSkladu skl "
                            . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                            . "LEFT JOIN TabKmenZbozi_EXT kme ON km.ID = kme.ID "
                            . "LEFT JOIN hvw_agrafa_cis_laminat clam ON clam.ID = kme._typ_laminatu_id "
                            . "WHERE km.SkupZbo = '151' AND kme._typ_laminatu_id = %i "
                            . "ORDER BY REPLACE(clam.Typ, ' ', ''), kme._Prumer1, kme._Prumer2, kme._DelkaLaminatu", $typLaminatu)->fetchPairs();
        }
    }

    /**
     * generování VČ materiálu (laminát)
     * @param type $idKmenZbozi
     * @return type
     */
    public function generateSNKmen($idKmenZbozi) {
        $stockId = $this->db->query("SELECT ID FROM TabStavSkladu WHERE IDKmenZbozi = %i AND IDSklad = '10000100'", $idKmenZbozi)->fetchSingle();
        $sn = $this->db->query("SELECT "
                        . "RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) + RIGHT('0000' +CONVERT(NVARCHAR(4), MAX(CONVERT(INT, SUBSTRING(vcs.Nazev1, 8, 4))) + 1), 4) "
                        . "FROM TabVyrCS vcs  "
                        . "JOIN TabStavSkladu skl ON skl.ID = vcs.IDStavSkladu "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "WHERE vcs.IDStavSkladu = %i AND SUBSTRING(vcs.Nazev1, 1, 6) = RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) "
                        . "GROUP BY vcs.IDStavSkladu, RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6)", $stockId)->fetchSingle();
        if (!$sn) {
            $sn = $this->db->query("SELECT "
                            . "RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) + '0001' "
                            . "FROM TabKmenZbozi km "
                            . "JOIN TabStavSkladu skl ON km.ID = skl.IDKmenZbozi "
                            . "WHERE skl.ID = %i ", $stockId)->fetchSingle();
        }
        return $sn;
    }
    
    /**
     * zpracování inventurní položky - startovací inventura!!!
     * @param type $vyrCislo
     * @param type $idKmenZbozi
     * @return int
     */
    public function processLamInventorySN($vyrCislo, $idKmenZbozi, $tisknoutStitek) {
        $res = 0;
        $res = $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @Res INT "
                        . "EXEC hpx_agrafa_sklad_inv_start_zpracuj @VyrCislo = %s, @IdKmenZbozi = %i, @TisknoutStitek = %b, @Res = @Res OUTPUT "
                        . "SELECT @Res", $vyrCislo, $idKmenZbozi, $tisknoutStitek)->fetchSingle();
        return $res;
        
    }

    /** -------------------- standardní inventura ------------------------------- */
    public function getInventoryList() {
        return $this->db->query("SELECT ID, Popis + ' (' + CONVERT(NVARCHAR(20), DatumGenerovani, 104) + ')' AS Nazev FROM TabInvHead WHERE IDSklad = '10000100' AND DatumClose IS NULL")->fetchPairs();
    }

    public function getInventoryHd($invHdId) {
        return $this->db->query("SELECT TabInvHead.ID, TabInvHead.Popis + ' (' + CONVERT(NVARCHAR(20), TabInvHead.DatumGenerovani, 104) + ')' AS Nazev, TabInvHead_EXT._inv_skupzbo AS InvSkupZbo FROM TabInvHead LEFT JOIN TabInvHead_EXT ON TabInvHead.ID = TabInvHead_EXT.ID WHERE TabInvHead.ID = %i", $invHdId)->fetch();
    }

    /**
     * 
     */
    public function getMaterialInv($skupZbo) {
        $res = $this->db->query("SELECT skl.ID, km.Nazev1 
            FROM TabStavSkladu skl 
            LEFT JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi 
            WHERE skl.IDSklad = '10000100' AND km.Skupzbo = %s 
            ORDER BY km.Nazev1", $skupZbo)->fetchPairs();
        return $res;
    }

    /**
     * zpracování inventurní položky - první inventura!!!
     * @param type $vyrCislo
     * @param type $idKmenZbozi
     * @return int
     */
    public function checkStdInventorySN($vyrCislo) {
        $res = $this->db->query("SELECT skl.ID 
            FROM TabVyrCS vcs 
            JOIN TabStavSkladu skl ON skl.ID = vcs.IDStavSkladu 
            WHERE vcs.Nazev1 = %s AND skl.IDSklad = '10000100'", $vyrCislo)->fetchSingle();
        if (!$res) {
            $res = -1;
        }
        return $res;
        
    }

    public function getInvItemInfo($idZboSklad) {
        return $this->db->query("SELECT km.SkupZbo AS SkupZbo 
            FROM TabStavSkladu skl 
            JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi 
            WHERE skl.ID = %i", $idZboSklad)->fetchSingle();
    }

    /**
     * generování VČ materiálu podle IDZboSklad
     * @param type $idZboSklad
     * @return type
     */
    public function generateSNSklad($idZboSklad) {
        $sn = $this->db->query("SELECT "
                        . "RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) + RIGHT('0000' +CONVERT(NVARCHAR(4), MAX(CONVERT(INT, SUBSTRING(vcs.Nazev1, 8, 4))) + 1), 4) "
                        . "FROM TabVyrCS vcs  "
                        . "JOIN TabStavSkladu skl ON skl.ID = vcs.IDStavSkladu "
                        . "JOIN TabKmenZbozi km ON km.ID = skl.IDKmenZbozi "
                        . "WHERE vcs.IDStavSkladu = %i AND SUBSTRING(vcs.Nazev1, 1, 6) = RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) "
                        . "GROUP BY vcs.IDStavSkladu, RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6)", $idZboSklad)->fetchSingle();
        if (!$sn) {
            $sn = $this->db->query("SELECT "
                            . "RIGHT('000000'+ CONVERT(NVARCHAR(6), km.ID), 6) + '0001' "
                            . "FROM TabKmenZbozi km "
                            . "JOIN TabStavSkladu skl ON km.ID = skl.IDKmenZbozi "
                            . "WHERE skl.ID = %i ", $idZboSklad)->fetchSingle();
        }
        return $sn;
    }
    
    /**
     * zpracování inventurní položky - standardní inventura
     * @param type $vyrCislo
     * @param type $idZboSklad
     * @param type $mnozstvi
     * @param type $tisknoutStitek
     * @return int
     */
    public function processStdInventorySN($invHdId, $vyrCislo, $idZboSklad, $mnozstvi, $tisknoutStitek) {
        $res = 0;
        $res = $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @Res INT "
                        . "EXEC hpx_agrafa_sklad_inv_std_zpracuj @IDInvHd = %i, @VyrCislo = %s, @IdZboSklad = %i, @Mnozstvi = %f, @TisknoutStitek = %b, @Res = @Res OUTPUT "
                        . "SELECT @Res", $invHdId, $vyrCislo, $idZboSklad, $mnozstvi, $tisknoutStitek)->fetchSingle();
        return $res;
        
    }

}
