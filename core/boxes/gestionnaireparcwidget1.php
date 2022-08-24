<?php
/* Copyright (C) 2004-2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2021  Frédéric France     <frederic.france@netlogic.fr>
 * Copyright (C) 2022 SuperAdmin <contact@cgibert.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    gestionnaireparc/core/boxes/gestionnaireparcwidget1.php
 * \ingroup gestionnaireparc
 * \brief   Widget provided by GestionnaireParc
 *
 * Put detailed description here.
 */

include_once DOL_DOCUMENT_ROOT."/core/boxes/modules_boxes.php";


/**
 * Class to manage the box
 *
 * Warning: for the box to be detected correctly by dolibarr,
 * the filename should be the lowercase classname
 */
class gestionnaireparcwidget1 extends ModeleBoxes
{
	/**
	 * @var string Alphanumeric ID. Populated by the constructor.
	 */
	public $boxcode = "gestionnaireparcbox";

	/**
	 * @var string Box icon (in configuration page)
	 * Automatically calls the icon named with the corresponding "object_" prefix
	 */
	public $boximg = "gestionnaireparc@gestionnaireparc";

	/**
	 * @var string Box label (in configuration page)
	 */
	public $boxlabel;

	/**
	 * @var string[] Module dependencies
	 */
	public $depends = array('gestionnaireparc');

	/**
	 * @var DoliDb Database handler
	 */
	public $db;

	/**
	 * @var mixed More parameters
	 */
	public $param;

	/**
	 * @var array Header informations. Usually created at runtime by loadBox().
	 */
	public $info_box_head = array();

	/**
	 * @var array Contents informations. Usually created at runtime by loadBox().
	 */
	public $info_box_contents = array();

	/**
	 * @var string 	Widget type ('graph' means the widget is a graph widget)
	 */
	public $widgettype = 'graph';


	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 * @param string $param More parameters
	 */
	public function __construct(DoliDB $db, $param = '')
	{

		global $user, $conf, $langs;
		// Translations
		$langs->loadLangs(array("boxes", "gestionnaireparc@gestionnaireparc"));

		parent::__construct($db, $param);

		$this->boxlabel = $langs->transnoentitiesnoconv("GestionnaireParcTableauDeBord");

		$this->param = $param;

		//$this->enabled = $conf->global->FEATURES_LEVEL > 0;         // Condition when module is enabled or not
		//$this->hidden = ! ($user->rights->gestionnaireparc->myobject->read);   // Condition when module is visible by user (test on permission)

		$this->enabled = 1;
		$this->hidden = false;
	}

