<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<meta http-equiv="refresh" content="600">
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>

function geraGrafico(dt,dt1,dt2,dt3)
{
	
	var table = new google.visualization.Table(document.getElementById('table'));
	table.draw(dt, {allowHtml: true,cssClassNames: {tableCell:'normal1'}});

        var table = new google.visualization.Table(document.getElementById('table1'));
	table.draw(dt3, {allowHtml: true,cssClassNames: {tableCell:'normal1'}});
	
	
	var grouped_table = new google.visualization.Table(document.getElementById('grouped_table'));
	grouped_table.draw(dt1, {cssClassNames: {tableCell:'normal'},allowHtml: true,sortAscending:false,sortColumn:5});
	
	
      	var chart = new google.visualization.ColumnChart(document.getElementById('visualization'));
      	
      	chart.draw(dt2,{ height:200,
      	legend:{position:'right'},animation:{duration: 3000,easing: 'out'},vAxis:{viewWindow:{min:0,max:18000}},backgroundColor:{stroke:"gray",strokeWidth:"1"},chartArea:{left:80,top:15,width:"80%",height:"80%"},reverseCategories:true,series: {6:{color: 'green'},5:{color: 'red'},4:{color: 'brown'},3:{color: 'orange'},2:{color: 'purple'},1:{color: 'navy'},0:{color: 'pink'}}});
      
	chart.draw(dt1,{ height:200,
      	legend:{position:'right'},animation:{duration: 3000,easing: 'out'},vAxis:{viewWindow:{min:0,max:18000}},backgroundColor:{stroke:"gray",strokeWidth:"1"},chartArea:{left:80,top:15,width:"80%",height:"80%"},reverseCategories:true,series: {6:{color: 'green'},5:{color: 'red'},4:{color: 'brown'},3:{color: 'orange'},2:{color: 'purple'},1:{color: 'navy'},0:{color: 'pink'}}});        
}
google.load('visualization', '1', {packages: ['table','corechart']});

</script>


<style>
#conteudo
{
    background-image:url(http://a3.twimg.com/profile_images/461783373/aaanima_normal.jpg); background-position:top right; background-repeat:no-repeat;
}

TD.normal
{
	font-size:13pt;
	text-align:center;
}

TD.normal1
{
	font-size:10pt;
	text-align:center;
}


#mensagem
{
	background-color:green; color:white;
	width:100%;
	height:100px;
	font-size:40px;
	text-align:center;
	vertical-align: middle;
	padding-top:50px;
	margin-top:50px;
}
</style>

</head>
<body>
<div id=conteudo>
<?php    
include_once("phpxmlconf.inc");
include_once(PHPXML_ROOT_DIR."classes/parser/class.XMLGeracao.php");
include_once("funcoes.php");
?>
<h1> Liberação Graduação <?=$strTitulo." (".$strBaseOracle.")"?></h1>


<?php
function conectaOracle($strSql)
{
	global $strBaseOracle;        		
	global $conn;
        
        if (empty($conn)) $conn = oci_connect('xaluno', '86906f802a02c013b867', $strBaseOracle);
	
	// Prepare the statement
	$stid = oci_parse($conn, $strSql);
	
	// Perform the logic of the query
	$r = oci_execute($stid);
	
	$arrSaida = Array();
	
	// Fetch the results of the query
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) 
	{
		
		//print_r($row);
		$arrSaida[] = $row;
	}
	
	oci_free_statement($stid);	
	
	return $arrSaida;

}

	
	$strSql = "

