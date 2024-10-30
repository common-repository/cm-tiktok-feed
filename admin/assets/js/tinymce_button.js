(function ($) {
    $(document).on('tinymce-editor-setup', function (event, editor) {

        if (void 0 === wtik_shortcodes) {
            console.log('Unknown error (wtik).');
            return;
        }

        if ($.isEmptyObject(wtik_shortcodes)) {
            return;
        }

        editor.settings.toolbar1 += ',wtik_insert_button';

        var menu = [];

        $.each(wtik_shortcodes, function (index, item) {
            menu.push({
                text: item.title,
                value: item.id,
                onclick: function () {
                    var selected_content = editor.selection.getContent();

                    if ('' === selected_content) {
                        editor.selection.setContent('[cm_tiktok_feed id="' + item.id + '"]');
                    } else {
                        editor.selection.setContent('[cm_tiktok_feed id="' + item.id + '"]');
                    }
                }
            });
        });

        editor.addButton('wtik_insert_button', {
            title: 'TikTok Feed',
            type: 'menubutton',
            icon: 'icon wtik-shortcode-icon',
            menu: menu
        });

    });
})(jQuery);