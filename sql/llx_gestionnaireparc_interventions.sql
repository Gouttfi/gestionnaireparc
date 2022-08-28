-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_gestionnaireparc_interventions(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
	compte_rendu text,
	date_creation datetime NOT NULL,
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	fk_user_creat integer NOT NULL,
	fk_user_modif integer,
	last_main_doc varchar(255),
	import_key varchar(14),
	model_pdf varchar(255),
	intervention_type integer NOT NULL,
	fk_machine integer,
	date_intervention datetime NOT NULL,
	agent integer NOT NULL,
	duree_intervention integer NOT NULL,
	fk_panne integer,
	statut_intervention integer NOT NULL,
	description text NOT NULL,
	ref varchar(64) NOT NULL,
	operation1 integer,
	ref_operation1 varchar(64),
	operation2 integer,
	ref_operation2 varchar(64),
	operation3 integer,
	ref_operation3 varchar(64),
	operation4 integer,
	ref_operation4 varchar(64),
	operation5 integer,
	ref_operation5 varchar(64),
	operation6 integer,
	ref_operation6 varchar(64),
	operation7 integer,
	ref_operation7 varchar(64),
	operation8 integer,
	ref_operation8 varchar(64),
	operation9 integer,
	ref_operation9 varchar(64),
	operation10 integer,
	ref_operation10 varchar(64),
	resultat_intervention integer NOT NULL,
	fk_actioncomm integer NOT NULL,
	maintenance_type integer
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

-- Trigger pour incrémenter le temps d'intervention cumulé sur une machine
DROP TRIGGER IF EXISTS `calcul_increment_temps_intervention_sur_machine`;
DELIMITER $$
CREATE TRIGGER `calcul_increment_temps_intervention_sur_machine`
AFTER UPDATE ON `llxsm_gestionnaireparc_interventions` FOR EACH ROW
IF (new.`statut_intervention` = 3) THEN
	UPDATE `llxsm_gestionnaireparc_machines` SET `stat_cumul_temps_intervention` = 'stat_cumul_temps_intervention'+ new.`duree_intervention`
	WHERE `llxsm_gestionnaireparc_machines`.`rowid` = new.`fk_machine`;
END IF;
$$
DELIMITER ;

-- Triggers pour mettre à jour le statut d'une panne après la création/modification/supression d'une intervention
DROP TRIGGER IF EXISTS `calcul_StatutPanne_programme_apres_creation_intervention`;
DELIMITER $$
CREATE TRIGGER `calcul_StatutPanne_programme_apres_creation_intervention`
AFTER INSERT ON `llxsm_gestionnaireparc_interventions` FOR EACH ROW
IF (new.`fk_panne` IS NOT NULL) THEN
	UPDATE `llxsm_gestionnaireparc_pannes` SET `phase_reparation` = 1, `etat` = 0, `fk_date_intervention` = new.`date_intervention`, `stat_nb_interventions` = `stat_nb_interventions`+1
	WHERE `llxsm_gestionnaireparc_pannes`.`rowid` = new.`fk_panne`;
END IF;
$$
DELIMITER ;

DROP TRIGGER IF EXISTS `calcul_StatutPanne_apres_modif_intervention`;
DELIMITER $$
CREATE TRIGGER `calcul_StatutPanne_apres_modif_intervention`
AFTER UPDATE ON `llxsm_gestionnaireparc_interventions` FOR EACH ROW
IF (new.`statut_intervention` = 3) THEN
	IF (new.`fk_panne` IS NOT NULL AND new.`resultat_intervention` = 1) THEN
		UPDATE `llxsm_gestionnaireparc_pannes` SET `phase_reparation` = 2, `etat` = 1, `fk_date_intervention` = new.`date_intervention`
		WHERE `llxsm_gestionnaireparc_pannes`.`rowid` = new.`fk_panne`;
	END IF;
	IF (new.`fk_panne` IS NOT NULL AND new.`resultat_intervention` = 2) THEN
		UPDATE `llxsm_gestionnaireparc_pannes` SET `phase_reparation` = 0, `etat` = 0, `fk_date_intervention` = new.`date_intervention`
		WHERE `llxsm_gestionnaireparc_pannes`.`rowid` = new.`fk_panne`;
	END IF;
END IF
$$
DELIMITER ;

DROP TRIGGER IF EXISTS `calcul_StatutPanne_a_programmer_apres_suppression_intervention`;
DELIMITER $$
CREATE TRIGGER `calcul_StatutPanne_a_programmer_apres_suppression_intervention`
AFTER DELETE ON `llxsm_gestionnaireparc_interventions` FOR EACH ROW
IF (old.`fk_panne` IS NOT NULL) THEN
	UPDATE `llxsm_gestionnaireparc_pannes` SET `phase_reparation` = 0, `etat` = 0, `fk_date_intervention` = old.`date_intervention`
	WHERE `llxsm_gestionnaireparc_pannes`.`rowid` = old.`fk_panne`;
END IF
$$
DELIMITER ;

-- Trigger pour mettre à jour le nombre d'opérations utilisées après la cloture d'une intervention
DROP TRIGGER IF EXISTS `calcul_utilisations_operations`;
DELIMITER $$
CREATE TRIGGER `calcul_utilisations_operations`
AFTER UPDATE ON `llxsm_gestionnaireparc_interventions` FOR EACH ROW
IF (new.`statut_intervention` = 3) THEN
	IF (new.`operation1` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation1`;
	END IF;
	IF (new.`operation2` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation2`;
	END IF;
	IF (new.`operation3` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation3`;
	END IF;
	IF (new.`operation4` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation4`;
	END IF;
	IF (new.`operation5` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation5`;
	END IF;
	IF (new.`operation6` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation6`;
	END IF;
	IF (new.`operation7` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation7`;
	END IF;
	IF (new.`operation8` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation8`;
	END IF;
	IF (new.`operation9` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation9`;
	END IF;
	IF (new.`operation10` IS NOT NULL) THEN
		UPDATE `llxsm_gestionnaireparc_operations` SET `nb_real` = `nb_real`+1
		WHERE `llxsm_gestionnaireparc_operations`.`rowid` = new.`operation10`;
	END IF;
END IF
$$
DELIMITER ;
