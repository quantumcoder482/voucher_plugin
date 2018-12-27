
Dropzone.autoDiscover = false;

$(document).ready(function () {


    $(".progress").hide();
    $("#emsg").hide();
    // $('#remark').redactor(
    //     {
    //         minHeight: 200 // pixels
    //     }
    // );


    $.fn.modal.defaults.width = '800px';

    var $modal = $('#ajax-modal');

    $('[data-toggle="tooltip"]').tooltip();

    $('#product').select2({
        theme:"bootstrap"
    });

    $('#status_id').select2({
        theme:"bootstrap"
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
    });


    var _url = $("#_url").val();
    var ib_submit = $("#submit");
    var $front_img = $("#front_img");
    var $back_img = $("#back_img");
    var upload_resp;



    // Front Image upload

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
            $front_img.val(upload_resp.file);
            $("#voucher_front").attr("src",upload_resp.fullpath);

        }
        else {
            toastr.error(upload_resp.msg);
        }

    });


    // Back image upload

    var ib_file2 = new Dropzone("#upload_container2",
        {
            url: _url + "voucher/app/voucher_image_upload/",
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
            $back_img.val(upload_resp.file);
            $("#voucher_back").attr("src",upload_resp.fullpath);

        }
        else {
            toastr.error(upload_resp.msg);
        }

    });



    ib_submit.click(function (e) {

        e.preventDefault();

        $('#ibox_form').block({ message: null });

        $.post(_url + 'voucher/app/post_page', $("#rform").serialize())
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