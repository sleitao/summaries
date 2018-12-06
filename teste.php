<?php
	header('Content-Type: text/html; charset=ISO-8859-15' );
	$dateTime = new DateTime("now", new DateTimeZone('GMT'));
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
		<title>Teste de inserção</title>
	</head>
	<body>
		<form name="sumario" method="POST" action="sig_sumarios.php">
			<table style="margin-right: auto; margin-left: auto;">
				<tr><th>Exemplo de sum&aacute;rio</th></tr>
				<tr>
					<td>Disciplina:</td><td><input type="text" name="shortname"></td>
				</tr>
                                <tr>
                                        <td>Disciplina id:</td><td><input type="text" name="id_sigarra"></td>
                                </tr>
				<tr>
					<td>Sum&aacute;rio:</td><td><input type="text" name="texto"></td>
				</tr>
				<tr>
					<td>Data e hora:</td><td> <input type="texto" name="data_hora" 
						value="<?php echo $dateTime->format("Y-m-d H:i:s");?>"></td>
				</tr>
				<tr>
					<td>Tipo:</td><td><input type="text" name="tipo"></td>
				</tr>
				<tr>
					<td>Sigla:</td><td><input type="text" name="turma_sigla"></td>
				</tr>
				<tr>
					<td>N aula:</td><td><input type="text" name="n_aula"></td>
				</tr>
				<tr>
					<td>Accao:</td><td><input type="text" name="accao"></td>
				</tr>
				<tr><td colspan=2>
					<input type="submit" >
				</td></tr>
			</table>
		</form>
	</body>
</html>
	