SELECT SGL_INSTITUICAO,
     NOM_CURSO,
     ALUNOS || ' (' || ATIVOS || ')' AS ALUNOS,
     NULL AS SOLICITADO,
     NULL AS RESERVADO,
     NULL AS CONFIRMADO,
     NULL AS ATIVOS,
     NULL AS REGULARES,
     NULL AS REGULARES_FINANCEIROS,
     NULL AS PROTOCOLOS,
     NVL(TO_CHAR((SELECT MAX(MAT.DAT_MOVIMENTO)
           FROM DBSIAF.MOV_MATRICULA MAT,
              DBSIAF.MATRICULA     MTR,
              DBSIAF.ALUNO         ALU1
          WHERE MTR.COD_ALUNO = ALU1.COD_ALUNO
              AND MAT.COD_MATRICULA = MTR.COD_MATRICULA
              AND MTR.COD_PERIODO_LETIVO = TAB.COD_PERIODO_LETIVO
              AND ALU1.COD_STA_ALUNO = 1
              AND ALU1.COD_CURSO = TAB.COD_CURSO), 'DD/MM | HH24:MI'), '-') AS ULTIMA_ALTERACAO

  FROM (SELECT INS.SGL_INSTITUICAO,
         CUR.NOM_CURSO NOM_CURSO,
         PER.COD_PERIODO_LETIVO,
         CUR.COD_CURSO,
         NVL(SUM((SELECT MAX(1)
             FROM DBSIAF.MATRICULA MAT
            WHERE MAT.COD_ALUNO = ALU.COD_ALUNO
                AND MAT.COD_PERIODO_LETIVO = PER.COD_PERIODO_LETIVO
                AND MAT.COD_STA_MATRICULA <> 9)), 0) ALUNOS,
         COUNT(ALU.COD_ALUNO) ATIVOS
    
      FROM DBSIAF.ALUNO              ALU,
         DBSIAF.PERIODO_LETIVO     PER,
         DBSIAF.INSTITUICAO_ENSINO INS,
         DBSIAF.CAMPUS             CAM,
         DBSIAF.ASS_CONTRATO       ASS,
         DBSIAF.TIPO_CONTRATO      TIP,
         DBSIAF.CURSO              CUR
     WHERE ALU.COD_CURSO = CUR.COD_CURSO
         AND CAM.COD_CAMPUS = ALU.COD_CAMPUS
         AND INS.COD_INSTITUICAO = PER.COD_INSTITUICAO
         AND PER.COD_INSTITUICAO = CAM.COD_INSTITUICAO
         AND PER.COD_NIV_CURSO = CUR.COD_NIV_CURSO
         AND PER.IND_FECHADO = 'N'
         AND PER.IND_LIBERADO = 'S'
         AND ASS.COD_ALUNO = ALU.COD_ALUNO
         AND ASS.IND_LIBERADO <> 'C'
         AND ASS.COD_TPO_CONTRATO = TIP.COD_TPO_CONTRATO
         AND TIP.COD_PERIODO_LETIVO = PER.COD_PERIODO_LETIVO
         AND ALU.COD_STA_ALUNO = 1
         AND CUR.COD_NIV_CURSO = 1
         AND CUR.IND_DIS_ISOLADA = 'N'
     GROUP BY CUR.NOM_CURSO,
          PER.COD_PERIODO_LETIVO,
          INS.SGL_INSTITUICAO,
          CUR.COD_CURSO) TAB

 WHERE TAB.ALUNOS <> TAB.ATIVOS

UNION

