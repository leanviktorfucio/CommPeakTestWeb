$(document).ready(() => {
    // prevent the upload form from reloading the page
    $('#uploadCSVForm').submit((e) => {
        e.preventDefault();
        
        const form = $(e.currentTarget);

        const action = form.attr('action');
        const method = form.attr('method');


        // create FormData
        const formData = new FormData();

        // get files
        const filesInput = form.find('input[type="file"]');
        const formFiles = filesInput[0].files;
        for (let i = 0; i < formFiles.length; i++) {
            formData.append(filesInput.attr('name'), formFiles[i]);
        }
        const csrfInput = form.find('input[type="hidden"]');
        formData.append(csrfInput.attr('name'), csrfInput.val());

        $.post({
            url: action,
            type: method,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (response) => {
                // $("#logContainer").html('<div class="alert alert-primary" role="alert">Success!</div>');
                $("#logContainer").html(response);
            },
            error: (response) => {
                const errors = JSON.parse(response.responseText);

                for(i in errors) {
                    $("#logContainer").append('<div class="alert alert-danger" role="alert">' + errors[i] + '</div>');
                }
            },
            beforeSend: () => {
                $('.form-container').css('opacity', .5);
                $('.form-container :input').attr('disabled', true);
                $("#logContainer").empty();
            },
            complete: (response) => {
                $('.form-container').css('opacity', '');
                $('.form-container :input').attr('disabled', false);
            }
        });
    });
});