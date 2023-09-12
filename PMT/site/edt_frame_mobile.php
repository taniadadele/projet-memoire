<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

   This file is part of Prométhée.

   Prométhée is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prométhée is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/


/*
 *		module   : edt_frame_mobile.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */
?>

<?php
session_start();
include_once("php/dbconfig.php");
include("include/urlencode.php");
include("php/functions.php");
require_once "page_session.php";
$msg  = new TMessage("msg/".$_SESSION["lang"]."/edt.php", $_SESSION["ROOTDIR"]);
require "msg/edt.php";

$IDcentre = ( @$_POST["IDcentre"] )					// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
$IDedt    = ( @$_POST["IDedt"] )					// type d'edt
	? (int) $_POST["IDedt"]
	: (int) @$_GET["IDedt"];
$IDitem   = ( @$_POST["IDitem"] )					// Identifiant de la salle, catégorie ou groupe classe
	? (int) $_POST["IDitem"]
	: (int) @$_GET["IDitem"] ;
$IDclass  = ( @$_POST["IDclass"] )					// Identifiant de la classe
	? (int) $_POST["IDclass"]
//	: (int) @$_GET["IDclass"] ;
	: (int) (@$_GET["IDclass"] ? $_GET["IDclass"] : $IDitem) ;
$IDuser   = ( @$_POST["IDuser"] )					// Identifiant de l'utilisateur
	? (int) $_POST["IDuser"]
	: (int) @$_GET["IDuser"] ;
$IDdata   = ( @$_POST["IDdata"] )					// Identifiant de l'edt
	? (int) $_POST["IDdata"]
	: (int) @$_GET["IDdata"] ;
$generique   = ( @$_POST["generique"] )
	?  $_POST["generique"]
	:  @$_GET["generique"] ;
$year   = ( @$_POST["year"] )
	?  $_POST["year"]
	:  @$_GET["year"] ;
$month   = ( @$_POST["month"] )
	?  $_POST["month"]
	:  @$_GET["month"] ;
