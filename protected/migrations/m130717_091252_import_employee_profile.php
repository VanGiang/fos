<?php

class m130717_091252_import_employee_profile extends CDbMigration
{

    public function safeUp()
    {
        $handle = fopen(Yii::app()->basePath . '/migrations/employee_profile.csv', 'r');
        if (false !== $handle) {
            fgetcsv($handle, 1000); // Skip header row
            while (false !== ($row = fgetcsv($handle, 1000))) {
                $this->insert('profiles', array(
                    'email' => $row[1],
                    'name' => $row[2],
                    'phone' => $row[3] === 'NULL' ? null : $row[3],
                    'address' => $row[4] === 'NULL' ? null : $row[4],
                    'employee_code' => $row[5] === 'NULL' ? null : $row[5],
                    'secret_key' => $row[6] === 'NULL' ? null : $row[6],
                    'position' => $row[7],
                    'date_of_birth' => $row[8] === 'NULL' ? null : $row[8],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
            }
            fclose($handle);
        }
    }

    public function safeDown()
    {
        try {
            $handle = fopen(Yii::app()->basePath . '/migrations/employee_profile.csv', 'r');
            if (false !== $handle) {
                fgetcsv($handle, 1000); // Skip header row
                while (false !== ($row = fgetcsv($handle, 1000))) {
                    $this->delete('profiles', "employee_code = \"{$row[5]}\"");
                }
            } else {
                echo "Error in open file.\n";
                return false;
            }
        } catch (Exception $e) {
            echo "Exception:{$e->getMessage()}\n";
            return false;
        }
    }

}
