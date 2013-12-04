<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

###############################
###	MELHORAR ESTE CÓDIGO!!! ###
###############################

class SnmpController extends Controller
{
	private $host;
	private $community;
	private $sysUpTime;
	private $sysName;
	private $version;
	private $codeInterface;
	
    /**
     * Construct
     */
    public function __construct($host, $community, $codeInterface)
    {
        $this->host = $host;
        $this->community = $community;
        $this->codeInterface = $codeInterface;
    }

	public function fcSnmpWalk($oId, $t = 1, $r = 1)
	{
		/*
     		Nao permite SNMP BULK.
     		Retorna Resposta, apenas, para a primeira OID solicitada
		*/

     	$com = "snmpwalk -Ov -t " . $t . " -r " . $r . " -c " . $this->community . " -v 1 " . $this->host . " " . $oId;
		$arrayInfo = explode("\n", shell_exec($com));

     	$arrayInfo = $this->formatSnmp($arrayInfo);

		return $arrayInfo;
	}

	public function fcSnmpGet($oIds, $t=1, $r=1)
	{
		$com = "snmpget -Ov -t " . $t . " -r " . $r . " -c " . $this->community . " -v 1 " . $this->host . " " . $oIds;
		$arrayInfo = explode("\n", shell_exec($com));

   		return (!empty($arrayInfo[0])) ? $this->formatSnmp($arrayInfo) : null;
	}


	public function formatSnmp($array)
	{
		for($i=0;$i<count($array);$i++)
		{
			if($array[$i] != NULL)
			{
                if(preg_match('/:/i',$array[$i]))
				{
			   		$newArray[$i] = substr(strstr($array[$i], ':'),1);
                    $newArray[$i] = str_replace("\"", '', trim($newArray[$i]));
				}
				else
                    $newArray[$i] = str_replace("\"", '', trim($array[$i]));
			}
		}
		return (isset($newArray)) ? (count($newArray)>1) ? $newArray : $newArray[0] : null;
	}

	public function sysUpTime()
	{
		$this->sysUpTime = $this->fcSnmpGet(".1.3.6.1.2.1.1.3.0");

		return ($this->sysUpTime == NULL) ? 0 : 1;
	}
	
	
	public function server()
	{
		$aux = shell_exec('ifconfig | head -2| tail -1');
        $aux = substr(strstr($aux, ':'),2);
		$server = explode(" " , $aux);
		return $server[0];
	}

	public function hostName()
	{
		$name = $this->fcSnmpGet(".1.3.6.1.2.1.1.5.0");

		if($name == NULL)
			$this->problems();

		$this->sysName = $name;
	}

	public function problems()
	{
		echo "<BR><BR>
		<DIV ALIGN='center'><H2>PROBLEMAS!!!&nbsp;&nbsp;&nbsp;<FONT COLOR='#0000FF'>" .
		$this->host .
		"</FONT></DIV></H2>
		<HR WIDTH='600' COLOR='#333399'>
		<PRE>
		<FONT FACE='Verdana' SIZE='3'>

		&gt;&gt;&gt;&nbsp;&nbsp;&nbsp;Verifique se o equipamento possui <B>'Hostname'</B>.

		&gt;&gt;&gt;&nbsp;&nbsp;&nbsp;Verifique se equipamento est&aacute; ligado. Usando comando <B>ping</B>.

		&gt;&gt;&gt;&nbsp;&nbsp;&nbsp;Verifique se o <B>IP</B> e/ou <B>Community</B> esta(&atilde;o) correto(s).
		</FONT>
		</PRE>
		";
		exit;
	}

	public function printHost()
	{
			$chassis = $this->chassis();
			echo "<h2>" . $this->host . "</h2> (Modelo: $chassis)<HR WIDTH='100%' COLOR='#006C36'>";
	}

