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
 *		module   : edt_frame.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */

session_start();
// include_once("php/dbconfig.php");
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

// Permet d'activer la modification de l'edt si on es un prof
if ($_SESSION['CnxGrp'] == 2 && $IDitem == 0 && ($IDedt == 0 || $IDedt == 2))
{
	$IDedt = 2;
	$IDitem = "-".$_SESSION['CnxID']."02";
	$IDuser = $_SESSION['CnxID'];
}

?>
<script>

var number_of_days_to_show_by_week_view = <?php echo getParam('numberDaysWeekViewEDT'); ?>;
</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title>	My Calendar </title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">


		<script>
			// Fonction qui vérifie si la date appelée est une date de vacances
			function checkHollidaysOnDate(date) {
				var arrayToSearchIn = '<?php echo checkIfHollidays(); ?>';
				if (arrayToSearchIn.indexOf(date) >= 0) var toReturn = "true";
				else var toReturn = "false";
				return toReturn;
			}
		</script>

		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


    <link href="css/calendar.css" rel="stylesheet" type="text/css" />

    <!-- On inclue le thème -->
		<script src="script/jquery.min.js"></script>
		<!-- <script>
		var jQuery = jQuery.noConflict();
		</script> -->
		<!-- <script src="vendor/jquery/jquery.min.js"></script> -->

		<!-- <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

		<!-- Permet de recrée des fonctions jQuery qui on disparues après la mise à jour de jQuery et qui bloquais les scripts -->
		<!-- <script src="js/jquery.browser.js"></script> -->

    <script src="script/Plugins/Common.js" type="text/javascript"></script>
    <script src="script/Plugins/datepicker_lang_US.js" type="text/javascript"></script>
    <script src="script/Plugins/jquery.datepicker.js" type="text/javascript"></script>

    <script src="script/Plugins/jquery.alert.js" type="text/javascript"></script>
    <script src="script/Plugins/jquery.ifrmdailog.js" defer="defer" type="text/javascript"></script>
    <script src="script/Plugins/wdCalendar_lang_<?php print($_SESSION["lang"]); ?>.js" type="text/javascript"></script>
		<script src="script/Plugins/jquery.calendar.js" type="text/javascript"></script>

		<!-- Fonctions personnelles -->
    <script src="js/functions.js" type="text/javascript"></script>



		<!-- **************** CSS DU THEME **************** -->
		<!-- Custom fonts for this template-->
		<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
	  <!-- Custom styles for this template-->
	  <link href="css/sb-admin-2.min.css" rel="stylesheet">
		<!-- CSS Customs -->
		<link href="css/custom.css" rel="stylesheet" type="text/css">


<style>

body {
	font-family: Nunito,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji" !important;
}

