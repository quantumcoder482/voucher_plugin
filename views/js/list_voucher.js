
Dropzone.autoDiscover = false;

$(document).ready(function () {

    // modal loading part....


    var _url = $("#_url").val();
    $.fn.modal.defaults.width = '850px';
    var $modal = $('#ajax-modal');
    $('[data-toggle="tooltip"]').tooltip();

    $('.footable').footable();


    $('.generate').on('click', function (e) {

        var id = this.id;

        e.preventDefault();

        $('body').modalmanager('loading');

        $modal.load(_url + 'voucher/app/modal_generate_voucher/'+id, '', function () {

            $modal.modal();

        });
    });

    $('.edit_voucher').on('click', function (e) {

        var id = this.id;

        e.preventDefault();

        $('body').modalmanager('loading');

        $modal.load(_url + 'voucher/app/modal_edit_voucher/'+id, '', function () {

            $modal.modal();

        });
    });

    $('.view_voucher').on('click', function (e) {

        var id = this.id;
        e.preventDefault();
        window.location.href = _url + "voucher/app/generated_voucher_list/" + id;
       
    });

    $(".cdelete").click(function (e) {

        e.preventDefault();
        id = this.id;
        var sure_msg = $('#sure_msg').val();

        bootbox.confirm(sure_msg, function (result) {

            if (result) {

                var _url = $("#_url").val();

                window.location.href = _url + "voucher/app/delete_voucher_format/" + id;
            }
        });
    });


    $modal.on('click', '.modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + 'voucher/app/post_voucher', $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location = base_url + 'voucher/app/list_voucher';

                }

                else {
                    $modal.modal('loading');
                    toastr.error(data);
                }

            });

    });

    $modal.on('click', '.generate_modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + 'voucher/app/post_generate_voucher', $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location = base_url + 'voucher/app/list_voucher';

                }

                else {
                    $modal.modal('loading');
                    toastr.error(data);
                }

            });

    });

    

});