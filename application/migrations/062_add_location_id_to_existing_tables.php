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
 * Add location_id columns to services, providers and appointments tables.
 */
class Migration_Add_location_id_to_existing_tables extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        // Add location_id to services table
        $this->dbforge->add_column('services', [
            'location_id' => [
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => true,
                'null' => true,
                'after' => 'id_service_categories'
            ]
        ]);

        // Add location_id to users table (providers)
        $this->dbforge->add_column('users', [
            'location_id' => [
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => true,
                'null' => true,
                'after' => 'language'
            ]
        ]);

        // Add location_id to appointments table
        $this->dbforge->add_column('appointments', [
            'location_id' => [
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => true,
                'null' => true,
                'after' => 'id_services'
            ]
        ]);

        // Add foreign key constraints
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix . 'services` ADD CONSTRAINT `fk_services_locations` FOREIGN KEY (`location_id`) REFERENCES `' . $this->db->dbprefix . 'locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix . 'users` ADD CONSTRAINT `fk_users_locations` FOREIGN KEY (`location_id`) REFERENCES `' . $this->db->dbprefix . 'locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix . 'appointments` ADD CONSTRAINT `fk_appointments_locations` FOREIGN KEY (`location_id`) REFERENCES `' . $this->db->dbprefix . 'locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        // Add indexes for better performance
        $this->db->query('CREATE INDEX `idx_services_location_id` ON `' . $this->db->dbprefix . 'services` (`location_id`)');
        $this->db->query('CREATE INDEX `idx_users_location_id` ON `' . $this->db->dbprefix . 'users` (`location_id`)');
        $this->db->query('CREATE INDEX `idx_appointments_location_id` ON `' . $this->db->dbprefix . 'appointments` (`location_id`)');

        // Set default location_id to 1 for existing records (first location)
        $this->db->query('UPDATE `' . $this->db->dbprefix . 'services` SET `location_id` = 1 WHERE `location_id` IS NULL');
        $this->db->query('UPDATE `' . $this->db->dbprefix . 'users` SET `location_id` = 1 WHERE `location_id` IS NULL AND `id_roles` IN (2, 3)'); // Only providers and secretaries
        $this->db->query('UPDATE `' . $this->db->dbprefix . 'appointments` SET `location_id` = 1 WHERE `location_id` IS NULL');
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        // Drop foreign key constraints
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix . 'services` DROP FOREIGN KEY `fk_services_locations`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix . 'users` DROP FOREIGN KEY `fk_users_locations`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix . 'appointments` DROP FOREIGN KEY `fk_appointments_locations`');

        // Drop indexes
        $this->db->query('DROP INDEX `idx_services_location_id` ON `' . $this->db->dbprefix . 'services`');
        $this->db->query('DROP INDEX `idx_users_location_id` ON `' . $this->db->dbprefix . 'users`');
        $this->db->query('DROP INDEX `idx_appointments_location_id` ON `' . $this->db->dbprefix . 'appointments`');

        // Drop columns
        $this->dbforge->drop_column('services', 'location_id');
        $this->dbforge->drop_column('users', 'location_id');
        $this->dbforge->drop_column('appointments', 'location_id');
    }
}