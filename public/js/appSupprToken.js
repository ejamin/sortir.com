/**
 * JavaScript pour le popup de suppression
 * composer require security
 * composer require symfony/security-csrf
 * composer require symfony/security-bundle
 */

document.querySelectorAll('.btnDelete').forEach(function (el){
    el.addEventListener('click', function(e){

        responseHandler(e);
    })

})

document.querySelectorAll('.btnPopUp').forEach(function (el){
    el.addEventListener('click', function(e){

        responseHandler(e);
    })

})

function responseHandler(e)
{
    if(confirm("Validez-vous cette action ?"))
    {

    }else
    {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }

}