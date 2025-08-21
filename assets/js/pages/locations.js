/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Locations page.
 *
 * This module implements the functionality of the locations page.
 */
App.Pages.Locations = (function () {
    const $locations = $('#locations');
    const $locationId = $('#location-id');
    const $locationName = $('#location-name');
    const $locationAddress = $('#location-address');
    const $locationPhone = $('#location-phone');
    const $locationEmail = $('#location-email');
    const $locationActive = $('#location-active');
    const $filterLocations = $('#filter-locations');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Add page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Filter Locations Form "Submit"
         */
        $locations.on('submit', '#filter-locations form', (event) => {
            event.preventDefault();
            const key = $filterLocations.find('.key').val();
            $filterLocations.find('.selected').removeClass('selected');
            App.Pages.Locations.resetForm();
            App.Pages.Locations.filter(key);
        });

        /**
         * Event: Filter Location Row "Click"
         */
        $locations.on('click', '.location-row', (event) => {
            if ($filterLocations.find('.filter').prop('disabled')) {
                $filterLocations.find('.results').css('color', '#AAA');
                return;
            }

            const locationId = $(event.currentTarget).attr('data-id');
            const location = filterResults.find((filterResult) => Number(filterResult.id) === Number(locationId));

            App.Pages.Locations.display(location);
            $filterLocations.find('.selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-location, #delete-location').prop('disabled', false);
        });

        /**
         * Event: Add New Location Button "Click"
         */
        $locations.on('click', '#add-location', () => {
            App.Pages.Locations.resetForm();
            $locations.find('.add-edit-delete-group').hide();
            $locations.find('.save-cancel-group').show();
            $locations.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $locations.find('.record-details .form-label span').prop('hidden', false);
            $filterLocations.find('button').prop('disabled', true);
            $filterLocations.find('.results').css('color', '#AAA');
            $locationName.trigger('focus');
        });

        /**
         * Event: Edit Location Button "Click"
         */
        $locations.on('click', '#edit-location', () => {
            $locations.find('.add-edit-delete-group').hide();
            $locations.find('.save-cancel-group').show();
            $locations.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $locations.find('.record-details .form-label span').prop('hidden', false);
            $filterLocations.find('button').prop('disabled', true);
            $filterLocations.find('.results').css('color', '#AAA');
            $locationName.trigger('focus');
        });

        /**
         * Event: Delete Location Button "Click"
         */
        $locations.on('click', '#delete-location', () => {
            const locationId = $locationId.val();
            const buttons = [
                {
                    text: lang('cancel'),
                    click: (event, messageModal) => {
                        messageModal.dispose();
                    },
                },
                {
                    text: lang('delete'),
                    click: (event, messageModal) => {
                        App.Pages.Locations.delete(locationId);
                        messageModal.dispose();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete_location'), lang('delete_record_prompt'), buttons);
        });

        /**
         * Event: Save Location Button "Click"
         */
        $locations.on('click', '#save-location', () => {
            App.Pages.Locations.save();
        });

        /**
         * Event: Cancel Location Button "Click"
         */
        $locations.on('click', '#cancel-location', () => {
            const locationId = $locationId.val();
            App.Pages.Locations.resetForm();
            if (locationId !== '') {
                App.Pages.Locations.select(locationId, true);
            }
        });
    }

    /**
     * Save location record to database.
     */
    function save() {
        const location = {
            name: $locationName.val(),
            address: $locationAddress.val(),
            phone: $locationPhone.val(),
            email: $locationEmail.val(),
            active: $locationActive.prop('checked'),
        };

        if ($locationId.val() !== '') {
            location.id = $locationId.val();
        }

        if (!validate()) {
            return;
        }

        App.Http.Locations.save(location).then((response) => {
            App.Layouts.Backend.displayNotification(lang('location_saved'));
            App.Pages.Locations.resetForm();
            $filterLocations.find('.key').val('');
            App.Pages.Locations.filter('', response.id, true);
        });
    }

    /**
     * Delete a location record from database.
     *
     * @param {Number} id Record ID to be deleted.
     */
    function remove(id) {
        App.Http.Locations.destroy(id).then(() => {
            App.Layouts.Backend.displayNotification(lang('location_deleted'));
            App.Pages.Locations.resetForm();
            App.Pages.Locations.filter($filterLocations.find('.key').val());
        });
    }

    /**
     * Validates a location record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $locations.find('.is-invalid').removeClass('is-invalid');
        $locations.find('.form-message').removeClass('alert-danger').hide();

        try {
            // Validate required fields.
            let missingRequired = false;

            $locations.find('.required').each((index, requiredField) => {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            return true;
        } catch (error) {
            $locations.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the location tab form back to its initial state.
     */
    function resetForm() {
        $filterLocations.find('.selected').removeClass('selected');
        $filterLocations.find('button').prop('disabled', false);
        $filterLocations.find('.results').css('color', '');

        $locations.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $locations.find('.record-details .form-label span').prop('hidden', true);
        $locations.find('.record-details .form-message').hide();
        $locationActive.prop('checked', true);

        $locations.find('.add-edit-delete-group').show();
        $locations.find('.save-cancel-group').hide();

        $('#edit-location, #delete-location').prop('disabled', true);

        $locations.find('.record-details .is-invalid').removeClass('is-invalid');
    }

    /**
     * Display a location record into the location form.
     *
     * @param {Object} location Contains the location record data.
     */
    function display(location) {
        $locationId.val(location.id);
        $locationName.val(location.name);
        $locationAddress.val(location.address);
        $locationPhone.val(location.phone);
        $locationEmail.val(location.email);
        $locationActive.prop('checked', Boolean(location.active));
    }

    /**
     * Filters location records depending a string keyword.
     *
     * @param {String} keyword This is used to filter the location records of the database.
     * @param {Number} selectId Optional, if set then after the filter operation the record with this
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, display = false) {
        App.Http.Locations.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $filterLocations.find('.results').empty();

            response.forEach((location, index) => {
                const html = App.Pages.Locations.getFilterHtml(location);
                $filterLocations.find('.results').append(html);
            });

            if (!response.length) {
                $filterLocations.find('.results').append(
                    $('<em/>', {
                        'text': lang('no_records_found'),
                    }),
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': lang('load_more'),
                    'click': () => {
                        filterLimit += 20;
                        filter(keyword, selectId, display);
                    },
                }).appendTo($filterLocations.find('.results'));
            }

            if (selectId) {
                App.Pages.Locations.select(selectId, display);
            }
        });
    }

    /**
     * Get Filter HTML
     *
     * Get a location row HTML for the filter results.
     *
     * @param {Object} location Contains the location record data.
     *
     * @return {String} The location row HTML code.
     */
    function getFilterHtml(location) {
        const name = location.name;
        const address = location.address;
        const phone = location.phone;
        const email = location.email;

        let html = $('<div/>', {
            'class': 'location-row entry',
            'data-id': location.id,
        });

        if (!location.active) {
            html.addClass('text-muted');
        }

        html.append(
            $('<strong/>', {
                'text': name,
            }),
        );

        if (address) {
            html.append($('<br/>'));
            html.append(
                $('<span/>', {
                    'text': address,
                }),
            );
        }

        if (phone) {
            html.append($('<br/>'));
            html.append(
                $('<small/>', {
                    'class': 'text-muted',
                    'text': phone,
                }),
            );
        }

        if (email) {
            html.append($('<br/>'));
            html.append(
                $('<small/>', {
                    'class': 'text-muted',
                    'text': email,
                }),
            );
        }

        return html;
    }

    /**
     * Select a specific record from the current filter results.
     *
     * If the location id does not exist in the filter results then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record in the form.
     */
    function select(id, display = false) {
        $filterLocations.find('.selected').removeClass('selected');

        $filterLocations.find('.location-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            const location = filterResults.find((filterResult) => Number(filterResult.id) === Number(id));

            App.Pages.Locations.display(location);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Pages.Locations.resetForm();
        App.Pages.Locations.filter('');
        App.Pages.Locations.addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        delete: remove,
        validate,
        resetForm,
        display,
        getFilterHtml,
        select,
        addEventListeners,
    };
})();