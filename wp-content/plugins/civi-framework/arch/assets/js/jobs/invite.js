(function ($) {
    "use strict";
    $(document).ready(function () {
        var ajax_url = civi_template_vars.ajax_url,
            $form_popup = $('#form-invite-popup');

        $(".civi-clear-invite").on("click", function (e) {
            e.preventDefault();
            $form_popup.find('input[type="checkbox"]').prop("checked", false);
        });

        $form_popup.each(function () {
            var $btn_submit = $form_popup.find('#btn-saved-invite');
            $btn_submit.on("click", function (e) {
                e.preventDefault();
                var $this = $(this),
                    candidate_id = $('input[name="candidate_id"]').val(),
                    author_id = $('input[name="author_id"]').val(),
                    list_jobs = $('input[name="list_jobs"]').val(),
                    jobs_id = $('input[name="jobs_invite[]"]:checked').map(function () {
                        return $(this).val();
                    }).get();

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "civi_add_to_invite",
                        candidate_id: candidate_id,
                        author_id : author_id,
                        list_jobs : list_jobs,
                        jobs_id: jobs_id,
                    },
                    beforeSend: function () {
                        $this.find(".btn-loading").fadeIn();
                    },
                    success: function (data) {
                        if (data.success == true) {
                            location.reload();
                        } else {
                            $(".message_error").text(data.message);
                        }
                        $this.find(".btn-loading").fadeOut();
                    },
                });
            });
        });
    });
})(jQuery);