	public function chassis()
	{
		$ChassisTypes[0] = "Desconhecido";
		$ChassisTypes[2] = "multibus";
		$ChassisTypes[3] = "agsplus";
		$ChassisTypes[4] = "igs";
		$ChassisTypes[5] = "c2000";
		$ChassisTypes[6] = "c3000";
		$ChassisTypes[7] = "c4000";
		$ChassisTypes[8] = "c7000";
		$ChassisTypes[9] = "cs500";
		$ChassisTypes[10] = "c7010";
		$ChassisTypes[11] = "c2500";
		$ChassisTypes[12] = "c4500";
		$ChassisTypes[13] = "c2102";
		$ChassisTypes[14] = "c2202";
		$ChassisTypes[15] = "c2501";
		$ChassisTypes[16] = "c2502";
		$ChassisTypes[17] = "c2503";
		$ChassisTypes[18] = "c2504";
		$ChassisTypes[19] = "c2505";
		$ChassisTypes[20] = "c2506";
		$ChassisTypes[21] = "c2507";
		$ChassisTypes[22] = "c2508";
		$ChassisTypes[23] = "c2509";
		$ChassisTypes[24] = "c2510";
		$ChassisTypes[25] = "c2511";
		$ChassisTypes[26] = "c2512";
		$ChassisTypes[27] = "c2513";
		$ChassisTypes[28] = "c2514";
		$ChassisTypes[29] = "c2515";
		$ChassisTypes[30] = "c3101";
		$ChassisTypes[31] = "c3102";
		$ChassisTypes[32] = "c3103";
		$ChassisTypes[33] = "c3104";
		$ChassisTypes[34] = "c3202";
		$ChassisTypes[35] = "c3204";
		$ChassisTypes[36] = "accessProRC";
		$ChassisTypes[37] = "accessProEC";
		$ChassisTypes[38] = "c1000";
		$ChassisTypes[39] = "c1003";
		$ChassisTypes[40] = "c1004";
		$ChassisTypes[41] = "c2516";
		$ChassisTypes[42] = "c7507";
		$ChassisTypes[43] = "c7513";
		$ChassisTypes[44] = "c7506";
		$ChassisTypes[45] = "c7505";
		$ChassisTypes[46] = "c1005";
		$ChassisTypes[47] = "c4700";
		$ChassisTypes[48] = "c2517";
		$ChassisTypes[49] = "c2518";
		$ChassisTypes[50] = "c2519";
		$ChassisTypes[51] = "c2520";
		$ChassisTypes[52] = "c2521";
		$ChassisTypes[53] = "c2522";
		$ChassisTypes[54] = "c2523";
		$ChassisTypes[55] = "c2524";
		$ChassisTypes[56] = "c2525";
		$ChassisTypes[57] = "c4700S";
		$ChassisTypes[58] = "c7206";
		$ChassisTypes[59] = "c3640";
		$ChassisTypes[60] = "as5200";
		$ChassisTypes[61] = "c1601";
		$ChassisTypes[62] = "c1602";
		$ChassisTypes[63] = "c1603";
		$ChassisTypes[64] = "c1604";
		$ChassisTypes[65] = "c7204";
		$ChassisTypes[66] = "c3620";
		$ChassisTypes[68] = "wsx3011";
		$ChassisTypes[72] = "c1503";
		$ChassisTypes[73] = "as5300";
		$ChassisTypes[74] = "as2509RJ";
		$ChassisTypes[75] = "as2511RJ";
		$ChassisTypes[77] = "c2501FRADFX";
		$ChassisTypes[78] = "c2501LANFRADFX";
		$ChassisTypes[79] = "c2502LANFRADFX";
		$ChassisTypes[80] = "wsx5302";
		$ChassisTypes[82] = "c12012";
		$ChassisTypes[84] = "c12004";
		$ChassisTypes[87] = "c2600";
		$ChassisTypes[165] = "c7606";
		$ChassisTypes[278] = "c7606";

		$chassis = $this->fcSnmpWalk(".1.3.6.1.4.1.9.3.6.1.0");

		if(isset($ChassisTypes[$chassis]))
		{
			return ($ChassisTypes[$chassis] == NULL) ? $ChassisTypes[0] : $ChassisTypes[$chassis];
		}

		return $ChassisTypes[0];
	}

