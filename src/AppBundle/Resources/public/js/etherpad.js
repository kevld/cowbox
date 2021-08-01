$(function(){
    $('#select-pads').on('change', function () {
        if($(this).val() == -1) {
            $('#iframe').hide();
        }
        else {
            var padName = $(this).val();
            $('#iframe').prop('src', "http://192.168.42.1:9001/p/" + padName);
            $('#iframe').show();
        }
    });
    $('#addPad').on('click', function () {
        $.ajax({
            method: 'POST',
            url: Routing.generate("etherpad_new_pad"),
            data: { nom: $('#input-name').val() },
            success: function (data) {
                $('#select-pads').prepend(
                    '<option value="' + data.nom + '" >' + data.nom + '</option>'
                );

            },
            error: function (xhr) {
                console.log(xhr.status);
                switch (xhr.status) {
                    case 404:
                        swal("Erreur !", "Veuillez sélectionner votre ranch", "error");
                        break;
                    case 403:
                        swal("Erreur !", "Vous n'avez pas l'autorisation d'accéder à ce pad", "error");
                        break;
                    default:
                        swal("Erreur !", "Une erreur s'est produite", "error");
                        break;
                }
            }
        });
    });
});