$day   = ( @$_POST["day"] )
	?  $_POST["day"]
	:  @$_GET["day"] ;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title>	My Calendar </title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link href="css/dailog.css" rel="stylesheet" type="text/css" />
    <link href="css/calendar.css" rel="stylesheet" type="text/css" />
    <link href="css/dp.css" rel="stylesheet" type="text/css" />
    <link href="css/alert.css" rel="stylesheet" type="text/css" />
    <link href="css/main.css" rel="stylesheet" type="text/css" />


    <script src="script/jquery.min.js" type="text/javascript"></script>

    <script src="script/Plugins/Common.js" type="text/javascript"></script>
    <script src="script/Plugins/datepicker_lang_US.js" type="text/javascript"></script>
    <script src="script/Plugins/jquery.datepicker.js" type="text/javascript"></script>

    <script src="script/Plugins/jquery.alert.js" type="text/javascript"></script>
    <script src="script/Plugins/jquery.ifrmdailog.js" defer="defer" type="text/javascript"></script>
    <script src="script/Plugins/wdCalendar_lang_<?php print($_SESSION["lang"]); ?>.js" type="text/javascript"></script>
    <script src="script/Plugins/jquery.calendar.mobile.js" type="text/javascript"></script>

		<!-- Fonctions personnelles -->
		<script src="js/functions.js" type="text/javascript"></script>

	<script type="text/javascript">
	// déclaration du tableau javascript
	var tabJS = new Array();

	<?php
	// semaine S1 / S2
	$query1  = "select _semaines from config_centre ";
	$query1 .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDcentre = $IDcentre ";
	$query1 .= "order by _IDcentre";

	$result1 = mysqli_query($mysql_link, $query1);
	$rows1    = ( $result1 ) ? mysqli_fetch_row($result1) : 0 ;

	$tab = json_decode($rows1[0]);
	$tabsem = Array();
	$tabsem = objectToArray($tab);

	// remplissage du tableau js
	foreach($tabsem as $key => $val)
	{
		echo "tabJS[".$key."] = '$val';";
	}
	?>
	</script>

    <script type="text/javascript">
		function DefSemaineNum(aaaa, mm, jj)
		{
			//initialisation des variables
			//----------------------------
			var MaDate  = new Date(aaaa,mm,jj);//date a traiter
			var annee = MaDate.getFullYear();//année de la date à traiter
			var NumSemaine = 0,//numéro de la semaine

			// calcul du nombre de jours écoulés entre le 1er janvier et la date à traiter.
			// ----------------------------------------------------------------------------
			// initialisation d'un tableau avec le nombre de jours pour chaque mois
			ListeMois = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
			// si l'année est bissextile alors le mois de février vaut 29 jours
			if (annee %4 == 0 && annee %100 !=0 || annee %400 == 0) {ListeMois[1]=29};
			// on parcours tous les mois précédants le mois à traiter
			// et on calcul le nombre de jour écoulé depuis le 1er janvier dans TotalJour
			var TotalJour=0;
			for(cpt=0; cpt<mm; cpt++){TotalJour+=ListeMois[cpt];}
			TotalJour+=jj;

			//Calcul du nombre de jours de la première semaine de l'année à retrancher de TotalJour
			//-------------------------------------------------------------------------------------
			//on initialise dans DebutAn le 1er janvier de l'année à traiter
			DebutAn = new Date(annee,0,1);
			//on determine ensuite le jour correspondant au 1er janvier
			//de 1 pour un lundi à 7 pour un dimanche/
			var JourDebutAn;
			JourDebutAn=DebutAn.getDay();
			if(JourDebutAn==0){JourDebutAn=7};

			//Calcul du numéro de semaine
			//----------------------------------------------------------------------
			//on retire du TotalJour le nombre de jours que dure la première semaine
			TotalJour-=8-JourDebutAn;
			//on comptabilise cette première semaine
			NumSemaine = 1;
			//on ajoute le nombre de semaine compléte (sans tenir compte des jours restants)
			NumSemaine+=Math.floor(TotalJour/7);
			// s'il y a un reste alors le n° de semaine est incrémenté de 1
			if(TotalJour%7!=0){NumSemaine+=1};

			return(NumSemaine);
		}

		var op;

        $(document).ready(function() {
           var view="day";

            var DATA_FEED_URL = "php/datafeed.db.mobile.php";
            op = {
                view: view,
                theme:3,
				readonly: true,
				enableDrag: false,
                showday: new Date(<?php echo "$year, $month, $day"; ?>),
                EditCmdhandler:Edit,
                DeleteCmdhandler:Delete,
                ViewCmdhandler:View,
                onWeekOrMonthToDay:wtd,
                onBeforeRequestData: cal_beforerequest,
                onAfterRequestData: cal_afterrequest,
                onRequestDataError: cal_onerror,
                autoload:true,
                url: DATA_FEED_URL + "?method=list&IDcentre=<?php echo $IDcentre; ?>&IDedt=<?php echo $IDedt; ?>&IDitem=<?php echo $IDitem; ?>&IDclass=<?php echo $IDclass; ?>&IDuser=<?php echo $IDuser; ?>&IDdata=<?php echo $IDdata; ?>&generique=<?php echo $generique; ?>&year=<?php echo $year; ?>&month=<?php echo $month; ?>&day=<?php echo $day; ?>",
                quickAddUrl: DATA_FEED_URL + "?method=add",
                quickUpdateUrl: DATA_FEED_URL + "?method=update&generique=<?php echo $generique; ?>",
                quickDeleteUrl: DATA_FEED_URL + "?method=remove&generique=<?php echo $generique; ?>"
            };
            var $dv = $("#calhead");
            var _MH = document.documentElement.clientHeight;
            var dvH = $dv.height() + 2;
            op.height = _MH - dvH;
            op.eventItems =[];

            var p = $("#gridcontainer").bcalendar(op).BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
                $("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
            }
            $("#caltoolbar").noSelect();

            $("#hdtxtshow").datepicker({ picker: "#txtdatetimeshow", showtarget: $("#txtdatetimeshow"),
            onReturn:function(r){
                            var p = $("#gridcontainer").gotoDate(r).BcalGetOp();
                            if (p && p.datestrshow) {
                                $("#txtdatetimeshow").text(p.datestrshow);
								$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                            }
                     }
            });
            function cal_beforerequest(type)
            {
                var t="Loading data...";
                switch(type)
                {
                    case 1:
                        t="Loading data...";
                        break;
                    case 2:
                    case 3:
                    case 4:
                        t="The request is being processed ...";
                        break;
                }
                $("#errorpannel").hide();
                $("#loadingpannel").html(t).show();
            }
            function cal_afterrequest(type)
            {
                switch(type)
                {
                    case 1:
                        $("#loadingpannel").hide();
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $("#loadingpannel").html("Success!");
                        window.setTimeout(function(){ $("#loadingpannel").hide();},2000);
                    break;
                }

            }
            function cal_onerror(type,data)
            {
                $("#errorpannel").show();
            }
            function Edit(data)
            {
				var data2min = data[2].getMinutes();
				var data3min = data[3].getMinutes();
				if(data[2].getMinutes() == 0)
				{
					data2min = "00";
				}
				if(data[3].getMinutes() == 0)
				{
					data3min = "00";
				}
				var formatstart = (data[2].getMonth()+1)+"/"+data[2].getDate()+"/"+data[2].getFullYear()+" "+data[2].getHours()+":"+data2min;
				var formatend = (data[3].getMonth()+1)+"/"+data[3].getDate()+"/"+data[3].getFullYear()+" "+data[3].getHours()+":"+data3min;
				var IDxx = data[0];

				var eurl="edit.db.php?type=new&start="+ formatstart +"&end="+ formatend +"&IDedt="+ $("#param_IDedt").val() +"&IDcentre="+ $("#param_IDcentre").val() +"&IDitem="+ $("#param_IDitem").val() +"&IDuser="+ $("#param_IDuser").val() +"&IDclass="+ $("#param_IDclass").val() +"&IDdata="+ $("#param_IDdata").val() +"&lang="+ $("#param_lang").val() +"&sid="+ $("#param_sid").val() +"&generique="+ $("#param_generique").val() +"&IDuser="+ $("#param_IDuser").val() +"&IDxx="+ IDxx;
                if(data)
                {
                    var url = StrFormat(eurl,data);
                    OpenModelWindow(url,{ width: 600, height: 350, caption: i18n.xgcalendar.update_detail,onclose:function(){
                       $("#gridcontainer").reload();
                    }});
                }
            }
            function View(data)
            {
                var str = "";
                $.each(data, function(i, item){
                    str += "[" + i + "]: " + item + "\n";
                });
            }
            function Delete(data,callback)
            {

                $.alerts.okButton="Ok";
                $.alerts.cancelButton="Cancel";
                hiConfirm("Are You Sure to Delete this Event", 'Confirm',function(r){ r && callback(0);});
            }
            function wtd(p)
            {
               if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $("#showdaybtn").addClass("fcurrent");
            }
            //to show day view
            $("#showdaybtn").click(function(e) {
                //document.location.href="#day";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("day").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }
            });
            //to show week view
            $("#showweekbtn").click(function(e) {
                //document.location.href="#week";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("week").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }

            });
            //to show month view
            $("#showmonthbtn").click(function(e) {
                //document.location.href="#month";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("month").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }
            });

            $("#showreflashbtn").click(function(e){
                $("#gridcontainer").reload();
            });

            //Add a new event
            $("#faddbtn").click(function(e) {
                var url ="edit.db.php";
                OpenModelWindow(url,{ width: 500, height: 400, caption: "Create New Calendar"});
            });
            //go to today
            $("#showtodaybtn").click(function(e) {
                var p = $("#gridcontainer").gotoDate().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }


            });
            //previous date range
            $("#sfprevbtn").click(function(e) {
                var p = $("#gridcontainer").previousRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }

            });
            //next date range
            $("#sfnextbtn").click(function(e) {
                var p = $("#gridcontainer").nextRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					// alert(DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate()));
					//alert(p.showday.getDate());
					$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
                }
            });

			$("#txtsemaineshow").text(i18n.xgcalendar.semaine +" "+tabJS[DefSemaineNum(p.showday.getFullYear(), p.showday.getMonth(), p.showday.getDate())]);
        });
    </script>
