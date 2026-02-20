<?php

namespace Database\Seeders;

use App\Models\SoftwareLicense;
use Illuminate\Database\Seeder;

class SoftwareLicenseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $licenses = [
            [
                'software_name' => 'Microsoft 365 Business Standard',
                'vendor' => 'Microsoft',
                'license_type' => 'Subscription',
                'total_licenses' => 120,
                'used_licenses' => 98,
                'license_key' => 'M365-BUS-STANDARD-XXXX-XXXX',
                'purchase_date' => '2025-01-10',
                'expiry_date' => '2026-01-10',
                'status' => 'active',
                'remarks' => 'Primary email and office productivity suite.',
            ],
            [
                'software_name' => 'Adobe Creative Cloud All Apps',
                'vendor' => 'Adobe',
                'license_type' => 'Per User',
                'total_licenses' => 25,
                'used_licenses' => 17,
                'license_key' => 'ADBE-CC-ALLAPPS-XXXX-XXXX',
                'purchase_date' => '2025-03-01',
                'expiry_date' => '2026-03-01',
                'status' => 'active',
                'remarks' => 'Assigned to design and marketing teams.',
            ],
            [
                'software_name' => 'Autodesk AutoCAD',
                'vendor' => 'Autodesk',
                'license_type' => 'Per Device',
                'total_licenses' => 15,
                'used_licenses' => 12,
                'license_key' => 'AUTOCAD-ENT-XXXX-XXXX',
                'purchase_date' => '2024-11-20',
                'expiry_date' => '2025-11-20',
                'status' => 'expired',
                'remarks' => 'For engineering desktops only.',
            ],
            [
                'software_name' => 'JetBrains All Products Pack',
                'vendor' => 'JetBrains',
                'license_type' => 'Per User',
                'total_licenses' => 30,
                'used_licenses' => 21,
                'license_key' => 'JB-ALL-PACK-XXXX-XXXX',
                'purchase_date' => '2025-06-15',
                'expiry_date' => '2026-06-15',
                'status' => 'active',
                'remarks' => 'Used by software development team.',
            ],
            [
                'software_name' => 'VMware vSphere Standard',
                'vendor' => 'VMware',
                'license_type' => 'Enterprise',
                'total_licenses' => 10,
                'used_licenses' => 10,
                'license_key' => 'VMW-VSPHERE-STD-XXXX',
                'purchase_date' => '2025-02-05',
                'expiry_date' => '2027-02-05',
                'status' => 'active',
                'remarks' => 'Fully allocated across production hosts.',
            ],
            [
                'software_name' => 'Zoom Pro',
                'vendor' => 'Zoom',
                'license_type' => 'Subscription',
                'total_licenses' => 80,
                'used_licenses' => 42,
                'license_key' => 'ZOOM-PRO-ORG-XXXX-XXXX',
                'purchase_date' => '2025-05-01',
                'expiry_date' => '2026-05-01',
                'status' => 'active',
                'remarks' => 'Department-managed pool licenses.',
            ],
            [
                'software_name' => 'Slack Business+',
                'vendor' => 'Slack',
                'license_type' => 'Subscription',
                'total_licenses' => 200,
                'used_licenses' => 167,
                'license_key' => 'SLACK-BUSINESS-PLUS-XXXX',
                'purchase_date' => '2025-04-20',
                'expiry_date' => '2026-04-20',
                'status' => 'active',
                'remarks' => 'Messaging and team collaboration platform.',
            ],
            [
                'software_name' => 'Windows Server Datacenter',
                'vendor' => 'Microsoft',
                'license_type' => 'Per Device',
                'total_licenses' => 8,
                'used_licenses' => 6,
                'license_key' => 'WIN-SRV-DTC-XXXX-XXXX',
                'purchase_date' => '2023-09-12',
                'expiry_date' => null,
                'status' => 'inactive',
                'remarks' => 'Perpetual license, retained for legacy servers.',
            ],
        ];

        foreach ($licenses as $license) {
            SoftwareLicense::updateOrCreate(
                ['software_name' => $license['software_name']],
                $license
            );
        }
    }
}
