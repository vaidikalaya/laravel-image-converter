<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ImageConverterController extends Controller
{

    public function index(Request $request){
        
        $manager = new ImageManager(Driver::class);

        /* save images and format in variables */
        $images=$request->images;
        $format=$request->format;

        /* make temporary directory where we storage converted images */
        $tempDir = 'converted_images_' . uniqid();
        Storage::makeDirectory($tempDir);

        /* 
            read all images one by one and convert to selected format and 
            save in temporary directory 
        */
        foreach($images as $image){
            $imageName=pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $readImage = $manager->read($image);
            $convertedImage = $readImage->encodeByExtension($format);
            Storage::put($tempDir.'/'.$imageName.'.'.$format, $convertedImage);
        }

        $zip = new ZipArchive;
        $zipPath = storage_path('app/' . $tempDir . '.zip');

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            
            $files = Storage::files($tempDir);
            foreach ($files as $file) {
                $zip->addFile(storage_path('app/' . $file), basename($file));
            }
            $zip->close();

            // Delete temporary directory
            Storage::deleteDirectory($tempDir);

            //Return the zip file as a download response
            return response()->download($zipPath, 'converted_images.zip')->deleteFileAfterSend(true);
        }
    }

    public function singleFileConvert(Request $request){

        $image=$request->image;
        $format=$request->format;
        $imageName=pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

        $manager = new ImageManager(Driver::class);
        $readFile = $manager->read($image);
        $convertedImage = $readFile->encodeByExtension($format);

        $headers = [
            'Content-Type' => 'image/'.$format,
            'Content-Disposition' => 'attachment; filename='.$imageName.'.'. $format,
        ];

        return response($convertedImage, 200, $headers);

    }
    
}
