
Dropzone.autoDiscover = false;

$(document).ready(function () {


    $(".progress").hide();
    $("#emsg").hide();
    $('#description').redactor(
        {
            minHeight: 200 // pixels
        }
    );

    $('.footable').footable();
    $.fn.modal.defaults.width = '800px';
    var $modal = $('#ajax-modal');
    $('[data-toggle="tooltip"]').tooltip();


    var $country_name = $('#country_name');
    var $category = $('#category');

    $country_name.select2({
        theme: "bootstrap"
    });

    $category.select2({
        theme:"bootstrap"
    });


    var _url = $("#_url").val();
    var ib_submit = $("#submit");
    var $flag_img = $("#flag_img");
    var upload_resp;



    $country_name.on('change', function(e){
        e.preventDefault();
        var c_name = $country_name.val();
        $.post(_url + 'voucher/app/get_prefix', {'country_name':c_name})
            .done(function(data){
                if(data){
                    $('#prefix').val(data);
                }
            });

    });


    $('.edit_country').on('click', function (e) {

        var id = this.id;

        e.preventDefault();

        $('body').modalmanager('loading');

        $modal.load(_url + 'voucher/app/modal_edit_country/'+id, '', function () {

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
                window.location.href = _url + "voucher/app/delete_country/" + id;
            }
        });
    });


    $modal.on('click', '.modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + "voucher/app/post_country/", $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location = base_url + 'voucher/app/add_list_country/';

                }

                else {
                    $modal.modal('loading');
                    toastr.error(data);
                }

            });

    });

    // Flag Image upload

    var ib_file = new Dropzone("#upload_container",
        {
            url: _url + "voucher/app/flagupload/",
            maxFiles: 1
        }
    );

    ib_file.on("sending", function () {

        ib_submit.prop('disabled', true);

    });

    ib_file.on("success", function (file, response) {

        ib_submit.prop('disabled', false);

        upload_resp = response;

        if (upload_resp.success == 'Yes') {

            toastr.success(upload_resp.msg);
            $flag_img.val(upload_resp.file);

        }
        else {
            toastr.error(upload_resp.msg);
        }

    });


    ib_submit.click(function (e) {

        e.preventDefault();

        $('#ibox_form').block({ message: null });

        $.post(_url + 'voucher/app/post_country', $("#rform").serialize())
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