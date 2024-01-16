<?php

namespace App\Http\Controllers\WEB\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport; 

class ExcelImportController extends Controller
{
    private $database;

    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $file = $request->file('excel_file');
        $data = $this->getDataFromExcel($file);

        $formattedData = $this->formatDataForFirebase($data);

        $collectionReference = $this->database->getReference('libreria/libro');

        foreach ($formattedData as $dataItem) {
            $collectionReference->push($dataItem);
        }

        return redirect('/registar-libro-excel');
    }


    private function getDataFromExcel($file)
    {


        $data = Excel::toArray([], $file)[0];
        $data = array_slice($data, 1);
        $formattedData = [];


        foreach ($data as $row) {
            $rowData = [
                'nombre' => $row[0],
                'nombre_editorial' => $row[1],
                'resenia' => $row[2],
                'anio_publicacion' => $row[3],
                'carrera' => $row[4],
                'area' => $row[5],
                'categoria' => $row[6],
                'autor' => $row[7],
                'documento' => $row[8] =1 ? 'true' : 'false' ,
                'fisico' => "true",
                'estado' => "Activo",

            ];

            $formattedData[] = $rowData;
        }

        return $formattedData;
    }


    private function formatDataForFirebase($data)
    {
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'nombre' => $item['nombre'],
                'nombre_editorial' => $item['nombre_editorial'],
                'resenia' => $item['resenia'],
                'anio_publicacion' => $item['anio_publicacion'],
                'carrera' => $item['carrera'],
                'area' => $item['area'],
                'categoria' => $item['categoria'],
                'autor' => $item['autor'],
                'documento' => $item['documento'],
                'fisico' => "true",
                'estado' => "Activo",
            ];
        }

        return $formattedData;
    }

    public function descargarTemplate()
    {
        $templatePath = public_path('templates/template.xlsx'); 

        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'template.xlsx', [], 'inline');
        } else {
            return back()->with('error', 'La plantilla no está disponible para descargar.');
        }
    }
}
