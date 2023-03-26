<?php

namespace App\Http\Controllers\Designs;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\IComment;
use App\Repositories\Contracts\IDesign;

class CommentController extends Controller
{
    protected $comments;
    protected $designs;

    public function __construct(IComment $comments, IDesign $designs)
    {
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $designId){
        $this->validate($request, [
            'body' => ['required']
        ]);
        
        $comment = $this->designs->addComment($designId, [
            'body' => $request->body,
            'user_id'=> auth()->id()
        ]);
        return new CommentResource($comment);
        // dd($comment);

    }

    public function update(Request $request,$id){
        $comment = $this->comments->find($id);
        
        $this->authorize('update',$comment);
        //validate
        $this->validate($request, [
            'body'=>['required']
        ]);
        $comment = $this->comments->update($id,[
            'body' => $request->body
        ]);
        return new CommentResource($comment); 
    }

    public function destroy($id){
        $comment = $this->comments->find($id);
        
        $this->authorize('delete',$comment);

        $this->comments->delete($id);

        return response()->json(['message'=>'Deleted!'], 200);
    }
}
