<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\DbDumper\Databases\MySql;

class ExportDbController extends Controller
{
    public function export()
    {
        $databaseConfig = config('database.connections')['mysql'];
        MySql::create()
            ->setDbName($databaseConfig['database'])
            ->setUserName($databaseConfig['username'])
            ->setPassword($databaseConfig['password'])
            ->excludeTables(['password_resets', 'failed_jobs', 'migrations'])
            ->dumpToFile('dump.sql');
        return response()->download('dump.sql');
    }
}