	public function general()
	{
		$oids = array
			(
				"sysUptime" => ".1.3.6.1.2.1.1.3.0",
				"whyReload" => ".1.3.6.1.4.1.9.2.1.2.0",
				"version"   => ".1.3.6.1.2.1.1.1.0",
				"location"  => ".1.3.6.1.2.1.1.6.0",
				"contact"   => ".1.3.6.1.2.1.1.4.0",
				"avgBusy1"  => ".1.3.6.1.4.1.9.2.1.57.0",
				"avgBusy5"  => ".1.3.6.1.4.1.9.2.1.58.0",
				"sysConfigName" => ".1.3.6.1.4.1.9.2.1.73.0",
				"tsLines" => ".1.3.6.1.4.1.9.2.9.1.0",
				"cmSystemInstalledModem" => ".1.3.6.1.4.1.9.9.47.1.1.1.0",
				"cmSystemModemsInUse" => ".1.3.6.1.4.1.9.9.47.1.1.6.0",
				"cmSystemModemsDead"  => ".1.3.6.1.4.1.9.9.47.1.1.10.0",
				"Memory"   => ".1.3.6.1.4.1.9.3.6.6.0",
				"Services" => ".1.3.6.1.2.1.1.7.0"
			);

		   $MIBs = 	$oids['sysUptime'] . " " .
		   			$oids['whyReload'] . " " .
			    	$oids['location']  . " " .
              	    $oids['contact']   . " " .
                	$oids['Memory']    . " " .
            	    $oids['avgBusy1']  . " " .
           	    	$oids['avgBusy5']  . " " .
           	    	$oids['tsLines']   . " " .
           	    	$oids['Services']  . " " .
           	    	$oids['sysConfigName'] 	. " " .
           	    	$oids['cmSystemInstalledModem'];

			$result = $this->fcSnmpGet($MIBs);

			$sysName  = $this->sysName;
			$services = $this->sysSrv((isset($result[8])) ? $result[8] : "");
			$memory = isset($result[4]) ? $result[4] : 0;
			$memory   /= (1024*1024); # TRANSFORMA PARA Mb

			$this->version = $this->sysVersion($oids["version"]);
			$this->sysUpTime = $this->upTime($result[0]);

			echo $services;	

			$this->y = 0;

			echo "
				<br>
				<TABLE border=0	 CLASS='tabela' CELLPADDING='2' ALIGN='center'>
				<TR><TH CLASS='thfont' COLSPAN='2'>Informa&ccedil;&otilde;es Gerais</TH></TR>
				";

			echo $this->printGeneralInfo("UpTime", $this->sysUpTime . "(motivo: $result[1])");
			echo $this->printGeneralInfo("Nome do Equipamento", "$sysName");
			echo $this->printGeneralInfo("Services", "$services");

			if ($result[2] != NULL)
				echo $this->printGeneralInfo("Localiza&ccedil;&atilde;o", $result[2]);

			if ($result[2] != NULL)
				echo $this->printGeneralInfo("Contato", $result[2]);

			echo $this->printGeneralInfo("Mem&oacute;ria Mb", "$memory");
			echo $this->printGeneralInfo("Vers&atilde;o", $this->version);
			echo $this->printGeneralInfo("1/5 min CPU util", ""); //$avgBusy1/$avgBusy5 %
 			echo $this->printGeneralInfo("Imagem Carregada", ""); //$sysCfgName
			echo $this->printGeneralInfo("Terminal lines", ""); //$tsLines
/*
			if ($sysModem > 0)
			{

				   $MIBs = 	$oids['cmSystemModemsInUse'] . " " .
	   						$oids['cmSystemModemsDead'];

					list($modemsInUse, $modemsDead) = $this->fcSnmpGet($MIBs);

					$this->printGeneralInfo("Digital modems", "$sysModem");
					$this->printGeneralInfo("In use modems", "$modemsInUse");
					$this->printGeneralInfo("Modems Dead", "$modemsDead");
			}
*/
		echo "</table>";
	}

    public function sysSrv($sv)
	{
		if ($sv &  1) {$srv = "Repeater"; }
		if ($sv &  2) {$srv = "$srv Bridge"; }
		if ($sv &  4) {$srv = "$srv Router"; }
		if ($sv &  8) {$srv = "$srv Gateway"; }
		if ($sv & 16) {$srv = "$srv Session"; }
		if ($sv & 32) {$srv = "$srv Terminal"; }
		if ($sv & 64) {$srv = "$srv Application"; }
		if (!$sv)     {$srv = "serviço SNMP não suportado"; }
		return $srv;
	}

	public function sysVersion($version)
	{
		$arrayVersion = $this->fcSnmpGet($version);

		for($i=0; $i<count($arrayVersion); $i++)
		{
			if(preg_match("/Version/i", $arrayVersion[$i]))
			{
				$aux = explode(",", stristr($arrayVersion[$i], "Version "));
				return $aux[0];
			}
		}
	}

	public function upTime($sysUpTime)
	{

        $sysUpTime = substr($sysUpTime, 1);
		$aux = explode(")",$sysUpTime);
		$sysUpTime = substr($aux[0], 0, -2);

        $this->UpTimeTickts = $sysUpTime;

		$day		= bcdiv($sysUpTime, 86400);
		$sysUpTime	= bcmod($sysUpTime, 86400);
		$hour		= bcdiv($sysUpTime, 3600);
		$sysUpTime	= bcmod($sysUpTime, 3600);
		$minute	    = bcdiv($sysUpTime, 60);
		$sec		= bcmod($sysUpTime, 60);

		$daystr = ($day == 1) ? "Dia" : "Dias";
		$hourstr = ($hour == 1) ? "hora" : "horas";
		$minutestr = ($minute == 1) ? "minuto" : "minutos";
		$secstr = ($sec == 1) ? "segundo" : "segundos";

		return "$day $daystr $hour $hourstr $minute $minutestr e $sec $secstr";
	}

    public function printGeneralInfo($th, $info)
	{
		$classe = $this->oddCouple();
		return "<TR><TD Class='$classe' ><B>$th</B></TD><TD Class='$classe'>$info</TD></TR>";
	}

    public function oddCouple()
	{
		$classe = ((bcmod($this->y, 2)) == 0 ) ? "par" : "impar";
		$this->y++;

		return $classe;
	}

