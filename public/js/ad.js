$('#add-image').click(function() {
    // Je récupère le numéros des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();

    // Je récupère le prototype des entrées        permet de remplacer les valeurs de name et le flag g répéter plusieur fois 
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    // Injection du code au sein de la div
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index + 1);
    
    // Je gère le bouton supprimer
    handleDeleteButtons();
});

// Fonction pour la suppresion du formulaire image
function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;

        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#ad_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();
