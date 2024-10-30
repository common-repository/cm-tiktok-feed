/**
 * Этот файл содержит скрипт исполняелся во время процедур с формой лицензирования.
 * Его основная роль отправка ajax запросов на проверку, активацию, деактивацию лицензии
 * и вывод уведомлений об ошибка или успешно выполнении проверок.
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 05.10.2018, Webcraftic
 * @version 1.1
 * @since 1.4.0
 */


jQuery(function ($) {

    $(document).on('click', '.wtik-control-btn', function () {

        $('.wtik-control-btn').hide();

        var wrapper = $('#wtik-license-wrapper'),
            loader = wrapper.data('loader');

        $(this).after('<img class="wtik-loader" src="' + loader + '">');

        var data = {
            action: 'wtik_check_license',
            _wpnonce: $('#_wpnonce').val(),
            license_action: $(this).data('action'),
            licensekey: ''
        };

        if ($(this).data('action') == 'activate') {
            data.licensekey = $('#license-key').val();
        }

        $.ajax(ajaxurl, {
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                var noticeId;

                if (!response || !response.success) {

                    $('.wtik-control-btn').show();
                    $('.wtik-loader').remove();

                    if (response.data) {
                        console.log(response.data.error_message);
                        alert('Error: [' + response.data.error_message + ']');
                    } else {
                        console.log(response);
                    }

                    return;
                }

                if (response.data && response.data.message) {
                    alert(response.data.message);

                    window.location.reload();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

                $('.wtik-control-btn').show();
                $('.wtik-loader').remove();

                console.log(xhr.status, xhr.responseText, thrownError);

                alert('Error: [' + thrownError + '] Status: [' + xhr.status + '] Error massage: [' + xhr.responseText + ']');
            }
        });

        return false;
    });

});
