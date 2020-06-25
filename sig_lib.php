<?php
/*************************************************************************************************/
/*                                  Módulo de sumários Moodle-Sigarra                            */
/*                           Integração do sumários do Sigarra-UP no Moodle-UP                   */
/*    Nuno Barbosa CCa - Faculdade de Ciências da U.Porto, Susana Leitão - Reitoria da U.Porto   */
/* Notas:                                                                                       */
/* Este ficheiro contém dados de configuração e funções auxiliares para a importação de sumários */
/*************************************************************************************************/

require_once("../../config.php");
require_once("sig_config.php");

// Códigos de retorno
$codes = array("OK"            => 0, 
               "LOGIN_FAILED"  => 100, 
               "LOGOUT_FAILED" => 101, 
               "DB_FAILED"     => 200, 
               "QUERY_FAILED"  => 300);


Class sumario {
    /**
     * Construtor
     * @param ip da máquina de origem
     **/
    function __construct($ip)  {
        if($this->valid_ip($ip)) {
        } else
            die("Não permitido IP $ip");
    }
    
    /**
     * Verifica se se a classe está bem definida
     * @return True caso esteja, False caso contrário
     **/
    function isok() {
        return True;
    }

    /**
     * Executa a acção
     * @param string $table é o nome da tabela
     * @param obj $dataobject são os dados a ser inseridos
     * @param int $action é a ação a efetuar (1,2,3)
     * @param array $conditions são as condições para procurar o registo
     * @return OK se tudo correr bem, código de erro caso contrário
     **/
    function execute_action($table,$dataobject,$action,$conditions) {
        global $codes, $DB;
        /**
         * Acções possiveis sobre os registos:
         * 1 - Adicionar
         * 2 - Alterar
         * 3 - Remover
        **/ 
        switch($action) {
            case 1:
                if (!$DB->record_exists($table, $conditions)) {
                    $return = $DB->insert_record($table, $dataobject);
                } else {
                    $return = FALSE;
                }
            break;
            case 2:
                if ($DB->record_exists($table, $conditions)) {
                    $dataobject->id = $DB->get_field($table,'id', $conditions);
                    print_r($conditions); echo " DATAID:$dataobject->id";
                    $return = $DB->update_record($table, $dataobject);
                } else {
                    $return = FALSE;
                }
            break;
            case 3:
                if ($DB->record_exists($table, $conditions)) {
                   $dataobject->id = $DB->get_field($table,'id', $conditions);
                   $return = $DB->delete_records($table, array('id'=>$dataobject->id));
                } else {
                    $return = FALSE;
                }
            break;
        } 
        return ($return);
        
    }
    
    /**
     * Verifica se é um ip válido
     * @param $ip referente ao ip a ser testado
     * @return True se for um ip válido, False caso contrário
     **/
    function valid_ip($ip) {
        global $CFGSIG;
        return in_array($ip,$CFGSIG->ips);
    }
}
/**
 * Verifcica se se uma string é utf8 válida
 * @param [mixed] $string     string a ser testada
 * @return True caso seja, False caso contrário
 **/
function is_utf8($string) {
  
    return preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
  
}
function get_client_ip() {
    if ($CFG->reverseproxy) {
    	$ip = $_SERVER['REMOTE_ADDR'];
    }
    else {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$ip = trim(end(explode(", ",$_SERVER['HTTP_X_FORWARDED_FOR'])));
    }
    return $ip;
}
?>