SELECT D.SGL_INSTITUICAO,
     'TOTAL' AS NOM_CURSO,
     TO_CHAR(COUNT(DISTINCT A.COD_ALUNO)) AS ALUNOS,
     (SELECT COUNT(DISTINCT AA.COD_ALUNO)
      FROM PERIODO_LETIVO PLT,
         ALUNO          AA
     WHERE EXISTS (SELECT 1
          FROM DBSIAF.MATRICULA A
         WHERE A.COD_STA_MATRICULA = 7
             AND A.COD_PERIODO_LETIVO = PLT.COD_PERIODO_LETIVO
             AND A.COD_ALUNO = AA.COD_ALUNO)
         AND AA.IND_REG_ACADEMICO = 'S'
         AND PLT.IND_LIBERADO = 'S'
         AND PLT.COD_NIV_CURSO = 1
         AND PLT.COD_INSTITUICAO = D.COD_INSTITUICAO) AS SOLICITADO,
     (SELECT COUNT(DISTINCT AA.COD_ALUNO)
      FROM PERIODO_LETIVO PLT,
         ALUNO          AA
     WHERE EXISTS (SELECT 1
          FROM DBSIAF.MATRICULA A
         WHERE A.COD_STA_MATRICULA = 2
             AND A.COD_PERIODO_LETIVO = PLT.COD_PERIODO_LETIVO
             AND A.COD_ALUNO = AA.COD_ALUNO)
         AND AA.IND_REG_ACADEMICO = 'S'
         AND PLT.IND_LIBERADO = 'S'
         AND PLT.COD_NIV_CURSO = 1
         AND PLT.COD_INSTITUICAO = D.COD_INSTITUICAO) AS RESERVADO,
     (SELECT COUNT(DISTINCT AA.COD_ALUNO)
      FROM PERIODO_LETIVO PLT,
         ALUNO          AA
     WHERE EXISTS (SELECT 1
          FROM DBSIAF.MATRICULA A
         WHERE A.COD_STA_MATRICULA = 5
             AND A.COD_PERIODO_LETIVO = PLT.COD_PERIODO_LETIVO
             AND A.COD_ALUNO = AA.COD_ALUNO)
         AND AA.IND_REG_ACADEMICO = 'S'
         AND PLT.IND_LIBERADO = 'S'
         AND PLT.COD_NIV_CURSO = 1
         AND PLT.COD_INSTITUICAO = D.COD_INSTITUICAO) AS CONFIRMADO,
     (SELECT COUNT(DISTINCT ALU.COD_ALUNO)
      FROM DBSIAF.ALUNO         ALU,
         DBSIAF.CAMPUS        CAM,
         DBSIAF.CURSO         CUR,
         DBSIAF.ASS_CONTRATO  ASS,
         DBSIAF.TIPO_CONTRATO TIP
     WHERE CAM.COD_CAMPUS = ALU.COD_CAMPUS
         AND CUR.COD_CURSO = ALU.COD_CURSO
         AND CUR.COD_NIV_CURSO = 1
         AND ASS.COD_ALUNO = ALU.COD_ALUNO
         AND ASS.IND_LIBERADO <> 'C'
         AND ASS.COD_TPO_CONTRATO = TIP.COD_TPO_CONTRATO
         AND TIP.COD_PERIODO_LETIVO = B.COD_PERIODO_LETIVO
         AND CUR.IND_DIS_ISOLADA = 'N'
         AND ALU.COD_STA_ALUNO = 1
         AND CAM.COD_INSTITUICAO = D.COD_INSTITUICAO) AS ATIVOS,
     
     (SELECT COUNT(DISTINCT ALU.COD_ALUNO)
      FROM DBSIAF.ALUNO         ALU,
         DBSIAF.CAMPUS        CAM,
         DBSIAF.CURSO         CUR,
         DBSIAF.ASS_CONTRATO  ASS,
         DBSIAF.TIPO_CONTRATO TIP
     WHERE CAM.COD_CAMPUS = ALU.COD_CAMPUS
         AND CUR.COD_CURSO = ALU.COD_CURSO
         AND CUR.COD_NIV_CURSO = 1
         AND ALU.IND_REG_ACADEMICO = 'S'
         AND ASS.COD_ALUNO = ALU.COD_ALUNO
         AND ASS.IND_LIBERADO <> 'C'
         AND ASS.COD_TPO_CONTRATO = TIP.COD_TPO_CONTRATO
         AND TIP.COD_PERIODO_LETIVO = B.COD_PERIODO_LETIVO
         AND CUR.IND_DIS_ISOLADA = 'N'
         AND ALU.COD_STA_ALUNO = 1
         AND CAM.COD_INSTITUICAO = D.COD_INSTITUICAO) AS REGULARES,
         
     (SELECT COUNT(DISTINCT ALU.COD_ALUNO)
      FROM DBSIAF.ALUNO         ALU,
         DBSIAF.CAMPUS        CAM,
         DBSIAF.CURSO         CUR,
         DBSIAF.ASS_CONTRATO  ASS,
         DBSIAF.TIPO_CONTRATO TIP
     WHERE CAM.COD_CAMPUS = ALU.COD_CAMPUS
         AND CUR.COD_CURSO = ALU.COD_CURSO
         AND CUR.COD_NIV_CURSO = 1
         AND ALU.IND_REG_FINANCEIRO = 'S'
         AND ASS.COD_ALUNO = ALU.COD_ALUNO
         AND ASS.IND_LIBERADO <> 'C'
         AND ASS.COD_TPO_CONTRATO = TIP.COD_TPO_CONTRATO
         AND TIP.COD_PERIODO_LETIVO = B.COD_PERIODO_LETIVO
         AND CUR.IND_DIS_ISOLADA = 'N'
         AND ALU.COD_STA_ALUNO = 1
         AND CAM.COD_INSTITUICAO = D.COD_INSTITUICAO) AS REGULARES_FINANCEIROS,
         
     (SELECT COUNT(DISTINCT ALU.COD_ALUNO)
      FROM DBSIAF.ALUNO         ALU,
         DBSIAF.CAMPUS        CAM,
         DBSIAF.CURSO         CUR,
         DBSIAF.ASS_CONTRATO  ASS,
         DBSIAF.TIPO_CONTRATO TIP
     WHERE CAM.COD_CAMPUS = ALU.COD_CAMPUS
         AND CUR.COD_CURSO = ALU.COD_CURSO
         AND CUR.COD_NIV_CURSO = 1
         AND ASS.COD_ALUNO = ALU.COD_ALUNO
         AND ASS.IND_LIBERADO <> 'C'
         AND ASS.COD_TPO_CONTRATO = TIP.COD_TPO_CONTRATO
         AND TIP.COD_PERIODO_LETIVO = B.COD_PERIODO_LETIVO
         AND CUR.IND_DIS_ISOLADA = 'N'
         AND ALU.COD_STA_ALUNO = 1
         AND CAM.COD_INSTITUICAO = D.COD_INSTITUICAO
         AND EXISTS (SELECT 1
                       FROM DBSIAF.SOLICITACAO    A LEFT JOIN DBSIAF.TIPO_SOLICITACAO_ASSOCIADA TSA ON (A.COD_TPO_SOLICITACAO = TSA.COD_TPO_SOLICITACAO)
                                                    LEFT JOIN DBSIAF.SOLICITACAO                SOL ON (TSA.COD_TPO_SOLICITACAO_ASSOCIADA = SOL.COD_TPO_SOLICITACAO),
                            DBSIAF.PERIODO_LETIVO PER
                      WHERE TRUNC(A.DAT_SOLICITACAO) >= TRUNC(PER.DAT_INI_PERIODO)
                        AND (A.COD_TPO_SOLICITACAO    = 994 OR SOL.COD_TPO_SOLICITACAO = 994)
                        AND A.COD_ALUNO               = ALU.COD_ALUNO
                        AND PER.COD_PERIODO_LETIVO    = B.COD_PERIODO_LETIVO
                        AND ROWNUM                    = 1
                      GROUP BY A.COD_TPO_SOLICITACAO)) AS PROTOCOLOS,
     
     NULL AS ULTIMA_ALTERACAO
  FROM MATRICULA            A,
     PERIODO_LETIVO       B,
     CAMPUS               C,
     INSTITUICAO_ENSINO   D,
     ALUNO                E,
     DBSIAF.ASS_CONTRATO  ASS1,
     DBSIAF.TIPO_CONTRATO TIP1,
     CURSO                F
 WHERE A.COD_STA_MATRICULA != 9
     AND E.COD_ALUNO = A.COD_ALUNO
     AND E.COD_CAMPUS = C.COD_CAMPUS
     AND C.COD_INSTITUICAO = D.COD_INSTITUICAO
     AND E.COD_CURSO = F.COD_CURSO
     AND F.COD_NIV_CURSO = 1
     AND ASS1.COD_TPO_CONTRATO = TIP1.COD_TPO_CONTRATO
     AND ASS1.COD_ALUNO = E.COD_ALUNO
     AND TIP1.COD_PERIODO_LETIVO = A.COD_PERIODO_LETIVO
     AND ASS1.IND_LIBERADO <> 'C'
     AND B.COD_PERIODO_LETIVO = A.COD_PERIODO_LETIVO
     AND B.IND_LIBERADO = 'S'
     AND F.IND_DIS_ISOLADA = 'N'
     AND E.COD_STA_ALUNO = 1
     AND B.IND_FECHADO = 'N'
     AND B.COD_NIV_CURSO = F.COD_NIV_CURSO 
   GROUP BY D.COD_INSTITUICAO, D.SGL_INSTITUICAO, B.COD_PERIODO_LETIVO  ORDER BY ULTIMA_ALTERACAO DESC
	
	
	";
	
	//print $strSql;
	
