<?php

namespace Database\Seeders;

use App\Models\TUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TUserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        TUser::each(function (TUser $user) use ($now) {
            $jenisUser = $user->JENIS_USER;

            $roles = [];

            if ($user->STATUS_PEGAWAI === 'Dosen') {
                $dosenRoles = ['Pembimbing', 'Penguji', 'Monev'];
                if ($jenisUser === 'KPPS') {
                    $dosenRoles[] = 'KPPS';
                }
                foreach ($dosenRoles as $i => $role) {
                    $roles[] = [
                        'ID_USER' => $user->id,
                        'ROLE' => $role,
                        'STATUS_DEFAULT' => $role === $jenisUser ? 't' : 'f',
                        'TGL_CREATE' => $now,
                        'TGL_UPDATE' => $now,
                    ];
                }
            } else {
                $roles[] = [
                    'ID_USER' => $user->id,
                    'ROLE' => $jenisUser,
                    'STATUS_DEFAULT' => 't',
                    'TGL_CREATE' => $now,
                    'TGL_UPDATE' => $now,
                ];
            }

            DB::table('t_user_role')->insert($roles);
        });
    }
}