	/**
	 * Load data into info_box_contents array to show array later. Called by Dolibarr before displaying the box.
	 *
	 * @param int $max Maximum number of records to load
	 * @return void
	 */
	public function loadBox($max = 5)
	{
		global $langs;

		// Use configuration value for max lines count
		$this->max = $max;

		//dol_include_once("/gestionnaireparc/class/gestionnaireparc.class.php");

		// Populate the head at runtime
		$text = $langs->trans("GestionnaireParcBoxDescription", $max);
		$this->info_box_head = array(
			// Title text
			'text' => $text,
			// Add a link
			'sublink' => '/custom/gestionnaireparc/gestionnaireparcindex.php?idmenu=6321&mainmenu=gestionnaireparc&leftmenu=',
			// Sublink icon placed after the text
			'subpicto' => 'object_gestionnaireparc@gestionnaireparc',
			// Sublink icon HTML alt text
			'subtext' => '',
			// Sublink HTML target
			'target' => '',
			// HTML class attached to the picto and link
			'subclass' => 'center',
			// Limit and truncate with "…" the displayed text lenght, 0 = disabled
			'limit' => 0,
			// Adds translated " (Graph)" to a hidden form value's input (?)
			'graph' => false
		);

		//Chargement des classes d'objets pour compter les éléments
		dol_include_once('/gestionnaireparc/class/machines.class.php');
		dol_include_once('/gestionnaireparc/class/pannes.class.php');
		dol_include_once('/gestionnaireparc/class/interventions.class.php');
		$machines = new Machines($this->db);
		$pannes = new Pannes($this->db);
		$interventions = new Interventions($this->db);

		$count_machines_fonctionnelles = count($machines->fetchAll('','',0,0,array('etat_actuel'=>0)));
		$count_machines_en_panne = count($machines->fetchAll('','',0,0,array('etat_actuel'=>1)));
		$count_pannes_legeres = count($pannes->fetchAll('','',0,0,array('gravite'=>0,'etat'=>0)));
		$count_pannes_lourdes = count($pannes->fetchAll('','',0,0,array('gravite'=>1,'etat'=>0)));
		$count_depannages_a_programmer = count($pannes->fetchAll('','',0,0,array('phase_reparation'=>0,'etat'=>0)));
		$count_maintenances_programmees = count($interventions->fetchAll('','',0,0,array('intervention_type'=>0,'statut_intervention'=>0)));
		$count_depannages_programmes = count($interventions->fetchAll('','',0,0,array('intervention_type'=>1,'statut_intervention'=>0)));
		$count_interventions_a_cloturer_realisees = count($interventions->fetchAll('','',0,0,array('statut_intervention'=>1)));
		$count_interventions_a_cloturer_vaines = count($interventions->fetchAll('','',0,0,array('statut_intervention'=>2)));

		// Populate the contents at runtime
		$this->info_box_contents = array(
			0 => array( // First line
				0 => array( // First Column
					//  HTML properties of the TR element. Only available on the first column.
					'tr' => 'class="left"',
					// HTML properties of the TD element
					'td' => '',

					// Main text for content of cell
					'text' => 'Machines dans le parc',
					// Link on 'text' and 'logo' elements
					//'url' => '/custom/gestionnaireparc/machines_list.php?idmenu=6322&mainmenu=gestionnaireparc&leftmenu=',
					// Link's target HTML property
					//'target' => '_blank',
					// Fist line logo (deprecated. Include instead logo html code into text or text2, and set asis property to true to avoid HTML cleaning)
					//'logo' => 'monmodule@monmodule',
					// Unformatted text, added after text. Usefull to add/load javascript code
					'textnoformat' => '',

					// Main text for content of cell (other method)
					//'text2' => '<p><strong>Another text</strong></p>',

					// Truncates 'text' element to the specified character length, 0 = disabled
					'maxlength' => 0,
					// Prevents HTML cleaning (and truncation)
					'asis' => false,
					// Same for 'text2'
					'asis2' => true
				),
				1 => array( // Another column
					// No TR for n≠0
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_machines_fonctionnelles>0)?4:4).' badge-status">'.$count_machines_fonctionnelles.'</span> Fonctionnelle·s',
					'url' => '/custom/gestionnaireparc/machines_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_etat_actuel=0',
				),
				2 => array( // Another column
					// No TR for n≠0
					'td' => '',
					'text' => ($count_machines_en_panne>0)?'<span class="badge badge-status8 badge-status">'.$count_machines_en_panne.'</span> En panne':'',
					'url' => '/custom/gestionnaireparc/machines_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_etat_actuel=1',
				)
			),
			1 => array( // Another line
				0 => array( // First Column
					'tr' => 'class="left"',
					'td' => '',
					'text' => 'Pannes en cours',
					'textnoformat' => '',
					'maxlength' => 0,
					'asis' => false,
					'asis2' => true
				),
				1 => array( // Another column
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_pannes_legeres>0)?1:4).' badge-status">'.$count_pannes_legeres.'</span> Légère·s',
					'url' => '/custom/gestionnaireparc/pannes_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_gravite=0&search_etat=0',
				),
				2 => array( // Another column
					// No TR for n≠0
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_pannes_lourdes>0)?8:4).' badge-status">'.$count_pannes_lourdes.'</span> Lourde·s',
					'url' => '/custom/gestionnaireparc/pannes_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_gravite=1&search_etat=0',
				)
			),
			2 => array( // Another line
				0 => array( // First Column
					'tr' => 'class="left"',
					'td' => '',
					'text' => 'Dépannages à programmer',
					'textnoformat' => '',
					'maxlength' => 0,
					'asis' => false,
					'asis2' => true
				),
				1 => array( // Another column
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_depannages_a_programmer>0)?8:4).' badge-status">'.$count_depannages_a_programmer.'</span> Panne·s',
					'url' => '/custom/gestionnaireparc/pannes_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_phase_reparation=0&search_etat=0',
				),
				2 => array( // Another column
					'td' => '',
					'text' => '',
				),
			),
			3 => array( // Another line
				0 => array( // First Column
					'tr' => 'class="left"',
					'td' => '',
					'text' => 'Interventions programmées',
					'textnoformat' => '',
					'maxlength' => 0,
					'asis' => false,
					'asis2' => true
				),
				1 => array( // Another column
					'td' => '',
					'text' => '<span class="badge badge-status4 badge-status">'.$count_maintenances_programmees.'</span> Maintenances',
					'url' => '/custom/gestionnaireparc/interventions_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_intervention_type=0&search_statut_intervention=0',
				),
				2 => array( // Another column
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_depannages_programmes>0)?1:4).' badge-status">'.$count_depannages_programmes.'</span> Dépannages',
					'url' => '/custom/gestionnaireparc/interventions_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_intervention_type=1&search_statut_intervention=0',
				),
			),
			4 => array( // Another line
				0 => array( // First Column
					'tr' => 'class="left"',
					'td' => '',
					'text' => 'Interventions à cloturer',
					'textnoformat' => '',
					'maxlength' => 0,
					'asis' => false,
					'asis2' => true
				),
				1 => array( // Another column
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_interventions_a_cloturer_realisees>0)?1:4).' badge-status">'.$count_interventions_a_cloturer_realisees.'</span> Réalisée',
					'url' => '/custom/gestionnaireparc/interventions_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_statut_intervention=1',
				),
				2 => array( // Another column
					'td' => '',
					'text' => '<span class="badge badge-status'.(($count_interventions_a_cloturer_vaines>0)?8:4).' badge-status">'.$count_interventions_a_cloturer_vaines.'</span> Vaines',
					'url' => '/custom/gestionnaireparc/interventions_list.php?idmenu=6322&mainmenu=gestionnaireparc&search_statut_intervention=2',
				),
			),
		);
	}

	/**
	 * Method to show box. Called by Dolibarr eatch time it wants to display the box.
	 *
	 * @param array $head       Array with properties of box title
	 * @param array $contents   Array with properties of box lines
	 * @param int   $nooutput   No print, only return string
	 * @return string
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		// You may make your own code here…
		// … or use the parent's class function using the provided head and contents templates
		return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}
}