	public function hardware()
	{
		$oids = array(
					"cards"	=> ".1.3.6.1.4.1.9.3.6.11.1.3",
					"ctype"	=> ".1.3.6.1.4.1.9.3.6.11.1.2",
					"slotnum"	=> ".1.3.6.1.4.1.9.3.6.11.1.7",
					"cardSlots" => ".1.3.6.1.4.1.9.3.6.12.0"
				);

		$CardTypes[1] = "desconhecido";
		$CardTypes[2] = "csc1";
		$CardTypes[3] = "csc2";
		$CardTypes[4] = "csc3";
		$CardTypes[5] = "csc4";
		$CardTypes[6] = "rp";
		$CardTypes[7] = "cpu-igs";
		$CardTypes[8] = "cpu-2500";
		$CardTypes[9] = "cpu-3000";
		$CardTypes[10] = "cpu-3100";
		$CardTypes[11] = "cpu-accessPro";
		$CardTypes[12] = "cpu-4000";
		$CardTypes[13] = "cpu-4000m";
		$CardTypes[14] = "cpu-4500";
		$CardTypes[15] = "rsp1";
		$CardTypes[16] = "rsp2";
		$CardTypes[17] = "cpu-4500m";
		$CardTypes[18] = "cpu-1003";
		$CardTypes[19] = "cpu-4700";
		$CardTypes[20] = "csc-m";
		$CardTypes[21] = "csc-mt";
		$CardTypes[22] = "csc-mc";
		$CardTypes[23] = "csc-mcplus";
		$CardTypes[24] = "csc-envm";
		$CardTypes[25] = "chassisInterface";
		$CardTypes[26] = "cpu-4700S";
		$CardTypes[27] = "cpu-7200-npe100";
		$CardTypes[28] = "rsp7000";
		$CardTypes[29] = "chassisInterface7000";
		$CardTypes[30] = "rsp4";
		$CardTypes[31] = "cpu-3600";
		$CardTypes[32] = "cpu-as5200";
		$CardTypes[33] = "c7200-io1fe";
		$CardTypes[34] = "cpu-4700m";
		$CardTypes[35] = "cpu-1600";
		$CardTypes[36] = "c7200-io";
		$CardTypes[37] = "cpu-1503";
		$CardTypes[38] = "cpu-1502";
		$CardTypes[39] = "cpu-as5300";
		$CardTypes[40] = "csc-16";
		$CardTypes[41] = "csc-p";
		$CardTypes[50] = "csc-a";
		$CardTypes[51] = "csc-e1";
		$CardTypes[52] = "csc-e2";
		$CardTypes[53] = "csc-y";
		$CardTypes[54] = "csc-s";
		$CardTypes[55] = "csc-t";
		$CardTypes[80] = "csc-r";
		$CardTypes[81] = "csc-r16";
		$CardTypes[82] = "csc-r16m";
		$CardTypes[83] = "csc-1r";
		$CardTypes[84] = "csc-2r";
		$CardTypes[56] = "sci4s";
		$CardTypes[57] = "sci2s2t";
		$CardTypes[58] = "sci4t";
		$CardTypes[59] = "mci1t";
		$CardTypes[60] = "mci2t";
		$CardTypes[61] = "mci1s";
		$CardTypes[62] = "mci1s1t";
		$CardTypes[63] = "mci2s";
		$CardTypes[64] = "mci1e";
		$CardTypes[65] = "mci1e1t";
		$CardTypes[66] = "mci1e2t";
		$CardTypes[67] = "mci1e1s";
		$CardTypes[68] = "mci1e1s1t";
		$CardTypes[69] = "mci1e2s";
		$CardTypes[70] = "mci2e";
		$CardTypes[71] = "mci2e1t";
		$CardTypes[72] = "mci2e2t";
		$CardTypes[73] = "mci2e1s";
		$CardTypes[74] = "mci2e1s1t";
		$CardTypes[75] = "mci2e2s";
		$CardTypes[100] = "csc-cctl1";
		$CardTypes[101] = "csc-cctl2";
		$CardTypes[110] = "csc-mec2";
		$CardTypes[111] = "csc-mec4";
		$CardTypes[112] = "csc-mec6";
		$CardTypes[113] = "csc-fci";
		$CardTypes[114] = "csc-fcit";
		$CardTypes[115] = "csc-hsci";
		$CardTypes[116] = "csc-ctr";
		$CardTypes[121] = "cpu-7200-npe150";
		$CardTypes[122] = "cpu-7200-npe200";
		$CardTypes[123] = "cpu-wsx5302";
		$CardTypes[124] = "gsr-rp";
		$CardTypes[126] = "cpu-3810";
		$CardTypes[150] = "sp";
		$CardTypes[151] = "eip";
		$CardTypes[152] = "fip";
		$CardTypes[153] = "hip";
		$CardTypes[154] = "sip";
		$CardTypes[155] = "trip";
		$CardTypes[156] = "fsip";
		$CardTypes[157] = "aip";
		$CardTypes[158] = "mip";
		$CardTypes[159] = "ssp";
		$CardTypes[160] = "cip";
		$CardTypes[161] = "srs-fip";
		$CardTypes[162] = "srs-trip";
		$CardTypes[163] = "feip";
		$CardTypes[164] = "vip";
		$CardTypes[165] = "vip2";
		$CardTypes[166] = "ssip";
		$CardTypes[167] = "smip";
		$CardTypes[168] = "posip";
		$CardTypes[169] = "feip-tx";
		$CardTypes[170] = "feip-fx";
		$CardTypes[178] = "cbrt1";
		$CardTypes[179] = "cbr120e1";
		$CardTypes[180] = "cbr75e";
		$CardTypes[181] = "vip2-50";
		$CardTypes[182] = "feip2";
		$CardTypes[183] = "acip";
		$CardTypes[200] = "npm-4000-fddi-sas";
		$CardTypes[201] = "npm-4000-fddi-das";
		$CardTypes[202] = "npm-4000-1e";
		$CardTypes[203] = "npm-4000-1r";
		$CardTypes[204] = "npm-4000-2s";
		$CardTypes[205] = "npm-4000-2e1";
		$CardTypes[206] = "npm-4000-2e";
		$CardTypes[207] = "npm-4000-2r1";
		$CardTypes[208] = "npm-4000-2r";
		$CardTypes[209] = "npm-4000-4t";
		$CardTypes[210] = "npm-4000-4b";
		$CardTypes[211] = "npm-4000-8b";
		$CardTypes[212] = "npm-4000-ct1";
		$CardTypes[213] = "npm-4000-ce1";
		$CardTypes[214] = "npm-4000-1a";
		$CardTypes[215] = "npm-4000-6e";
		$CardTypes[217] = "npm-4000-1fe";
		$CardTypes[218] = "npm-4000-1hssi";
		$CardTypes[230] = "pa-1fe";
		$CardTypes[231] = "pa-8e";
		$CardTypes[232] = "pa-4e";
		$CardTypes[233] = "pa-5e";
		$CardTypes[234] = "pa-4t";
		$CardTypes[235] = "pa-4r";
		$CardTypes[236] = "pa-fddi";
		$CardTypes[237] = "sa-encryption";
		$CardTypes[238] = "pa-ah1t";
		$CardTypes[239] = "pa-ah2t";
		$CardTypes[241] = "pa-a8t-v35";
		$CardTypes[242] = "pa-1fe-tx-isl";
		$CardTypes[243] = "pa-1fe-fx-isl";
		$CardTypes[244] = "pa-1fe-tx-nisl";
		$CardTypes[245] = "sa-compression";
		$CardTypes[246] = "pa-atm-lite-1";
		$CardTypes[247] = "pa-ct3";
		$CardTypes[248] = "pa-oc3sm-mux-cbrt1";
		$CardTypes[249] = "pa-oc3sm-mux-cbr120e1";
		$CardTypes[254] = "pa-ds3-mux-cbrt1";
		$CardTypes[255] = "pa-e3-mux-cbr120e1";
		$CardTypes[257] = "pa-8b-st";
		$CardTypes[258] = "pa-4b-u";
		$CardTypes[259] = "pa-fddi-fd";
		$CardTypes[260] = "pm-cpm-1e2w";
		$CardTypes[261] = "pm-cpm-2e2w";
		$CardTypes[262] = "pm-cpm-1e1r2w";
		$CardTypes[263] = "pm-ct1-csu";
		$CardTypes[264] = "pm-2ct1-csu";
		$CardTypes[265] = "pm-ct1-dsx1";
		$CardTypes[266] = "pm-2ct1-dsx1";
		$CardTypes[267] = "pm-ce1-balanced";
		$CardTypes[268] = "pm-2ce1-balanced";
		$CardTypes[269] = "pm-ce1-unbalanced";
		$CardTypes[270] = "pm-2ce1-unbalanced";
		$CardTypes[271] = "pm-4b-u";
		$CardTypes[272] = "pm-4b-st";
		$CardTypes[273] = "pm-8b-u";
		$CardTypes[274] = "pm-8b-st";
		$CardTypes[275] = "pm-4as";
		$CardTypes[276] = "pm-8as";
		$CardTypes[277] = "pm-4e";
		$CardTypes[278] = "pm-1e";
		$CardTypes[280] = "pm-m4t";
		$CardTypes[281] = "pm-16a";
		$CardTypes[282] = "pm-32a";
		$CardTypes[283] = "pm-c3600-1fe-tx";
		$CardTypes[284] = "pm-c3600-compression";
		$CardTypes[285] = "pm-dmodem";
		$CardTypes[288] = "pm-c3600-1fe-fx";
		$CardTypes[288] = "pm-c3600-1fe-fx";
		$CardTypes[290] = "as5200-carrier";
		$CardTypes[291] = "as5200-2ct1";
		$CardTypes[292] = "as5200-2ce1";
		$CardTypes[310] = "pm-as5xxx-12m";
		$CardTypes[330] = "wm-c2500-5in1";
		$CardTypes[331] = "wm-c2500-t1-csudsu";
		$CardTypes[332] = "wm-c2500-sw56-2wire-csudsu";
		$CardTypes[333] = "wm-c2500-sw56-4wire-csudsu";
		$CardTypes[334] = "wm-c2500-bri";
		$CardTypes[335] = "wm-c2500-bri-nt1";
		$CardTypes[360] = "wic-serial-1t";
		$CardTypes[364] = "wic-s-t-3420";
		$CardTypes[365] = "wic-s-t-2186";
		$CardTypes[366] = "wic-u-3420";
		$CardTypes[367] = "wic-u-2091";
		$CardTypes[368] = "wic-u-2091-2081";
		$CardTypes[400] = "pa-jt2";
		$CardTypes[401] = "pa-posdw";
		$CardTypes[402] = "pa-4me1-bal";
		$CardTypes[414] = "pa-a8t-x21";
		$CardTypes[415] = "pa-a8t-rs232";
		$CardTypes[416] = "pa-4me1-unbal";
		$CardTypes[417] = "pa-4r-fdx";
		$CardTypes[424] = ",pa-1fe-fx-nisl";
		$CardTypes[435] = ",mc3810-dcm";
		$CardTypes[436] = ",mc3810-mfm-e1balanced-bri";
		$CardTypes[437] = ",mc3810-mfm-e1unbalanced-bri";
		$CardTypes[438] = ",mc3810-mfm-e1-unbalanced";
		$CardTypes[439] = ",mc3810-mfm-dsx1-bri";
		$CardTypes[440] = ",mc3810-mfm-dsx1-csu";
		$CardTypes[441] = ",mc3810-vcm";
		$CardTypes[442] = ",mc3810-avm";
		$CardTypes[443] = ",mc3810-avm-fxs";
		$CardTypes[444] = ",mc3810-avm-fxo";
		$CardTypes[445] = ",mc3810-avm-em";
		$CardTypes[445] = ",mc3810-avm-em";
		$CardTypes[480] = ",as5300-4ct1";
		$CardTypes[481] = ",as5300-4ce1";
		$CardTypes[482] = ",as5300-carrier";
		$CardTypes[500] = ",vic-em";
		$CardTypes[501] = "vic-fxo";
		$CardTypes[502] = "vic-fxs";
		$CardTypes[503] = "vpm-2v";
		$CardTypes[504] = "vpm-4v";
		$CardTypes[530] = ",pos-qoc3-mm";
		$CardTypes[531] = ",pos-qoc3-sm";
		$CardTypes[532] = ",pos-oc12-mm";
		$CardTypes[533] = ",pos-oc12-sm";
		$CardTypes[534] = ",atm-oc12-mm";
		$CardTypes[535] = ",atm-oc12-sm";
		$CardTypes[536] = ",pos-oc48-mm-l";
		$CardTypes[537] = ",pos-oc48-sm-l";
		$CardTypes[538] = ",gsr-sfc";
		$CardTypes[539] = ",gsr-csc";
		$CardTypes[540] = ",gsr-csc4";
		$CardTypes[541] = ",gsr-csc8";
		$CardTypes[542] = ",gsr-sfc8";
		$CardTypes[545] = ",gsr-oc12chds3-mm";
		$CardTypes[546] = ",gsr-oc12chds3-sm";
		$CardTypes[546] = ",gsr-oc12chds3-sm";
		$CardTypes[546] = ",gsr-oc12chds3-sm";
		$CardTypes[605] = ",pm-atm25";

		$cards 	= $this->fcSnmpWalk($oids['cards']);
		$ctype 	= $this->fcSnmpWalk($oids['ctype']);
		$slotnum	= $this->fcSnmpWalk($oids['slotnum']);
		$cardSlots	= $this->fcSnmpGet($oids['cardSlots']) -1 ;

		$this->y = 0;

		if(count($cards) > 1)
		{
			echo "<BR>
					<TABLE border=0	 CLASS='tabela' CELLPADDING='3' ALIGN='center'>
					<TR><TH CLASS='thfont' COLSPAN='3'>Informa&ccedil;&otilde;es de Hardware</TH></TR>
					<TR BGCOLOR='#CDEDCF'><TD Class='topo' ALIGN='center'><B>Descri&ccedil;&atilde;o</B></TD><TD Class='topo' ALIGN='center'><B>Tipo</B></TD><TD Class='topo' ALIGN='center'><B>Slot</B></TD></TR>
					";

			for ($x = 0; $x<count($cards); $x++)
			{
				$CdTypes = ($CardTypes[$ctype[$x]]== NULL) ? $CardTypes[1] : $CardTypes[$ctype[$x]];

				$this->printInfoHardware($cards[$x], $CdTypes, $slotnum[$x]);
			}
			echo   "<TR>
						<TD COLSPAN='2' Class='base' BGCOLOR='#FFFFCC' ALIGN='center'>
							<B>N&uacute;mero mais elevado de Slot</B>
						</TD><td Class='base' BGCOLOR='#FFFFCC' ALIGN='center'>
							<B>$cardSlots</B>
						</TD>
					</TR>
				</TABLE>";
		}
	}

