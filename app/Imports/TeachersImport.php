<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class TeachersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => 'guru',
            'password' => Hash::make($row['password'] ?? 'default123'),
            'address' => $row['address'] ?? null,
            'phone' => $row['phone'] ?? null,
        ]);

        return new Teacher([
            'user_id' => $user->id,
            'nip' => $row['nip'],
        ]);
    }
}
