<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="locations-page">
    <div class="row" id="locations">
        <div id="filter-locations" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" aria-label="keyword">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h4 class="text-black-50 mb-3 fw-light">
                <i class="fas fa-map-marker-alt me-2"></i>
                <?= lang('locations') ?>
            </h4>

            <?php slot('after_page_title'); ?>
            
            <div class="results">
                <!-- JS -->
            </div>
        </div>

        <div class="record-details column col-12 col-md-5">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-location" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-location" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-location" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group" style="display:none;">
                    <button id="save-location" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-location" class="btn btn-outline-secondary">
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('details') ?>
            </h4>

            <div class="form-message alert" style="display:none;"></div>

            <form>
                <fieldset>
                    <legend class="d-none">
                        <?= lang('location_details') ?>
                    </legend>

                    <input id="location-id" type="hidden">

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label" for="location-name">
                                    <?= lang('name') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="location-name" class="form-control required" maxlength="256">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="location-address">
                                    <?= lang('address') ?>
                                </label>
                                <textarea id="location-address" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="location-phone">
                                    <?= lang('phone_number') ?>
                                </label>
                                <input id="location-phone" class="form-control" maxlength="20">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="location-email">
                                    <?= lang('email') ?>
                                </label>
                                <input id="location-email" class="form-control" maxlength="256" type="email">
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input id="location-active" class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label" for="location-active">
                                        <?= lang('active') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<div id="locations-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('location') ?></h3>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>
                    <?= lang('delete_record_prompt') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= lang('cancel') ?>
                </button>
                <button id="confirm-delete-location" class="btn btn-danger">
                    <?= lang('delete') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/pages/locations.js') ?>"></script>

<?php end_section('scripts'); ?>