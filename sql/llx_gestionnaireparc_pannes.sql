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


CREATE TABLE llx_gestionnaireparc_pannes(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	description text, 
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	last_main_doc varchar(255), 
	import_key varchar(14), 
	model_pdf varchar(255), 
	fk_machines integer NOT NULL, 
	date date NOT NULL, 
	cause varchar(128) NOT NULL, 
	gravite_panne integer NOT NULL, 
	agent integer NOT NULL, 
	statut_panne integer NOT NULL, 
	fk_date_intervention date, 
	ref varchar(64) DEFAULT '(AUTO)' NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

-- Trigger pour changer le statut d'une machine lors de la déclaration d'une nouvelle panne
CREATE TRIGGER `calcul_EtatActuel_machine_apres_nouvelle_panne`
AFTER INSERT ON `llxsm_gestionnaireparc_pannes`
FOR EACH ROW UPDATE `llxsm_gestionnaireparc_machines` SET `etat_actuel` = '1'
WHERE `llxsm_gestionnaireparc_machines`.`rowid` = new.`fk_machines`; 
