const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

//If creating element
if(document.querySelector('#type') !== null)
{
    type = document.querySelector('#type').value;
}

//If reading element
if(urlParams.get("id") !== null)
{
    //type = document.querySelector('td.fieldname_type:nth-child(2)').innerHTML;
    type = document.querySelector('#type').value;
}
reloadFields();


$(document.body).on("change","#type",function(){
    type = this.value;
    reloadFields();
});

//Mises en formes conditionnelles ici
function reloadFields()
{
    //Avant d'ajouter les classes hideobject selon la machine choisie, retrait de ces classes dans tous les champs
    document.querySelector('.field_numero_serie').classList.remove('hideobject');
    document.querySelector('.field_immatriculation').classList.remove('hideobject');
    document.querySelector('.field_kilometrage').classList.remove('hideobject');
    document.querySelector('.field_heures').classList.remove('hideobject');
    document.querySelector('.field_derniere_revision').classList.remove('hideobject');
    document.querySelector('.field_ref_filtre_huile_moteur').classList.remove('hideobject');
    document.querySelector('.field_ref_filtre_huile_hydrau').classList.remove('hideobject');
    document.querySelector('.field_type_huile_pont').classList.remove('hideobject');
    document.querySelector('.field_ref_filtre_air').classList.remove('hideobject');
    document.querySelector('.field_ref_lames').classList.remove('hideobject');
    document.querySelector('.field_ref_courroie_moteur').classList.remove('hideobject');
    document.querySelector('.field_ref_plateau_tondeuse').classList.remove('hideobject');
    document.querySelector('.field_type_bougies').classList.remove('hideobject');

    if(type == 0 || type == "Véhicule")
    {
        document.querySelector('.field_numero_serie').classList.add('hideobject');
        document.querySelector('.field_heures').classList.add('hideobject');
        document.querySelector('.field_ref_lames').classList.add('hideobject');
        document.querySelector('.field_ref_courroie_moteur').classList.add('hideobject');
        document.querySelector('.field_ref_plateau_tondeuse').classList.add('hideobject');
    }
    else if(type == 2 || type == "Tondeuse")
    {
        document.querySelector('.field_immatriculation').classList.add('hideobject');
        document.querySelector('.field_kilometrage').classList.add('hideobject');
        document.querySelector('.field_derniere_revision').classList.add('hideobject');
        document.querySelector('.field_type_bougies').classList.add('hideobject');
    }
    else if(type == 7 || type == "Électroportatif")
    {
        document.querySelector('.field_immatriculation').classList.add('hideobject');
        document.querySelector('.field_kilometrage').classList.add('hideobject');
        document.querySelector('.field_heures').classList.add('hideobject');
        document.querySelector('.field_derniere_revision').classList.add('hideobject');
        document.querySelector('.field_ref_filtre_huile_moteur').classList.add('hideobject');
        document.querySelector('.field_ref_filtre_huile_hydrau').classList.add('hideobject');
        document.querySelector('.field_type_huile_pont').classList.add('hideobject');
        document.querySelector('.field_ref_filtre_air').classList.add('hideobject');
        document.querySelector('.field_ref_lames').classList.add('hideobject');
        document.querySelector('.field_ref_courroie_moteur').classList.add('hideobject');
        document.querySelector('.field_ref_plateau_tondeuse').classList.add('hideobject');
        document.querySelector('.field_type_bougies').classList.add('hideobject');
    }
    else
    {
        document.querySelector('.field_immatriculation').classList.add('hideobject');
        document.querySelector('.field_kilometrage').classList.add('hideobject');
        document.querySelector('.field_derniere_revision').classList.add('hideobject');
        document.querySelector('.field_type_bougies').classList.add('hideobject');
        document.querySelector('.field_ref_plateau_tondeuse').classList.add('hideobject');
    }
}