$strSql2 = "
    
SELECT DATA_ATUALIZADO,
	   ULTIMA_ATUALIZACAO,
	   SGL_INSTIUICAO,
	   ACAO,
	   ERRO
  FROM (SELECT A.DAT_ERRO DATA_ATUALIZADO,
			   TO_CHAR(A.DAT_ERRO, 'DD/MM - HH24:MI') AS ULTIMA_ATUALIZACAO,
			   INS.SGL_INSTITUICAO SGL_INSTIUICAO,
			   A.DSC_ACAO ACAO,
			   REPLACE(REPLACE(TO_CHAR(A.DSC_ERRO), 'ORA-20500:', ''), 'Inicio', '<b>Inicio</b>') ERRO
		  FROM DBSIAF.LOG_PROCESSO_MATRICULA A,
			   DBSIAF.INSTITUICAO_ENSINO     INS,
			   DBSIAF.PERIODO_LETIVO         PER
		 WHERE PER.COD_PERIODO_LETIVO = A.COD_PERIODO_LETIVO
			   AND TRUNC(A.DAT_ERRO) = TRUNC(SYSDATE)
			   AND INS.COD_INSTITUICAO = PER.COD_INSTITUICAO
			   AND PER.IND_FECHADO = 'N'
			   AND PER.COD_NIV_CURSO = 1
			   AND PER.IND_LIBERADO = 'S'
		 ORDER BY A.DAT_ERRO DESC) TAB

