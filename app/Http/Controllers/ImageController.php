<?php

namespace App\Http\Controllers;

use App\Models\Segments\Task;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller {
    //todo make images to have view permissions and use temp links - right now everything is public

    public function uploadOnTask($id, Request $request) {
        $request->validate($this->validations());
        $task = Task::find($id);
        $this->uploadImagesFromRequest($task,$request);
        return back();
    }

    public function update($id,$image_id, Request $request) {
        //todo dont delete image if a test with image is published (and copied)
        //todo check task id is same with image's task   id
        $request->validate([
          'title' => 'required|string'
        ]);
        $image = Image::find($image_id);
        $image->title = $request->title;
        $image->save();
        return back();
    }

    public function remove($id,$image_id, Request $request) {
        //todo dont delete image if a test with image is published (and copied)
        //todo check task id is same with image's task   id
        $image = Image::findOrFail($image_id);
//        Storage::delete($image->path);
        $image->delete();
        return back();
    }

    private function validations(){
        return [
          'images'                => 'array',
          'images.*'              => 'required|file|image',
        ];
    }

    public function uploadImagesFromRequest($entity,$request){
        if($request->hasFile('images')){
            foreach ($request->file('images') as $item) {
                Image::uploadForEntity($entity,$item);
            }
        }
    }

}
