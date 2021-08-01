$(function(){
    var tmp;
    refreshList();
    $('#add-user').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: Routing.generate('user_new'),
            success: function (data) {
                $('#modal').html(data);
                $('#editUserEvent').hide();
                $('#modal').modal('show');
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    $(document).on('click', '.edit', function () {
        tmp = $(this).parent().data('id');
        $.ajax({
            url: Routing.generate('user_update', {id: tmp}),
            success: function (data) {
                $('#modal').html(data);
                $('#addUserEvent').hide();
                var role = $('#role').data('val');
                $('#role').val(role);
                $('#modal').modal('show');
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    $(document).on('click', '#addUserEvent', function () {
        var username = $(document).find('#nom').val();
        var role = $(document).find('#role').val();
        $.ajax({
            method: 'POST',
            url: Routing.generate('user_new'),
            data: {username: username, role: role},
            success: function (data) {
                $('#modal').html(data);
                $('#modal').modal('hide');
                swal("Succès !", "Utilisateur ajouté", "success");
                refreshList();
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    $(document).on('click', '#editUserEvent', function () {
        var username = $(document).find('#nom').val();
        var role = $(document).find('#role').val();
        $.ajax({
            method: 'POST',
            url: Routing.generate('user_update', {id: tmp}),
            data: {username: username, role: role},
            success: function (data) {
                $('#modal').html(data);
                $('#modal').modal('hide');
                swal("Succès !", "Utilisateur mis à jour", "success");
                refreshList();
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    $(document).on('click', '.suppr', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        if(confirm("Supprimer cet utilisateur ?")) {
            $.ajax({
                method: 'POST',
                url: Routing.generate('user_del', { id: id }),
                success: function (data) {
                    swal("Succès !", "Utilisateur supprimé", "success");
                    refreshList();
                },
                error: function (xhr) {
                    swal("Erreur !", "Une erreur s'est produite", "error");
                }
            });
        }
    });
    function refreshList()
    {
        $.ajax({
            url: Routing.generate('user_get'),
            success: function (data) {
                $('#table-users').html(data);
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    }
});