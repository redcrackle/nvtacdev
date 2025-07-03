/*jslint regexp: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdbbPressToolsInit,tinymce,tinyMCE,jQuery*/

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbto = window.wp.gdbto || {};

    window.wp.gdbto.front = {
        storage: {},
        get_selection: function() {
            var t = '';

            if (window.getSelection) {
                t = window.getSelection();
            } else if (document.getSelection) {
                t = document.getSelection();
            } else if (document.selection) {
                t = document.selection.createRange().text;
            }

            return t.toString().trim();
        },
        init: function() {
            $(document).on("click", ".d4p-bbt-quote-link", function(e) {
                e.preventDefault();

                var rc = $("#bbp_reply_content"),
                    button = $(this);

                if (rc.length > 0) {
                    var qout = wp.gdbto.front.get_selection(),
                        id = button.data("id"),
                        quote_id = '#d4p-bbp-quote-' + id;

                    if (qout === "") {
                        qout = $(quote_id).html();
                    }

                    qout = qout.replace(/&nbsp;/g, " ");
                    qout = qout.replace(/<p>|<br>/g, "");
                    qout = qout.replace(/<\/\s*p>/g, "\n");
                    qout = qout.trim();

                    if (gdbbPressToolsInit.quote_method === "bbcode") {
                        qout = "[quote quote=" + id + "]" + qout + "[/quote]";
                    } else {
                        var title = '<div class="d4p-bbp-quote-title"><a href="' + button.data("url") + '">';

                        title += button.data("author") + ' ' + gdbbPressToolsInit.quote_wrote + ':</a></div>';
                        qout = '<blockquote class="d4pbbc-quote">' + title + qout + '</blockquote>';
                    }

                    if (gdbbPressToolsInit.wp_editor === "1" && !rc.is(":visible")) {
                        tinymce.get("bbp_reply_content").execCommand("mceInsertContent", false, qout);
                    } else {
                        var cntn = rc.val();

                        if (cntn.trim() !== '') {
                            qout = "\n\n" + qout;
                        }

                        rc.val(cntn + qout);
                    }

                    $("html, body").animate({scrollTop: $("#new-post").offset().top}, 1000);
                }
            });
        }
    };

    $(document).ready(function() {
        wp.gdbto.front.init();
    });
})(jQuery, window, document);
