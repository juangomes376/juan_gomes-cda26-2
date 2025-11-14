document.addEventListener('DOMContentLoaded', function() {
    let activeInput = null;
    const prixAchatInput = document.getElementById('prix-achat-input');
    const prixVenteInput = document.getElementById('prix-vente-input');
    const selectElement = document.getElementById('elect');

    prixAchatInput.addEventListener('click', function() {
        activeInput = prixAchatInput;
        console.log("Entrée active : Prix d'achat");
        prixAchatInput.classList.add('active-keypad-target');
        prixVenteInput.classList.remove('active-keypad-target');
    });

    prixVenteInput.addEventListener('click', function() {
        activeInput = prixVenteInput;
        console.log("Entrée active : Prix de vente");
        prixVenteInput.classList.add('active-keypad-target');
        prixAchatInput.classList.remove('active-keypad-target');
    });

    selectElement.addEventListener('change', function() {
        const selectedValue = this.value;
        if (selectedValue === 'prixachat') {
            activeInput = prixAchatInput;
            prixAchatInput.click(); 
        } else if (selectedValue === 'prixvente') {
            activeInput = prixVenteInput;
            prixVenteInput.click();
        }
        // console.log("Entrée active via Select: " + selectedValue);
    });

    function appendKey(value) {
        if (activeInput) {
            activeInput.value += value;
        } else {
            alert("Veuillez sélectionner 'Prix d'achat' ou 'Prix de vente' avant de saisir.");
        }
    }

    function clearDisplay() {
        if (activeInput) {
            activeInput.value = '';
        }
    }

    if (prixAchatInput) {
        prixAchatInput.click();
    }

    [prixAchatInput, prixVenteInput].forEach(input => {
        input.addEventListener('focus', function(e) {
            e.preventDefault(); 
            this.blur();
        });
    });

    function cash() {
        const prixAchatInput = document.getElementById('prix-achat-input');
        const prixVenteInput = document.getElementById('prix-vente-input');
        const mode = document.querySelector('select[name="mode"]').value;
        const achat = prixAchatInput.value;
        const vente = prixVenteInput.value;

        if (!achat || !vente) {
            alert('Veuillez remplir les deux champs : Prix d\'achat et Prix de vente.');
            return;
        }

        $.ajax({
            url: '/cash2/assets/scripts/process_cash.php',
            type: 'POST',
            data: {
                prix_achat: achat,
                prix_vente: vente,
                mode: mode
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let html = '<h3>Change : ' + data.change + ' €</h3><ul>';
                    data.items.forEach(item => {
                        html += '<li>' + item.quantite + ' ' + item.type + (item.quantite > 1 ? 's' : '') + ' de ' + item.valeur + '</li>';
                    });
                    html += '</ul>';
                    document.getElementById('result').innerHTML = html;
                } else {
                    document.getElementById('result').innerHTML = '<p>Erreur : ' + data.message + '</p>';
                }
            },
            error: function() {
                alert('Erreur dans la requête AJAX.');
            }
        });
    }

    window.appendKey = appendKey;
    window.clearDisplay = clearDisplay;
    window.cash = cash;
});