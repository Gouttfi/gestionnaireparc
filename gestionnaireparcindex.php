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
dol_include_once('/gestionnaireparc/class/interventions.class.php');
dol_include_once('/gestionnaireparc/class/operations.class.php');
dol_include_once('/gestionnaireparc/class/pannes.class.php');


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

llxHeader("", $langs->trans("GestionnaireParcStatistiques"));

print load_fiche_titre($langs->trans("GestionnaireParcStatistiques"), '', 'object_gestionnaireparc@gestionnaireparc');

print '<div class="fichecenter"><div class="fichethirdleft">';

// Security check
if (! $user->rights->gestionnaireparc->statistiques->read) {
	//accessforbidden();
	echo "Naviguez à travers les éléments du module via le menu de gauche.";
}

// Récupération de la configuration "limite" des tableaux
/*print_r(gettype($conf->global->gestionnaireparc_statistiques_limite));
if(gettype($conf->global->gestionnaireparc_statistiques_limite) !== "integer" || $conf->global->gestionnaireparc_statistiques_limite == 0)
{
$limit = 5;
}
else
{*/
$limit = $conf->global->gestionnaireparc_statistiques_limite;
/*}*/

// Classement des interventions à venir
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, date_intervention, statut_intervention, agent FROM ".MAIN_DB_PREFIX."gestionnaireparc_interventions WHERE `date_intervention` >= NOW() ORDER BY `date_intervention` ASC LIMIT $limit";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementProchainesInterventionsPrevues", $max).($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'');
		print '</th>';
		print '<th class="right">'.$langs->trans("DateIntervention").'</th>';
		print '<th class="right">'.$langs->trans("AgentConcerne").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$object_db = $db->fetch_object($resql);

				$object = new Interventions($db);
				$object->fetch($object_db->rowid);

				//Récupération de la machine associée
				/*$machines = new Machines($db);
				$machine = $machines->fetchAll('','',0,0,array('rowid'=>$obj->fk_machine))[$obj->fk_machine];

				var_dump($machine->label);
				$object->label = $machine->label;*/

				print '<tr class="oddeven"><td class="nowrap">';

				print $object->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.$object->showOutputField($object->fields['date_intervention'], 'date_intervention', $object->date_intervention).'</td>';
				print '<td class="right" class="nowrap">'.$object->showOutputField($object->fields["agent"], 'agent', $object->agent).'</td></tr>';
				$i++;
				//$total += $obj->stat_nb_pannes;
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

// Classement des interventions récemment réalisées
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, date_intervention, agent, statut_intervention FROM ".MAIN_DB_PREFIX."gestionnaireparc_interventions WHERE `date_intervention` < NOW() AND `resultat_intervention` != 0 ORDER BY `date_intervention` DESC LIMIT $limit";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementInterventionsRecemmentRealisees", $max).($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'');
		print '</th>';
		print '<th class="right">'.$langs->trans("DateIntervention").'</th>';
		print '<th class="right">'.$langs->trans("AgentConcerne").'</th>';
		print '<th class="right">'.$langs->trans("StatutIntervention").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$object_db = $db->fetch_object($resql);

				$object = new Interventions($db);
				$object->fetch($object_db->rowid);

				switch($object->statut_intervention) {
					case 0:
					$status = 1;
					break;
					case 1:
					$status = 4;
					break;
					case 2:
					$status = 8;
					break;
					case 3:
					$status = 9;
					break;
				}

				print '<tr class="oddeven"><td class="nowrap">';

				print $object->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.$object->showOutputField($object->fields['date_intervention'], 'date_intervention', $object->date_intervention).'</td>';
				print '<td class="right" class="nowrap">'.$object->showOutputField($object->fields["agent"], 'agent', $object->agent).'</td>';
				print '<td class="right" class="nowrap"><span class="badge  badge-status'.$status.' badge-status" title="'.$object->showOutputField($object->fields["statut_intervention"], 'statut_intervention', $object->statut_intervention).'">'.$object->showOutputField($object->fields["statut_intervention"], 'statut_intervention', $object->statut_intervention).'</span></td></tr>';
				$i++;
				//$total += $obj->stat_nb_pannes;
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

