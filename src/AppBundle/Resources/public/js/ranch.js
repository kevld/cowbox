$(function () {
    var currentRanchId;
    reloadRanchs();
    //Supprimer un ranch
    $(document).on('click', '.suppr', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        swal(
            {
                title: "Attention !",
                text: "Supprimer ce ranch ?",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function(){
                $.ajax({
                    method: 'POST',
                    url: Routing.generate('ranch_del', { id: id }),
                    success: function (data) {
                        swal("Succès","Ranch supprimé","success");
                        reloadRanchs();
                    },
                    error: function (xhr) {
                        swal("Erreur !", "Une erreur s'est produite", "error");
                    }
                });
            }
        );
    });
    //postupload
    $(document).on('fileuploaded', '#ranch_id', function(e, data) {
        swal("Succès","Liste mise à jour","success");
    });
    //Affiche la div d'import
    $(document).on('click', '.import', function (e) {
        e.preventDefault();
        //this = <a> parent -> <td> ->parent -> <tr>
        currentRanchId = $(this).parent().parent().data('id');
        configFileInput();
        $('#form_ranchs').toggle('display');
    });
    //Ouvre la modal d'édition du ranch
    $(document).on('click', '.ranch', function (e) {
        e.preventDefault();
        currentRanchId = $(this).parent().parent().data('id');

        $.ajax({
            url: Routing.generate('ranch_get_users', { id: currentRanchId }),
            success: function (data) {
                $('#modal').html(data);
                $('#modal').modal('show');
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    //nouveau ranch
    $('#add-ranch').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: Routing.generate('ranch_new'),
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
            url: Routing.generate('ranch_new'),
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
    //Gérer les élèves d'un ranch
    $(document).on('click', '.user-ranch', function (e) {
        var user = $(this).data('id');
        var check;
        if($(this).prop('checked')) {
            check = 'active';
        }
        else {
            check = 'inactive';
        }
        //icones sauvegarde
        $.ajax({
            beforeSend: function () {
                $(document).find('#floppy-ranch').addClass('text-warning');
                $(document).find('#floppy-ranch').removeClass('text-success');
                $(document).find('#floppy-ranch').removeClass('text-danger');
            },
            url: Routing.generate('ranch_update_user', { ranch: currentRanchId, user: user, check: check }),
            success: function (data) {
                console.log(data);
                $(document).find('#floppy-ranch').addClass('text-success');
                $(document).find('#floppy-ranch').removeClass('text-warning');
                $(document).find('#floppy-ranch').removeClass('text-danger');
                //$('#modal').modal('hide');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                $.growl.error({ message: "Une erreur s'est produite" });
                $(document).find('#floppy-ranch').addClass('text-danger');
                $(document).find('#floppy-ranch').removeClass('text-warning');
                $(document).find('#floppy-ranch').removeClass('text-success');
            }
        });
    });
    //filtre par classe
    $(document).on('change', '#classes', function () {
        var classe = $(this).val();
        $.ajax({
            url: Routing.generate('ranch_find_students_by_classe', { idClasse: classe, idRanch: currentRanchId }),
            success: function (data) {
                $(document).find('#user-list').html(data);
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });

    });
    $(document).on('click', '.download', function (e) {
        currentRanchId = $(this).parent().parent().data('id');
        $.ajax({
            url: Routing.generate('ranch_download_files', { id: currentRanchId }),
            success: function (data) {
                var link = document.createElement("a");
                link.download = data + ".zip";
                link.href = "/zip/" + data + ".zip";
                link.click();
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    function reloadRanchs() {
        $.ajax({
            url: Routing.generate('ranchs_tab_get'),
            success: function (data) {
                $('#tab').html(data);
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    }
    function configFileInput(){
        //preupload
        $(document).find("#ranch_id").fileinput({

            language: "fr",
            type: 'POST',
            cache: false,
            uploadAsync: true,
            allowedFileExtensions: ['csv'],
            maxFileSize: 200,
            uploadUrl: Routing.generate('ranch_import_users', {id: currentRanchId}),
            maxFileCount: 1,
            multiple:true,
            enctype: 'multipart/form-data',
            overwriteInitial: true,
            //uploadExtraData: { id: currentRanchId },
            msgSizeTooLarge: "Le fichier {name} ({size} KB) dépasse la taille de téléchargement maximale de {maxSize} KB. Veuillez réessayer",
            msgFilesTooMany: "Le nombre de fichiers selectionnés ({n}) dépasse la limite maximale de {m}",
            msgInvalidFileType: 'Le type du fichier "{name}" est invalide. Seuls les fichiers de types {types} sont supportés.',
            msgInvalidFileExtension: 'L\'extension du fichier {name} est invalide. Seulement les extensions "{extensions}" sont supportées.'
        });
    }
});