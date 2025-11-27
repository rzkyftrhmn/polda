<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            [
                'level' => 'polda',
                'permissions' => json_encode([
                    'inspection' => true,
                    'investigation' => false,
                ]),
                'name' => 'Subbid Paminal',
                'type' => 'satker',
            ],
            [
                'level' => 'polda',
                'permissions' => json_encode([
                    'inspection' => false,
                    'investigation' => true,
                ]),
                'name' => 'Subbid Provos',
                'type' => 'satker',
            ],
            [
                'level' => 'polda',
                'permissions' => json_encode([
                    'inspection' => false,
                    'investigation' => true,
                ]),
                'name' => 'Subbid Wabprof',
                'type' => 'satker',
            ],
            [
                'level' => 'polres',
                'permissions' => json_encode([
                    'inspection' => true,
                    'investigation' => false,
                ]),
                'name' => 'Unit Paminal',
                'type' => 'satker',
            ],
            [
                'level' => 'polres',
                'permissions' => json_encode([
                    'inspection' => false,
                    'investigation' => true,
                ]),
                'name' => 'Unit Provos',
                'type' => 'satker',
            ],
            [
                'level' => 'Polda',
                'permissions' => json_encode([
                    'inspection' => false,
                    'investigation' => false,
                ]),
                'name' => 'Polda Jawa Barat',
                'type' => 'satwil',
                'childrens' => [
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polrestabes Bandung',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polresta Bandung',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polresta Bandung',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polresta Bandung',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polresta Bandung',
                                'type' => 'satker',
                            ],
                        ],
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polresta Bogor',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polresta Bogor Kota',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polresta Bogor Kota',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polresta Bogor Kota',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Bogor',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Bogor',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Bogor',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Sukabumi Kota',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Sukabumi Kota',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Sukabumi Kota',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Sukabumi',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Sukabumi',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Sukabumi',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Cianjur',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Cianjur',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Cianjur',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Purwakarta',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Purwakarta',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Purwakarta',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Karawang',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Karawang',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Karawang',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Subang',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Subang',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Subang',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Cimahi',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Cimahi',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Cimahi',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Sumedang',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Sumedang',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Sumedang',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Garut',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Garut',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Garut',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Tasikmalaya Kota',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Tasikmalaya Kota',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Tasikmalaya Kota',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Tasikmalaya',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Tasikmalaya',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Tasikmalaya',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Ciamis',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Ciamis',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Ciamis',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Cirebon',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Cirebon',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Cirebon',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polresta Cirebon',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polresta Cirebon',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polresta Cirebon',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Indramayu',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Indramayu',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Indramayu',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Majalengka',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Majalengka',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Majalengka',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Kuningan',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Kuningan',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Kuningan',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                    [
                        'level' => 'Polres',
                        'permissions' => json_encode([
                            'inspection' => false,
                            'investigation' => false,
                        ]),
                        'name' => 'Polres Banjar',
                        'type' => 'satwil',
                        'childrens' => [
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => true,
                                    'investigation' => false,
                                ]),
                                'name' => 'Unit Propam Paminal - Polres Banjar',
                                'type' => 'satker',
                            ],
                            [
                                'level' => 'Polres',
                                'permissions' => json_encode([
                                    'inspection' => false,
                                    'investigation' => true,
                                ]),
                                'name' => 'Unit Propam Provos - Polres Banjar',
                                'type' => 'satker',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // truncate table
        Division::truncate();
        foreach ($datas as $data) {
            $parent = Division::updateOrCreate(
                [
                    'name' => $data['name']
                ],
                [
                    'level' => $data['level'],
                    'permissions' => $data['permissions'],
                    'type' => $data['type'],
                ]
            );

            if (isset($data['childrens'])) {
                foreach ($data['childrens'] as $child) {
                    $childStore = Division::updateOrCreate(
                        [
                            'name' => $child['name'],
                            'parent_id' => $parent->id,
                        ],
                        [
                            'level' => $child['level'],
                            'permissions' => $child['permissions'],
                            'type' => $child['type'],
                        ]
                    );

                    foreach ($child['childrens'] as $grandchild) {
                        Division::updateOrCreate(
                            [
                                'name' => $grandchild['name'],
                                'parent_id' => $childStore->id,
                            ],
                            [
                                'level' => $grandchild['level'],
                                'permissions' => $grandchild['permissions'],
                                'type' => $grandchild['type'],
                            ]
                        );
                    }
                }
            }
        }
    }
}