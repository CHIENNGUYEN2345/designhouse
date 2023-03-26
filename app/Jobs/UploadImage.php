<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Image;
use File;
class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $design;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $filename = $this->design->image;
        $original_file = storage_path() . '/uploads/original/' . $filename;

        try{
            //create the Large Image and save to tmp disk
            Image::make($original_file)->fit(800, 600, function($constraint){
                $constraint->aspectRatio();
            })
            ->save($large = storage_path('uploads/large/'.$filename));

            //create the thumbnail.
            Image::make($original_file)->fit(250, 200, function($constraint){
                $constraint->aspectRatio();
            })
            ->save($thumbnail = storage_path('uploads/thumbnail/'.$filename));

            //store image to pernament disk
            //original image
            if(Storage::disk($disk)->put('uploads/designs/original/'.$filename, fopen($original_file, 'r+'))){
                File::delete($original_file);
            };

            //large image
            if(Storage::disk($disk)->put('uploads/designs/large/'.$filename, fopen($large, 'r+'))){
                File::delete($large);
            };

            //thumbnail image
            if(Storage::disk($disk)->put('uploads/designs/thumbnail/'.$filename, fopen($thumbnail, 'r+'))){
                File::delete($thumbnail);
            };

            //update the database record with successflag
            $this->design->update([
                'upload_successful' => true,
            ]);

        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
    }
}
