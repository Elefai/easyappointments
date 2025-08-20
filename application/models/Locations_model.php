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
 * Locations model.
 *
 * Handles all the database operations of the location resource.
 *
 * @package Models
 */
class Locations_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'address' => 'address',
        'phone' => 'phone',
        'email' => 'email',
        'active' => 'active',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    /**
     * Save (insert or update) a location.
     *
     * @param array $location Associative array with the location data.
     *
     * @return int Returns the location ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $location): int
    {
        $this->validate($location);

        if (empty($location['id'])) {
            return $this->insert($location);
        } else {
            return $this->update($location);
        }
    }

    /**
     * Validate the location data.
     *
     * @param array $location Associative array with the location data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $location): void
    {
        // If a location ID is provided then check whether the record exists in the database.
        if (!empty($location['id'])) {
            $count = $this->db->get_where('locations', ['id' => $location['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException('The provided location ID does not exist in the database: ' . $location['id']);
            }
        }

        // Make sure all required fields are provided.
        if (empty($location['name'])) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($location, true));
        }
    }

    /**
     * Insert a new location into the database.
     *
     * @param array $location Associative array with the location data.
     *
     * @return int Returns the location ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $location): int
    {
        $location['created_at'] = date('Y-m-d H:i:s');
        $location['updated_at'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('locations', $location)) {
            throw new RuntimeException('Could not insert location.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing location.
     *
     * @param array $location Associative array with the location data.
     *
     * @return int Returns the location ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $location): int
    {
        $location['updated_at'] = date('Y-m-d H:i:s');

        if (!$this->db->update('locations', $location, ['id' => $location['id']])) {
            throw new RuntimeException('Could not update location.');
        }

        return $location['id'];
    }

    /**
     * Remove an existing location from the database.
     *
     * @param int $location_id Location ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $location_id): void
    {
        $this->db->delete('locations', ['id' => $location_id]);
    }

    /**
     * Get a specific location from the database.
     *
     * @param int $location_id The ID of the record to be returned.
     * @param bool $with_trashed
     *
     * @return array Returns an array with the location data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $location_id, bool $with_trashed = false): array
    {
        if (!$with_trashed) {
            $this->db->where('active', 1);
        }

        $location = $this->db->get_where('locations', ['id' => $location_id])->row_array();

        if (!$location) {
            throw new InvalidArgumentException('The provided location ID was not found in the database: ' . $location_id);
        }

        $this->cast($location);

        return $location;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $location_id Location ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected location value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $location_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($location_id)) {
            throw new InvalidArgumentException('The location ID argument cannot be empty.');
        }

        // Check whether the location exists.
        $query = $this->db->get_where('locations', ['id' => $location_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided location ID was not found in the database: ' . $location_id);
        }

        // Check if the required field is part of the location data.
        $location = $query->row_array();

        $this->cast($location);

        if (!array_key_exists($field, $location)) {
            throw new InvalidArgumentException('The requested field was not found in the location data: ' . $field);
        }

        return $location[$field];
    }

    /**
     * Get all locations that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     * @param bool $with_trashed
     *
     * @return array Returns an array of locations.
     */
    public function get(array|string $where = null, int $limit = null, int $offset = null, string $order_by = null, bool $with_trashed = false): array
    {
        if ($where !== null) {
            $this->db->where($where);
        }

        if (!$with_trashed) {
            $this->db->where('active', 1);
        }

        if ($order_by !== null) {
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by('name ASC');
        }

        $locations = $this->db->get('locations', $limit, $offset)->result_array();

        foreach ($locations as &$location) {
            $this->cast($location);
        }

        return $locations;
    }

    /**
     * Get all active locations.
     *
     * @return array Returns an array of active locations.
     */
    public function get_active(): array
    {
        return $this->get(['active' => 1]);
    }

    /**
     * Get the query builder interface, used for applying additional query conditions.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('locations');
    }

    /**
     * Search locations by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     * @param bool $with_trashed
     *
     * @return array Returns an array of locations.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null, bool $with_trashed = false): array
    {
        if (!$with_trashed) {
            $this->db->where('active', 1);
        }

        $locations = $this
            ->db
            ->select()
            ->from('locations')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('address', $keyword)
            ->or_like('phone', $keyword)
            ->or_like('email', $keyword)
            ->group_end()
            ->order_by($order_by ?? 'name ASC')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->result_array();

        foreach ($locations as &$location) {
            $this->cast($location);
        }

        return $locations;
    }

    /**
     * Load related resources to a location.
     *
     * @param array $location Associative array with the location data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$location, array $resources): void
    {
        // Locations do not currently have related resources.
    }

    /**
     * Convert the database location record to the equivalent API resource.
     *
     * @param array $location Location data.
     */
    public function api_encode(array &$location): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $location) ? (int)$location['id'] : null,
            'name' => $location['name'],
            'address' => $location['address'] ?? null,
            'phone' => $location['phone'] ?? null,
            'email' => $location['email'] ?? null,
            'active' => (bool)$location['active'],
            'createdAt' => $location['created_at'],
            'updatedAt' => $location['updated_at'],
        ];

        $location = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database record.
     *
     * @param array $location API resource.
     * @param array|null $base Base location data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$location, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $location)) {
            $decoded_resource['id'] = $location['id'];
        }

        if (array_key_exists('name', $location)) {
            $decoded_resource['name'] = $location['name'];
        }

        if (array_key_exists('address', $location)) {
            $decoded_resource['address'] = $location['address'];
        }

        if (array_key_exists('phone', $location)) {
            $decoded_resource['phone'] = $location['phone'];
        }

        if (array_key_exists('email', $location)) {
            $decoded_resource['email'] = $location['email'];
        }

        if (array_key_exists('active', $location)) {
            $decoded_resource['active'] = $location['active'];
        }

        if (array_key_exists('createdAt', $location)) {
            $decoded_resource['created_at'] = $location['createdAt'];
        }

        if (array_key_exists('updatedAt', $location)) {
            $decoded_resource['updated_at'] = $location['updatedAt'];
        }

        $location = $decoded_resource;
    }
}