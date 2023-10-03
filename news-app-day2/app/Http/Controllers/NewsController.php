<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use App\Models\News;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NewsCollection;
use App\Models\Image;
use Illuminate\Support\Facades\Validator;
use Exception;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = News::all();

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => new NewsCollection($articles),
        ],200);
    }

    public function public_index()
    {
        $articles = News::where('visible', true)->get();

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => new NewsCollection($articles),
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|min:3|unique:news,title",
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }
        
        $news_id = NULL;
        try {
            
            $image_path = $request->file('thumbnail')->store('thumbnails','public');
            $article = News::create([
                "title" => $request->title,
                "body" => $request->body,
                "thumbnail" => $image_path,
                "completed" => $request->completed,
                "visible" => $request->visible,
            ]);
            $news_id = $article->id;

            if($request->categories) {
                $categories = $request->categories;
                $article->categories()->attach($categories);
            }

            if($request->images) {
                $images = $request->images;
                Image::whereIn('id', $images)->update(['news_id' => $news_id]);
                Image::where('news_id', $news_id)->whereNotIn('id', $images)->delete();
            }
            
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! news article created.", 
                "data" => new NewsResource($article),
            ], 201);
        }
        catch(Exception $exception) {

            if($news_id) {  
                $article = News::find($news_id);            
                $article->categories()->detach();
                Image::where('news_id', $id)->delete();
                $article->delete();
            }
                        
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "message" => $exception->getMessage(),
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = News::find($id);

        if($article) {
            return response()->json([
                "status" => "success",
                "error" => false, 
                "data" => new NewsResource($article),
            ], 200);
        }
        
        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed! no news article found."
        ], 404);
    }

    public function public_show($id)
    {
        $article = News::where('visible', true)->find($id);

        if($article) {
            return response()->json([
                "status" => "success",
                "error" => false, 
                "data" => new NewsResource($article),
            ], 200);
        }
        
        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed! no news article found."
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $article = News::find($id);

        if($article) {

            $validator = Validator::make($request->all(), [
                "title" => "required|min:3",
            ]);

            if($validator->fails()) {
                return response()->json([
                    "status" => "fail", 
                    "error" => true, 
                    "validation_errors" => $validator->errors()
                ]);
            }

            $article['title'] = $request->title;

            // if has body
            if($request->body) {
                $article['body'] = $request->body;
            }

            // if has thumbnail image
            if($request->file('thumbnail')) {
                $image_path = $request->file('thumbnail')->store('thumbnails', 'public');
                $article['thumbnail'] = $image_path;
            }

            // if has visible
            if($request->visible) {
                $article['visible'] = $request->visible;
            }

            // if has completed
            if($request->completed) {
                $article['completed'] = $request->completed;
            }

            // if has categories
            if($request->categories) {
                $categories = $request->categories;
                $article->categories()->sync($categories);
            }

            // if has album images
            if($request->images) { 
                $images = $request->images;
                Image::whereIn('id', $images)->update(['news_id' => $id]);
                Image::where('news_id', $id)->whereNotIn('id', $images)->delete();
            }

            $article->save();

            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! news article updated.", 
                "data" => new NewsResource($article)
            ], 201);
        }

        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed no news article found."
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = News::find($id);

        if($article) {
            $article->categories()->detach();
            Image::where('news_id', $id)->delete();
            $article->delete();
            
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! news article deleted."
            ], 200);
        }

        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed no news article found."
        ], 404);
    }
}
