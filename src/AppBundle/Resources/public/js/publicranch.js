reloadRanchs();
//nouveau ranch
$('#add-ranch').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: Routing.generate('public_ranch_new'),
        success: function (data) {
            $('#modal').html(data);
            $('#modal').modal('show');
        },
        error: function (xhr) {
            swal("Erreur !", "Une erreur s'est produite", "error");
        }
    });
});
//ajout ranch
$(document).on('click', '#addRanchEvent', function (e) {
    e.preventDefault();
    $.ajax({
        method: 'POST',
        url: Routing.generate('public_ranch_new'),
        data: $(document).find('#form-ajout-ranch').serialize(),
        success: function (data) {
            $('#modal').modal('hide');
            swal("Succès","Ranch ajouté","success");
            reloadRanchs();
        },
        error: function (xhr) {
            swal("Erreur !", "Une erreur s'est produite", "error");
        }
    });
});

function reloadRanchs() {
    $.ajax({
        url: Routing.generate('public_ranchs_get'),
        success: function (data) {
            $('#tab').html(data);
        },
        error: function (xhr) {
            swal("Erreur !", "Une erreur s'est produite", "error");
        }
    });
}