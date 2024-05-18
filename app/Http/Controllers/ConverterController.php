<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ConverterController extends Controller
{

    public $formates=[
        'png'=>'png',
        'jpeg'=>'jpeg',
        'jpg'=>'jpg',
        'gif'=>'gif'
    ];

    public function index(Request $request){

        set_time_limit(0);

        $format=$request->convert_to;
        $files=$request->files->get('files');

        if(count($files)>1){
            return $this->multiFile($files,$format);
        }
        else{
            return $this->singleFile($files[0],$format);
        }

    }

    public function singleFile($file,$format){

        $manager = new ImageManager(Driver::class);
        $fileOrignalName=$this->fileName($file);
        $format=$this->formates[$format];
        
        $readFile = $manager->read($file);
        $convertedFile = $readFile->encodeByExtension($format);

        $headers = [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename='.$fileOrignalName.'.'. $format,
        ];

        return response($convertedFile, 200, $headers);
    }

    public function multiFile($files,$format){

        $manager = new ImageManager(Driver::class);
        $format=$this->formates[$format];
        $tempDir = 'converted_images_' . uniqid();
        Storage::makeDirectory($tempDir);
        
        foreach($files as $file){
            $fileOrignalName=$this->fileName($file);
            $readFile = $manager->read($file);
            $convertedFile = $readFile->encodeByExtension($format);
            Storage::put($tempDir.'/'.$fileOrignalName.'.'.$format, $convertedFile);
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

    public function fileName($file){
        return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }
}
