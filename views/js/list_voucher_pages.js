
$(document).ready(function () {

    // modal loading part....


    var _url = $("#_url").val();
    // $.fn.modal.defaults.width = '850px';
    var $modal = $('#ajax-modal');
    $('[data-toggle="tooltip"]').tooltip();

    $('.footable').footable();


    $('.clone_page').on('click', function (e) {

        var vid = $('#vid').val();
        var id = this.id;
        e.preventDefault();

        window.location.href = _url + "voucher/app/clone_page/" + vid +'/' + id;

    });

    $('.edit_page').on('click', function (e) {
        var vid = $('#vid').val();
        var id = this.id;
        e.preventDefault();

        window.location.href = _url + "voucher/app/add_page/" + vid +'/' + id;

    });

    $('.view_page').on('click', function (e) {
        var vid = $('#vid').val();
        var id = this.id;
        e.preventDefault();
        window.location.href = _url + "voucher/app/view_page/" + vid +'/' + id;
       
    });

    $(".cdelete").click(function (e) {

        e.preventDefault();
        var vid = $('#vid').val();
        var id = this.id;
        var sure_msg = $('#sure_msg').val();

        bootbox.confirm(sure_msg, function (result) {

            if (result) {

                var _url = $("#_url").val();

                window.location.href = _url + "voucher/app/delete_page/" + vid + '/' + id;
            }
        });
    });

    $('.view_redeem_page').on('click', function (e) {
        var gid = $('#gid').val();
        var id = this.id;
        e.preventDefault();
        window.location.href = _url + "voucher/app/view_redeem_page/" + gid +'/' + id +'/view';

    });

    $('.edit_redeem_page').on('click', function (e) {
        var gid = $('#gid').val();
        var id = this.id;
        e.preventDefault();
        window.location.href = _url + "voucher/app/view_redeem_page/" + gid +'/' + id + '/edit';

    });

    $('.redeem_page').on('click', function (e) {
        var gid = $('#gid').val();
        var id = this.id;
        e.preventDefault();
        window.location.href = _url + "voucher/app/view_redeem_page/" + gid +'/' + id + '/redeem';

    });

    $modal.on('click', '.modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + 'voucher/app/post_voucher_page', $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location = base_url + 'voucher/app/list_voucher_pages';

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