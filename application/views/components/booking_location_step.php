<?php
/**
 * Local variables.
 *
 * @var array $available_locations
 */
?>

<div id="wizard-frame-1" class="wizard-frame" style="visibility: hidden;">
    <div class="frame-container">
        <h2 class="frame-title mt-md-5">
            <i class="fas fa-map-marker-alt me-2"></i>
            <?= lang('select_location') ?>
        </h2>

        <div class="row frame-content">
            <div class="col col-md-8 offset-md-2">
                <div class="mb-3">
                    <label for="select-location">
                        <strong><?= lang('location') ?></strong>
                    </label>

                    <select id="select-location" class="form-select">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                        <?php foreach ($available_locations as $location): ?>
                            <option value="<?= $location['id'] ?>" 
                                    data-address="<?= e($location['address']) ?>"
                                    data-phone="<?= e($location['phone']) ?>">
                                <?= e($location['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php slot('after_select_location'); ?>

                <div id="location-details" class="small" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= lang('location_details') ?>
                            </h6>
                            <div id="location-address" class="mb-2">
                                <!-- JS -->
                            </div>
                            <div id="location-phone">
                                <!-- JS -->
                            </div>
                        </div>
                    </div>
                </div>

                <?php slot('after_location_details'); ?>
                
            </div>
        </div>
    </div>

    <div class="command-buttons">
        <span>&nbsp;</span>

        <button type="button" id="button-next-1" class="btn button-next btn-dark"
                data-step_index="1">
            <?= lang('next') ?>
            <i class="fas fa-chevron-right ms-2"></i>
        </button>
    </div>
</div>