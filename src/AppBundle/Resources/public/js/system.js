$(function(){
    $.ajax({
        url: Routing.generate('system_service_state', {service: 'apache2'}),
        success: function (data) {
            console.log("Apache2 : " + data);
            if(data != 1) {
                $('#lbl-apache').removeAttr('checked');
            }
            $('#lbl-apache').bootstrapSwitch();
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
    $.ajax({
        url: Routing.generate('system_service_state', {service: 'hostapd'}),
        success: function (data) {
            console.log("Hostapd : " + data);
            if(data != 1) {
                $('#lbl-hostapd').removeAttr('checked');
            }
            $('#lbl-hostapd').bootstrapSwitch();
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
    $.ajax({
        url: Routing.generate('system_service_state', {service: 'mysql'}),
        success: function (data) {
            console.log("MySQL : " + data);
            if(data != 1) {
                $('#lbl-mysql').removeAttr('checked');
            }
            $('#lbl-mysql').bootstrapSwitch();
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
    $.ajax({
        url: Routing.generate('system_service_state', {service: 'proftpd'}),
        success: function (data) {
            console.log("Proftpd : " + data);
            if(data != 1) {
                $('#lbl-proftpd').removeAttr('checked');
            }
            $('#lbl-proftpd').bootstrapSwitch();
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
    $.ajax({
        url: Routing.generate('system_state_card'),
        success: function (data) {
            $('#state-card').html(data);
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
    $.ajax({
        url: Routing.generate("system_ssid_get"),
        success: function (data) {
            $("#ssid-name").html(data);
        },
        error: function () {
            swal("Erreur !", "Une erreur a été détectée dans la récupération du ssid de la cowbox", "error");
        }
    });
    $('#change-ssid').on('click', function () {
        var ssid = $('#input-ssid').val();
        if(ssid == "" || ssid == undefined || ssid == null) {
            swal("Erreur !", "Renseignez un SSID valide", "error");
        }
        else {
            swal(
                {
                    title: "Attention !",
                    text: "Mettre à jour le SSID de la cowbox ?",
                    type: "info",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                },
                function(){
                    $.ajax({
                        method: "POST",
                        url: Routing.generate("system_ssid_change", {nom: ssid}),
                        success: function (data) {
                            swal("Succès !", "Le SSID sera mis à jour au prochain redémarrage de la cowbox", "success");

                        },
                        error: function (xhr) {
                            swal("Erreur !", "Une erreur s'est produite", "error");
                        }
                    });
                }
            );
        }
    });
});