	public function printInfoHardware($cards, $CdTypes, $slotnum)
	{
		$classe = $this->oddCouple();
		echo "
			<TR>
				<TD CLASS='$classe'>$cards</TD>
				<TD CLASS='$classe'>$CdTypes</TD>
				<TD CLASS='$classe' ALIGN='center'>$slotnum</TD>
			</TR>
			";
	}

	public function memoryFlash()
	{
		if (!preg_match("/[1-11]\./i", $this->version))
   		{
				$oids = array
			(
			  "flashSupported"   => ".1.3.6.1.4.1.9.9.10.1.1.1.0",
			  "flashSize" 	   => ".1.3.6.1.4.1.9.9.10.1.1.2.1.2",
			  "flashFileName"	   => ".1.3.6.1.4.1.9.9.10.1.1.4.2.1.1.5",
			  "flashDeviceDescr" => ".1.3.6.1.4.1.9.9.10.1.1.2.1.8",
			  "flashFreeSpace"   => ".1.3.6.1.4.1.9.9.10.1.1.4.1.1.5"
			);
		}

		echo
			"<BR>
			<TABLE border=0	 CLASS='tabela' CELLPADDING='2' ALIGN='center' WIDTH='500'>
			<TR>
				<TH CLASS='thfont' COLSPAN='4'>&Iacute;ndices de Mem&oacute;ria Flash</TH>
			</TR>
			<TR BGCOLOR='#CDEDCF'>
				<TD Class='topo' ALIGN='center'><B>Filename</B></TD>
				<TD Class='topo' ALIGN='center'><B>Dispositivo</B></TD>
				<TD Class='topo' ALIGN='center'><B>Size (MB)</B></TD>
				<TD Class='topo' ALIGN='center'><B>Free (MB)</B></TD>
			</TR>";

            $flashSupported = $this->fcSnmpGet($oids{'flashSupported'});
            $flashSize = $this->fcSnmpWalk($oids{'flashSize'});

			for ($x = 0; $x< count($flashSize); $x++)
			{
                $flashSize[$x] = round( ($flashSize[$x]/(1024*1024)), 1);

				if($flashSize[$x])
				{
					$MIBs = $oids{'flashFileName'}    . '.' . ($x + 1) . ".1.1 " .
							$oids{'flashDeviceDescr'} . '.' . ($x + 1) . " " .
						    $oids{'flashFreeSpace'}   . '.' . ($x + 1) . ".1 ";

					list($flashFileName, $flashDeviceDescr, $flashFreeSpace) = $this->fcSnmpGet($MIBs);

					$flashFreeSpace	= round(($flashFreeSpace/(1024*1024)), 1);

					$this->printInfoFlash($flashFileName, $flashDeviceDescr, $flashSize[$x], $flashFreeSpace);
				 }
			}

		echo "</table>";
	}

