<?php

namespace App\Imports;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Str;


class CsvData implements ToModel
// class CsvData implements ToModel, WithStartRow, WithCustomCsvSettings
{
    /* public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => '|'
        ];
    } */

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $firstname="";
        $lastname="";
        if(isset($row[1])){
            $names = explode(' ', $row[1]);
            $firstname = isset($names[0]) ? $names[0] : '';
            $lastname  = isset($names[1]) ? $names[1] : '';
        }

        $strs = "";
        $users = User::where('email', $row[2])->where('phone', $row[3])->first();

        if(!$users){
            $user = User::where('reg_id', $row[0])->orderBy('reg_id', 'desc')->first();
            if($user && (int)$user->reg_id == $row[0]){
                $reg_id = $row[0] + 2; // check the last reg_id and increment when adding data to database
            }else{
                $reg_id = $row[0];
            }

            // return new User([
            User::insertOrIgnore([
                'uuid'          => Str::uuid(),
                'reg_id'        => sprintf("%05s", $reg_id),
                'firstname'     => $firstname,
                'lastname'      => $lastname,
                'email'         => isset($row[2]) ? $row[2] : '',
                'password'      => \Hash::make(isset($row[2]) ? $row[2] : '123456'),
                'phone'         => isset($row[3]) ? $row[3] : '',
                'address'       => isset($row[4]) ? $row[4] : '',
            ]);
        }
    }
}
