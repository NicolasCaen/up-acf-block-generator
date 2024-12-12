(function($) {
    'use strict';

    $(document).ready(function() {
        const $iconInput = $('#block_icon');
        const $toggleButton = $('.toggle-icons');
        const $picker = $('.dashicons-picker');
        const $pickerItems = $('.dashicons-picker-item');

        // Toggle picker
        $toggleButton.on('click', function(e) {
            e.preventDefault();
            $picker.toggle();
        });

        // Select icon
        $pickerItems.on('click', function() {
            const icon = $(this).data('icon');
            $iconInput.val(icon);
            $pickerItems.removeClass('selected');
            $(this).addClass('selected');
            $picker.hide();
        });

        // Close picker when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.icon-selector').length) {
                $picker.hide();
            }
        });

        // Highlight current icon
        const currentIcon = $iconInput.val();
        $(`.dashicons-picker-item[data-icon="${currentIcon}"]`).addClass('selected');
    });

})(jQuery); 