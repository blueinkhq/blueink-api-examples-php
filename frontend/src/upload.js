import $ from 'jquery';
import BlueInkEmbed from '@blueink360/blueink-embed-js';


$(function() {
    $('#formExample1').on('submit', function(ev) {
        const fd = new FormData(ev.target);

        ev.preventDefault();
        onSubmit();
        console.log('Submitting data via AJAX request');

        // Submit data with axios
        axios.post('/embed/from_upload', fd)
            .then(response => {
                console.log('Response received');

                try {
                    const embedUrl = response.data.embedUrl;
                    const embed = new BlueInkEmbed(BLUEINK_PUBLIC_API_KEY);

                    embed.on(BlueInkEmbed.EVENT.ANY, (eventType, eventData) => {
                        console.log('Received event from embedded iFrame', eventType, eventData);
                        logEvent(eventType, eventData);
                    });

                    embed.on(BlueInkEmbed.EVENT.READY, (eventData) => {
                        $('#ready-container').html('<span class="badge badge-success">Ready to Sign</span>');
                        console.log('Received READY event from embedded iFrame', eventData);
                    });

                    embed.mount(embedUrl, '#iframe-container', { replace: true });
                    console.log('Mounted embedded signing iFrame');
                } catch(error) {
                    displayError(error.message);
                    console.log(error);
                }
            })
            .catch(error => {
                // Catch any error returned by our request to the Symfony example app
                displayError(error.response.data.error || error.message);
                console.log(error);
            })
    });

    function displayError(message) {
        const errorHtml = '<div class="alert alert-danger" role="alert">'
            + '<h3 class="alert-heading">Error Mounting iFrame</h3>'
            + '<p>' + message + '</p>'
            + '</div>';

        $('#iframe-container').html(errorHtml);
    }

    function logEvent(evType, evData) {
        const d = new Date();
        $('#event-container').append(`${d.toISOString()} -- ${evType}:\n${JSON.stringify(evData)}\n\n`);
    }

    function onSubmit() {
        $('#formExample1 fieldset').prop('disabled', true);
        $('#formContainer').append(
            '<div class="alert alert-primary" role="alert">'
            + 'Data submitted. Embedded signing is loading. Refresh to start over.'
            + '</div>'
        );
    }
});