</style>


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

	$isModif = "false";
	$isReadonly = "true";
	$isDevoir = "false";

	// Si on est admin alors on peut modifier l'edt
	if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
	{
		$isModif = "true";
		$isReadonly = "false";

	}
	// Si on est prof alors on peut créer des cours (= indisponibilités) mais pas les modifier
	elseif ($_SESSION["CnxGrp"] == 2)
	{
		$isModif = "false";
		$isReadonly = "false";
	}
	else
	{
		$isModif = "false";
		$isReadonly = "true";
	}
	if($IDitem == 0)
	{
		// $isModif = "false";
		// $isReadonly = "true";
	}

	if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
	{
		$isDevoir = "true";
	}
	else
	{
		$isDevoir = "false";
	}
	?>
	</script>

	<style>
	.linkmodif {
		<?php $linkmodif = ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) ? "inline-block" : "none"; ?>
		display: <?php echo $linkmodif; ?>
	}
	</style>

    <script type="text/javascript">
		var stringdel_0 = "";
		var stringdel_1 = "";
		var stringdel_2 = "";
		var stringdel_3 = "";
		var stringdel_4 = "";
		var stringdel_5 = "";

		<?php if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) { ?>

		function delDay_0()
		{
			if (confirm("<?php echo $msg->read($EDT_ALLDEL); ?>"))
			{
				if(stringdel_0 != "")
				{
					$.ajax({url: "php/datafeed.db.php?method=removeDay&generique=off&ids="+stringdel_0, success: function(result){
						// $("#gridcontainer").reload();
						$('#showreflashbtn').click();
						// location.reload();
					}});
				}
			}
		}
		function delDay_1()
		{
			if (confirm("<?php echo $msg->read($EDT_ALLDEL); ?>"))
			{
				if(stringdel_1 != "")
				{
					$.ajax({url: "php/datafeed.db.php?method=removeDay&generique=off&ids="+stringdel_1, success: function(result){
						// $("#gridcontainer").reload();
						$('#showreflashbtn').click();
						// location.reload();
					}});
				}
			}
		}
		function delDay_2()
		{
			if (confirm("<?php echo $msg->read($EDT_ALLDEL); ?>"))
			{
				if(stringdel_2 != "")
				{
					$.ajax({url: "php/datafeed.db.php?method=removeDay&generique=off&ids="+stringdel_2, success: function(result){
						// $("#gridcontainer").reload();
						$('#showreflashbtn').click();
						// location.reload();
					}});
				}
			}
		}
		function delDay_3()
		{
			if (confirm("<?php echo $msg->read($EDT_ALLDEL); ?>"))
			{
				if(stringdel_3 != "")
				{
					$.ajax({url: "php/datafeed.db.php?method=removeDay&generique=off&ids="+stringdel_3, success: function(result){
						// $("#gridcontainer").reload();
						$('#showreflashbtn').click();
						// location.reload();
					}});
				}
			}
		}
		function delDay_4()
		{
			if (confirm("<?php echo $msg->read($EDT_ALLDEL); ?>"))
			{
				if(stringdel_4 != "")
				{
					$.ajax({url: "php/datafeed.db.php?method=removeDay&generique=off&ids="+stringdel_4, success: function(result){
						// $("#gridcontainer").reload();
						$('#showreflashbtn').click();
						// location.reload();
					}});
				}
			}
		}

		<?php
		}
		?>

		var op;

        $(document).ready(function() {
           var view="week";

            var DATA_FEED_URL = "php/datafeed.db.php";
            op = {
                view: view,
                theme:3,
				<?php
				$setdate  = ( isset($_GET["setdate"]) )
					? $_GET["setdate"]
					: $_SESSION["setdate"] ;
				$month_showdate = @intval(date("n", $setdate));
				$month_showdate = @$month_showdate - 1;
				?>
                showday: <?php if(isset($setdate) && $setdate != "") {echo "new Date(".date("Y", $setdate).",".$month_showdate.",".date("d", $setdate).")";} else {echo "new Date()";} ?>,
                // showday: new Date(),
				enableDrag: <?php echo $isModif; ?>,
				readonly: <?php echo $isReadonly; ?>,
                EditCmdhandler:Edit,
                DupliCmdhandler:Duplication,
                RetablirCmdhandler:Retablir,
                DeleteCmdhandler:Delete,

								<?php
									if (getParam("edtDebug") == 1) $edtDebug = "View";
									else $edtDebug = "false";
								?>
								ViewCmdhandler:<?php echo $edtDebug; ?>,

                onWeekOrMonthToDay:wtd,
                onBeforeRequestData: cal_beforerequest,
                onAfterRequestData: cal_afterrequest,
                onRequestDataError: cal_onerror,
                autoload:true,
                url: DATA_FEED_URL + "?method=list&IDcentre=<?php echo $IDcentre; ?>&IDedt=<?php echo $IDedt; ?>&IDitem=<?php echo $IDitem; ?>&IDclass=<?php echo $IDclass; ?>&IDuser=<?php echo $IDuser; ?>&IDdata=<?php echo $IDdata; ?>&generique=<?php echo $generique; ?>&isModif=<?php echo $isModif; ?>&isDevoir=<?php echo $isDevoir; ?>",
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
            }
            $("#caltoolbar").noSelect();

            $("#hdtxtshow").datepicker({ picker: "#txtdatetimeshow", showtarget: $("#txtdatetimeshow"),
            onReturn:function(r){
                            var p = $("#gridcontainer").gotoDate(r).BcalGetOp();
                            if (p && p.datestrshow) {
                                $("#txtdatetimeshow").text(p.datestrshow);
                            }
                     }
            });
            function cal_beforerequest(type)
            {
                var t="Chargement...";
                switch(type)
                {
                    case 1:
                        t="Chargement...";
                        break;
                    case 2:
                    case 3:
                    case 4:
                        t="La requête est en cours de traitement ...";
                        break;
                }
                // $("#errorpannel").hide();
                // $("#loadingpannel").html(t).show();
								$('#loader', parent.document).show();
            }
            function cal_afterrequest(type)
            {
                switch(type)
                {
                    case 1:
                        // $("#loadingpannel").hide();
												$('#loader', parent.document).hide();
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $("#loadingpannel").html("Success!");
												// window.setTimeout(function(){ $("#loadingpannel").hide();},2000);
                        window.setTimeout(function(){ $('#loader', parent.document).hide();},2000);
                    break;
                }

            }
            function cal_onerror(type,data)
            {
	            // $("#errorpannel").show();
							console.log('Il y a eu une erreur: Type: ' + type + ' - Data: ' + data);
            }
            function Edit(data)
            {
							var data2min = data[2].getMinutes();
							var data3min = data[3].getMinutes();
							if(data[2].getMinutes() == 0) data2min = "00";
							if(data[3].getMinutes() == 0) data3min = "00";
							var formatstart = (data[2].getMonth()+1)+"/"+data[2].getDate()+"/"+data[2].getFullYear()+" "+data[2].getHours()+":"+data2min;
							var formatend = (data[3].getMonth()+1)+"/"+data[3].getDate()+"/"+data[3].getFullYear()+" "+data[3].getHours()+":"+data3min;
							var IDxx = data[0];

							var eurl="edit.db.php?type=new&start="+ formatstart +"&end="+ formatend +"&IDedt="+ $("#param_IDedt").val() +"&IDcentre="+ $("#param_IDcentre").val() +"&IDitem="+ $("#param_IDitem").val() +"&IDuser="+ $("#param_IDuser").val() +"&IDclass="+ $("#param_IDclass").val() +"&IDdata="+ $("#param_IDdata").val() +"&lang="+ $("#param_lang").val() +"&sid="+ $("#param_sid").val() +"&generique="+ $("#param_generique").val() +"&IDuser="+ $("#param_IDuser").val() +"&IDxx="+ IDxx;
							if(data)
							{
								var url = StrFormat(eurl,data);
								OpenModelWindow(url,{ width: 900, height: 500, caption: i18n.xgcalendar.update_detail,onclose:function(){
									// $("#gridcontainer").reload();
									$('#showreflashbtn').click();
									// location.reload();
								}});
							}
            }
			function Duplication(data)
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

				var eurl="duplique.db.php?type=new&start="+ formatstart +"&end="+ formatend +"&IDedt="+ $("#param_IDedt").val() +"&IDcentre="+ $("#param_IDcentre").val() +"&IDitem="+ $("#param_IDitem").val() +"&IDuser="+ $("#param_IDuser").val() +"&IDclass="+ $("#param_IDclass").val() +"&IDdata="+ $("#param_IDdata").val() +"&lang="+ $("#param_lang").val() +"&sid="+ $("#param_sid").val() +"&generique="+ $("#param_generique").val() +"&IDuser="+ $("#param_IDuser").val() +"&IDxx="+ IDxx;
                if(data)
                {
                    var url = StrFormat(eurl,data);
                    OpenModelWindow(url,{ width: 900, height: 500, caption: "Dupliquer",onclose:function(){
                       // $("#gridcontainer").reload();
											 $('#showreflashbtn').click();
											 // location.reload();
                    }});
                }
            }
  			function Retablir(data)
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


                if(data)
                {
                    var url = StrFormat(eurl,data);
                    OpenModelWindow(url,{ width: 900, height: 150, caption: "Retablir",onclose:function(){
                       // $("#gridcontainer").reload();
											 $('#showreflashbtn').click();
											 // location.reload();
                    }});
                }
            }
            function View(data)
            {
                var str = "";
                $.each(data, function(i, item){
                    str += "[" + i + "]: " + item + "\n";
                });
                alert(str);
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
                }

				// Lien impression
				var link_print = $("#link_print", window.parent.document).attr("href").replace("&view=week", "&view=day").replace("&view=month", "&view=day");
				$("#link_print", window.parent.document).attr("href", link_print);
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
                }

				// Lien impression
				var link_print = $("#link_print", window.parent.document).attr("href").replace("&view=day", "&view=week").replace("&view=month", "&view=week");
				$("#link_print", window.parent.document).attr("href", link_print);
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
                }

				// Lien impression
				var link_print = $("#link_print", window.parent.document).attr("href").replace("&view=day", "&view=month").replace("&view=week", "&view=month");
				$("#link_print", window.parent.document).attr("href", link_print);
            });

            $("#showreflashbtn").click(function(e){
							console.log('reload')
							$('#modalEventModifHtml').remove();
                $("#gridcontainer").reload();

								// $('#showreflashbtn').click();
								// location.reload();
            });

            //Add a new event
            $("#faddbtn").click(function(e) {
                var url ="edit.db.php";
                OpenModelWindow(url,{ width: 500, height: 500, caption: "Create New Calendar"});
            });
            //go to today
            $("#showtodaybtn").click(function(e) {
                var p = $("#gridcontainer").gotoDate().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }


            });
            //previous date range
            $("#sfprevbtn").click(function(e) {
                var p = $("#gridcontainer").previousRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }

            });
            //next date range
            $("#sfnextbtn").click(function(e) {
                var p = $("#gridcontainer").nextRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
					//alert(p.showday.getDate());
                }
            });
        });
    </script>
