<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rt;

class RtSeeder extends Seeder
{
    public function run(): void
    {
        $rts = [
            ['kode_rt' => 'RT 01', 'nama_rt' => 'RT 01', 'is_active' => true, 'keterangan' => 'Rukun Tetangga 01'],
            ['kode_rt' => 'RT 02', 'nama_rt' => 'RT 02', 'is_active' => true, 'keterangan' => 'Rukun Tetangga 02'],
        ];

        foreach ($rts as $rt) {
            $model = Rt::firstOrCreate(['kode_rt' => $rt['kode_rt']], $rt);
            // seed master alamat for each RT
            $alamats = [
                'RT 01' => ['alamat'=>'Jl. Raya Bogor','rw'=>'05','kelurahan'=>'Sukamaju','kecamatan'=>'Bogor Timur','kota'=>'Bogor','provinsi'=>'Jawa Barat','kode_pos'=>'16143'],
                'RT 02' => ['alamat'=>'Jl. Raya Bogor','rw'=>'05','kelurahan'=>'Sukamaju','kecamatan'=>'Bogor Timur','kota'=>'Bogor','provinsi'=>'Jawa Barat','kode_pos'=>'16144'],
            ];
            if (isset($alamats[$rt['kode_rt']])) {
                $a = $alamats[$rt['kode_rt']];
                \App\Models\AlamatRt::firstOrCreate(['rt_id'=>$model->id], array_merge($a, ['is_active'=>true]));
            }
        }
    }
}
