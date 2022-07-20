<?php
/* Copyright (C) 2017-2019 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see https://www.gnu.org/
 */

/**
 *	\file			core/actions_interventions.inc.php
 *  \brief			Code pour actions personnalisées pour l'objet Interventions
 */


// $action or $cancel must be defined
// $object must be defined
// $permissiontoadd must be defined
// $permissiontodelete must be defined
// $backurlforlist must be defined
// $backtopage may be defined
// $triggermodname may be defined

if (!empty($permissionedit) && empty($permissiontoadd)) {
	$permissiontoadd = $permissionedit; // For backward compatibility
}

if ($cancel) {
	/*var_dump($cancel);var_dump($backtopage);var_dump($backtopageforcancel);exit;*/
	if (!empty($backtopageforcancel)) {
		header("Location: ".$backtopageforcancel);
		exit;
	} elseif (!empty($backtopage)) {
		header("Location: ".$backtopage);
		exit;
	}
	$action = '';
}

// Action pour valider les dernières modifications effectuées PUIS réaliser l'intervention
if ($action == 'confirm_realisation' && $confirm == 'yes' && $permissiontoadd) {
    // Valider les modifications
    foreach ($object->fields as $key => $val) {
        // Check if field was submited to be edited
        if ($object->fields[$key]['type'] == 'duration') {
            if (!GETPOSTISSET($key.'hour') || !GETPOSTISSET($key.'min')) {
                continue; // The field was not submited to be saved
            }
        } elseif ($object->fields[$key]['type'] == 'boolean') {
            if (!GETPOSTISSET($key)) {
                $object->$key = 0; // use 0 instead null if the field is defined as not null
                continue;
            }
        } else {
            if (!GETPOSTISSET($key)) {
                continue; // The field was not submited to be saved
            }
        }
        // Ignore special fields
        if (in_array($key, array('rowid', 'entity', 'import_key'))) {
            continue;
        }
        if (in_array($key, array('date_creation', 'tms', 'fk_user_creat', 'fk_user_modif'))) {
            if (!in_array(abs($val['visible']), array(1, 3, 4))) {
                continue; // Only 1 and 3 and 4, that are cases to update
            }
        }

        // Set value to update
        if (preg_match('/^(text|html)/', $object->fields[$key]['type'])) {
            $tmparray = explode(':', $object->fields[$key]['type']);
            if (!empty($tmparray[1])) {
                $value = GETPOST($key, $tmparray[1]);
            } else {
                $value = GETPOST($key, 'restricthtml');
            }
        } elseif ($object->fields[$key]['type'] == 'date') {
            $value = dol_mktime(12, 0, 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int')); // for date without hour, we use gmt
        } elseif ($object->fields[$key]['type'] == 'datetime') {
            $value = dol_mktime(GETPOST($key.'hour', 'int'), GETPOST($key.'min', 'int'), GETPOST($key.'sec', 'int'), GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'), 'tzuserrel');
        } elseif ($object->fields[$key]['type'] == 'duration') {
            if (GETPOST($key.'hour', 'int') != '' || GETPOST($key.'min', 'int') != '') {
                $value = 60 * 60 * GETPOST($key.'hour', 'int') + 60 * GETPOST($key.'min', 'int');
            } else {
                $value = '';
            }
        } elseif (preg_match('/^(integer|price|real|double)/', $object->fields[$key]['type'])) {
            $value = price2num(GETPOST($key, 'alphanohtml')); // To fix decimal separator according to lang setup
        } elseif ($object->fields[$key]['type'] == 'boolean') {
            $value = ((GETPOST($key, 'aZ09') == 'on' || GETPOST($key, 'aZ09') == '1') ? 1 : 0);
        } elseif ($object->fields[$key]['type'] == 'reference') {
            $value = array_keys($object->param_list)[GETPOST($key)].','.GETPOST($key.'2');
        } else {
            if ($key == 'lang') {
                $value = GETPOST($key, 'aZ09');
            } else {
                $value = GETPOST($key, 'alphanohtml');
            }
        }
        if (preg_match('/^integer:/i', $object->fields[$key]['type']) && $value == '-1') {
            $value = ''; // This is an implicit foreign key field
        }
        if (!empty($object->fields[$key]['foreignkey']) && $value == '-1') {
            $value = ''; // This is an explicit foreign key field
        }

        $object->$key = $value;
        if ($val['notnull'] > 0 && $object->$key == '' && is_null($val['default'])) {
            $error++;
            setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv($val['label'])), null, 'errors');
        }

        // Validation of fields values
        if (getDolGlobalInt('MAIN_FEATURES_LEVEL') >= 2 || !empty($conf->global->MAIN_ACTIVATE_VALIDATION_RESULT)) {
            if (!$error && !empty($val['validate']) && is_callable(array($object, 'validateField'))) {
                if (!$object->validateField($object->fields, $key, $value)) {
                    $error++;
                }
            }
        }

        if ($conf->categorie->enabled) {
            $categories = GETPOST('categories', 'array');
            if (method_exists($object, 'setCategories')) {
                $object->setCategories($categories);
            }
        }
    }

    // Fill array 'array_options' with data from add form
    if (!$error) {
        $ret = $extrafields->setOptionalsFromPost(null, $object, '@GETPOSTISSET');
        if ($ret < 0) {
            $error++;
        }
    }

    if (!$error) {
        $result = $object->update($user);
        if ($result > 0) {
            //$action = 'view';
        } else {
            $error++;
            // Creation KO
            setEventMessages($object->error, $object->errors, 'errors');
            //$action = 'edit';
        }
    } else {
        //$action = 'edit';
    }
    // Réaliser l'intervention
	$result = $object->confirm_realisation($user);
	if ($result >= 0) {
		// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
			if (method_exists($object, 'generateDocument')) {
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id', 'aZ09')) {
					$newlang = GETPOST('lang_id', 'aZ09');
				}
				if ($conf->global->MAIN_MULTILANGS && empty($newlang)) {
					$newlang = $object->thirdparty->default_lang;
				}
				if (!empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}

				$ret = $object->fetch($id); // Reload to get new records

				$model = $object->model_pdf;

				// CLEMENT 20/07 CETTE LIGNE FONCTIONNE PAS FAUT TROUVER POURQUOI : $retgen = $object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);

				if ($retgen < 0) {
					setEventMessages($object->error, $object->errors, 'warnings');
				}
			}
		}
	} else {
		$error++;
		setEventMessages($object->error, $object->errors, 'errors');
	}
	header("Location: ".$_SERVER['PHP_SELF'].'?id='.$object->id);
}

// Action pour valider les dernières modifications effectuées PUIS réaliser l'intervention
if ($action == 'cloturer' && $confirm == 'yes' && $permissiontoadd) {
    // Cloturer
	$result = $object->cloturer_intervention($user);
	if ($result >= 0) {
		// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
			if (method_exists($object, 'generateDocument')) {
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id', 'aZ09')) {
					$newlang = GETPOST('lang_id', 'aZ09');
				}
				if ($conf->global->MAIN_MULTILANGS && empty($newlang)) {
					$newlang = $object->thirdparty->default_lang;
				}
				if (!empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}

				$ret = $object->fetch($id); // Reload to get new records

				$model = $object->model_pdf;

				// CLEMENT 20/07 CETTE LIGNE FONCTIONNE PAS FAUT TROUVER POURQUOI : $retgen = $object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);

				if ($retgen < 0) {
					setEventMessages($object->error, $object->errors, 'warnings');
				}
			}
		}
	} else {
		$error++;
		setEventMessages($object->error, $object->errors, 'errors');
	}
	header("Location: ".$_SERVER['PHP_SELF'].'?id='.$object->id);
}
