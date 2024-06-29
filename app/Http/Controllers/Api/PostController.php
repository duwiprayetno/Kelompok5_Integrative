<?php

namespace App\Http\Controllers\Api;

//import model Post

use App\Http\Controllers\Controller;

//import resource PostResource
use App\Http\Resources\PostResource;
use App\Models\laptop;
//import Http request
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
//import facade Validator
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
{
    // Mengambil data dari model 'laptop'
    $posts = laptop::latest()->paginate(5);

    // Mengembalikan data sebagai sumber daya (resource)
    return new PostResource(true, 'List Data Posts', $posts);
}

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image' =>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'merk'     => 'required',
            'jenis'   => 'required',
            'model'   => 'required',
            'spek'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = laptop::create([
            'image'     => $image->hashName(),
            'merk'     => $request->merk,
            'jenis'   => $request->jenis,
            'model'   => $request->model,
            'spek'   => $request->spek,
        ]);

        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

     /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //find post by ID
        $post = laptop::find($id);

        //return single post as a resource
        return new PostResource(true, 'Detail Data Post!', $post);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image' =>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'merk'     => 'required',
            'jenis'   => 'required',
            'model'   => 'required',
            'spek'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $post = laptop::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/' . basename($post->image));

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'merk'     => $request->merk,
                'jenis'   => $request->jenis,
                'model'   => $request->model,
                'spek'   => $request->spek,
            ]);
        } else {

            //update post without image
            $post->update([
                'merk'     => $request->merk,
                'jenis'   => $request->jenis,
                'model'   => $request->model,
                'spek'   => $request->spek,
            ]);
        }

        //return response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {

        //find post by ID
        $post = laptop::find($id);

        //delete image
        Storage::delete('public/posts/'.basename($post->image));

        //delete post
        $post->delete();

        //return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}