UNION

SELECT DATA_ATUALIZADO,
	   ULTIMA_ATUALIZACAO,
	   SGL_INSTIUICAO,
	   ACAO,
	   ERRO
  FROM (
		
		SELECT MAT.DAT_ULTIMA_ALTERACAO DATA_ATUALIZADO,
				TO_CHAR(MAT.DAT_ULTIMA_ALTERACAO, 'DD/MM - HH24:MI') AS ULTIMA_ATUALIZACAO,
				INS.SGL_INSTITUICAO SGL_INSTIUICAO,
				'Erro aluno: ' || ALU.NUM_MATRICULA ACAO,
				REPLACE(REPLACE(TO_CHAR(TIP.DSC_TPO_ERRO), 'ORA-20500:', ''), 'Inicio', '<b>Inicio</b>') ERRO
		  FROM DBSIAF.MATRICULA          MAT,
				DBSIAF.INSTITUICAO_ENSINO INS,
				DBSIAF.ALUNO              ALU,
				DBSIAF.TIPO_ERRO          TIP,
				DBSIAF.CURSO              CUR,
				DBSIAF.PERIODO_LETIVO     PER
		 WHERE PER.COD_PERIODO_LETIVO = MAT.COD_PERIODO_LETIVO
			   AND ALU.COD_ALUNO = MAT.COD_ALUNO
			   AND CUR.COD_CURSO = ALU.COD_CURSO
			   AND INS.COD_INSTITUICAO = PER.COD_INSTITUICAO
			   AND PER.IND_FECHADO = 'N'
			   AND PER.COD_NIV_CURSO = 1
			   AND PER.IND_LIBERADO = 'S'
			   AND TIP.COD_TPO_ERRO = MAT.COD_TPO_ERRO
			   AND TRUNC(MAT.DAT_ULTIMA_ALTERACAO) = TRUNC(SYSDATE)
		 ORDER BY MAT.DAT_ULTIMA_ALTERACAO DESC) TAB1
 WHERE ROWNUM = 1
 ORDER BY DATA_ATUALIZADO DESC

