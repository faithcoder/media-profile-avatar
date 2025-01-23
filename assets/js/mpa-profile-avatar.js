jQuery(document).ready(function ($) {
    var mediaUploader;

    $('#mpa-upload-avatar').click(function (e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Profile Picture',
            button: {
                text: 'Use this picture',
            },
            multiple: false,
        });
        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mpa-profile-avatar').val(attachment.url);
            
            // Update or create preview image
            if ($('#mpa-profile-preview').length) {
                $('#mpa-profile-preview').attr('src', attachment.url);
            } else {
                $('#mpa-upload-avatar').after('<img src="' + attachment.url + '" id="mpa-profile-preview" style="max-width:100px; margin-top:10px; display:block;">');
            }
            $('#mpa-remove-avatar').show();
        });
        mediaUploader.open();
    });

    $('#mpa-remove-avatar').click(function (e) {
        e.preventDefault();
        $('#mpa-profile-avatar').val('');
        $(this).hide();
        $('#mpa-profile-preview').remove();
    });
});