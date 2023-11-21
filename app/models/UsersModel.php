<?php

namespace App\Models;

use Nette,
    Nette\Security\Passwords;

class UsersModel extends BaseModel implements Nette\Security\IAuthenticator {

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username) = $credentials;

        $auth = $this->db->query("SELECT zam.ID AS id, "
                        . "zam.Cislo AS username, "
                        . "CONCAT(zam.Jmeno, ' ', zam.Prijmeni) AS fullname,"
                        . "CASE WHEN ISNULL(zame._intranet_exp, 0) = 1 THEN 'expedice' ELSE '' END AS exp,"
                        . "CASE WHEN ISNULL(zame._intranet_prijem_mat, 0) = 1 THEN 'skladMat' ELSE '' END AS skladmat,"
                        . "CASE WHEN ISNULL(zame._intranet_prijem_matzak, 0) = 1 THEN 'prijMatZak' ELSE '' END AS prijmatzak,"
                        . "zame._intranet_vychozi AS vychozi "
                        . "FROM TabCisZam zam "
                        . "LEFT JOIN TabCisZam_EXT zame ON zam.ID = zame.ID "
                        . "WHERE zam.Cislo = %i", $username)->fetch();

        if (!$auth) {
            throw new Nette\Security\AuthenticationException('Chybné uživatelské jméno (mail).', self::IDENTITY_NOT_FOUND);
        }
        $roles = array();
        if ($auth->exp != "") {
            array_push($roles, $auth->exp);
        }
        if ($auth->skladmat != "") {
            array_push($roles, $auth->skladmat);
        }
        if ($auth->prijmatzak != "") {
            array_push($roles, $auth->prijmatzak);
        }
        

        return new Nette\Security\Identity($auth->id, $roles, $auth);
    }

    /**
     * vraci udaje o jednom vybranem uzivateli podle id
     * @param type $id
     * @return type
     * @throws \DibiException
     */
    public function getSingle($id) { // userManager
        try {
            return $this->db->query("SELECT zam.ID AS id, "
                        . "zam.Cislo AS username, "
                        . "zam.PrijmeniJmeno AS fullname "
                        . "FROM TabCisZam zam "
                        . "WHERE zam.ID = %i", $id)->fetch();
        } catch (\DibiException $e) {
            throw $e;
        }
    }

    /**
     * vraci udaje o všech uzivatelich
     * @return type
     * @throws \DibiException
     */
    public function getAll() {
        try {
            return $this->db->query("SELECT us.ID AS id, "
                            . "us.FullName AS username, "
                            . "us.Email AS mail, "
                            . "usx._intranet_login AS login, "
                            . "usx._intranet_typ AS usertype, "
                            . "usx._intranet_aktivni AS active "
                            . "FROM TabUserCfg us "
                            . "LEFT JOIN TabUserCfg_EXT usx ON us.ID = usx.ID ")->fetchAll();
        } catch (\DibiException $e) {
            throw $e;
        }
    }

    /**
     * odstranění uživatele intranet - smazání externích údajů
     * @param type $id
     * @throws \DOMException
     */
    public function updateUser($id, $values) {
        $this->db->begin();
        try {
            $isExt = $this->db->query("SELECT ID FROM TabUserCfg_EXT WHERE ID = %i", $id)->fetch();
            if (isset($isExt->ID)) {
                $this->db->query("UPDATE TabUserCfg_EXT "
                        . "SET "
                        . "_intranet_login = %s, "
                        . "_intranet_typ = %i, "
                        . "_intranet_aktivni = 1"
                        . "WHERE id = %i", $values->login, $values->usertype, $id);
            } else {
                $this->db->query("INSERT INTO TabUserCfg_EXT (ID, _intranet_login, _intranet_typ, _intranet_aktivni) "
                        . "VALUES (%i, %s, %i, 1)", $id, $values->login, $values->usertype);
                
            }
            $this->db->commit();
        } catch (\DOMException $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * odstranění uživatele intranet - smazání externích údajů
     * @param type $id
     * @throws \DOMException
     */
    public function delete($id) {
        $this->db->begin();
        try {
            $this->db->query("UPDATE TabUserCfg_EXT "
                    . "SET "
                    . "_intranet_login = NULL, "
                    . "_intranet_typ = NULL, "
                    . "_intranet_aktivni = NULL "
                    . "WHERE id = %i", $id);
            $this->db->commit();
        } catch (\DOMException $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * vyber uzivatele podle mailu
     * @param type $userMail
     * @return type
     * @throws \DibiException
     */
    public function getSingleByMail($userMail) { // resetPassword, users
        try {
            return $this->db->query("SELECT * FROM [users] WHERE [mail] = %s", $userMail)->fetch();
        } catch (\DibiException $e) {
            throw $e;
        }
    }

    /**
     * vyber uzivatele podle jména
     * @param type $userName
     * @return type
     * @throws \DibiException
     */
    public function getSingleByName($userName) { // resetPassword, users
        try {
            return $this->db->query("SELECT us.ID AS id, "
                            . "us.FullName AS username, "
                            . "us.Email AS mail, "
                            . "usx._intranet_login AS login, "
                            . "usx._intranet_typ AS usertype, "
                            . "usx._intranet_aktivni AS active "
                            . "FROM TabUserCfg us "
                            . "LEFT JOIN TabUserCfg_EXT usx ON us.ID = usx.ID "
                            . "WHERE usx._intranet_login = %s", $userName)->fetch();
        } catch (\DibiException $e) {
            throw $e;
        }
    }

    /**
     * pozadavek na reset hesla - zaznam tokenu a jeho platnosti
     * @param type $id
     * @param type $values
     */
    public function resetPasswordUpdate($id, $values) { // resetPassword
        $this->db->query("UPDATE TabUserCfg_EXT SET _intranet_token = %s, _intranet_token_platido = %d WHERE [id]=%i", $values->token, $values->validTo, $id);
    }

    /**
     * zmena hesla
     * @param type $id
     * @param type $values
     */
    public function updatePassword($id, $values) { // resetPassword
        $this->db->query("UPDATE TabUserCfg_EXT SET _intranet_password = %s, _intranet_aktivni = %i, _intranet_toke_platido  = %d WHERE ID = %i", $values->password, $values->active, $values->validTo, $id);
        if ($values->token != "0") {
            $this->db->query("UPDATE TabUserCfg_EXT SET _intranet_aktivni = %i WHERE ID = %i", $values->active, $id);
        }
    }

    /**
     * seznam jazyku pro vyber
     * @return type
     */
    public function getLanguages() { // consumer.base, sign, admin.base, resetPassword
        //return $this->db->query("SELECT * FROM driver.languages ORDER BY name")->fetchAll();
    }

    /**
     * odblokovani uzivatele po chybnych prihlasenich
     * @param type $userMail
     */
    public function unblockUser($userMail) {
        $this->db->query("DELETE FROM lockedUsers WHERE mail = %s", $userMail);
    }

    public function insertUsersLog($id, $projectId, $action) { // consumer.base, sign, meter, admin.base
        $this->db->query("INSERT INTO usersLog ([tstamp], [tstampwo], [action], [user_id]) VALUES (GETDATE(), GETDATE(), %s, %i, %i)", $action, $id, $projectId);
    }

    /**
     * změna hesla
     * @param type $values
     * @return type
     */
    public function setPassword($values) {
        return $this->db->query("UPDATE TabUserCfg_EXT SET _intranet_password = %s WHERE id = %i", Passwords::hash($values->password), $values->id);
    }

}