	public function printInfoFlash($flashFileName, $flashDeviceDescr, $flashSize, $flashFreeSpace)
	{
		$classe = $this->oddCouple();

        echo   "<TR>
					<TD CLASS='$classe'> $flashFileName</TD>
			            <TD CLASS='$classe'>$flashDeviceDescr</TD>
					<TD CLASS='$classe'>$flashSize</TD>
					<TD CLASS='$classe'>$flashFreeSpace</TD>
				</TR>";
	}

    public function interfaces()
	{
		$oids = array
			(
				"sysUptime" => ".1.3.6.1.2.1.1.3.0",
				"IfIndex" => ".1.3.6.1.2.1.2.2.1.1",
				"IfDescr" => ".1.3.6.1.2.1.2.2.1.2",
				"Description" => ".1.3.6.1.2.1.31.1.1.1.18",
				"Ip" => ".1.3.6.1.2.1.4.20.1.1",
				"IpPorder" => ".1.3.6.1.2.1.4.20.1.2",
				"IfAdminStatus" => ".1.3.6.1.2.1.2.2.1.7",
				"IfOperStatus" => ".1.3.6.1.2.1.2.2.1.8",
				"LastChange" => ".1.3.6.1.2.1.2.2.1.9",
				"ifMac" => ".1.3.6.1.2.1.2.2.1.6"
			 );

		echo
		"	<BR>
			<TABLE border=1 width='97%' CLASS='tabela' CELLPADDING='2'>
			<TR><TH COLSPAN='8' CLASS='thfont'>Informa&ccedil;&otilde;es das Interfaces</TH></TR>
			<TR><TH CLASS='topo' ROWSPAN='2'>N&ordm;<BR>Interface</TH><TH CLASS='topo' ROWSPAN='2'>Tipo de <BR>Interface</TH><TH CLASS='topo' ROWSPAN='2'>Descri&ccedil;&atilde;o da Interface</TH><TH CLASS='topo' COLSPAN='2'>Status</TH><TH CLASS='topo' ROWSPAN='2'>Endere&ccedil;o<br>Ip</TH><TH CLASS='topo' ROWSPAN='2'>Endere&ccedil;o<br>MAC</TH><TH CLASS='topo' ROWSPAN='2'>Last Change</TH></TR>
			<TR><TH CLASS='topo'>Adm</TH><TH CLASS='topo'>Opr</TH></TR>
		";

        $this->y=0;

        echo $this->sysUpTime;

		$ArrayIndex = $this->fcSnmpWalk($oids{'IfIndex'});
	    $IP 		= $this->fcSnmpWalk($oids{'Ip'});
		$IpPorder	= $this->fcSnmpWalk($oids{'IpPorder'});


		if(count($IP) == count($IpPorder))
		{
			for ($i=0; $i < count($ArrayIndex); $i++)
			{
				for ($x=0; $x<count($IP); $x++)
				{
					$indice = $ArrayIndex[$i];

					if ($ArrayIndex[$i] == $IpPorder[$x])
						$IPs[$indice] = $IP[$x];
 				}
			}
		}

		for ($i=0; $i< count($ArrayIndex); $i++)
		{
			$index = $ArrayIndex[$i];

	   		$MIBs =	$oids{'IfDescr'}       	 . ".$index " .
	      		      $oids{'IfAdminStatus'} . ".$index " .
	      		      $oids{'IfOperStatus'}	 . ".$index " .
	      		      $oids{'ifMac'}		 . ".$index " .
	      		      $oids{'LastChange'}	 . ".$index " .
			    	  $oids{'Description'}   . ".$index ";

        	list($IfDescr, $IfAdminStatus, $IfOperStatus,
		  		$ifMac, $LastChange, $Description) = $this->fcSnmpGet($MIBs);

		  	$IfAdminStatus	= $this->status($IfAdminStatus);
			$IfOperStatus 	= $this->status($IfOperStatus);
		    $ifMac		    = $this->ifMacAddress($ifMac);
		  	$LastChange     = $this->lastChange($LastChange);

			$classe = $this->oddCouple();

			$ip = isset($IPs[$index]) ? $IPs[$index] : null;

			echo "<TR ALIGN='center'>
				<TD ALIGN='center' CLASS='$classe'>$index</TD>
				<TD CLASS='$classe'>$IfDescr</TD>
				<TD CLASS='$classe'>$Description</TD>
				<TD CLASS='$classe'>$IfAdminStatus</TD>
				<TD CLASS='$classe'>$IfOperStatus</TD>
				<TD CLASS='$classe'>$ip</TD>
				<TD CLASS='$classe'>$ifMac</TD>
				<TD CLASS='$classe'>$LastChange</TD>
			</TR>";
		}
		echo "</TABLE>";
	}

    public function status($status)
	{
		return (preg_match("/UP/i", $status)) ? "UP" : "DOWN";
	}

	public function ifMacAddress($Mac)
	{
		$ifMac = '&nbsp;';

		$partes = explode(":", strtoupper($Mac));

		for($i=0; $i<count($partes); $i++)
		{
			if ($partes[$i]=='0')
                $partes[$i]="00";

			$ifMac .= " " . $partes[$i];
		}

		return $ifMac;
	}

	public function lastChange($LsCh)
	{
  	    $LsCh	= substr($LsCh, 1);
		$aux	= explode(")",$LsCh);
		$LsCh	= substr($aux[0], 0, -2);

		$x = $this->UpTimeTickts - $LsCh;

		$day	= bcdiv($x, 86400);
		$x		= bcmod($x, 86400);
		$hour	= bcdiv($x, 3600);
		$x		= bcmod($x, 3600);
		$minute	= bcdiv($x, 60);
		$sec	= bcmod($x, 60);

		return "<B>$day</B>d <B>$hour</B>h <B>$minute</B>m e <B>$sec</B>s";
	}
}
