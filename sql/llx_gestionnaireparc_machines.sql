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


CREATE TABLE llx_gestionnaireparc_machines(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(128) NOT NULL, 
	label varchar(64), 
	description text, 
	date_creation datetime NOT NULL, 
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	equipe integer, 
	magasin varchar(128) NOT NULL, 
	marque varchar(64) NOT NULL, 
	modele varchar(64) NOT NULL, 
	numero_serie varchar(64), 
	date_achat date NOT NULL, 
	heures integer, 
	etat_general integer DEFAULT 4 NOT NULL, 
	type_huile_moteur varchar(64), 
	quantite_huile_moteur integer, 
	type integer NOT NULL, 
	immatriculation varchar(9), 
	kilometrage integer, 
	derniere_revision date, 
	type_huile_hydrau varchar(64), 
	quantite_huile_hydrau integer, 
	type_huile_pont varchar(64), 
	quantite_huile_pont integer, 
	ref_filtre_air varchar(64), 
	ref_filtre_carburant varchar(64), 
	ref_filtre_huile_moteur varchar(64), 
	ref_filtre_huile_hydrau varchar(64), 
	type_bougies varchar(64), 
	ref_lames varchar(64), 
	ref_courroie_lame varchar(128), 
	ref_courroie_moteur varchar(64), 
	ref_plateau_tondeuse varchar(64), 
	instructions_maintenance text, 
	etat_actuel integer NOT NULL, 
	stat_nb_pannes integer NOT NULL, 
	stat_cumul_temps_intervention integer NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
