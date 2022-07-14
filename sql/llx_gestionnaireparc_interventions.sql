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
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	last_main_doc varchar(255), 
	import_key varchar(14), 
	model_pdf varchar(255), 
	intervention_type integer NOT NULL, 
	fk_machine integer NOT NULL, 
	resultat_intervention integer NOT NULL, 
	date_intervention date NOT NULL, 
	agent integer NOT NULL, 
	duree_intervention integer NOT NULL, 
	operation1 integer, 
	ref_operation1 varchar(64), 
	fk_panne integer NOT NULL, 
	status integer NOT NULL, 
	description text NOT NULL, 
	ref integer NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
