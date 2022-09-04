# GESTIONNAIREPARC FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## Fonctionnalités

- Gestion du parc de matériel de l'entreprise, avec inscription des références utiles pour l'entretien de l'appareil.
- Supervision de l'état de santé des appareils avec possibilité de déclarer des pannes et d'en effectuer le suivi.
- Plannification des interventions de maintenance ou de dépannage.

<!--
![Screenshot gestionnaireparc](img/screenshot_gestionnaireparc.png?raw=true "GestionnaireParc"){imgmd}
-->

## Traductions

Traduit entièrement en français.
Fichier langue anglais disponible, traduction manquante.

Modification des fichiers dans le répertoire *langs*.

<!--
This module contains also a sample configuration for Transifex, under the hidden directory [.tx](.tx), so it is possible to manage translation using this service.

For more informations, see the [translator's documentation](https://wiki.dolibarr.org/index.php/Translator_documentation).

There is a [Transifex project](https://transifex.com/projects/p/dolibarr-module-template) for this module.
-->

<!--

## Installation

### From the ZIP file and GUI interface

- If you get the module in a zip file (like when downloading it from the market place [Dolistore](https://www.dolistore.com)), go into
menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.

Note: If this screen tell you there is no custom directory, check your setup is correct:

- In your Dolibarr installation directory, edit the ```htdocs/conf/conf.php``` file and check that following lines are not commented:

    ```php
    //$dolibarr_main_url_root_alt ...
    //$dolibarr_main_document_root_alt ...
    ```

- Uncomment them if necessary (delete the leading ```//```) and assign a sensible value according to your Dolibarr installation

    For example :

    - UNIX:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';
        ```

    - Windows:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';
        ```

### From a GIT repository

- Clone the repository in ```$dolibarr_main_document_root_alt/gestionnaireparc```

```sh
cd ....../custom
git clone git@github.com:gitlogin/gestionnaireparc.git gestionnaireparc
```

### <a name="final_steps"></a>Final steps

From your browser:

  - Log into Dolibarr as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module

-->

## Licenses

### Code source

GPLv3. Voir fichier COPYING pour plus d'informations.

### Documentation

Toute la documentation est sous licence GFDL.
