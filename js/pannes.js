const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

if(document.querySelector('.field_titre') !== null && urlParams.get("id") !== null)
{
    type = document.querySelector('.field_titre').classList.add('hideobject');
}
