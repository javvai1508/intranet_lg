<?php

namespace App\Models;

use Nette;

class CustMaterialModel extends BaseModel {

    /**
     * našeptávač firma
     * @param type $subStr
     * @return type
     */
    public function getAutocompleteFirma($subStr) {
        return $this->db->query("SELECT Firma + ', ' + Misto + ' (' + CONVERT(NVARCHAR(10), CisloOrg) + ')' AS Firma FROM TabCisOrg WHERE (Firma LIKE %~like~ OR Misto LIKE %~like~) AND ISNULL(Fakturacni, 0) = 1 AND Stav = 0 AND ISNULL(JeOdberatel, 0) = 1 ORDER BY Firma + ', ' + Misto + ' (' + CONVERT(NVARCHAR(10), CisloOrg) + ')'", $subStr, $subStr)->fetchAll();
    }

    /**
     * uložení nového materiálu zákazníka
     * @param type $values
     */
    public function saveNewCustomerMaterial($values) {
        if ($values->VCepL == '') {
            $values->VCepL = NULL;
        }
        if ($values->VCepP == '') {
            $values->VCepP = NULL;
        }
        return $this->db->query("SET NOCOUNT ON "
                        . "DECLARE @IDDoklad INT "
                        . "EXEC hpx_agrafa_matzak_prijem @IDDoklad = @IDDoklad OUTPUT, @PouzitiKarty = 1, @CisloOrg = %i, @Mnozstvi = %i, @Nazev = %s, @VPrumerJadra = %f, @VPrumerPogumu = %f, @VDelkaPogumu = %f, @VDelkaValce = %f, @VCepL = %f, @VCepP = %f, @Poznamka = %s, @TisknoutKusy = %i, @IDZboSklad = NULL "
                        . "SELECT @IDDoklad", $values->CisloOrg, $values->Mnozstvi, $values->Nazev, $values->VPrumerJadra, $values->VPrumerPogumu, $values->VDelkaPogumu, $values->VDelkaValce, $values->VCepL, $values->VCepP, $values->Poznamka, $values->TisknoutKusy)->fetchSingle();
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
     * vrátí podklad pro tisk štítku
     * @param type $custMatId
     * @return type
     */
    public function getReceiptItem($custMatId) {
        return $this->db->query("SELECT TOP 1 " 
                . "do.ID, "
                . "CONVERT(NVARCHAR(20), do.DatPorizeni, 104) AS DatPorizeni,  "
                . "co.Firma,"
                . "po.SkupZbo + po.RegCis AS RegCis, "
                . "po.Nazev1,"
                . "ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerJadra), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPrumerPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaPogumu), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VPracDelka), '-') + '/' + ISNULL(CONVERT(NVARCHAR(20), kme._VDelkaValce), '-') AS Rozmery, "
                . "po.Mnozstvi AS Mnozstvi "
                . "FROM TabPohybyZbozi po "
                . "JOIN TabDokladyZbozi do ON po.IDDoklad = do.ID "
                . "JOIN TabStavSkladu skl ON po.IDZboSklad = skl.ID "
                . "JOIN TabKmenZbozi km ON skl.IDKmenZbozi = km.ID "
                . "LEFT JOIN TabKmenZbozi_EXT kme ON kme.ID = km.ID "
                . "LEFT JOIN TabCisOrg co ON do.CisloOrg = co.CisloOrg "
                . "WHERE po.IDDoklad = %i", $custMatId)->fetch();
    }
}
