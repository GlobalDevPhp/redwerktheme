jQuery(document).ready(function ($) {

    var form = $('.form-send-mail'),
            action = form.attr('action'),
            pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

    form.find('.req-field').addClass('empty-field');

    function checkInput() {
        form.find('.req-field').each(function () {
            var el = $(this);
            if (el.hasClass('rf-file')) {
                if (validateFile(el.val())) {
                    el.removeClass('empty-field');
                } else {
                    el.addClass('empty-field');
                }
            } else if (el.hasClass('rf-mail')) {
                if (pattern.test(el.val())) {
                    el.removeClass('empty-field');
                } else {
                    el.addClass('empty-field');
                }
            } else if (el.val() != '') {
                el.removeClass('empty-field');
            } else {
                el.addClass('empty-field');
            }
        });
    }

    function lightEmpty() {
        form.find('.empty-field').addClass('rf-error');
        setTimeout(function () {
            form.find('.rf-error').removeClass('rf-error');
        }, 1000);
    }
    
    function validateFile(filename) {
        var fileExtension = ['png', 'jpg', 'jpeg', 'gif'];
        if ($.inArray(filename.split('.').pop().toLowerCase(), fileExtension) == -1) {
            return false;
        } else
            return true;
    }
    $(document).on('submit', '.form-send-mail', function (e) {
        e.preventDefault();
        var file_data = $('#client_attach').prop('files')[0];

        var form_data = new FormData();
        form_data.append('attach_file', file_data);
        form_data.append('client_title', $('#client_title').prop('value'));
        form_data.append('client_mail', $('#client_mail').prop('value'));
        form_data.append('action', 'send_email');        

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html(percentComplete+'%');
                    }
                }, false);
                return xhr;
            },            
            url: action,
            type: 'post',
            contentType: false,
            processData: false,
            data: form_data,
            cache: false,
            beforeSend: function () {
                form.addClass('is-sending');
            },
            error: function (request, txtstatus, errorThrown) {
                console.log(request);
                console.log(txtstatus);
                console.log(errorThrown);
            },
            success: function () {
                form.removeClass('is-sending').addClass('is-sending-complete');
                form.find('input').val('');
                form.removeClass('is-sending-complete');
                
            }
        });
    });

    $(document).on('click', '.form-send-mail button[type="submit"]', function (e) {

        checkInput();

        var errorNum = form.find('.empty-field').length;

        if (errorNum > 0) {
            lightEmpty();
            e.preventDefault();
        }

    });

});