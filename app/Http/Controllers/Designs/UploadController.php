<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;

class UploadController extends Controller
{
    public function upload(Request $request){
        //validate the req
        $this->validate($request, [
            'image' => ['required','mimes:jpeg,gif,bmp,png,jpg','max:2048'],
        ]);
        //get the image
        $image = $request->file('image');
        $image_path = $image->getPathname();
        //get the original file name and replace any spaces with underscore
        //business cards.png -> timestamp()_business_cards.png
        $filename = time()."_".preg_replace('/\$+/', '_', strtolower($image->getClientOriginalName()));
        //move the image to the temporary location (tmp)
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp' );
        //create the database record for the design
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk'),

        ]);

        
        //dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200); 

    }
}
