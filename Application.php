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
        // Each pdf must be a DOMPDF (\PDF) object
        foreach($pdfs as $pdf) {
            $name = "name_of_your_file.pdf";
            $relativePath = "pdf/".$name;
            
            $content = $pdf->download($relativePath)->getOriginalContent();

            // Create Temp File for the PDF
            $temp = tmpfile();
            fwrite($temp, $content);
            $path = stream_get_meta_data($temp)['uri'];
            array_push($temps, $temp);

            // Add the PDF file to the Zip
            $zip->addFile($path, $name);

        }
        // Close the Zip Archive
        $zip->close();

        // Delete Temp Files
        foreach($temps as $temp) {
            fclose($temp);
        }

        // Download Zip
        return response()->download($zip_file, "boletines_pdfs.zip");
    }
}
