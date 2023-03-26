<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Http\Request;

class DesignRepository extends BaseRepository implements IDesign
{
    // public function all()
    // {
    //     return Design::all();
    // }
    public function model(){
        return Design::class; //App\Models\Designs
    }

    // public function allLive(){
    //     return $this->model->where('is_live', true)->get();
    // }

    public function applyTags($id, array $data){
        $design = $this->find($id);
        $design->retag($data);
    }

    public function addComment($designId, array $data)
    {
        //get the design for which we want to create a comment
        $design = $this->find($designId);
        //create the cmt for the design
        $comment = $design->comments()->create($data);

        return $comment;
    }

    public function like($id){
        $design = $this->model->findOrFail($id);
        if($design->isLikedByUser(auth()->id())){
            $design->unlike();
        }else{
            $design->like();
        }
    }

    public function isLikedByUser($id){
        $design = $this->model->findOrFail($id);

        return $design->isLikedByUser(auth()->id());
    }

    public function search(Request $request){
        $query = (new $this->model)->newQuery();

        $query->where('is_live',true);

        //return only design with comments
        if($request->has_comments){
            $query->has('comments');
        }

        //return only design assigned to teams
        if($request->has_teams){
            $query->has('team');
        }

        //http://mysite.com?q=hello-world&has_comment=1&has_teams=abc
        //search title and desc for provided string
        if($request->q){
            $query->where(function($q) use ($request){
                $q->where('title','like','%'.$request->q.'%')
                    ->orWhere('description','like','%'.$request->q.'%');
            });
        }

        //order the query by likes or latest first
        if($request->orderBy == 'likes'){
            $query->withCount('likes')
                    ->orderByDesc('likes_count');
        }else{
            $query->latest();
        }

        return $query->get();
    }
}