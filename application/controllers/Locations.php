<?php defined('BASEPATH') or exit('No direct script access allowed');

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
 * Locations controller.
 *
 * Handles the locations related operations.
 *
 * @package Controllers
 */
class Locations extends EA_Controller
{
    /**
     * Locations constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('locations_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend locations page.
     *
     * This method will display the locations management page.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('locations')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_LOCATIONS)) {
            if ($user_id) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'date_format' => setting('date_format'),
            'time_format' => setting('time_format'),
            'first_weekday' => setting('first_weekday'),
            'require_phone_number' => setting('require_phone_number'),
            'timezones' => $this->timezones->to_array(),
        ]);

        html_vars([
            'page_title' => lang('locations'),
            'active_menu' => PRIV_LOCATIONS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/locations');
    }

    /**
     * Filter locations by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_LOCATIONS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'name ASC';

            $limit = request('limit');

            $offset = request('offset', '0');

            $locations = $this->locations_model->search($keyword, $limit, $offset, $order_by);

            json_response($locations);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new location.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_LOCATIONS)) {
                abort(403, 'Forbidden');
            }

            $location = request('location');

            $this->locations_model->api_decode($location);

            $location_id = $this->locations_model->save($location);

            $location = $this->locations_model->find($location_id);

            $this->locations_model->api_encode($location);

            $this->webhooks_client->trigger(WEBHOOK_LOCATION_SAVE, $location);

            json_response([
                'success' => true,
                'id' => $location_id
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a location.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_LOCATIONS)) {
                abort(403, 'Forbidden');
            }

            $location_id = request('location_id');

            $location = $this->locations_model->find($location_id);

            $this->locations_model->api_encode($location);

            json_response($location);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a location.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_LOCATIONS)) {
                abort(403, 'Forbidden');
            }

            $location_id = request('location_id');

            $location = request('location');

            $this->locations_model->api_decode($location, $this->locations_model->find($location_id));

            $location_id = $this->locations_model->save($location);

            $location = $this->locations_model->find($location_id);

            $this->locations_model->api_encode($location);

            $this->webhooks_client->trigger(WEBHOOK_LOCATION_SAVE, $location);

            json_response([
                'success' => true,
                'id' => $location_id
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a location.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_LOCATIONS)) {
                abort(403, 'Forbidden');
            }

            $location_id = request('location_id');

            $location = $this->locations_model->find($location_id);

            $this->locations_model->delete($location_id);

            $this->locations_model->api_encode($location);

            $this->webhooks_client->trigger(WEBHOOK_LOCATION_DELETE, $location);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}