</head>
<body>
    <div>


      <div style="padding:1px;">

        <div class="t1 chromeColor">
            &nbsp;</div>
        <div class="t2 chromeColor">
            &nbsp;</div>
        <div id="dvCalMain" class="calmain printborder">
            <div id="gridcontainer" style="overflow-y: visible;">
            </div>
        </div>
        <div class="t2 chromeColor">

            &nbsp;</div>
        <div class="t1 chromeColor">
            &nbsp;
        </div>
        </div>

  </div>

<input type="hidden" id="param_IDedt" value="<?php echo $IDedt; ?>" />
<input type="hidden" id="param_IDcentre" value="<?php echo $IDcentre; ?>" />
<input type="hidden" id="param_IDitem" value="<?php echo $IDitem; ?>" />
<input type="hidden" id="param_IDuser" value="<?php echo $IDuser; ?>" />
<input type="hidden" id="param_IDclass" value="<?php echo $IDclass; ?>" />
<input type="hidden" id="param_IDdata" value="<?php echo $IDdata; ?>" />
<input type="hidden" id="param_lang" value="<?php echo $_SESSION["lang"]; ?>" />
<input type="hidden" id="param_sid" value="<?php echo myurlencode($_SESSION["sessID"]); ?>" />
<input type="hidden" id="param_generique" value="<?php echo $generique ?>" />
<input type="hidden" id="param_IDuser" value="<?php echo $IDuser ?>" />

<script>
var p = $("#gridcontainer").bcalendar(op).BcalGetOp();
$("#txtdatetimeshow").text(p.datestrshow);
</script>

</body>
</html>
