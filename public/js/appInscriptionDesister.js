let buttonsInscrire = Array.from(document.getElementsByClassName('btn-inscrire'));
let idParticipant = document.querySelector('.idParticipant').value;
let idSortie;
buttonsInscrire.forEach(button =>
    button.addEventListener('click', function (event) {
        event.preventDefault();
        idSortie = button.querySelector('.idSortie').value;
        axios.get(inscriptionBtnURL, {
            params: {
                'idSortie': idSortie,
                'idParticipant': idParticipant
            }
        })
            .then(function (response) {
                console.log(response.data);
                let nbInscritContainer = document.getElementById("inscription-" + idSortie);
                let estInscritContainer = document.getElementById("estInscrit-" + idSortie);
                nbInscritContainer.innerHTML = response.data;
                estInscritContainer.innerHTML = "<div class=\"text-success\"> Inscrit/e</div>";
                button.style.visibility = 'hidden';
                let buttonDesister = document.getElementsByClassName('btn-desister btn-'+idSortie)[0]
                console.log(buttonDesister);
                buttonDesister.style.visibility= 'visible';

            });
    })
);
let buttonsDesister = Array.from(document.getElementsByClassName('btn-desister'));
buttonsDesister.forEach(button =>
    button.addEventListener('click', function (event) {
        console.log(button);
        event.preventDefault();
        idSortie = button.querySelector('.idSortie').value;
        axios.get(desisterBtnURL, {
            params: {
                'idSortie': idSortie,
                'idParticipant': idParticipant
            }
        })
            .then(function (response) {
                    console.log(response.data);
                    let nbInscritContainer = document.getElementById("inscription-" + idSortie);
                    let estInscritContainer = document.getElementById("estInscrit-" + idSortie);

                    nbInscritContainer.innerHTML = response.data;
                    estInscritContainer.innerHTML = "<div> Non inscrit/e</div>";
                    button.style.visibility= 'hidden';
                    let buttonInscrire = document.getElementsByClassName('btn-inscrire btn-'+idSortie)[0]
                    console.log(buttonInscrire);
                    buttonInscrire.style.visibility= 'visible';

                }
            )
            .catch(error=> console.error(error));
    })
);