print '</div><div class="fichetwothirdright">';

// Classement des agents déclarant le plus de pannes
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, agent, count(agent) AS count FROM ".MAIN_DB_PREFIX."gestionnaireparc_pannes GROUP BY `agent` ORDER BY `count` DESC LIMIT $limit";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementAgentsDeclarantsPannes", $max).($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'');
		print '</th>';
		//print '<th class="right">'.$langs->trans("ClassementAgentsDeclarantsPannes").'</th>';
		print '<th class="right">'.$langs->trans("NombrePannesDeclarees").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$object_db = $db->fetch_object($resql);

				$object = new Pannes($db);
				$object->fetch($object_db->rowid);
				$object->count_nb_pannes = $object_db->count;

				print '<tr class="oddeven"><td class="nowrap">';

				print $object->showOutputField($object->fields['agent'], 'agent', $object->agent);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				//print '<td class="right" class="nowrap">'.$object->showOutputField($object->fields['agent'], 'agent', $object->agent).'</td>';
				print '<td class="right" class="nowrap">'.$object->count_nb_pannes.'</td></tr>';
				$i++;
				$total += $object->count_nb_pannes;
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

// Classement des machines les plus en pannes
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, etat_actuel, label, modele, equipe, etat_general, stat_nb_pannes FROM ".MAIN_DB_PREFIX."gestionnaireparc_machines ORDER BY `stat_nb_pannes` DESC LIMIT $limit";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementMachinesPannes", $max).($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'');
		print '</th>';
		print '<th class="right">'.$langs->trans("EtatGeneral").'</th>';
		print '<th class="right">'.$langs->trans("NombrePannes").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$object_db = $db->fetch_object($resql);

				$object = new Machines($db);
				$object->fetch($object_db->rowid);

				print '<tr class="oddeven"><td class="nowrap">';

				print $object->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.$object->showOutputField($object->fields['etat_general'], 'etat_general', $object->etat_general).'</td>';
				print '<td class="right" class="nowrap">'.$object->stat_nb_pannes.'</td></tr>';
				$i++;
				$total += $object->stat_nb_pannes;
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

// Classement des machines avec le plus de temps d'intervention cumulé
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, etat_actuel, label, modele, equipe, stat_cumul_temps_intervention FROM ".MAIN_DB_PREFIX."gestionnaireparc_machines ORDER BY `stat_cumul_temps_intervention` DESC LIMIT $limit";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementMachinesCumulDureeIntervention", $max).($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'');
		print '</th>';
		print '<th class="right">'.$langs->trans("CumulTempsIntervention").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$object_db = $db->fetch_object($resql);

				$object = new Machines($db);
				$object->fetch($object_db->rowid);

				print '<tr class="oddeven"><td class="nowrap">';

				print $object->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.convertSecondToTime($object->stat_cumul_temps_intervention, 'allhourmin').'</td></tr>';
				$i++;
				$total += $object->stat_cumul_temps_intervention;
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

// Classement des opérations les plus réalisées
if (! empty($conf->gestionnaireparc->enabled) && $user->rights->gestionnaireparc->statistiques->read)
{
	$sql = "SELECT rowid, ref, label, nb_real FROM ".MAIN_DB_PREFIX."gestionnaireparc_operations ORDER BY `nb_real` DESC LIMIT $limit";

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("ClassementOperationsUtilisees", $max).($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'');
		print '</th>';
		print '<th class="right">'.$langs->trans("TotalUtilisationOperation").'</th>';
		print '</tr>';

		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$object_db = $db->fetch_object($resql);

				$object = new Operations($db);
				$object->fetch($object_db->rowid);

				print '<tr class="oddeven"><td class="nowrap">';

				print $object->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.$object->nb_real.'</td></tr>';
				$i++;
				$total += $object->nb_real;
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
