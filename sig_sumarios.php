<?php
/*************************************************************************************************/
/*                                  Módulo de sumários Moodle-Sigarra                            */
/*                           Integração do sumários do Sigarra-UP no Moodle-UP                   */
/*    Nuno Barbosa CCa - Faculdade de Ciências da U.Porto, Susana Leitão - Reitoria da U.Porto   */
/* Notas:                                                                                        */
/* Efetua a introdução, edição e remoção dos sumarios, provenientes do SIGARRA, na BD do Moodle  */
/*************************************************************************************************/

// para acesso à base-de-dados
require_once("../../config.php");
require_once("sig_config.php");
require_once("sig_lib.php");

global $CFG, $CFGSIG;

// tipo de acção
$action = $_POST['accao'];

// dados do sumário
$course_id = $_POST['shortname'];  // Shortname em mdl_course
$uc_sigarra_id = $_POST['id_sigarra']; // Identificacao SIGARRA da UC
$date_time = $_POST['data_hora'];  // Data e hora
$type = $_POST['tipo'];            // Tipo ex.: T; TP; P...
$n_aula = $_POST['n_aula'];
$text = $_POST['texto'];

$text = stripslashes($text);
$text = addslashes($text);

// O sumário tem de se verificar se está em utf8
if(!is_utf8($_POST['texto'])) { // se não for, codifica
    $text = utf8_encode($_POST['texto']);
//    $text.= ' CODIFICADO!';
} else {
    $text = $_POST['texto'];
//    $text.= ' INTACTO!';
}


// N.º da turma
if($_POST['turma_sigla'] != "")
    $class_num = $_POST['turma_sigla'];

// Faz login
$sum = new sumario(get_client_ip());

$conditions = array('uc_sigarra_id' => $uc_sigarra_id, 'data_aula' => $date_time, 'tipo' => $type, 'sigla_turma' => $class_num, 'n_aula' => $n_aula);

if($sum->isok()) {
    $table = $CFGSIG->prefix . $CFGSIG->table_name;
    $dataobj = new StdClass;
    $dataobj->uc_sigarra_id = $uc_sigarra_id;
    $dataobj->shortname = $course_id;
    $dataobj->data_aula = $date_time;
    $dataobj->tipo = $type;
    $dataobj->sigla_turma = $class_num;
    $dataobj->n_aula = $n_aula;
    $dataobj->texto = $text;
    
    if($sum->execute_action($table,$dataobj,$action,$conditions)) {
        echo "Ok";
    } else {
        echo "Não foi possivel executar a acção...";
    }
}
?>
