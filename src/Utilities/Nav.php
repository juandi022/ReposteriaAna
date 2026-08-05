<?php

namespace Utilities;

class Nav {

    public static function setNavContext(){
        $tmpNAVIGATION = array();
        $userID = \Utilities\Security::getUserId();
        if (\Utilities\Security::isAuthorized($userID, "Controllers\\Compras\\ComprasList")) {
            $tmpNAVIGATION[] = array(
                "nav_url" => "index.php?page=Compras-ComprasList",
                "nav_label" => "Compras"
            );
        }
        if (\Utilities\Security::isAuthorized($userID, "Controllers\\Historial\\TransaccionesList")) {
            $tmpNAVIGATION[] = array(
                "nav_url" => "index.php?page=Historial-TransaccionesList",
                "nav_label" => "Historial"
            );
        }
       
        \Utilities\Context::setContext("NAVIGATION", $tmpNAVIGATION);
    }


    private function __construct()
    {
        
    }
    private function __clone()
    {
        
    }
}
?>
