<?php
/*************************************************************************************************/
/*                                  Módulo de sumários Moodle-Sigarra                            */
/*                           Integração do sumários do Sigarra-UP no Moodle-UP                   */
/*    Nuno Barbosa CCa - Faculdade de Ciências da U.Porto, Susana Leitão - Reitoria da U.Porto   */
/* Notas:                                                                                        */
/* Este ficheiro contém dados de configuração                                                    */
/*************************************************************************************************/

unset($CFGSIG);
$CFGSIG = new stdClass();

// Configurações relativas à bd
$CFGSIG->prefix = "sig_";
$CFGSIG->table_name = "sumarios";

// Ip's
$CFGSIG->ips = array('127.0.0.1','192.168.216.1','193.137.30.11','193.137.54.119','193.137.33.210','172.16.20.184','192.168.216.6','192.168.212.48','192.168.212.26','192.168.212.56','193.136.37.163','192.168.212.64','193.136.37.172','192.168.212.31','192.168.212.33','192.168.141.93','193.137.54.14');
?>
