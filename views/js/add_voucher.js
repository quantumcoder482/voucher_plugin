
Dropzone.autoDiscover = false;

$(document).ready(function () {


    $(".progress").hide();
    $("#emsg").hide();
    $('#description').redactor(
        {
            toolbar: false,
            minHeight: 200 // pixels
        }
    );

    $('.footable').footable();
    $.fn.modal.defaults.width = '800px';
    var $modal = $('#ajax-modal');
    $('[data-toggle="tooltip"]').tooltip();


    var $country = $('#country');
    var $category = $('#category');
    var $template = $('#template');
    var $expiry_duration = $('#expiry_duration');


    $country.select2({
        theme: "bootstrap"
    });

    $category.select2({
        theme:"bootstrap"
    });

    $template.select2({
       theme:"bootstrap"
    });

    $expiry_duration.select2({
        theme:"bootstrap"
    });

    $('#expiry_day').select2({
        theme:"bootstrap"
    });

    $('#billing_cycle').select2({
        theme:"bootstrap"
    });


    var _url = $("#_url").val();
    var ib_submit = $("#submit");
    var $voucher_img = $("#voucher_img");

    $country.on('change', function(e){
        e.preventDefault();
        var c_id = $country.val();
        $.post(_url + 'voucher/app/get_country_info', {'id':c_id})
            .done(function(data){
                if(data){
                    $('#prefix').val(data.prefix);
                }
            });

    });

    $template.on('change', function(e){
       e.preventDefault();
       var t_id = $template.val();
       $.post(_url + 'voucher/app/get_template_info', {'id':t_id})
           .done(function(data){
               console.log(data);
               if(data){
                   $voucher_img.val(data.cover_img);
                   $("#voucher_image").attr("src",'storage/system/'+data.cover_img);
               }
           });
    });


    $('.add_country').on('click', function (e) {

        var id = this.id;

        e.preventDefault();

        $('body').modalmanager('loading');

        $modal.load(_url + 'voucher/app/modal_edit_country/'+id, '', function () {

            $modal.modal();
            $modal.css("width", "800px");
            $modal.css("margin-left", "-349px");

        });
    });

    $modal.on('click', '.modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + "voucher/app/post_country/", $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location.reload();

                }

                else {
                    $modal.modal('loading');
                    toastr.error(data);
                }

            });

    });


    // Voucher Image upload

    // var ib_file = new Dropzone("#upload_container",
    //     {
    //         url: _url + "voucher/app/voucher_image_upload/",
    //         maxFiles: 1
    //     }
    // );
    //
    // ib_file.on("sending", function () {
    //
    //     ib_submit.prop('disabled', true);
    //
    // });
    //
    // ib_file.on("success", function (file, response) {
    //
    //     ib_submit.prop('disabled', false);
    //
    //     upload_resp = response;
    //
    //     if (upload_resp.success == 'Yes') {
    //
    //         toastr.success(upload_resp.msg);
    //         $voucher_img.val(upload_resp.file);
    //         $("#voucher_image").attr("src",upload_resp.fullpath);
    //
    //     }
    //     else {
    //         toastr.error(upload_resp.msg);
    //     }
    //
    // });


    ib_submit.click(function (e) {

        e.preventDefault();

        $('#ibox_form').block({ message: null });

        $.post(_url + 'voucher/app/post_voucher', $("#rform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location.href = _url + 'voucher/app/list_voucher';
                }
                else {
                    $('#ibox_form').unblock();

                    $("#emsgbody").html(data);
                    $("#emsg").show("slow");
                }
            });
    });


});