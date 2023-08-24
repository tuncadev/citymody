var FOLLOW_CANDIDATE = FOLLOW_CANDIDATE || {};
(function ($) {
    "use strict";

    FOLLOW_CANDIDATE = {
        init: function () {
            var follow_candidate_save = civi_template_vars.follow_save,
                follow_candidate_saved = civi_template_vars.follow_saved,
				wishlist_save = civi_template_vars.wishlist_save,
				wishlist_saved = civi_template_vars.wishlist_saved,
                ajax_url = civi_template_vars.ajax_url;

            $("body").on("click", ".civi-add-to-follow-candidate", function (e) {
                e.preventDefault();
                if (!$(this).hasClass("on-handle")) {
                    var $this = $(this).addClass("on-handle"),
                        candidate_inner = $this
                            .closest(".candidate-inner")
                            .addClass("candidate-active-hover"),
                        candidate_id = $this.attr("data-candidate-id"),
                        save = "";

                    if (!$this.hasClass("added")) {
                        var offset = $this.offset(),
                            width = $this.width(),
                            height = $this.height(),
                            coords = {
                                x: offset.left + width / 2,
                                y: offset.top + height / 2,
                            };
                    }

                    $.ajax({
                        type: "post",
                        url: ajax_url,
                        dataType: "json",
                        data: {
                            action: "civi_add_to_follow_candidate",
                            candidate_id: candidate_id,
                        },
                        beforeSend: function () {
                            $this.children(".icon-plus i").removeClass("far fa-plus");
                            $this
                                .find(".icon-plus")
                                .html('<span class="civi-dual-ring"></span>');
                        },
                        success: function (data) {
                            if (data.added) {
                                $this.removeClass("removed").addClass("added");
                                $this.parents(".civi-candidate-item").removeClass("removed-follow_candidate");
                                $this.html('<i class="fa-regular fa-heart" style="color:#2876BB;"></i>');
                            } else {
                                $this.removeClass("added").addClass("removed");
                                $this.parents(".civi-candidate-item").addClass("removed-follow_candidate");
                                $this.html('<i class="fa-regular fa-heart" style="color:#2876BB; font-weight: 700;"></i>');
                            }
                            if (typeof data.added == "undefined") {
                                console.log("login?");
                            }
                            $this.removeClass("on-handle");
                            candidate_inner.removeClass("candidate-active-hover");
                        },
                        error: function (xhr) {
                            var err = eval("(" + xhr.responseText + ")");
                            console.log(err.Message);
                            $this.children("i").removeClass("fa-spinner fa-spin");
                            $this.removeClass("on-handle");
                            candidate_inner.removeClass("candidate-active-hover");
                        },
                    });
                }
            });
        },
    };
    $(document).ready(function () {
        FOLLOW_CANDIDATE.init();
    });
})(jQuery);
