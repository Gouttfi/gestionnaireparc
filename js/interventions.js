const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

//If reading element
if(urlParams.get("id") !== null && urlParams.get("action") !== "edit")
{
    type = document.querySelector('td.fieldname_intervention_type:nth-child(2)').innerHTML;
    op1_value = document.querySelector('td.fieldname_operation1:nth-child(2)').innerHTML;
    if(op1_value==""){op1_value=-1};
    op2_value = document.querySelector('td.fieldname_operation2:nth-child(2)').innerHTML;
    if(op2_value==""){op1_value=-1};
    op3_value = document.querySelector('td.fieldname_operation3:nth-child(2)').innerHTML;
    if(op3_value==""){op2_value=-1};
    op4_value = document.querySelector('td.fieldname_operation4:nth-child(2)').innerHTML;
    if(op4_value==""){op3_value=-1};
    op5_value = document.querySelector('td.fieldname_operation5:nth-child(2)').innerHTML;
    if(op5_value==""){op4_value=-1};
    op6_value = document.querySelector('td.fieldname_operation6:nth-child(2)').innerHTML;
    if(op6_value==""){op5_value=-1};
    op7_value = document.querySelector('td.fieldname_operation7:nth-child(2)').innerHTML;
    if(op7_value==""){op6_value=-1};
    op8_value = document.querySelector('td.fieldname_operation8:nth-child(2)').innerHTML;
    if(op8_value==""){op7_value=-1};
    op9_value = document.querySelector('td.fieldname_operation9:nth-child(2)').innerHTML;
    if(op9_value==""){op8_value=-1};
    op10_value = document.querySelector('td.fieldname_operation10:nth-child(2)').innerHTML;
    if(op10_value==""){op9_value=-1};
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
    if(urlParams.get("action") == "create" || urlParams.get("action") == "edit")
    {
        type = document.querySelector('#intervention_type').value;
        op1_value = document.querySelector('#operation1').value;
        op2_value = document.querySelector('#operation2').value;
        op3_value = document.querySelector('#operation3').value;
        op4_value = document.querySelector('#operation4').value;
        op5_value = document.querySelector('#operation5').value;
        op6_value = document.querySelector('#operation6').value;
        op7_value = document.querySelector('#operation7').value;
        op8_value = document.querySelector('#operation8').value;
        op9_value = document.querySelector('#operation9').value;
    }

    if(type == 0 || type == "Maintenance")
    {
        document.querySelector('.field_fk_panne').classList.add('hideobject');
        document.querySelector('.field_fk_machine').classList.remove('hideobject');
        document.querySelector('.field_duree_intervention').classList.remove('hideobject');
    }
    if(type == 1 || type == "Dépannage")
    {
        document.querySelector('.field_fk_panne').classList.remove('hideobject');
        document.querySelector('.field_fk_machine').classList.add('hideobject');
        document.querySelector('.field_duree_intervention').classList.add('hideobject');
    }

    //Affichage des opérations remplies
    if(op1_value != -1)
    {
        document.querySelector('.field_operation2').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation2').classList.add('hideobject');
    }

    if(op2_value != -1)
    {
        document.querySelector('.field_operation3').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation3').classList.add('hideobject');
    }

    if(op3_value != -1)
    {
        document.querySelector('.field_operation4').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation4').classList.add('hideobject');
    }

    if(op4_value != -1)
    {
        document.querySelector('.field_operation5').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation5').classList.add('hideobject');
    }

    if(op5_value != -1)
    {
        document.querySelector('.field_operation6').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation6').classList.add('hideobject');
    }

    if(op6_value != -1)
    {
        document.querySelector('.field_operation7').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation7').classList.add('hideobject');
    }

    if(op7_value != -1)
    {
        document.querySelector('.field_operation8').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation8').classList.add('hideobject');
    }

    if(op8_value != -1)
    {
        document.querySelector('.field_operation9').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation9').classList.add('hideobject');
    }

    if(op9_value != -1)
    {
        document.querySelector('.field_operation10').classList.remove('hideobject');
    }
    else
    {
        document.querySelector('.field_operation10').classList.add('hideobject');
    }
}
