window.addEventListener('load', function () {
    /**
     * @param {string} mainContainerId
     * @returns {Object}
     * @constructor
     */
    function TestForm(mainContainerId) {
        const fioId = 'fio';
        const emailId = 'email';
        const phoneId = 'phone';
        const fillableFields = [fioId, emailId, phoneId];

        const formSelector = '#' + mainContainerId + ' #myForm';
        const form = document.querySelector(formSelector);
        const resultContainer = document.querySelector('#' + mainContainerId + ' #resultContainer');
        const submitButton = getFieldInputWithId('submitButton');

        /**
         * Whitelist of domains
         * @type {string[]}
         */
        const allowedDomains = ['ya.ru', 'yandex.ru', 'yandex.ua', 'yandex.by', 'yandex.kz', 'yandex.com'];

        form.addEventListener("submit", submitEventHandler);

        /**
         * Wrapper for submit to prevent default event and use submit separately from form event
         *
         * @param {Object} e
         */
        function submitEventHandler(e) {
            e.preventDefault();
            submit();
        }

        /**
         * Returns input with a specified id
         *
         * @param {string} fieldId
         * @returns {Element}
         */
        function getFieldInputWithId(fieldId) {
            return document.querySelector(formSelector + ' #' + fieldId)
        }

        /**
         * Validates and submits the form
         */
        function submit() {
            let validationResult = validateAndShowErrors();

            if (validationResult.isValid) {
                submitButton.disabled = true;
                resultContainer.classList = '';

                let xhr = new XMLHttpRequest();
                xhr.open('GET', form.action, true);
                //Can be used for testing random responses
                //xhr.open('GET', ["responses/progress.json", "responses/success.json", "responses/error.json"][Math.floor(Math.random()*3)], true);
                xhr.onload = handleResponse;
                xhr.send();
            }
        }

        /**
         * Handles submit response from server
         */
        function handleResponse() {
            let response = JSON.parse(this.response);

            switch (response.status) {
                case "success":
                    resultContainer.innerText = "Success";
                    resultContainer.classList.add("success");
                    submitButton.disabled = false;
                    break;
                case "error":
                    resultContainer.innerText = response.reason;
                    resultContainer.classList.add("error");
                    submitButton.disabled = false;
                    break;
                case "progress":
                    resultContainer.innerText = '';
                    setTimeout(submit, response.timeout);
                    break;
                default:
                    submitButton.disabled = false;
                    console.error('Something went wrong');
            }
        }

        /**
         * Calls validation and shows errors for fields. Not a fan of this function - it has multiple responsibilities.
         *
         * @returns {Object}
         */
        function validateAndShowErrors() {
            fillableFields.forEach(function (fieldId) {
                getFieldInputWithId(fieldId).classList.remove("error");
            });

            let validationResult = validate();

            if (!validationResult.isValid) {
                validationResult.errorFields.forEach(function (fieldId) {
                    getFieldInputWithId(fieldId).classList.add("error");
                });
            }

            return validationResult;
        }

        /**
         * Validates fields of the form
         *
         * @returns {Object}
         */
        function validate() {
            const fieldsIdToValidator = {
                [fioId]: validateFio,
                [emailId]: validateEmail,
                [phoneId]: validatePhone
            };

            let errorFields = [];

            for (let fieldId in fieldsIdToValidator) {
                if (!fieldsIdToValidator[fieldId]()) {
                    errorFields.push(fieldId);
                }
            }

            return  {
                isValid: errorFields.length === 0,
                errorFields: errorFields
            }
        }

        /**
         * @returns {boolean}
         */
        function validateFio() {
            let fio = getFieldInputWithId(fioId).value.trim();
            if (!fio) {
                return false;
            }

            let words = fio.split(" ");
            return words.length === 3;
        }

        /**
         * @returns {boolean}
         */
        function validateEmail() {
            let email = getFieldInputWithId(emailId).value.trim();
            if (!email) {
                return false;
            }

            let emailMatch = email.match(/^[^@\s]+@([^@\s]+)/);
            let domain = emailMatch[1];

            //domain must be in the whitelist
            return allowedDomains.includes(domain);
        }

        /**
         * @returns {boolean}
         */
        function validatePhone() {
            let phone = getFieldInputWithId(phoneId).value.trim();
            if (!phone) {
                return false;
            }

            if (!phone.match(/^^\+7\([0-9]{3}\)[0-9]{3}-[0-9]{2}-[0-9]{2}$/)) {
                return false;
            }

            let digits = phone.replace(/\D+/g, '').split('');

            //digits sum must not exceed given number
            return digits.reduce((a, b) => parseInt(a) + parseInt(b), 0) <= 30;
        }

        return {
            /**
             * Submits the form
             */
            submit() {
                submit();
            },

            /**
             * Validates the form
             *
             * @returns {Object}
             */
            validate() {
                return validateAndShowErrors();
            },

            /**
             * Returns object with form fields names/values
             *
             * @returns {Object}
             */
            getData() {
                let data = {};
                fillableFields.forEach(function (fieldId) {
                    data[fieldId] = getFieldInputWithId(fieldId).value;
                });

                return data;
            },

            /**
             * Sets fillable form fields, other fields are ignored
             *
             * @param {Object} data
             */
            setData(data) {
                fillableFields.forEach(function (fieldId) {
                    getFieldInputWithId(fieldId).value = data[fieldId];
                });
            }
        }
    }

    window.MyForm = new TestForm('test-app');
});