</head>
<body>
    <div>

      <div id="calhead" style="padding-left:1px;padding-right:1px;">
				<div class="cHead">
					<div id="loadingpannel" class="ptogtitle loadicon" style="display: none;">Chargement...</div>
					<div id="errorpannel" class="ptogtitle loaderror" style="display: none;">Désolé, il y a eu une erreur, merci de réessayer plus tard...</div>
				</div>
				<?php // echo "<script>alert(".$generique.");</script>"; ?>
				<?php if($generique == "off" || $generique == "" || $generique == "modif") { ?>
          <div id="caltoolbar" class="ctoolbar" style="display: none;">
						<div class="btnseparator"></div>
						<div id="showtodaybtn" class="btn btn-primary">
							<?php echo $msg->read($EDT_TODAY); ?>
						</div>
						<div class="btnseparator"></div>

						<div id="showdaybtn" class="btn btn-primary">
							Jour
						</div>
						<div  id="showweekbtn" class="btn btn-primary">
							<?php echo $msg->read($EDT_WEEK); ?>
						</div>
						<div  id="showmonthbtn" class="btn btn-primary">
							Mois
						</div>
						<div class="btnseparator"></div>
						<div  id="showreflashbtn" class="fbutton">
							<?php echo $msg->read($EDT_REFRESH); ?>
						</div>
						<div class="btnseparator"></div>



						<div id="sfprevbtn" title="Prev"  class="fbutton">
							<span class="fprev"></span>

						</div>
						<div id="sfnextbtn" title="Next" class="fbutton">
							<span class="fnext"></span>
						</div>
						<div class="fshowdatep fbutton">
							<div>
								<input type="hidden" name="txtshow" id="hdtxtshow" />
								<span id="txtdatetimeshow"></span>
							</div>
						</div>
						<div class="fshowdatep fbutton" style="float: right">
							<div>
								<span id="txtsemaineshow"></span>
							</div>
						</div>
						<div class="clear"></div>


          </div>
				<?php } ?>
      </div>
      <div style="padding:1px;">

        <div class="t1 chromeColor">
            &nbsp;</div>
        <div class="t2 chromeColor">
            &nbsp;</div>
        <div id="dvCalMain" class="calmain printborder">
	        <div id="gridcontainer" style="overflow-y: visible;">
	        </div>
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

