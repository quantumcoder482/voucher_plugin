$(document).ready(function () {


    var $modal = $('#ajax-modal');
    var sysrender = $('#application_ajaxrender');

    var vid = $('#vid').val();
    var pid = $('#pid').val();


    sysrender.on('click', '.cdelete', function(e){
        e.preventDefault();
        var id = this.id;
        var vid = $('#vid').val();
        var pid = $('#pid').val();
        var lan_msg = "Are you sure?";
        bootbox.confirm(lan_msg, function(result) {
            if(result){
                var _url = $("#_url").val();
                if(pid){
                    window.location.href = _url + "voucher/app/delete_customfield/" + id + '/' + vid + '/' + pid + '/';
                }else{
                    window.location.href = _url + "voucher/app/delete_customfield/" + id + '/' + vid + '/';
                }
            }
        });
    });



    sysrender.on('click', '.sys_add', function(e){
        e.preventDefault();
        $('body').modalmanager('loading');
        var _url = $("#_url").val();
        $modal.load(_url + 'voucher/app/customfields-ajax-add/'+vid+'/'+pid+'/','', function(){
            $modal.modal(
                {
                    width: '600'
                }
            );
        });
    });


    $modal.on('click', '#add_submit', function(){
        $modal.modal('loading');

        var _url = $("#_url").val();
        $.post(_url + 'voucher/app/customfields-post/', $('#add_form').serialize(), function(data){

            var _url = $("#_url").val();
            if ($.isNumeric(data)) {

                location.reload();
            }
            else {

                $modal
                    .modal('loading')
                    .find('.modal-body')
                    .prepend('<div class="alert alert-danger fade in">' + data +

                        '</div>');

            }
        });

    });


    sysrender.on('click', '.sys_edit', function(e){
        e.preventDefault();
        $('body').modalmanager('loading');
        var _url = $("#_url").val();
        var fid = this.id;
        var id = fid.replace("f", "");

        $modal.load(_url + 'voucher/app/customfields-ajax-edit/' + id + '/' + vid+ '/'+ pid,'', function(){
            $modal.modal(
                {

                    width: '600'
                }
            );
        });
    });


    $modal.on('click', '#edit_submit', function(){
        $modal.modal('loading');

        var _url = $("#_url").val();
        $.post(_url + 'voucher/app/customfield-edit-post/', $('#edit_form').serialize(), function(data){

            var _url = $("#_url").val();
            if ($.isNumeric(data)) {

                location.reload();
            }
            else {

                $modal
                    .modal('loading')
                    .find('.modal-body')
                    .prepend('<div class="alert alert-danger fade in">' + data +

                        '</div>');

            }

        });

    });


});
