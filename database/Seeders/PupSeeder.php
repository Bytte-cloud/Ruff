<?php

namespace Database\Seeders;

use Ruff\Models\Pup;
use Illuminate\Database\Seeder;

class PupSeeder extends Seeder
{
    /**
     * The default catalogue of VPS templates shipped with the panel. Keyed by
     * name so the seeder can be re-run safely (existing rows are left untouched,
     * including any image_url an administrator has customised).
     *
     * Linux entries point at upstream cloud images (cloud-init ready). Windows
     * entries are placeholders an administrator must point at a prepared qcow2
     * template, since Windows images cannot be redistributed.
     */
    private array $pups = [
        [
            'name' => 'Ubuntu 22.04 LTS',
            'description' => 'Ubuntu Server 22.04 LTS (Jammy Jellyfish) cloud image.',
            'os_type' => 'linux',
            'firmware' => 'bios',
            'image_url' => 'https://cloud-images.ubuntu.com/jammy/current/jammy-server-cloudimg-amd64.img',
        ],
        [
            'name' => 'Ubuntu 24.04 LTS',
            'description' => 'Ubuntu Server 24.04 LTS (Noble Numbat) cloud image.',
            'os_type' => 'linux',
            'firmware' => 'bios',
            'image_url' => 'https://cloud-images.ubuntu.com/noble/current/noble-server-cloudimg-amd64.img',
        ],
        [
            'name' => 'Debian 12',
            'description' => 'Debian 12 (Bookworm) generic cloud image.',
            'os_type' => 'linux',
            'firmware' => 'bios',
            'image_url' => 'https://cloud.debian.org/images/cloud/bookworm/latest/debian-12-genericcloud-amd64.qcow2',
        ],
        [
            'name' => 'Debian 11',
            'description' => 'Debian 11 (Bullseye) generic cloud image.',
            'os_type' => 'linux',
            'firmware' => 'bios',
            'image_url' => 'https://cloud.debian.org/images/cloud/bullseye/latest/debian-11-genericcloud-amd64.qcow2',
        ],
        [
            'name' => 'Rocky Linux 9',
            'description' => 'Rocky Linux 9 generic cloud image.',
            'os_type' => 'linux',
            'firmware' => 'bios',
            'image_url' => 'https://download.rockylinux.org/pub/rocky/9/images/x86_64/Rocky-9-GenericCloud.latest.x86_64.qcow2',
        ],
        [
            'name' => 'Windows Server 2022',
            'description' => 'Windows Server 2022. Point image_url at a prepared qcow2 template on the node.',
            'os_type' => 'windows',
            'firmware' => 'uefi',
            'image_url' => '/var/lib/pterodactyl/vm-templates/windows-server-2022.qcow2',
        ],
        [
            'name' => 'Windows 11',
            'description' => 'Windows 11. Point image_url at a prepared qcow2 template on the node.',
            'os_type' => 'windows',
            'firmware' => 'uefi',
            'image_url' => '/var/lib/pterodactyl/vm-templates/windows-11.qcow2',
        ],
    ];

    /**
     * Seed the default VPS templates, skipping any that already exist by name.
     *
     * @throws \Ruff\Exceptions\Model\DataValidationException
     */
    public function run(): void
    {
        foreach ($this->pups as $pup) {
            if (Pup::query()->where('name', $pup['name'])->exists()) {
                continue;
            }

            Pup::create($pup);
        }
    }
}
