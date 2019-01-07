
Dropzone.autoDiscover = false;

$(document).ready(function () {


    $(".progress").hide();
    $("#emsg").hide();
    $('#description').redactor(
        {
            toolbar:false,
            minHeight: 150 // pixels
        }
    );

    $('.footable').footable();
    $.fn.modal.defaults.width = '800px';
    var $modal = $('#ajax-modal');
    $('[data-toggle="tooltip"]').tooltip();


    var _url = $("#_url").val();
    var ib_submit = $("#submit");
    var $cover_img = $("#cover_img");
    var $voucher_template = $("#voucher_template");
    var upload_resp;



    $('.edit_template').on('click', function (e) {

        var id = this.id;

        e.preventDefault();

        $('body').modalmanager('loading');

        $modal.load(_url + 'voucher/app/modal_edit_template/'+id, '', function () {

            $modal.modal();
            $modal.css("width", "800px");
            $modal.css("margin-left", "-349px");

        });
    });


    $(".cdelete").click(function (e) {
        e.preventDefault();
        id=this.id;
        var sure_msg=$('#sure_msg').val();
        bootbox.confirm(sure_msg, function (result) {
            if (result) {
                var _url = $("#_url").val();
                window.location.href = _url + "voucher/app/delete_template/" + id;
            }
        });
    });


    $modal.on('click', '.modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + "voucher/app/post_template/", $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location = base_url + 'voucher/app/pdf_template/';

                }

                else {
                    $modal.modal('loading');
                    toastr.error(data);
                }

            });

    });

    // Voucher Image upload

    var ib_file1 = new Dropzone("#upload_container1",
        {
            url: _url + "voucher/app/voucher_image_upload/",
            maxFiles: 1
        }
    );

    ib_file1.on("sending", function () {

        ib_submit.prop('disabled', true);

    });

    ib_file1.on("success", function (file, response) {

        ib_submit.prop('disabled', false);

        upload_resp = response;

        if (upload_resp.success == 'Yes') {

            toastr.success(upload_resp.msg);
            $cover_img.val(upload_resp.file);

        }
        else {
            toastr.error(upload_resp.msg);
        }

    });

    // Voucher Template upload

    var ib_file2 = new Dropzone("#upload_container2",
        {
            url: _url + "voucher/app/voucher_upload/",
            maxFiles: 1
        }
    );

    ib_file2.on("sending", function () {

        ib_submit.prop('disabled', true);

    });

    ib_file2.on("success", function (file, response) {

        ib_submit.prop('disabled', false);

        upload_resp = response;

        if (upload_resp.success == 'Yes') {

            toastr.success(upload_resp.msg);
            $voucher_template.val(upload_resp.file);

        }
        else {
            toastr.error(upload_resp.msg);
        }

    });


    ib_submit.click(function (e) {

        e.preventDefault();

        $('#ibox_form').block({ message: null });

        $.post(_url + 'voucher/app/post_template', $("#rform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    location.reload();
                }
                else {
                    $('#ibox_form').unblock();

                    $("#emsgbody").html(data);
                    $("#emsg").show("slow");
                }
            });
    });


});