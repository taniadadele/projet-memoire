<?php
				switch ($item) {
					// déconnexion
					case '-1': $page = 'user_login.php'; break;

					// gestion des connexions
					case '1':
						switch ($cmde) {
							// Création de compte
							case 'new': if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) $page = 'user_'.$cmde.'.php'; break;

							case 'account':	// compte utilisateur
							case 'lost':		// mot de passe perdu
								$page = 'user_'.$cmde.'.php';
								break;
							// liste des utilisateurs
							default: if ($_SESSION['CnxGrp'] > 1) $page = 'user_visu.php'; break;
						}
						break;




					case '4':	// messagerie instantannée (post-it)
						switch ($cmde) {
							case 'post':	// création d'un nouveau post-it
							case 'visu':	// visualisation des post-it
								$page = RESSOURCE_PATH['PAGES_FOLDER'].'postit/postit_'.$cmde.'.php';
								break;
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'postit/postit.php'; break;
						}
						break;

					// statistiques
					case '7':
						switch ($cmde) {
							case 'dba':	// liste des bases de données installées
							case 'items':	// liste des articles des auteurs
								$page = RESSOURCE_PATH['PAGES_FOLDER'].'stats/stats_'.$cmde.'.htm';
								break;
							default:
								$page = RESSOURCE_PATH['PAGES_FOLDER'].'stats/stats.htm';
								break;
						}
						$page = 'maintenance';		// On affiche la page de maintenance
						break;


					case '9':	// Liste des promos
						$page = RESSOURCE_PATH['PAGES_FOLDER'].'campus/campus_'.$cmde.'.php';
						// $page = 'maintenance';		// On affiche la page de maintenance
						break;

					case '11' :	// Liste de diffusion
						switch ($cmde) {
							// nouvelle liste / gestion de liste existante
							case 'gestion': $page = RESSOURCE_PATH['PAGES_FOLDER'].'lidi/lidi_'.$cmde.'.php'; break;
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'lidi/lidi.php'; break;
						}
						break;

					case '13' :	// cahier de texte numérique (CTN)
						switch ($cmde) {
							case 'post':	// TODO: Vérifier l'utilité
							case 'post_add':
							case 'gestion':
								if ($_SESSION['CnxGrp'] > 1) $page = RESSOURCE_PATH['PAGES_FOLDER'].'ctn/ctn_'.$cmde.'.php';
								break;
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'ctn/ctn.php'; break;
						}
						break;

					// Page de liste des heures effectuées pour les formateurs
					case '16': if ($_SESSION['CnxGrp'] >= 2) $page = "user_planning.php"; break;

					// A actualiser
					case '20' :	// saisie des flash infos
						// accès réservé aux modérateurs ou aux gestionnaires
						if ($_SESSION['CnxGrp'] > 1)
							switch ($cmde) {
								case 'post':	// saisie des flash infos
								case 'gestion':	// gestion des flash infos
									$page = 'flash_'.$cmde.'.php';
									$reload = 'on';
									break;
								// flash info Prométhée
								default: $page = 'flash_promethee.htm'; break;
							}
						break;

					case '21' :	// configuration intranet
						switch ($cmde) {
							case 'dba':	// gestion de la base de données
							case 'skin':	// gestion des revêtements
							case 'menu':	// gestion des menus
							case 'edt':	// gestion de l'edt
							case 'access':	// gestion des accès
							case 'usrmenu':	// gestion des menus utilisateurs
							case 'pole':	// gestion de la base de données des pôles
							case 'submenu':	// gestion des sous menus
								if ($_SESSION['CnxAdm'] == 255) $page = RESSOURCE_PATH['PAGES_FOLDER'].'config/config_'.$cmde.'.php';
								break;
							case 'skin_css':
								if ($_SESSION['CnxAdm'] == 255) $page = RESSOURCE_PATH['PAGES_FOLDER'].'config/skin/config_skin_theme.php';
								break;
							// config générale
							default: if ($_SESSION['CnxAdm'] == 255) $page = RESSOURCE_PATH['PAGES_FOLDER'].'config/config_ent.php'; break;
						}
						$reload = 'on';
						break;


					// Nouveau module de calendrier
					case '22': $page = RESSOURCE_PATH['PAGES_FOLDER'].'calendar/calendar.php'; break;


					// TODO: vérifier utilité et mettre à jours
					case '27' :	// accès réservé au Big Chef
						if ($_SESSION['CnxAdm'] == 255) {
							switch ($cmde) {
								case 'import' :	// import des données
								case 'reset' :	// RAZ des tables
								case 'csv' :	// export logs d'erreurs
									$page = 'admin_'.$cmde.'.php';
									break;
							}
						}
						break;


					case '28' :	// GED - Mes fichiers
						switch ($cmde) {
							case 'shared': $page = 'ged_files_shared.php'; break;
							case 'search': $page = 'ged_files_search.php'; break;
							case 'userFiles': if ($_SESSION['CnxAdm'] == 255) $page = 'ged_specific_user_files.php'; break;
							case 'myWork': if (getParam('importCopies') == 1) $page = 'ged_files_copies.php'; break;
							// Page de téléchargement de fichiers partagés avec le lien
							case 'link_shared': $page = 'ged_files_link_shared.php'; break;
							default: $page = 'ged_files.php'; break;
						}
						break;

					// Liste des éléments de l'emploi du temps
					case '29': $page = 'edt_list.php'; break;

					case '38':	// gestion des listes élèves
						switch ($cmde) {
							// gestion des élèves
							// case 'gestion': if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) $page = RESSOURCE_PATH['PAGES_FOLDER'].'student/student_'.$cmde.'.php'; break;
							case 'gestion':
								$page = 'maintenance';		// On affiche la page de maintenance
								break;
							// Page de modification d'un compte
							case 'account': if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) $page = RESSOURCE_PATH['PAGES_FOLDER'].'student/student_'.$cmde.'.php'; break;

							case 'groupe' :	// liste des groupes
							case 'listgroupe' :	// liste des groupes
								$page = RESSOURCE_PATH['PAGES_FOLDER'].'student/student_'.$cmde.'.php';
								break;
							// liste des élèves
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'student/student_visu.php'; break;
						}

							// $page = 'maintenance';		// On affiche la page de maintenance
						break;

					case '60':	// bulletins de notes
						switch ($cmde) {
							// modèle vue controleur
							case 'mvc': require RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_'.$cmde.'.php'; break;

							case 'show':
							case 'visu':
							case 'post':	// accès réservé au personnel
								if ($_SESSION['CnxAdm'] == 255 || (getParam('can_teachers_modify_grades') && $_SESSION['CnxGrp'] > 1)) $page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_'.$cmde.'.php';
								else $page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_view.php';

								if ($cmde != 'post') $page = 'maintenance';		// On affiche la page de maintenance
								break;
							case 'view' :
								//if ( getAccess() == 2 )
									$page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_'.$cmde.'.php';

									$page = 'maintenance';		// On affiche la page de maintenance
									break;
							case 'gestion' :	// accès réservé au Big Chef
								if ($_SESSION['CnxAdm'] == 255) $page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_'.$cmde.'.php';

								$page = 'maintenance';		// On affiche la page de maintenance
								break;
							case 'barcode':
								// if ($_SESSION['CnxGrp'] > 1) $page = "notes_$cmde.php";
								if ($_SESSION['CnxGrp'] > 1 && getParam('isCurrentlyWorking') == 2) $page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_barcode_file_list.php';
								elseif ($_SESSION['CnxGrp'] > 1) $page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_barcode.php';

								$page = 'maintenance';		// On affiche la page de maintenance
								break;

							default : $page = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes.php'; break;
							}
						break;




					case '61': // Import des comptes bancaires
						switch($cmde) {
							case 'import': if ($_SESSION['CnxAdm'] == 255) $page = 'bank_'.$cmde.'.php'; break;
							default: $page = 'bank_list.php'; break;
						}
						break;

					case '62': // Page de vue des heures de stages par promo
						if ($_SESSION['CnxGrp'] > 1) $page = 'stage_hours_view.php';
						$page = 'maintenance';		// On affiche la page de maintenance
						break;


					// abscences
					case '63': $page = RESSOURCE_PATH['PAGES_FOLDER'].'absent/absent.php'; break;

					// emplois du temps
					case '64': $page = 'edt.php'; break;

					// Page de liste des rattrapages
					case '65':
						$page = RESSOURCE_PATH['PAGES_FOLDER'].'rattrapage/rattrapage_view.php';
						$page = 'maintenance';		// On affiche la page de maintenance
						break;


					case '69':	// Syllabus
						switch ($cmde) {
							case 'new': if ($_SESSION['CnxAdm'] == 255 or $_SESSION['CnxGrp'] > 1) $page = RESSOURCE_PATH['PAGES_FOLDER'].'syllabus/syllabus_gestion.php'; break;
							case 'view': $page = RESSOURCE_PATH['PAGES_FOLDER'].'syllabus/syllabus_'.$cmde.'.php'; break;
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'syllabus/syllabus.php'; break;
						}
						break;

					// Examens
					case '70':
						switch ($cmde) {
							case 'new': if ($_SESSION['CnxAdm'] == 255 or $_SESSION['CnxGrp'] > 1) $page = RESSOURCE_PATH['PAGES_FOLDER'].'examen/examen_'.$cmde.'.php'; break;
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'examen/examen.php'; break;
						}
						break;

					case '92' :	// logs de connexion
						// accès réservé au Grand Chef
						if ($_SESSION['CnxAdm'] == 255)
							switch ($cmde) {
								case 'csv':		// export csv
								case 'mail': 	// logs d'envois de mails
								case 'ip':		// gestion des IP en liste brûlée
									$page = RESSOURCE_PATH['PAGES_FOLDER'].'log/log_'.$cmde.'.php';
									break;
								default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'log/log.php'; break;
							}
							$page = 'maintenance';		// On affiche la page de maintenance
						break;

					case '112':	// Traduction V13
						switch ($cmde) {
							case 'gestion':
							case 'post':
								if ($_SESSION['CnxAdm'] == 255) $page = RESSOURCE_PATH['PAGES_FOLDER'].'traduction/traduction_'.$cmde.'.htm';
								break;
							// le détail par élève
							case 'visu': $page = RESSOURCE_PATH['PAGES_FOLDER'].'traduction/traduction_'.$cmde.'.htm'; break;
							default: $page = RESSOURCE_PATH['PAGES_FOLDER'].'traduction/traduction.htm'; break;
						}
						$page = 'maintenance';		// On affiche la page de maintenance
						break;

					// Lien de validation mail comptes
					case '1100': $page = 'mail_validate.php'; break;

					// création de compte
					case '1000':
						list($iscreat, $nil) = explode(':', $AUTHUSER);
						if ($iscreat) $page = 'user_new.php';


					// page d'accueil
					default:
						$_SESSION['CampusName'] = '';
						// la page d'accueil
						$page = getService($item);
						break;
				}
?>
