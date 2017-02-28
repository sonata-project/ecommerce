var request = false;

jQuery(document).ready(function() {
    jQuery('form[id^="form_add_basket"]').on('submit', function (e) {
        e.preventDefault();

        if (false === request) {
            request = true;
            var self = $(this);

            jQuery.ajax({type: self.attr('method'), url: self.attr('action'), data: self.serialize(),
                success: function (data) {
                    if (data) {
                        request = false;
                        jQuery(self.attr('data-target')).html(data).modal('show');
                    }
                }
            });
        } else {
            return false;
        }
    });
});