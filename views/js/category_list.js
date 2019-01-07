
$(document).ready(function () {


    $(".progress").hide();
    $("#emsg").hide();
    $('#description').redactor(
        {
            toolbar: false,
            minHeight: 150 // pixels
        }
    );

    $('.footable').footable();
    $.fn.modal.defaults.width = '600px';
    var $modal = $('#ajax-modal');
    $('[data-toggle="tooltip"]').tooltip();


    var $categorr_name = $('#category_name');
    // var $category = $('#category');

    var _url = $("#_url").val();
    var ib_submit = $("#submit");


    $('.edit_category').on('click', function (e) {

        var id = this.id;

        e.preventDefault();

        $('body').modalmanager('loading');

        $modal.load(_url + 'voucher/app/modal_edit_category/'+id, '', function () {

            $modal.modal();
            $modal.css("width", "600px");

        });
    });


    $(".cdelete").click(function (e) {
        e.preventDefault();
        id=this.id;
        var sure_msg=$('#sure_msg').val();
        bootbox.confirm(sure_msg, function (result) {
            if (result) {
                var _url = $("#_url").val();
                window.location.href = _url + "voucher/app/delete_category/" + id;
            }
        });
    });


    $modal.on('click', '.modal_submit', function (e) {

        e.preventDefault();

        $modal.modal('loading');

        $.post(_url + "voucher/app/post_category/", $("#mrform").serialize())
            .done(function (data) {

                if ($.isNumeric(data)) {

                    window.location = base_url + 'voucher/app/add_category/';

                }

                else {
                    $modal.modal('loading');
                    toastr.error(data);
                }

            });

    });

    ib_submit.click(function (e) {

        e.preventDefault();

        $('#ibox_form').block({ message: null });

        $.post(_url + 'voucher/app/post_category', $("#rform").serialize())
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