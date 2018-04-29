window.addEventListener('load', function () {
    /**
     * @param {string} formClass
     * @param formClass
     * @constructor
     */
    function AddPostForm(formClass) {
        let $form = $('.' + formClass);
        let $title = $form.find('input[name="title"]');
        let $content = $form.find('textarea[name="content"]');
        let $image = $form.find('input[name="image"]');
        let $email = $form.find('input[name="email"]');
        let $submitButton = $form.find('input[type="submit"]');
        let $fileUpload = $form.find('input[type="file"]');
        let $removeImage = $form.find('.remove-image');
        let $filePath = $form.find('.file-path');
        let $usedWordsBlock = $('.blog-used-words');
        let $loader = $('.loader');
        let $errorsContainer = $('.errors ul');

        $fileUpload.change(function(e) {
            if (e.target.files[0]) {
                $removeImage.show();
                $filePath.text(e.target.files[0].name)
            } else {
                $removeImage.hide();
                $filePath.text('')
            }
        });

        $removeImage.click(function(e) {
                e.preventDefault();
                removeImage();
            }
        );

        $form.submit(function(e) {
            e.preventDefault();
            clearErrors();
            submit();
        });

        /**
         * Validates and submits the form
         */
        function submit() {
            let errors = validate();

            if (errors.length === 0) {
                $loader.show();
                $submitButton.prop('disabled', true);

                //Imitate long long connection
                setTimeout(function() {
                    let formData = new FormData($form[0]);

                    fetch($form.attr('action'), {
                        method: "POST",
                        body: formData
                    }).then((response) => response.json())
                    .then(function(response) {
                        let parsedResponse = JSON.parse(response);

                        if (parsedResponse.data) {
                            if (parsedResponse.data.post) {
                                $('.blog-posts').prepend(parsedResponse.data.post);
                            }

                            console.log(parsedResponse.data);
                            if (parsedResponse.data.usedWords) {
                                $usedWordsBlock.html(parsedResponse.data.usedWords);
                            }

                            clearForm();
                        } else if (parsedResponse.errors) {
                            showErrors(parsedResponse.errors);
                        }

                        $loader.hide();
                        $submitButton.prop('disabled', false);
                    });
                }, 2000);
            } else {
                showErrors(errors);
            }
        }

        function removeImage() {
            $removeImage.hide();
            $fileUpload.val('');
            $filePath.text('');
        }

        function clearForm() {
            removeImage();
            $form.trigger('reset');
        }

        function showErrors(errors) {
            clearErrors();
            errors.forEach(function (error) {
                $errorsContainer.append($('<li>' + error + '</li>'));
            });
        }

        function clearErrors() {
            $errorsContainer.html('');
        }

        /**
         * Validates fields of the form
         *
         * @returns {Object}
         */
        function validate() {
            let errors = [];

            if (!$title.val()) {
                errors.push('Title can\'t be empty');
            }

            if (!validateEmail()) {
                errors.push('Email must look like example@domain.com')
            }

            if (!$content.val() && !$image.val()) {
                errors.push('Either image or content must be specified');
            }

            return errors;
        }

        /**
         * Validates email
         *
         * @returns {boolean}
         */
        function validateEmail() {
            let email = $email.val();
            if (!email) {
                return false;
            }

            return /^[^@\s]+@([^@\s]+)/.test(email);
        }
    }

    new AddPostForm('blog-add-post-form');
});
