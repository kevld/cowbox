$(function () {
    var cpt = 0;

    if(currentRanchId != -1) {
        configFileInput();
    }
    else {
        swal("Erreur !", "Vous n'avez pas sélectionné votre ranch", "error");
    }

    //Compteur de fichiers
    $(document).on('click', '.fileinput-upload-button', function(e){
        $(document).find('.file-preview-frame').each(function(){
            cpt++;
        });
    });
    //formulaire images actualité (postupload)
    $(document).on('fileuploaded', '#ftp_id', function(e, data) {

        cpt--;
        if(cpt == 0) {
            swal("Succès !", "Vos fichiers ont été correctement envoyé", "success");
        }
    });
    function configFileInput(){
        //preupload
        $(document).find("#ftp_id").fileinput({
            language: "fr",
            type: 'POST',
            cache: false,
            uploadAsync: true,
            //allowedFileExtensions: ['zip', 'tar', 'gz', 'bz', 'bz2', '7z'],
            maxFileSize: 10240,
            uploadUrl: Routing.generate('ftp_upload', {id: currentRanchId}),
            maxFileCount: 5,
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