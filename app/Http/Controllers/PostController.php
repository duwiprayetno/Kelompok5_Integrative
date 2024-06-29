<?php

namespace App\Http\Controllers;

use App\Models\laptop;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $posts = laptop::latest()->paginate(5);

        //render view with posts
        return view('posts.index', compact('posts'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $request->validate([
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'merk'     => 'required|min:5',
            'jenis'   => 'required|min:5',
            'model'   => 'required|min:5',
            'spek'   => 'required|min:5'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        laptop::create([
            'image'     => $image->hashName(),
            'merk'     => $request->merk,
            'jenis'   => $request->jenis,
            'model'   => $request->model,
            'spek'     => $request->spek,
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * edit
     *
     * @param  mixed $post
     * @return void
     */
    public function edit(laptop $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, laptop $post)
    {
        //validate form
        $request->validate([
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'merk'     => 'required|min:5',
            'jenis'   => 'required|min:5',
            'model'   => 'required|min:5',
            'spek'   => 'required|min:5'
        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'merk'     => $request->merk,
            'jenis'   => $request->jenis,
            'model'   => $request->model,
            'spek'     => $request->spek,
            ]);

        } else {

            //update post without image
            $post->update([
                'merk'     => $request->merk,
            'jenis'   => $request->jenis,
            'model'   => $request->model,
            'spek'     => $request->spek,
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(laptop $post)
    {
        //delete image
        Storage::delete('public/posts/'. $post->image);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
