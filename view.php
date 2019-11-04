<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of summaries
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage summaries
 * @copyright  2012 Nuno Barbosa, Susana Leitão
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once('sig_config.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // summaries instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('summaries', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $summaries  = $DB->get_record('summaries', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $summaries  = $DB->get_record('summaries', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $summaries->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('summaries', $summaries->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

// Atribui is_meta se a UC é uma Meta-disciplina e guarda array de filhos da meta
$is_meta = false;
$meta_sons = array();
$enrol_instances = enrol_get_instances($course->id, true);
foreach ($enrol_instances as $ei){
    if ($ei->enrol=='meta') { 
        $is_meta = true;
        $son_course = $DB->get_record('course',array('id'=>$ei->customint1));
        array_push($meta_sons,$son_course);
    }
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

//add_to_log($course->id, 'summaries', 'view', "view.php?id={$cm->id}", $summaries->name, $cm->id);

$event = \mod_summaries\event\course_module_viewed::create(array('objectid' => $PAGE->cm->instance,'context' => $PAGE->context));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot($cm->modname, $summaries);
$event->trigger();


/// Print the page header

$PAGE->set_url('/mod/summaries/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($summaries->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('summaries-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($summaries->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('summaries', $summaries, $cm->id), 'generalbox mod_introbox', 'summariesintro');
}
echo $OUTPUT->heading($summaries->name);

/// Print the main part of the page
	
	////
	// Existem 3 casos que vão ser analisados
	// 1- Consulta de um só sumário
	// 2- Consulta de todos os sumários de uma turma
	// 3- Caso não esteja definido os casos anteriores, mostra links para todas as turmas
	///
	
    // Verifica se está a ser consultado um ou vários
	// se $_GET['single'] estiver definido então é só a consulta de um
	if(isset($_GET['single'])) {
		$id_sumario = $_GET['single'];
		$OUTPUT->heading('GET single');
		
		// Consulta
		if(!($q = get_record($CFGSIG->prefix . $CFGSIG->table_name,'id',$id_sumario))) {
			?>
			<center><?php print_string('nosummary','summaries');?></center>
			<?php
			$OUTPUT->heading('No summary');
		}
		// só mostra se for visivel e existir
		if(isset($q) && $q->visivel) {
			$OUTPUT->heading('Visível');
		?>
		<center>
		<div id="apresentacao_sumarios" style="padding-top:20px;">
			<table width="600" class="flexible generaltable generalbox">
				<tr valign="middle">
					<td align="left">
						<div class="title">
							<h2><?php echo $q->sigla_turma . " " . $q->tipo; ?></h2>
						</div>
					</td>
				</tr>
				<tr valign="middle">
					<td align="left">
						<?php
							// pré-processamento da data
							$aux = explode(' ',$q->data_aula);
							$aux1 = explode('-',$aux[0]);

							$data = $aux1[2] . '-' . $aux1[1] . '-' . $aux1[0];

							// pré-processamento da hora
							$aux2 = explode(':',$aux[1]);
							if($aux2[0] == '00')
								$hora = '';
							else
								$hora = '/ ' . $aux2[0] . ':' . $aux2[1];

							$cabecalho->n_aula = $q->n_aula;
							$cabecalho->data    = $data;
							$cabecalho->hora    = $hora;
						?>
						<div class="clearfix generalbox">
						<b><?php print_string('summaryfrom','summaries', $cabecalho);?></b>
					</div>
					</td>
				</tr>
				<tr>
					<td align="justify">
						<div id="summaries" class="box generalbox generalboxcontent boxaligncenter">
							<?php echo format_text($q->texto); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		</center>
	<?php
		} else { 
			$OUTPUT->heading('Não há possibilidade de ver sumário');
			// informa o utilizador que não há possibilidade de ver sumário ?>
		<center>
		<div id="apresentacao_sumarios" style="padding-top:20px;">
			<table width="600" class="flexible generaltable generalbox">
				<tr valign="middle">
					<td align="center">
						<b><?php print_string('summarynotvisible','summaries') ?></b>
					</td>
				</tr>
			</table>
		</div>
		</center>
	<?php
		}
	}
	 else
		if(isset($_GET['sigla_turma'])) {
			$OUTPUT->heading('Vamos consultar todos os sumários visiveis de uma turma...');
			$sigla = $_GET['sigla_turma'];
			$tipo  = $_GET['tipo'];
			// No caso de estar definido shortname, estamos numa meta-disciplinas
			if(isset($_GET['shortname']))
				$shortname = $_GET['shortname'];
			else // Caso contrário, estamos numa cadeira normal
				$shortname = $COURSE->shortname;
			// Primeiro devemos escolher de que turma devemos ver os sumários
			$query = "SELECT * FROM " . $CFG->prefix . $CFGSIG->prefix . $CFGSIG->table_name 
						 . " WHERE shortname = '" . $shortname . "'" . " AND tipo = '" . $tipo . "'" 
						 . " AND sigla_turma = '" . $sigla . "' ORDER BY data_aula, n_aula" ;
			$res     = $DB->get_records_sql($query);
			
			// Esta variável conta a quantidade de sumários disponíveis
			// Se no final for = a 0 então não existem sumários visiveis
			$count = 0;
			
			// agora imprimimos os sumários
			echo '<center>
							<table width="600">
								<tr valign="top"><td>&nbsp;</td></tr>
								<tr valign="middle">
									<td align="center">
										<b>' . $sigla . ' ' . $tipo . '</b>
									</td>
								</tr>
								<tr valign="top"><td>&nbsp;</td></tr>';
			foreach($res as $r)
				if($r->visivel) {
				?>
				<tr valign="middle">
					<td align="left">
						<?php
							// pré-processamento da data
							$aux = explode(' ',$r->data_aula);
							$aux1 = explode('-',$aux[0]);
							
							$data = $aux1[2] . '-' . $aux1[1] . '-' . $aux1[0];
							
							// pré-processamento da hora
							$aux2 = explode(':',$aux[1]);
							if($aux2[0] == '00')
								$hora = '';
							else
								$hora = '/ ' . $aux2[0] . ':' . $aux2[1];
							
							$cabecalho = new stdClass();
							$cabecalho->n_aula = $r->n_aula;
							$cabecalho->data    = $data;
							$cabecalho->hora    = $hora;
						?>
						<div class="clearfix boxaligncenter" style="margin-top:15px">
						<b><?php print_string('summaryfrom','summaries', $cabecalho);?></b>
						</div>
					</td>
				</tr>
				<tr>
					<td align="justify">
						<div id="summaries" class="box generalbox generalboxcontent boxaligncenter">
							<?php echo format_text(stripslashes($r->texto)); ?>
						</div>
					</td>
				</tr>
			<?php 
				$count++;
			}
			if(!$count) {
				?>
				<tr valign="middle">
					<td align="center">
						<b><?php print_string('summariesnotvisibles','summaries') ?></b>
					</td>
				</tr>
				<?php
			}
			echo '</table></center>';
	} else { // Último caso, lista links para os sumários das turmas
		// 1- É necessário ver a turmas todas por ordem lógica:
		// T -> TP -> P -> Extra ->Sub (Foi alterado, linha abaixo)
		/* Adicionado em 09/12/2008 */
		// Permite que haja mais tipos de aulas
		$OUTPUT->heading('Lista links para os sumários das turmas');
		$tipos = array();
                $turmas = new stdClass();

                if(!$is_meta) {
			// Permite que haja mais tipos de aulas
			$query = "SELECT DISTINCT tipo FROM " . $CFG->prefix . $CFGSIG->prefix
							 . $CFGSIG->table_name . " WHERE shortname = '" . $COURSE->shortname . "'";
			$ver_tipos = $DB->get_records_sql($query);
			// Vai criar um array de todos os tipos que de facto existem
			$i = 0;
			if($ver_tipos != NULL)
				foreach($ver_tipos as $tipo) $tipos[$i++] = $tipo->tipo;
			else
				$erro = get_string('no_classes','summaries',$COURSE->fullname);
		} else {
                 	/* Adicionado na v.2 para aceder a meta-disciplinas */
			//$meta_sons = array();
			//$meta_sons = get_courses_in_metacourse($COURSE->id);
                        $i         = 0;
			foreach($meta_sons as $son) {		
				$query = "SELECT DISTINCT tipo FROM " . $CFG->prefix . $CFGSIG->prefix
					   . $CFGSIG->table_name . " WHERE shortname = '" . $son->shortname . "'";
				$ver_tipos = $DB->get_records_sql($query);
                                if($ver_tipos != NULL)
					foreach($ver_tipos as $tipo) $tipos[$i++] = $tipo->tipo;
				else
					if(!isset($tipos))
                                            $erro = get_string('no_classes','summaries',$COURSE->fullname);
			}
		}
		/*----------------------------------------------------------------*/
		if(!isset($erro))
			foreach(array_unique($tipos) as $t) {
				$siglas          = array();
				$sons_siglas = array();
                                if(!$is_meta) {
					$query = "SELECT DISTINCT(sigla_turma) FROM " . $CFG->prefix . $CFGSIG->prefix
								 . $CFGSIG->table_name . " WHERE shortname = '" . $COURSE->shortname . "'"
								 . " AND tipo = '" . $t . "'" ;
					$siglas = $DB->get_records_sql($query);
				} else {
					/* Adicionado na v.2 para aceder a meta-disciplinas */
					if($is_meta)
						foreach($meta_sons as $son) {
							$query = "SELECT DISTINCT(sigla_turma) FROM " . $CFG->prefix . $CFGSIG->prefix
									 . $CFGSIG->table_name . " WHERE shortname = '" . $son->shortname . "'"
									 . " AND tipo = '" . $t . "'";
							if(($teste_siglas  = $DB->get_records_sql($query)) != NULL)
								$sons_siglas = $teste_siglas;
						}
				}
				/*----------------------------------------------------------------*/
				// Vai criar um array com todas as turmas de todos os tipos que defacto existem
                                 if ($siglas != NULL || $sons_siglas != NULL) {
					if($siglas != NULL)
						foreach($siglas as $sigla) {
							if(!isset($turmas->$t)) { // inicializa o array caso ainda não tenha sido
								$turmas->$t = array();
                                                        }
							$turmas->$t = array_merge($turmas->$t, array($sigla->sigla_turma));
						}
					if($sons_siglas != NULL)
						foreach($sons_siglas as $sigla) { // para o caso de ser meta-disciplina
							if(!isset($turmas->$t)) { // inicializa o array caso ainda não tenha sido
								$turmas->$t = array();
                                                        }
							$turmas->$t = array_merge($turmas->$t, array($sigla->sigla_turma));
						}
				}
			}
		if(!isset($turmas)) {
                       $enrol_instances = enrol_get_instances($course->id, true); 
                       //$erro = $enrol_instances[1]; 
                       //print_object($enrol_instances);  
                       $erro = get_string('no_classes','summaries',$COURSE->fullname);
		}
		// 2- Header da tabela
		?>
		<center>
		<div id="apresentacao_sumarios" class="generalbox" style="padding-top:20px;width:600px;margin-top:20px;">
			<table width="600">
				<tr valign="middle">
					<td align="center" colspan="<?php if($is_meta) echo 3; else echo 2;?>">
						<div class="clearfix">
							<h3 style="text-align:center;">
								<?php 
									if(!isset($erro)) 
										print_string('classesfrom','summaries',$COURSE->fullname);
									else 
										echo $erro;
								?>
							</h3>
						</div>
					</td>
				</tr>
				<?php if(!isset($erro)) { ?>
				<tr valign="top">
					<?php if($is_meta) {?>
						<td align="center"><b><?php print_string('son_shortname','summaries'); ?></b></td>
					<?php } ?>
					<td align="center"><b><?php print_string('class_type','summaries'); ?></b></td> 
					<td align="left"><b><?php print_string('class','summaries'); ?></b></td> 
				</tr>
		<?php
		}
		// 3- Consultamos as turmas de cada tipo
		if(!isset($erro)) 
			if(!$is_meta) {
				$type = key($turmas);
				foreach($turmas as $tipo) {
					echo '<tr valign="top">';
					// Consulta dos sumários da turma
					foreach($tipo as $t) {?>
						<td align="center"> <?php echo $type; ?></td>
						<td align="left">
							<?php
								// Consulta os sumários
								$query = "SELECT DISTINCT sigla_turma FROM " . $CFG->prefix . $CFGSIG->prefix . $CFGSIG->table_name
											.  " WHERE tipo = '" . $type . "' AND shortname = '" . $COURSE->shortname . "' ORDER BY sigla_turma";
								$res = $DB->get_records_sql($query);
								
								// Coloca todos os sumários
								if($res != NULL) {
									foreach($res as $r) 
										echo '<a href="' . $CFG->wwwroot . '/mod/summaries/view.php?id=' . $_GET['id']
												. '&sigla_turma=' . urlencode($r->sigla_turma) . '&tipo=' . $type . '">'. $r->sigla_turma . '</a>&nbsp; ';
								}
								break;
								?>
						</td>
					<?php }
                    next($turmas);
					$type = key($turmas);
				}
			} else {
				// Meta-disciplina
				// Para cada disciplina filha
				$anterior = "";
				foreach($meta_sons as $son) {
					$query = "SELECT DISTINCT tipo FROM " . $CFG->prefix . $CFGSIG->prefix . $CFGSIG->table_name
						   . " WHERE shortname = '" . $son->shortname . "' ORDER BY tipo";
					$res = $DB->get_records_sql($query);
					// Se exitir registos
					if($res != NULL) {
						if($anterior != $son->shortname) { // Só imprime uma vez
						   $anterior = $son->shortname;
					?>
						<tr style="border-top:#CCC dotted 1px">
							<td align="center" rowspan="<?php echo sizeof($res);?>">
								<?php echo $son->shortname; ?>
							</td>
							<?php }
							// Para cada tipo imprime as turmas
							$i = 0;
							foreach($res as $tipos) {
								if($i == 0) 
									echo '<td align="center">' . $tipos->tipo . '</td>';
								else echo '<tr><td align="center">' . $tipos->tipo . '</td>';
								$i++;
								// Consulta das turmas de tipo
								$query  = "SELECT DISTINCT sigla_turma FROM " . $CFG->prefix . $CFGSIG->prefix . $CFGSIG->table_name
										. " WHERE tipo = '" . $tipos->tipo . "' AND shortname = '" . $son->shortname . "' ORDER BY sigla_turma";
								$siglas = $DB->get_records_sql($query);
								echo '<td>';
								foreach($siglas as $sigla)
									echo '<a href="' . $CFG->wwwroot . '/mod/summaries/view.php?id=' . $_GET['id']
										. '&sigla_turma=' . urlencode($sigla->sigla_turma) . '&tipo=' . $tipos->tipo . '&shortname=' . $son->shortname . '">'. $sigla->sigla_turma . '</a>&nbsp; ';
								echo '</td>';
							}
							echo '</tr>';
							?>
						
					<?php
					}
				}
			}
		echo '</table></div></center>';
	}

// Finish the page
echo $OUTPUT->footer();
