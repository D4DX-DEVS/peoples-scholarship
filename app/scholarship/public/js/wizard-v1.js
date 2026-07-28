"use strict";

// Class definition
var KTWizard1 = function () {
    // Base elements
    var wizardEl;
    var formEl;
    var validator;
    var wizard;

    // Private functions
    var initWizard = function () {
        // Initialize form wizard
        wizard = new KTWizard('kt_wizard_v1', {
            startStep: 1
        });

        // Validation before going to next page
        wizard.on('beforeNext', function (wizardObj) {
            if (validator.form() !== true) {
                // don't go to the next step
                wizardObj.stop();
            }
        })

        // Change event
        wizard.on('change', function (wizard) {
            setTimeout(function () {
                KTUtil.scrollTop();
            }, 500);
        });
    }

    var initValidation = function () {
        validator = formEl.validate({
            // Validate only visible fields
            ignore: ":hidden",

            // Validation rules
            rules: {
                //= Step 1
                address1: {
                    required: true
                },
                address1: {
                    required: true
                },
                postcode: {
                    required: true
                },
                city: {
                    required: true
                },
                state: {
                    required: true
                },
                latitude: {
                    required: false
                },

                //= Step 2
                longitude: {
                    required: false
                },
                country: {
                    required: true
                },
                shopname: {
                    required: true
                },
                logo: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                username: {
                    required: true

                },
                password: {
                    required: true
                },
                category: {
                    required: true
                },
                contactperson: {
                    required: true
                },
                contactemail: {
                    required: true
                },
                contactno: {
                    required: true
                },
                ownername: {
                    required: true
                },
                shopcode: {
                    required: true
                },


            },

            // Display error
            invalidHandler: function (event, validator) {
                KTUtil.scrollTop();

                swal.fire({
                    "title": "",
                    "text": "There are some errors in your submission. Please correct them.",
                    "type": "error",
                    "confirmButtonClass": "btn btn-primary"
                });
            },

            // Submit valid form
            submitHandler: function (form) {

            }
        });
    }

    var initSubmit = function () {
        var btn = formEl.find('[data-ktwizard-type="action-submit"]');
        var res;
        btn.on('click', function (e) {
            e.preventDefault();

            if (validator.form()) {
                // See: src\js\framework\base\app.js
                KTApp.progress(btn);
                //KTApp.block(formEl);

                // See: http://malsup.com/jquery/form/#ajaxSubmit
                formEl.ajaxSubmit({
                    success: function (res) {
                        console.log(res)
                        // debugger;
                        KTApp.unprogress(btn);
                        KTApp.unblock(formEl);
                        if (res == 1) {
                            swal.fire({
                                "title": "",
                                "text": "Shop Details Added Successfully",
                                "type": "success",
                                "confirmButtonClass": "btn btn-primary"
                            });
                        } else {
                            swal.fire({
                                "title": "",
                                "text": "Something Went Wrong!",
                                "type": "error",
                                "confirmButtonClass": "btn btn-danger"
                            });
                        }
                        window.location = "/shop_list";


                    }
                });
            }
        });
    }

    return {
        // public functions
        init: function () {
            wizardEl = KTUtil.get('kt_wizard_v1');
            formEl = $('#kt_form');

            initWizard();
            initValidation();
            initSubmit();
        }
    };
}();

jQuery(document).ready(function () {
    KTWizard1.init();
});
