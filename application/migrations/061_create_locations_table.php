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
 * Create locations table for multi-location support.
 */
class Migration_Create_locations_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => false,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ],
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('name');
        $this->dbforge->add_key('active');
        $this->dbforge->create_table('locations', true, ['engine' => 'InnoDB']);

        // Insert default locations
        $this->db->insert('locations', [
            'name' => 'Clínica ABC',
            'address' => 'Rua das Flores, 123 - Centro - São Paulo/SP',
            'phone' => '(11) 3333-4444',
            'email' => 'contato@clinicaabc.com.br',
            'active' => 1
        ]);

        $this->db->insert('locations', [
            'name' => 'Clínica XYZ',
            'address' => 'Av. Paulista, 1000 - Bela Vista - São Paulo/SP',
            'phone' => '(11) 5555-6666',
            'email' => 'atendimento@clinicaxyz.com.br',
            'active' => 1
        ]);
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        $this->dbforge->drop_table('locations', true);
    }
}