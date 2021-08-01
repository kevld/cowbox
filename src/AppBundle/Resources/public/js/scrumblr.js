$(function(){
    $('#select-scrumblrs').on('change', function () {
        if($(this).val() == -1) {
            $('#iframe').hide();
        }
        else {
            var scrumblrName = $(this).val();
            $('#iframe').prop('src', "http://192.168.42.1:9003/" + scrumblrName);
            $('#iframe').show();
        }
    });
    $('#addScrumblr').on('click', function () {
        $.ajax({
            method: 'POST',
            url: Routing.generate("scrumblr_new"),
            data: { nom: $('#input-name').val() },
            success: function (data) {
                $('#select-scrumblrs').prepend(
                    '<option value="' + data.nom + '" >' + data.nom + '</option>'
                );
                swal('Succès !', "Scrumblr crée", "success");
            },
            error: function (xhr) {
                console.log(xhr.status);
                switch (xhr.status) {
                    case 404:
                        swal("Erreur !", "Veuillez sélectionner votre ranch", "error");
                        break;
                    case 403:
                        swal("Erreur !", "Vous n'avez pas l'autorisation d'accéder à ce Scrumblr", "error");
                        break;
                    default:
                        swal("Erreur !", "Une erreur s'est produite", "error");
                        break;
                }
            }
        });
    });
});