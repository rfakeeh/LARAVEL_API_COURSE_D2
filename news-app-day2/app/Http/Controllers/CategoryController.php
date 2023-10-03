<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Illuminate\Support\Facades\Validator;
use Exception;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => new CategoryCollection($categories),
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
            "name" => "required|min:3|unique:categories,name",
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }

        try {
            $category = Category::create([
                "name" => $request->name,
                "description" => $request->description
            ]);
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! category created.", 
                "data" => new CategoryResource($category),
            ], 201);
        }
        catch(Exception $exception) {
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
        $category = Category::find($id);
        
        if($category) {
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "data" => new CategoryResource($category)
            ], 200);
        }
        return response()->json([
            "status" => "fail", 
            "error" => true, 
            "message" => "Failed! no category found."
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
        $category = Category::find($id);

        if($category) {

            $validator = Validator::make($request->all(), [
                "name" => "required|min:3",
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    "status" => "fail", 
                    "error" => true, 
                    "validation_errors" => $validator->errors()
                ]);
            }

            $category['name'] = $request->name;

            // if it has description
            if($request->description) {
                $category['description'] = $request->description;
            }

            $category->save();
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! category updated.", 
                "data" => new CategoryResource($category)
            ], 200);
        }
        return response()->json([
            "status" => "fail", 
            "error" => true, 
            "message" => "Failed no category found."
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
        $category = Category::find($id);

        if($category) {
            $category->delete();
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! category deleted."
            ], 200);
        }
        return response()->json([
            "status" => "fail", 
            "error" => true, 
            "message" => "Failed no category found."
        ], 404);     
    }

    /***
     * New function
     */
    public function open_document()
    {
        $category = Category::latest()->first();

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => new CategoryResource($category)
        ], 200);

    }
}