<input type="hidden" name="hollidaysShow" id="hollidaysShow">


<?php
	// Fonction qui génère une liste avec les différentes dates de vacances
	function checkIfHollidays()
	{
	  $rowToReturn = ";";

	  $query  = "select _vacances from config_centre ";
	  $query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
	  $query .= "order by _IDcentre";
	  $result = mysqli_query($mysql_link, $query);
	  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

	    // Pour chaques périodes de vacances
	    $jsonOfRow = json_decode($row[0], TRUE);
	    foreach ($jsonOfRow['start'] as $key => $value)
	    {
	      $end = $jsonOfRow['end'][$key];

	      if ($value != "")
	      {
	        // Transformation des formats de dates (EU: d-m-Y US: d/m/Y)
	        $newValue = str_replace("/", "-", $value);
	        $currentDate = strtotime($newValue);

	        $currentDate = date('d/m/Y', $currentDate);
	        $newEnd = date("d/m/Y", strtotime(str_replace("/", "-", $end). ' + 1 days'));

	        while ($currentDate != $newEnd)
	        {
	          // On enregistre la valeur dans un tableau
	          $newformat = date('Ymd',strtotime(str_replace("/", "-", $currentDate)));
	          $rowToReturn .= $newformat.";";

	          // On gère les transformation entre les formats de dates américains et européens (EU: d-m-Y US: d/m/Y)
	          $newValue = str_replace("/", "-", $currentDate);
	          $currentDate = strtotime($newValue);

	          $newValue2 = str_replace("/", "-", $currentDate);

	          // On ajoute un jour à la boucle
	          $newCurrentDate = date('d-m-Y', $newValue2);
	          $currentDate = date('d/m/Y', strtotime($newCurrentDate. ' + 1 days'));
	        }
	      }
	    }
	  }
	  // On renvois le tableau avec les dates de vacances au bon format
	  return $rowToReturn;
	}
?>


</body>
</html>



<!-- <script src="script/sweetalert2.min.js"></script> -->
<!-- <script src="script/sweetalert.min.js"></script> -->
<script src="script/swal2fire.js"></script>
