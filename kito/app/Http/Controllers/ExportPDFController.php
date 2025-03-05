<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ExportPDFController extends Controller
{
    //
    // public function exportPDF(){
    //     $data = Presence::get()->toArray();
    //     return Excel::create('$path_name', function($excel) use ($data){
    //         $excel->sheet('mySheet', function($sheet) use ($data){
    //             $sheet->fromArray($data);
    //         });
    //     })->download('pdf');
    // }
}
