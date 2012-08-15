<?php
/*=====================================================
ShowStat - ����� ���������� ������������������ ����� (������������ �� 9.4 � 9.6)
=======================================================
�����: �������� 
URL: http://pafnuty.name/
ICQ: 817233 
email: pafnuty10@gmail.com
=======================================================
����:  showstat.php
-------------------------------------------------------
������: 2.1 (15.08.2012)
=======================================================

��� ������ ���������� � ����� main.tpl ����� </body> ��������� ������: 
{include file="engine/modules/showstat.php"}

-------------------------------------------------------
��� �� ����� ���������� �������� &size=XX ��� XX ������������ ������ � ���������� ���-����� (�� ��������� - 40�� ��� ����� 100-140 �����).

-----------------
----��������!---- ����������� ����� �������� �� ���� ����� � ���� (����� �������� ���� ������ - �� ���� ������.)
-----------------

��� ����������� �������� � �� � ������:

engine/classes/mysqli.class.php
engine/classes/mysql.class.php

����������������� ������:
============================================================================================
//			$this->query_list[] = array( 'time'  => ($this->get_real_time() - $time_before), 
//										 'query' => $query,
//										 'num'   => (count($this->query_list) + 1));
============================================================================================
� ������ ����������� �������� &show_query=y

��������� ��� ����� ������ {include file="engine/modules/showstat.php?&size=15&show_query=y"}

-------------------------------------------------------
� css ���� ��������:

.showstat {
	position: fixed;
	top: 10px;
	right: 10px;
	background: #ffffff;
	background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5));
	background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
	background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
	background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
	background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 100%);
	font: normal 12px/18px Arial, Helvetica, sans-serif;
	color: #323232;
	padding: 20px 20px 10px 20px;
	text-align: right;
	border: solid 1px #fff;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	text-shadow: 1px 1px 0 #fff;
	-webkit-box-shadow:  1px 1px 5px 1px rgba(0, 0, 0, 0.3);
	box-shadow:  1px 1px 5px 1px rgba(0, 0, 0, 0.3);
	}
	.showstat p {
		margin: 0;
		}
	
=======================================================
*/


if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if ($member_id['user_group'] == 1 OR $member_id['user_group'] == 2) {

	global $config, $Timer, $db, $tpl;


	$size = 40; //������������ ������ ����� ����

	$statfile = ROOT_DIR . '/uploads/stat_log.html';
	$dtime = date ('d.m.Y  H:i:s', $_TIME);
	$timer = $Timer->stop();
	$tpl_time = round($tpl->template_parse_time, 5);
	$db_q = $db->query_num;
	$mysql_time = round($db->MySQL_time_taken, 5);

	if ($show_query) {
		$total_time_query = $db->query_list;
		if(is_array($total_time_query)){
			for ($i = 0; $i < count($total_time_query); $i++) { 
			$time_query .= "".$total_time_query[$i][time] > 0.01."" ? "<p><span style=\"color:red\">".round($total_time_query[$i][time],5)."</span> ���. - [".$total_time_query[$i][query]."]</p>" : "<p><span style=\"color:green\">".round($total_time_query[$i][time],5)."</span> ���. - [".$total_time_query[$i][query]."]</p>";}
		}
	}

	if(function_exists( "memory_get_peak_usage" )) $mem_usg = round(memory_get_peak_usage()/(1024*1024),2)."��";


	if (file_exists($statfile) && filesize($statfile) > $size*1024) {
		unlink($statfile);
	}
	if (!file_exists($statfile)) {
		$cFile = fopen( $statfile, "wb" );
		$firstText = "
						<!DOCTYPE html><html lang='ru'><head><meta charset='windows-1251'><title>��� ���������� ��������� ��������</title></head>
						<style>.stattable{margin: 50px auto;border-collapse:collapse;border:solid 1px #ccc;font:normal 14px Arial,Helvetica,sans-serif;}.stattable th, .stattable td{font-size:12px;border:solid 1px #ccc; padding: 5px;}.stattable tr:hover {background: #f0f0f0; color: #1d1d1d;} b{color:#c00;}p{margin:0 -5px -6px;padding:11px 10px 5px;border-bottom:solid 1px #eee;}</style>
						<body>
						<table width='800' class='stattable'>
						<tr>
							<th scope='col' width='300'>����� �������� � ������� � �� (�����������)</th>
							<th scope='col'>����</th>
							<th scope='col'>���� ���������� �������</th>
							<th scope='col'>����� �������� �������</th>
							<th scope='col'>���-�� ��������</th>
							<th scope='col'>����� ���������� ��������</th>
							<th scope='col'>������� ������</th>
						</tr>
						\r\n</table></body></html>";
		fwrite( $cFile, $firstText);
		fclose( $cFile );

	} else {
		$cFileArr = file($statfile);
		$lastLine = array_pop($cFileArr);
		$newText = implode("", $cFileArr);

		$newTextAdd = "��������� ������\r\n";
		$newTextAdd = "	<tr>
							<td><a href='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."' title='������� �� ��������' target='_blank'>".$_SERVER['REQUEST_URI']."</a> <br />".$time_query."</td>
							<td>$dtime</td>
							<td><b>".$timer."�</b></td>
							<td>".$tpl_time."�</td>
							<td>".$db_q."</td>
							<td>".$mysql_time."�</td>
							<td>".$mem_usg."</td>
						</tr>\r\n";

		$cFile = fopen( $statfile, "w" );	

		fwrite( $cFile, $newText.$newTextAdd.$lastLine);
		fclose( $cFile );
	}

	$showstat .= "<div class=\"showstat\">";
	$showstat .= "
		<p>������ �������� ��: <b>".$timer."�</b></p>
		<p>������ ������ ��: <b>".$tpl_time."�</b></p>
		<p>�������: <b>".$db_q."</b></p>
		<p>��������� ��: <b>".$mysql_time."�</b></p>";
	if($mem_usg) $showstat .="<p> ������ ��������� <b>".$mem_usg."</b> </p>";

	$showstat .= "<p><a href='".$config['http_home_url']."uploads/stat_log.html' target='_blank' title='��� �������� ��� ���������� ".$size."��'>".fgets($statfile)."���������� ��� (".round(filesize($statfile)/1024,2)."��)</a></p>";
	$showstat .= "</div>";
	echo $showstat;

}

?>