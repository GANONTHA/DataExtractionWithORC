<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $imagePath = storage_path('app/' . $imagePath);


        //output path
        // $outputPath = 'ocr_output.txt';
        $outputPath = storage_path('app/ocr_output.txt');

        //execute the tesseract command
        // Full path to the Tesseract executable

        $imagePath = str_replace('/', '\\', $imagePath);
        $outputPath = str_replace('/', '\\', $outputPath);


        $tesseractPath = '"C:\\Program Files\\Tesseract-OCR\\tesseract.exe"';

        $command = $tesseractPath . " " . escapeshellarg($imagePath) . " \"" . $outputPath . "\"";

        $result = shell_exec($command . " 2>&1");

        //add a delay 
        sleep(15);

        //Log the command and its result
        Log::info('Tesseract command: ' . $command);
        Log::info('Tesseract command result: ' . $result);
        //Print the result of the command
        // return response()->json(['command' => $command, 'result' => $result]);
        //Check if the command was successful
        if ($result === null) {
            return response()->json(['error' => 'Tesseract command failed.'], 500);
        }
        //Log the output path
        Log::info('Output path: ' . $outputPath);

        //Wait for the output file to become readable
        $timeout = 10; //wait for up to 10 seconds
        $start = time();
        while (!is_readable($outputPath) && time() - $start < $timeout) {
            usleep(100000); //wait for 100 milliseconds before checking again
        }
        //Check if the output file exists before trying to read it
        if (!is_readable($outputPath)) {
            return response()->json(['error' => 'OCR output file not found.'], 404);
        }

        //Read the OCR output from the file
        $ocr_output = file_get_contents($outputPath);

        //return the OCR output
        return view('welcome', ['content' => $ocr_output]);
        // return response()->json(['ocr_output' => $ocr_output], 200);
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
