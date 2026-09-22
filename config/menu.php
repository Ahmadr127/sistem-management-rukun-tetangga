<?php

/*
|--------------------------------------------------------------------------
| Menu Sidebar
|--------------------------------------------------------------------------
|
| Struktur menu sidebar berbasis konfigurasi.
| Setiap grup punya 'title' + 'menus'.
| Item menu bisa berupa:
|   - Link tunggal  : 'permission' (string) + 'route' + 'route_pattern' + 'icon'
|   - Dropdown      : 'permissions' (array) + 'children' (array link tunggal)
|
| 'route_pattern' dipakai untuk menentukan status aktif (request()->routeIs).
| Ikon memakai Font Awesome tanpa prefix, contoh: 'fa-tachometer-alt'.
|
*/

return [
    [
        'menus' => [
            [
                'label' => 'Dashboard',
                'icon' => 'bi-grid-fill',
                'route' => 'dashboard',
                'route_pattern' => 'dashboard',
                'permission' => 'view_dashboard',
            ],
        ],
    ],
    [
        'menus' => [
            [
                'label' => 'RT',
                'icon' => 'bi-geo-alt-fill',
                'permissions' => ['view_rt','view_alamat_rt'],
                'children' => [
                    [
                        'label' => 'Data RT',
                        'icon' => 'bi-geo-alt-fill',
                        'route' => 'rts.index',
                        'route_pattern' => 'rts.*',
                        'permission' => 'view_rt',
                    ],
                    [
                        'label' => 'Management Alamat',
                        'icon' => 'bi-signpost-2-fill',
                        'route' => 'alamat-rt.index',
                        'route_pattern' => 'alamat-rt.*',
                        'permission' => 'view_alamat_rt',
                    ],
                ],
            ],
            [
                'label' => 'Pengguna & Akses',
                'icon' => 'bi-person-fill-gear',
                'permissions' => ['manage_users', 'manage_roles', 'manage_permissions'],
                'children' => [
                    [
                        'label' => 'Users',
                        'icon' => 'bi-person-fill-gear',
                        'route' => 'users.index',
                        'route_pattern' => 'users.*',
                        'permission' => 'manage_users',
                    ],
                    [
                        'label' => 'Roles',
                        'icon' => 'bi-person-fill-check',
                        'route' => 'roles.index',
                        'route_pattern' => 'roles.*',
                        'permission' => 'manage_roles',
                    ],
                    [
                        'label' => 'Permissions',
                        'icon' => 'bi-key-fill',
                        'route' => 'permissions.index',
                        'route_pattern' => 'permissions.*',
                        'permission' => 'manage_permissions',
                    ],
                ],
            ],
        ],
    ],
    [
        'menus' => [
            [
                'label' => 'Data Warga',
                'icon' => 'bi-people-fill',
                'route' => 'warga.index',
                'route_pattern' => 'warga.*',
                'permission' => 'view_warga',
            ],
            [
                'label' => 'Kartu Keluarga',
                'icon' => 'bi-house-heart-fill',
                'route' => 'kartu-keluarga.index',
                'route_pattern' => 'kartu-keluarga.*',
                'permission' => 'view_kk',
            ],
            [
                'label' => 'Mutasi Warga',
                'icon' => 'bi-arrow-left-right',
                'route' => 'mutasi-warga.index',
                'route_pattern' => 'mutasi-warga.*',
                'permission' => 'view_mutasi',
            ],
        ],
    ],
    [
        'menus' => [
            [
                'label' => 'Keuangan',
                'icon' => 'bi-cash-coin',
                'permissions' => ['view_keuangan', 'view_laporan_keuangan', 'view_kas'],
                'children' => [
                    [
                        'label' => 'Pemasukan',
                        'icon' => 'bi-arrow-down-circle-fill',
                        'route' => 'keuangan.pemasukan.index',
                        'route_pattern' => 'keuangan.pemasukan.*',
                        'permission' => 'view_keuangan',
                    ],
                    [
                        'label' => 'Pengeluaran',
                        'icon' => 'bi-arrow-up-circle-fill',
                        'route' => 'keuangan.pengeluaran.index',
                        'route_pattern' => 'keuangan.pengeluaran.*',
                        'permission' => 'view_keuangan',
                    ],
                    [
                        'label' => 'Laporan Keuangan',
                        'icon' => 'bi-graph-up',
                        'route' => 'keuangan.laporan',
                        'route_pattern' => 'keuangan.laporan',
                        'permission' => 'view_laporan_keuangan',
                    ],
                    [
                        'label' => 'Kas Warga',
                        'icon' => 'bi-wallet2',
                        'route' => 'kas-warga.index',
                        'route_pattern' => 'kas-warga.*',
                        'permission' => 'view_kas',
                    ],
                ],
            ],
        ],
    ],
    [
        'menus' => [
            [
                'label' => 'Inventaris',
                'icon' => 'bi-box-seam-fill',
                'permissions' => ['view_inventaris', 'view_peminjaman', 'view_laporan_inventaris'],
                'children' => [
                    [
                        'label' => 'Data Barang',
                        'icon' => 'bi-box-seam',
                        'route' => 'inventaris.index',
                        'route_pattern' => ['inventaris.index','inventaris.create','inventaris.show','inventaris.edit'],
                        'permission' => 'view_inventaris',
                    ],
                    [
                        'label' => 'Peminjaman',
                        'icon' => 'bi-box-arrow-right',
                        'route' => 'peminjaman-inventaris.index',
                        'route_pattern' => 'peminjaman-inventaris.*',
                        'permission' => 'view_peminjaman',
                    ],
                    [
                        'label' => 'Laporan Inventaris',
                        'icon' => 'bi-clipboard-data-fill',
                        'route' => 'inventaris.laporan',
                        'route_pattern' => 'inventaris.laporan',
                        'permission' => 'view_laporan_inventaris',
                    ],
                ],
            ],
        ],
    ],
    [
        'menus' => [
            [
                'label' => 'Organisasi',
                'icon' => 'bi-buildings-fill',
                'permissions' => ['manage_organization_types', 'manage_organization_units'],
                'children' => [
                    [
                        'label' => 'Tipe Organisasi',
                        'icon' => 'bi-diagram-3-fill',
                        'route' => 'organization-types.index',
                        'route_pattern' => 'organization-types.*',
                        'permission' => 'manage_organization_types',
                    ],
                    [
                        'label' => 'Unit Organisasi',
                        'icon' => 'bi-buildings-fill',
                        'route' => 'organization-units.index',
                        'route_pattern' => 'organization-units.*',
                        'permission' => 'manage_organization_units',
                    ],
                ],
            ],
        ],
    ],
    [
        'menus' => [
            [
                'label' => 'Audit Log',
                'icon' => 'bi-clock-history',
                'route' => 'activity-logs.index',
                'route_pattern' => 'activity-logs.*',
                'permission' => 'view_activity_logs',
            ],
            [
                'label' => 'Pengaturan Sistem',
                'icon' => 'bi-gear-fill',
                'route' => 'settings.index',
                'route_pattern' => 'settings.*',
                'permission' => 'view_settings',
            ],
        ],
    ],
];
