(function ($) {

    $(document).ready(function ($) {

        // Modfiy options when search for is changed
        $('.wtik_feed_type').on('change', function (e) {
            var type = $(this);
            if (type.val() === 'hashtag') {
                $('input#wtik_feed_hashtag').animate({opacity: 'show', height: 'show'}, 0);
                $('input#wtik_feed_account').animate({opacity: 'hide', height: 'hide'}, 0);
            } else if (type.val() === 'account') {
                $('input#wtik_feed_hashtag').animate({opacity: 'hide', height: 'hide'}, 0);
                $('input#wtik_feed_account').animate({opacity: 'show', height: 'show'}, 0);
            }
        });

        //------OLD
        // Modfiy options when search for is changed
        var search = $('.wtik-container select[id$="search"]')
        var search_opt = $("option[value$='"+search.val()+"']");
        if (search_opt.data('type') === 'hashtag') {
            search.closest('.wtik-container').find('input[id$="blocked_users"]').closest('p').animate({
                opacity: 'show',
                height: 'show'
            }, 200);
        } else if (search_opt.data('type') === 'account') {
            search.closest('.wtik-container').find('input[id$="blocked_users"]').closest('p').animate({
                opacity: 'hide',
                height: 'hide'
            }, 200);
        }

        var template = $('.wtik-container select[id$="template"]')
        if (template.val() == 'thumbs' || template.val() == 'thumbs-no-border' || template.val() == 'slider' || template.val() == 'slider-overlay') {
            template.closest('.wtik-container').find('select[id$="images_link"] option[value="popup"]').animate({
                opacity: 'hide',
                height: 'hide'
            }, 200);

            //window.image_link_val = template.closest('.wtik-container').find('select[id$="images_link"]').val();
            //template.closest('.wtik-container').find('select[id$="images_link"]').val("image_link");
        } else {
            template.closest('.wtik-container').find('select[id$="images_link"] option[value="popup"]').animate({
                opacity: 'show',
                height: 'show'
            }, 200);
            //template.closest('.wtik-container').find('select[id$="images_link"]').val(window.image_link_val);
        }

        // Hide Custom Url if image link is not set to custom url
        $('body').on('change', '.wtik-container select[id$="images_link"]', function (e) {
            var images_link = $(this);
            if (images_link.val() != 'custom_url') {
                images_link.closest('.wtik-container').find('input[id$="custom_url"]').val('').parent().animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                images_link.closest('.wtik-container').find('input[id$="custom_url"]').parent().animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
        });

        // Modify options based on template selections
        $('body').on('change', '.wtik-container select[id$="template"]', wtik_template);

        function wtik_template (e) {
            var template = $(this);
            if (template.val() == 'thumbs' || template.val() == 'thumbs-no-border') {
                template.closest('.wtik-container').find('.wtik-slider-options').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                template.closest('.wtik-container').find('input[id$="columns"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            } else {
                template.closest('.wtik-container').find('.wtik-slider-options').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                template.closest('.wtik-container').find('input[id$="columns"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            }
            if (template.val() != 'masonry') {
                template.closest('.wtik-container').find('.masonry_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                template.closest('.wtik-container').find('.masonry_notice').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wtik-container').find('.masonry_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                template.closest('.wtik-container').find('.masonry_notice').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() != 'slick_slider') {
                template.closest('.wtik-container').find('.slick_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wtik-container').find('.slick_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() != 'highlight') {
                template.closest('.wtik-container').find('.highlight_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wtik-container').find('.highlight_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() != 'slider' && template.val() != 'slider-overlay') {
                template.closest('.wtik-container').find('.slider_normal_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wtik-container').find('.slider_normal_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() == 'highlight' || template.val() == 'slick_slider' || template.val() == 'thumbs' || template.val() == 'thumbs-no-border') {
                template.closest('.wtik-container').find('.words_in_caption').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wtik-container').find('.words_in_caption').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }

            if (template.val() == 'thumbs' || template.val() == 'thumbs-no-border' || template.val() == 'slider' || template.val() == 'slider-overlay') {
                template.closest('.wtik-container').find('select[id$="images_link"] option[value="popup"]').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);

                window.image_link_val = template.closest('.wtik-container').find('select[id$="images_link"]').val();
                //template.closest('.wtik-container').find('select[id$="images_link"]').val("image_link");
            } else {
                template.closest('.wtik-container').find('select[id$="images_link"] option[value="popup"]').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                //template.closest('.wtik-container').find('select[id$="images_link"]').val(window.image_link_val);
            }
        }
        // Modfiy options when search for is changed
        $('body').on('change', '.wtik-container select[id$="search"]', function (e) {
            var search_for = $(this);
            var search_opt = $("option[value$='"+$(this).val()+"']");
            if (search_opt.data('type') === 'hashtag') {
                search_for.closest('.wtik-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            } else if (search_opt.data('type') === 'account') {
                search_for.closest('.wtik-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            }
        });

        // Toggle advanced options
        $('body').on('click', '.wtik-advanced', function (e) {
            e.preventDefault();
            var advanced_container = $(this).parent().next();

            if (advanced_container.is(':hidden')) {
                $(this).html('[ - Close ]');
            } else {
                $(this).html('[ + Open ]');
            }
            advanced_container.toggle();
        });

        // Delete account with ajax
        $('.wtik-delete-account').on('click', function (e) {
            e.preventDefault();

            var c = confirm(wtik.remove_account);

            if (!c) {
                return false;
            }

            var $item = $(this),
                $tr = $item.closest('tr'),
                $spinner = $('#wtik-delete-spinner-' + $item.data('item_id'));

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'wtik_delete_account',
                    item_id: $item.data('item_id'),
                    is_business: $item.data('is_business'),
                    nonce: wtik.nonce
                },
                beforeSend: function () {
                    $spinner.addClass('is-active');
                },
                success: function (response) {
                    if (response.success) {
                        $tr.fadeOut();
                        //window.location.reload();
                    } else {
                        alert(response.data);
                    }
                },
                complete: function () {
                    $spinner.removeClass('is-active');
                },
                error: function (jqXHR, textStatus) {
                    console.log(textStatus);
                }
            });
        });

        jQuery('span.wtik_demo_pro').on('click', function (e) {
            e.preventDefault();
            window.open('https://cm-wp.com/tiktok-feed/pricing/', '_blank');
        });

    }); // Document Ready

})(jQuery);