"        ;
        
	 $objResultado = conectaOracle($strSql);
     $objResultado1 = conectaOracle($strSql2);
	 
	$strArray = "";
	$strArrayGeral = "";
	
	if (count($objResultado)>0) 
	{
		$arrDados = $objResultado;
		for ($x=0; $x<count($arrDados); $x++)
		{
			
			IF ($arrDados[$x]['NOM_CURSO']!="TOTAL")
			{
				$strArray .= "dt.addRow(['".($arrDados[$x]['NOM_CURSO'])."', '".$arrDados[$x]['SGL_INSTITUICAO']."','".$arrDados[$x]['ALUNOS']."','".$arrDados[$x]['ULTIMA_ALTERACAO']."']);\n";
			}
			ELSE
			{
				//tabela 2
				$strArray .= "dt1.addRow(['".$arrDados[$x]['SGL_INSTITUICAO']."',".$arrDados[$x]['PROTOCOLOS'].",".$arrDados[$x]['CONFIRMADO'].",".$arrDados[$x]['RESERVADO'].",".$arrDados[$x]['REGULARES'].",".$arrDados[$x]['SOLICITADO'].",".$arrDados[$x]['ALUNOS'].",".$arrDados[$x]['ATIVOS']."]);\n";
				$strArray .= "dt2.addRow(['".$arrDados[$x]['SGL_INSTITUICAO']."',0,0,0,0,0,0,".$arrDados[$x]['ATIVOS']."]);\n";
			}	
						
			$arrCor[$arrDados[$x]['SGLINSTITUICAO']] = corTipo($arrDados[$x]['SGLINSTITUICAO']);
		}
		
                
		for ($x=0; $x<count($objResultado1); $x++)
		{
                   $strArray .= "dt3.addRow(['".$objResultado1[$x]['SGL_INSTIUICAO']."','".$objResultado1[$x]['ACAO']."','".str_replace(chr(10),"",str_replace("'","",$objResultado1[$x]['ERRO']))."','".$objResultado1[$x]['ULTIMA_ATUALIZACAO']."']);\n";                    
                }
                
		while ( list( $strGrupo, $strCor ) = each( $arrCor ) )
		{
			$strCores .= "'".$strCor."',";
		}
		
		
		
		print '
		<table cellpadding=10 border=0 width=100%>
		<tr>
		<td valign=top colspan=2>
				<div id="grouped_table"></div>
			</td>
		</tr>
		<tr>
			<td colspan=2 valign=top >
				<div id="visualization"></div>
			</td>
		</tr>
		<tr>
			<td width=50% valign=top>
				<div id="table"></div>
			</td>
                        <td width=50% valign=top><div id="table1"></div></td>
			</tr>
			
		</table>';
		
		
		
		print "<script>
		";
		
		print "
		
		var cores = [".$strCores."];
		
		var dt = new google.visualization.DataTable();
		dt.addColumn('string', 'CURSO');
		dt.addColumn('string', 'INSTITUICAO');
		dt.addColumn('string', 'LIBERADOS/ATIVOS');
		dt.addColumn('string', 'ATUALIZACAO');
  
                var dt3 = new google.visualization.DataTable();
		dt3.addColumn('string', 'INSTITUICAO');
		dt3.addColumn('string', 'ACAO');
		dt3.addColumn('string', 'MENSAGEM');
		dt3.addColumn('string', 'ATUALIZACAO');

		var dt1 = new google.visualization.DataTable();
		dt1.addColumn('string', 'INSTITUICAO');
		dt1.addColumn('number', 'PROTOCOLOS');
		dt1.addColumn('number', 'CONFIRMADOS');
		dt1.addColumn('number', 'RESERVADOS');
		dt1.addColumn('number', 'REGULARES');
		dt1.addColumn('number', 'SOLICITADOS');		
		dt1.addColumn('number', 'LIBERADOS');
		dt1.addColumn('number', 'ATIVOS');		
		
		var dt2 = new google.visualization.DataTable();
		dt2.addColumn('string', 'INSTITUICAO');
		dt2.addColumn('number', 'PROTOCOLOS');
		dt2.addColumn('number', 'CONFIRMADOS');		
		dt2.addColumn('number', 'RESERVADOS');
		dt2.addColumn('number', 'REGULARES');
		dt2.addColumn('number', 'SOLICITADOS');
		dt2.addColumn('number', 'LIBERADOS');
		dt2.addColumn('number', 'ATIVOS');

		".$strArray."
		
		google.setOnLoadCallback(geraGrafico(dt,dt1,dt2,dt3));
			
		</script>";
		
	}		
	else
	{
	?>
	<div id=mensagem>Erro ao Coletar Dados</div>
	<?	
	}
?>

</div>



    
    

<script>
geraGrafico();
</script>    
    
</body>
</html>
