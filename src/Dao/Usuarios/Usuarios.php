<?php

namespace Dao\Usuarios;

use Dao\Table;
use DateTime;

class Usuarios extends Table{

    public static function getUsuarios(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ){

        $sqlstr = "SELECT u.usercod, u.useremail, u.username, u.userpswd, u.userfching, u.userpswdest, u.userpswdexp, u.userest, u.useractcod, u.userpswdchg, u.usertipo,
        CASE WHEN u.userest = 'ACT' then 'Activo'
        WHEN u.userest = 'INA' then 'Inactivo'
        ELSE 'Sin Asignar'
        END AS userestDesc,

        CASE WHEN u.userpswdest = 'ACT' then 'Activo'
        WHEN u.userpswdest = 'INA' THEN 'Inactivo'
        ELSE 'Sin Asignar'
        END AS userpswdestDesc,

        CASE WHEN u.usertipo = 'NOR' THEN 'Normal'
        WHEN u.usertipo = 'CON' THEN 'Consultor'
        WHEN u.usertipo = 'CLI' THEN 'Cliente'
        ELSE 'Sin Asignar'
        END AS usertipoDesc 
        FROM usuario u";

        $sqlstrCount = "SELECT COUNT(*) AS count FROM usuario u";

        $conditions = [];

        $params = [];

        if($partialName != ""){
            $conditions[] = "useremail LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%"; 
        }

        if(!in_array($status, ["ACT", "INA", ""])){
            throw new \Exception("Error Procesando la Petición, Estado Tiene un Valor Inválido");
        }

        if($status != ""){
            $conditions[] = "userest = :status";
            $params["status"] = $status;
        }

        if(count($conditions) > 0){
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if(!in_array($orderBy, ["usercod", "useremail", "userest", "usertipo", ""])){
            throw new \Exception("Error Procesando la Petición, OrderBy Tiene un Valor Inválido");
        }

        if($orderBy != ""){
            $sqlstr .= " ORDER BY " . $orderBy;

            if($orderDescending){
                $sqlstr .= " DESC";
            }
        }

        $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
        $pagesCount = ceil($numeroDeRegistros / $itemsPerPage);

        if($page > $pagesCount - 1){
            $page = $pagesCount - 1;
        }

        $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);
        return [
            "usuarios" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
            ];
    }

    public static function getUsuarioById(int $usuarioCod){
        $sqlstr = "SELECT usercod, useremail, username, userpswd, userfching, userpswdest, userpswdexp, userest, useractcod, userpswdchg, usertipo
        FROM usuario
        WHERE usercod = :usuarioCod";

        $params = ["usuarioCod" => $usuarioCod];

        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function getUsuariosForOptions(){
        $sqlstr = "SELECT u.usercod, u.useremail, u.username
        FROM usuario u
        WHERE u.userest = 'ACT'
        ORDER BY u.useremail";

        return self::obtenerRegistros($sqlstr, array());
    }

    public static function insertUsuario(
            string $useremail,
            string $username,
            string $userpswd,
            DateTime $userfching,
            string $userpswdest,
            DateTime $userpswdexp,
            string $userest,
            string $useractcod,
            string $userpswdchg,
            string $usertipo
        ) {
            $sqlstr = "INSERT INTO usuario (useremail, username, userpswd,
            userfching, userpswdest, userpswdexp, userest, useractcod, userpswdchg, usertipo)
            VALUES (:useremail, :username, :userpswd,
            :userfching, :userpswdest, :userpswdexp, :userest, :useractcod,
            :userpswdchg, :usertipo)";
            $params = [
                "useremail" => $useremail,
                "username" => $username,
                "userpswd" => $userpswd,
                "userfching" => $userfching->format('Y-m-d H:i:s'),
                "userpswdest" => $userpswdest,
                "userpswdexp" => $userpswdexp->format('Y-m-d H:i:s'),
                "userest" => $userest,
                "useractcod" => $useractcod,
                "userpswdchg" => $userpswdchg,
                "usertipo" => $usertipo
            ];
            return self::executeNonQuery($sqlstr, $params);
        }

        public static function updateUsuario(
            int $usercod,
            string $useremail,
            string $username,
            string $userpswd,
            DateTime $userfching,
            string $userpswdest,
            DateTime $userpswdexp,
            string $userest,
            string $useractcod,
            string $userpswdchg,
            string $usertipo
        ) {
            $sqlstr = "UPDATE usuario SET useremail = :useremail,
            username = :username, userpswd = :userpswd, userfching = :userfching,
            userpswdest = :userpswdest, userpswdexp = :userpswdexp, userest = :userest,
            useractcod = :useractcod, userpswdchg = :userpswdchg, usertipo = :usertipo
            WHERE usercod = :usercod";
            $params = [
                "usercod" => $usercod,
                "useremail" => $useremail,
                "username" => $username,
                "userpswd" => $userpswd,
                "userfching" => $userfching->format('Y-m-d H:i:s'),
                "userpswdest" => $userpswdest,
                "userpswdexp" => $userpswdexp->format('Y-m-d H:i:s'),
                "userest" => $userest,
                "useractcod" => $useractcod,
                "userpswdchg" => $userpswdchg,
                "usertipo" => $usertipo
            ];
            return self::executeNonQuery($sqlstr, $params);
        }

        public static function deleteUsuario(int $usercod)
        {
            $sqlstr = "DELETE FROM usuario WHERE usercod = :usercod";
            $params = ["usercod" => $usercod];
            return self::executeNonQuery($sqlstr, $params);
        }
}
?>