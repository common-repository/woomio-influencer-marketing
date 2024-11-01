(function ($) {
    'use strict';

    $("#tbl-token-list").DataTable();
    $("#tbl-discount-code-list").DataTable();
    $("#tbl-token-sales").DataTable();
    $("#tbl-token-form").DataTable({
        "bLengthChange": false,
        "paging": false,
        "bInfo": false
    });

    $("#wfw-coupon").select2({
        width: '100%',
    });

    // Create token
    $("#form-token").validate({
        submitHandler: function () {
            wfw_clear_alert();
            wfw_loader();
            var postData = $("#form-token").serialize();
            postData += "&action=wfw_admin_ajax&param=cu_token";
            $.post(ajaxurl, postData, function (res) {
                if (res.status === 1) {
                    wfw_show_alert(res.message);
                    $("#form-token").trigger("reset");
                    $("#wfw-coupon").val(null).trigger('change');
                    wfw_loader(false);
                    wfw_reload_page();
                }
                if (res.status === 0) {
                    wfw_loader(false);
                    wfw_show_alert(res.message, 'danger');
                }
            }).fail(function (res) {
                wfw_loader(false);
                alert("Something went wrong. Please try again later.");
            });
        }
    });

    // Delete token
    $(document).on('click', ".btn-delete-token", function (e) {
        if (confirm("Are you sure you want to delete token?")) {
            wfw_clear_alert();
            wfw_loader();
            var token_id = $(this).data('id');
            var postData = "action=wfw_admin_ajax&param=delete_token&token_id=" + token_id;
            $.post(ajaxurl, postData, function (res) {
                if (res.status === 1) {
                    wfw_loader(false);
                    wfw_show_alert(res.message);
                    wfw_reload_page();
                } else {
                    alert("Something went wrong. Please try again.");
                }
            });
        }
    });

    // Check all coupons
    $("#wfwSelectAllCodes").click(function () {
        $('.wfwCodeCheck').prop('checked', this.checked);
    });

    function wfw_loader(show = true) {
        if (show) {
            $("#btnText").hide();
            $("#loader").show();
            $("#btnSubmit").attr('disabled', true);
        } else {
            $("#loader").hide();
            $("#btnText").show();
            $("#btnSubmit").attr('disabled', false);
        }
    }

    function wfw_show_alert(message, type = 'success') {
        if (type === 'success') {
            $("#alertSuccess").show();
            $("#alertSuccess").text(message);
        } else {
            $("#alertDanger").show();
            $("#alertDanger").text(message);
        }
    }

    function wfw_clear_alert() {
        $("#alertSuccess").hide();
        $("#alertDanger").hide();
    }

    function wfw_reload_page(time = 1000) {
        setInterval(function () {
            window.location.reload(true);
        }, time);
    }

})(jQuery);
