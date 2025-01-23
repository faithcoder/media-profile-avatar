jQuery(document).ready(function ($) {
    var mediaUploader;

    $('#mpa_upload_profile_picture_button').click(function (e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media({
            title: 'Select Profile Picture',
            button: {
                text: 'Use this picture',
            },
            multiple: false,
        });
        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mpa_custom_profile_picture').val(attachment.url);
            $('#mpa_upload_profile_picture_button').after('<img src="' + attachment.url + '" style="max-width:100px; margin-top:10px; display:block;">');
            $('#mpa_remove_profile_picture_button').show();
        });
        mediaUploader.open();
    });

    $('#mpa_remove_profile_picture_button').click(function (e) {
        e.preventDefault();
        $('#mpa_custom_profile_picture').val('');
        $(this).hide();
        $('#mpa_upload_profile_picture_button').next('img').remove();
    });
});
