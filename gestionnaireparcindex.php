<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       gestionnaireparc/gestionnaireparcindex.php
 *	\ingroup    gestionnaireparc
 *	\brief      Home page of gestionnaireparc top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
dol_include_once('/gestionnaireparc/class/machines.class.php');

// Load translation files required by the page
$langs->loadLangs(array("gestionnaireparc@gestionnaireparc"));

$action = GETPOST('action', 'aZ09');


// Security check
// if (! $user->rights->gestionnaireparc->statistiques->read) {
// 	accessforbidden();
// }
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("GestionnaireParcArea"));

print load_fiche_titre($langs->trans("GestionnaireParcArea"), '', 'gestionnaireparc@gestionnaireparc');

print '<div class="fichecenter"><div class="fichethirdleft">';

// Security check
 if (! $user->rights->gestionnaireparc->statistiques->read) {
 	//accessforbidden();
	echo "Naviguez à travers les éléments du module via le menu de gauche.";
 }

// Classement des machines les plus en pannes
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, etat_general, stat_nb_pannes FROM ".MAIN_DB_PREFIX."gestionnaireparc_machines ORDER BY `stat_nb_pannes` DESC";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementMachinesPannes", $max);
		print '</th>';
		print '<th class="right">'.$langs->trans("EtatGeneral").'</th>';
		print '<th class="right">'.$langs->trans("NombrePannes").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);

				$obj = $db->fetch_object($resql);

				$myobjectstatic = new Machines($db);
				$myobjectstatic->id = $obj->rowid;
				$myobjectstatic->ref = $obj->ref;

				print '<tr class="oddeven"><td class="nowrap">';

				print $myobjectstatic->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.$myobjectstatic->showOutputField($myobjectstatic->fields["etat_general"], $obj->rowid, $obj->etat_general, '', '', '', 0).'</td>';
				print '<td class="right" class="nowrap">'.$obj->stat_nb_pannes.'</td></tr>';
				$i++;
				$total += $obj->stat_nb_pannes;
			}
			if ($total>0)
			{

				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="3" class="right">'.$total."</td></tr>";
			}
		}
		else
		{

			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoOrder").'</td></tr>';
		}
		print "</table><br>";

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}
}

echo "TODO : <br>
LES N INTERVENTIONS A VENIR <br>
LES N INTERVENTIONS REALISEES <br>
CLASSEMENT OPERATIONS REALISEES <br>
";


print '</div><div class="fichetwothirdright">';


// Classement des machines avec le plus de temps d'intervention cumulé
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, stat_cumul_temps_intervention FROM ".MAIN_DB_PREFIX."gestionnaireparc_machines ORDER BY `stat_cumul_temps_intervention` DESC";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementMachinesCumulDureeIntervention", $max);
		print '</th>';
		print '<th class="right">'.$langs->trans("CumulTempsIntervention").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);

				$obj = $db->fetch_object($resql);

				$myobjectstatic = new Machines($db);
				$myobjectstatic->id = $obj->rowid;
				$myobjectstatic->ref = $obj->ref;
				$myobjectstatic->stat_cumul_temps_intervention = $obj->stat_cumul_temps_intervention;

				print '<tr class="oddeven"><td class="nowrap">';

				print $myobjectstatic->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.convertSecondToTime($myobjectstatic->stat_cumul_temps_intervention, 'allhourmin').'</td></tr>';
				$i++;
				$total += $myobjectstatic->stat_cumul_temps_intervention;
			}
			if ($total>0)
			{

				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.convertSecondToTime($total, 'allhourmin')."</td></tr>";
			}
		}
		else
		{

			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoOrder").'</td></tr>';
		}
		print "</table><br>";

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}
}

print '</div></div>';

// End of page
llxFooter();
$db->close();
