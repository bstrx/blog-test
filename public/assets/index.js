window.addEventListener('load', function () {
    /**
     * @param {string} formClass
     * @param formClass
     * @constructor
     */
    function AddPostForm(formClass) {
        let $form = $('.' + formClass);
        let $title = $form.find('input[name="title"]');
        let $content = $form.find('input[name="content"]');
        let $image = $form.find('input[name="image"]');
        let $email = $form.find('input[name="email"]');

        $form.submit(function(e) {
            e.preventDefault();
            submit();
        });

        /**
         * Validates and submits the form
         */
        function submit() {
            let validationResult = validate();

            if (validationResult.isValid) {
                $.post($form.attr('action'), $form.serialize(), handleResponse);
            } else {
                //TODO
                validationResult.errorFields.forEach(function (fieldId) {
                    console.log('error in field: ' + fieldId);
                    //getFieldInputWithId(fieldId).classList.add("error");
                });
            }
        }

        /**
         * Handles submit response from server
         */
        function handleResponse(response) {
            response = JSON.parse(response);

            if (response.data) {
                $('.blog-posts').prepend(response.data);
            } else if ($response.error) {
                //TODO show errors
            }


            // switch (response.status) {
            //     case "success":
            //         $('.blog-posts').prepend(response.data);
            //         resultContainer.innerText = "Success";
            //         resultContainer.classList.add("success");
            //         submitButton.disabled = false;
            //         break;
                // case "error":
                //     resultContainer.innerText = response.reason;
                //     resultContainer.classList.add("error");
                //     submitButton.disabled = false;
                //     break;
                // case "progress":
                //     resultContainer.innerText = '';
                //     setTimeout(submit, response.timeout);
                //     break;
                // default:
                //     submitButton.disabled = false;
                //     console.error('Something went wrong');
            // }
        }

        /**
         * Validates fields of the form
         *
         * @returns {Object}
         */
        function validate() {
            let errorFields = [];

            if (!$title.val()) {
                errorFields.push('title');
            }

            if (!validateEmail()) {
                errorFields.push('email')
            }

            if (!$content.val() && !$image.val()) {
                errorFields.push('content');
                errorFields.push('image');
            }

            return  {
                isValid: errorFields.length === 0,
                errorFields: errorFields
            }
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
