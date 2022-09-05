const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const operations_possibles = 15;
var op_value = [];

//If reading element
if(urlParams.get("id") !== null && urlParams.get("action") !== "edit" && urlParams.get("action") !== "edit_avant_realiser")
{
    type = document.querySelector('td.fieldname_intervention_type:nth-child(2)').innerHTML;

    for(i = 1; i<=operations_possibles ; i++)
    {
        op_value[i] = document.querySelector('td.fieldname_operation'+i+':nth-child(2)').innerHTML;
        if(op_value[i]==""){op_value[i-1]=-1};
    }
}
reloadFields();


// Détection d'un changement sur toute la table de création/édition
const targetNode = document.querySelector('.conditionnal');
const config = { childList: true, subtree: true };

const callback = function(mutationsList, observer) {
    for(let mutation of mutationsList) {
        if (mutation.type === 'childList') {
            type = document.querySelector('#intervention_type').value;
            reloadFields();
        }
    }
};

const observer = new MutationObserver(callback);
observer.observe(targetNode, config);

//Mises en formes conditionnelles ici
function reloadFields()
{
    //Si mode création ou édition, lecture des valeurs de tous les champs surveillés pour le traitement conditonnel
    if(urlParams.get("action") == "create" || urlParams.get("action") == "edit" || urlParams.get("action") == "edit_avant_realiser")
    {
        type = document.querySelector('#intervention_type').value;
        for(i = 1; i<=operations_possibles ; i++)
        {
            op_value[i] = document.querySelector('#operation'+i).value;
        }
    }

    if(type == 0 || type == "Maintenance" && urlParams.get("action") != "edit_avant_realiser")
    {
        document.querySelector('.field_fk_panne').classList.add('hideobject');
        document.querySelector('.field_fk_machine').classList.remove('hideobject');
        document.querySelector('.field_maintenance_type').classList.remove('hideobject');
    }
    if(type == 1 || type == "Dépannage" && urlParams.get("action") != "edit_avant_realiser")
    {
        document.querySelector('.field_fk_panne').classList.remove('hideobject');
        document.querySelector('.field_fk_machine').classList.add('hideobject');
        document.querySelector('.field_maintenance_type').classList.add('hideobject');
    }
    if(urlParams.get("action") == "edit_avant_realiser")
    {
        document.querySelector('.field_intervention_type').classList.add('hideobject');
        document.querySelector('.field_fk_panne').classList.add('hideobject');
        document.querySelector('.field_fk_machine').classList.add('hideobject');
        document.querySelector('.field_maintenance_type').classList.add('hideobject');
        document.querySelector('.field_description').classList.add('hideobject');
    }



    //Affichage des opérations remplies
    for(i = 1; i<=operations_possibles ; i++)
    {
        if(op_value[i] != -1)
        {
            if(i != operations_possibles)
            {
                document.querySelector('.field_operation'+(i+1)).classList.remove('hideobject');
            }
            if(urlParams.get("action") == "create" || urlParams.get("action") == "edit" || urlParams.get("action") == "edit_avant_realiser")
            {
                fk_operation = document.querySelector('#operation'+i).value;
                fk_machine = document.querySelector('#fk_machine').value;
                getReferenceRecommandee('#ref_operation'+i,fk_operation,fk_machine);
            }
        }
        else
        {
            if(i != operations_possibles)
            {
                document.querySelector('.field_operation'+(i+1)).classList.add('hideobject');
            }
        }
    }
}

//Fonctionnalité de suggestion de référence pour les opérations

//Fonction pour récupérer la référence
function getReferenceRecommandee(id,fk_operation,fk_machine)
{
    return fetch('/custom/gestionnaireparc/api.php?action=getReferenceRecommandee&fk_operation='+fk_operation+'&fk_machine='+fk_machine)
    .then((response) => response.text())
    .then((text) => {
        if(text != "")
        {
            document.querySelector(id).placeholder = "Préconisé : " + text;
        }
        else
        {
            document.querySelector(id).placeholder = "";
        }
    });

}
