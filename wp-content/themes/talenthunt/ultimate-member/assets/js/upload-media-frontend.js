var globalurl = "";

(function($) {
    $(document).ready(function() {
        $('.customemediauploader').on('click', function() {
            var currentbtn = $(this);

            var frame = new wp.media.view.MediaFrame.Post({
                multiple: false,
                library: {
                    order: 'ASC',
                    orderby: 'title',
                    type: 'image',
                    search: null,
                    uploadedTo: null
                }
            });
            frame.on('ready', function() {});
            frame.on('attach', function() {});
            frame.on('open', function() {});
            frame.on('escape', function() {});
            frame.on('close', function() {});
            frame.on('select', function() {

                var url = "";

                jQuery('.embed-url input[type="url"]').each(function() {

                    if (jQuery(this).val() != 'http://' && jQuery(this).val() != 'https://') {
                        url = jQuery(this).val();
                    }
                });

                if (url != '') {

                    var ext = get_url_extension(url);
                    ext = ext.toLowerCase();

                    var type = "file";

                    if (ext == 'jpg' || ext == 'jpeg' || ext == 'bmp' || ext == 'png')
                        type = "image";
                    else {

                        var id = matchYoutubeUrl(url);
                        var vimdeo = vimeovalidate(url);
                        if (id != false) {
                            type = "iframe"
                            url = "https://www.youtube.com/embed/" + id;
                        } else if (vimdeo != false) {
                            type = "iframe"
                            url = "https://player.vimeo.com/video/" + vimdeo;
                        }
                    }


                    var dataarray = "";
                    dataarray += (type + '~' + url) + '~~';

                    if (jQuery('#' + jQuery(currentbtn).attr('data-key')).val() != '')
                        jQuery('#' + jQuery(currentbtn).attr('data-key')).val(jQuery('#' + jQuery(currentbtn).attr('data-key')).val() + '~~' + dataarray);
                    else
                        jQuery('#' + jQuery(currentbtn).attr('data-key')).val(dataarray);
                    umem_display(currentbtn);
                }

            });
            frame.on('update', function() {
                images = frame.state().get('library');
                var dataarray = "";
                i = 0;
                images.each(function(attachment) {
                    dataarray += (attachment.attributes.type + '~' + attachment.attributes.url) + '~~';
                });
                if (jQuery('#' + jQuery(currentbtn).attr('data-key')).val() != '')
                    jQuery('#' + jQuery(currentbtn).attr('data-key')).val(jQuery('#' + jQuery(currentbtn).attr('data-key')).val() + '~~' + dataarray);
                else
                    jQuery('#' + jQuery(currentbtn).attr('data-key')).val(dataarray);
                umem_display(currentbtn);
            });
            frame.on('insert', function() {

                console.log('jjj');
                attachment = frame.state().get('selection').first().toJSON();
                var attachments = frame.state().get('selection').map(
                    function(attachment) {
                        attachment.toJSON();
                        return attachment;
                    });
                var i;
                var dataarray = "";
                for (i = 0; i < attachments.length; ++i) {
                    dataarray += (attachments[i].attributes.type + '~' + attachments[i].attributes.url) + '~~';
                }
                if (jQuery('#' + jQuery(currentbtn).attr('data-key')).val() != '')
                    jQuery('#' + jQuery(currentbtn).attr('data-key')).val(jQuery('#' + jQuery(currentbtn).attr('data-key')).val() + '~~' + dataarray);
                else
                    jQuery('#' + jQuery(currentbtn).attr('data-key')).val(dataarray);
                umem_display(currentbtn);
            });
            frame.on('activate', function() {});
            frame.on('{region}:deactivate', function() {});
            frame.on('{region}:deactivate:{mode}', function() {});
            frame.on('{region}:create', function() {});
            frame.on('{region}:create:{mode}', function() {});
            frame.on('{region}:render', function() {});
            frame.on('{region}:render:{mode}', function() {});
            frame.on('{region}:activate', function() {});
            frame.on('{region}:activate:{mode}', function() {});
            frame.state();
            frame.lastState();
            frame.open();
        });
    });
})(jQuery);

function umem_display(mediauploaderbtnselector) {
    if (mediauploaderbtnselector != '') {
        var datas = jQuery('#' + jQuery(mediauploaderbtnselector).attr('data-key')).val().replace("~~~~", '~~');
        dataarray = datas.split('~~');
        var output = '<div width="100%" class="custommediatable customemdiatable_' + jQuery(mediauploaderbtnselector).attr('data-key') + '">';
        for (var i = 0; i < dataarray.length; i++) {
            if (dataarray[i].trim() != '') {
                var ind = dataarray[i].split('~');
                output += '<div id="row_data_' + jQuery(mediauploaderbtnselector).attr('data-key') + '_' + i + '" data-key-type="' + ind[0] + '" data-key-url="' + ind[1] + '" class="item">';
                output += '<div width="45%">';
                if (ind[0] == 'image')
                    output += '<img src="' + ind[1] + '"  width="100" height="100"/>';
                if (ind[0] == 'audio')
                    output += '<audio controls><source src="' + ind[1] + '" type="audio/mpeg">Your browser does not support the audio tag.</audio>';
                if (ind[0] == 'video')
                    output += '<video width="100" height="100" controls><source src="' + ind[1] + '" type="video/mp4">Your browser does not support the video tag.</video>';

                if (ind[0] == 'iframe')
                    output += '<iframe width="100" height="100" src="' + ind[1] + '" frameborder="0" ></iframe>';

                if (ind[0] != 'video' && ind[0] != 'audio' && ind[0] != 'image' && ind[0] != 'iframe')
                    output += '<a href="' + ind[1] + '" target="_blank">File</a>';
                output += '</div>';
                output += '<div>';
                output += '<a href="javascript:removedata(\'row_data_' + jQuery(mediauploaderbtnselector).attr('data-key') + '_' + i + '\',\'' + jQuery(mediauploaderbtnselector).attr('data-key') + '\');" class="um-button">X</a>';
                output += '</div>';
                output += '</div>';
            }
        }
        output += '</table>';
        jQuery('#custommediawrapper_' + jQuery(mediauploaderbtnselector).attr('data-key')).html(output);
    }
}
jQuery(document).ready(function() {
    jQuery('.customemediauploader').each(function() {
        umem_display(jQuery(this));
    });
});

function removedata(rowid, mediaselector) {
    jQuery('#' + rowid).remove();
    var output = "";
    jQuery('.customemdiatable_' + mediaselector + ' div.item').each(function() {
        output += jQuery(this).attr('data-key-type') + '~' + jQuery(this).attr('data-key-url') + '~~';
    });
    jQuery('#' + mediaselector).val(output);
}

function get_url_extension(url) {
    return url.split(/[#?]/)[0].split('.').pop().trim();
}

function matchYoutubeUrl(url) {
    var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    var matches = url.match(p);
    if (matches) {
        return matches[1];
    }
    return false;
}


function vimeovalidate(url) {
    // Look for a string with 'vimeo', then whatever, then a
    // forward slash and a group of digits.
    var match = /vimeo.*\/(\d+)/i.exec(url);

    // If the match isn't null (i.e. it matched)
    if (match) {
        // The grouped/matched digits from the regex
        return match[1];
    }
    return false;
}