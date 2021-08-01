$(function () {
    var currentClasseId;
    reloadClasses();
    //nouvelle classe
    $(document).on('click', '#classe-add', function () {
        $.ajax({
            url: Routing.generate('classes_new'),
            success: function (data) {
                $('#modal').html(data);
                $('#modal').modal('show');
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    //Enregistrer la classe crée
    $(document).on('click', '#addClasseEvent', function () {
        $.ajax({
            method: 'POST',
            data: $(document).find('#form-ajout-classe').serialize(),
            url: Routing.generate('classes_new'),
            success: function (data) {
                swal("Succès","Classe crée","success");
                $('#modal').modal('hide');
                reloadClasses();
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    //Supprimer une classe
    $(document).on('click', '.suppr', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        swal(
            {
                title: "Attention !",
                text: "Supprimer cette classe ?",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function(){
                $.ajax({
                    method: 'POST',
                    url: Routing.generate('classe_del', { id: id }),
                    success: function (data) {
                        swal("Succès","Classe supprimée","success");
                        reloadClasses();
                    },
                    error: function (xhr) {
                        swal("Erreur !", "Une erreur s'est produite", "error");
                    }
                });
            }
        );
    });
    //postupload
    $(document).on('fileuploaded', '#classe_id', function(e, data) {
        swal("Succès","Liste mise à jour","success");
    });
    //Affiche la div d'import
    $(document).on('click', '.import', function (e) {
        e.preventDefault();
        //this = <a> parent -> <td> ->parent -> <tr>
        currentClasseId = $(this).parent().parent().data('id');
        configFileInput();
        $('#form_classes').toggle('display');
    });
    //Ouvre la modal d'édition de la classe
    $(document).on('click', '.classe', function (e) {
        e.preventDefault();
        currentClasseId = $(this).parent().parent().data('id');

        $.ajax({
            url: Routing.generate('classe_get_users', { id: currentClasseId }),
            success: function (data) {
                $('#modal').html(data);
                $('#modal').modal('show');
            },
            error: function (xhr) {
                swal("Erreur !", "Une erreur s'est produite", "error");
            }
        });
    });
    //Gérer les élèves d'une classe
    $(document).on('click', '.user-classe', function (e) {
        var user = $(this).data('id');
        var check;
        if($(this).prop('checked')) {
            check = 'active';
        }
        else {
            check = 'inactive';
        }

        $.ajax({
            beforeSend: function () {
                $(document).find('#floppy-classe').addClass('text-warning');
                $(document).find('#floppy-classe').removeClass('text-success');
                $(document).find('#floppy-classe').removeClass('text-danger');

            },
            url: Routing.generate('classe_update_user', { classe: currentClasseId, user: user, check: check }),
            success: function (data) {
                console.log(data);
                //$('#modal').modal('hide');
                $(document).find('#floppy-classe').addClass('text-success');
                $(document).find('#floppy-classe').removeClass('text-warning');
                $(document).find('#floppy-classe').removeClass('text-danger');
            },
            error: function (xhr) {
                $(document).find('#floppy-classe').addClass('text-danger');
                $(document).find('#floppy-classe').removeClass('text-warning');
                $(document).find('#floppy-classe').removeClass('text-success');
            }
        });
    });

    function reloadClasses() {
        $.ajax({
            url: Routing.generate('classes_tab_get'),
            success: function (data) {
                $('#tab').html(data);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    function configFileInput(){
        //preupload
        $(document).find("#classe_id").fileinput({

            language: "fr",
            type: 'POST',
            cache: false,
            uploadAsync: true,
            allowedFileExtensions: ['csv'],
            maxFileSize: 200,
            uploadUrl: Routing.generate('classe_import_users', {id: currentClasseId}),
            maxFileCount: 1,
            multiple:true,
            enctype: 'multipart/form-data',
            overwriteInitial: true,
            msgSizeTooLarge: "Le fichier {name} ({size} KB) dépasse la taille de téléchargement maximale de {maxSize} KB. Veuillez réessayer",
            msgFilesTooMany: "Le nombre de fichiers selectionnés ({n}) dépasse la limite maximale de {m}",
            msgInvalidFileType: 'Le type du fichier "{name}" est invalide. Seuls les fichiers de types {types} sont supportés.',
            msgInvalidFileExtension: 'L\'extension du fichier {name} est invalide. Seulement les extensions "{extensions}" sont supportées.'
        });
    }
});