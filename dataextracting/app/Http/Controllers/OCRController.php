<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OCRController extends Controller
{
    // process image
    public function processImage(Request $request)
    {
        // validate the image
        $request->validate([
            'image' => 'required|image'
        ]);
        // get imagePath file and store it in the folder uploaded_image
        $imagePath = $request->file('image')->store('uploaded_image');

        //output path
        $outputPath = 'ocr_output.txt';

        //execute the tesseract command
        $command = "tesseract $imagePath $outputPath";
        $result = shell_exec($command);

        //Read the OCR output from the file
        $ocr_output = file_get_contents($outputPath);

        //handle error if the command fails
        if (!$result) {
            // $ocr_output = "Error: Command failed";
            return response()->json(['error' => 'OCR processing failed.'], 500);
        }

        //return the OCR output
        return response()->json(['ocr_output' => $ocr_output], 200);
    }

    //process pdf
    public function processPdf(Request $request)
    {
        //validate the pdf
        $request->validate([
            'pdf' => 'required|mimes:pdf'
        ]);

        //get pdfPath file and store it in the folder uploaded_pdf
        $pdfPath = $request->file('pdf')->store('uploaded_pdf');

        //output path
        $outputPath = 'ocr_output.txt';

        //execute the tesseract command
        $command = "tesseract $pdfPath $outputPath";
        $result = shell_exec($command);

        //Read the OCR output from the file
        $ocr_output = file_get_contents($outputPath);

        //handle error if the command fails
        if (!$result) {
            // $ocr_output = "Error: Command failed";
            return response()->json(['error' => 'OCR processing failed.'], 500);
        }

        //return the OCR output
        return response()->json(['ocr_output' => $ocr_output], 200);
    }
}
