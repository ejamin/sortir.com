/**
 * NE FONCTIONNE PLUS !
 */

let participantID = document.querySelector('.participantID').value;
let sortieID;

let buttonsInscrire = Array.from(document.getElementsByClassName('btn-inscrire'));
buttonsInscrire.forEach(button =>
    button.addEventListener('click', function (event) {
        console.log("INSCRIPTION");
        event.preventDefault();
        sortieID = button.querySelector('.sortieID').value;
        axios.get(inscriptionBtnURL, {
            params: {
                'sortieID': sortieID,
                'participantID': participantID
            }
        })
            .then(function (response) {
                console.log('COUCOU');
                let nbInscritContainer = document.getElementById("inscription-" + sortieID);
                let estInscritContainer = document.getElementById("estInscrit-" + sortieID);
                nbInscritContainer.innerHTML = response.data;
                estInscritContainer.innerHTML = "<div class=\"text-success\"> Inscrit/e</div>";
                button.style.visibility = 'hidden';
                let buttonDesister = document.getElementsByClassName('btn-desister btn-'+sortieID)[0]
                console.log(buttonDesister);
                buttonDesister.style.visibility= 'visible';

            });
    })
);


let buttonsDesister = Array.from(document.getElementsByClassName('btn-desister'));
buttonsDesister.forEach(button => button.addEventListener('click', function (event) {
    console.log("DESISTEMENT");
    event.preventDefault();
    console.log('1');
    sortieID = button.querySelector('.sortieID').value;
    console.log('2');
    axios.get(desisterBtnURL, { params: {
        'sortieID': sortieID, 'participantID': participantID,
    }})
    .then(function(response) {
        console.log(response);
        let nbInscritContainer = document.getElementById("inscription-" + sortieID);
        let estInscritContainer = document.getElementById("estInscrit-" + sortieID);
                    nbInscritContainer.innerHTML = response.data;
                    estInscritContainer.innerHTML = "<div> Non inscrit/e</div>";
                    button.style.visibility= 'hidden';
                    let buttonInscrire = document.getElementsByClassName('btn-inscrire btn-'+sortieID)[0]
                    console.log(buttonInscrire);
                    buttonInscrire.style.visibility= 'visible';

                }
            )
            .catch(error=> console.error(error));
    })
);