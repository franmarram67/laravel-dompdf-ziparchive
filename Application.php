<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PDFController extends Controller
{
    public function massGenerate(Request $request) 
    {
        // Generate Temp Zip
        $zip_file = tempnam(sys_get_temp_dir(), "PDF");
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $temps = [];
        // Each
        foreach($pdfs as $pdf) {
            $name = "name_of_your_file.pdf";
            $relativePath = "pdf/".$name;
            
            $content = $pdf->download($relativePath)->getOriginalContent();

            //Creamos el archivo temporal
            $temp = tmpfile();
            fwrite($temp, $content);
            $path = stream_get_meta_data($temp)['uri'];
            array_push($temps, $temp);

            //Añadimos el archivo al zip
            $zip->addFile($path, $name);

            $zip->setExternalAttributesName($path,
            \ZipArchive::OPSYS_UNIX,
            fileperms($path) << 16);

        }
        $zip->close();

        //Borramos los archivos temporales
        foreach($temps as $temp) {
            fclose($temp);
        }

        //Descargamos el zip
        return response()->download($zip_file, "boletines_pdfs.zip");
    }
}
