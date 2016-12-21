<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Author;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Input;
use Session;
use Validator;   

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */ 
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request -> ajax()) {
            $authors = Author::select(['id','name']);
            return Datatables::of($authors) -> addColumn('action', function($author){
                return view('datatable.action', [
                    'model'    => $author,  
                    'delete_url' => route('admin.authors.destroy', $author->id),
                    'edit_url' => route('admin.authors.edit', $author->id),   
                    'confirm_message' => 'Yakin mau menghapus ' . $author->name . '?',
                ]);
            }) -> make(true);
        }
        $html = $htmlBuilder
                ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Nama'])
                -> addColumn(['data' => 'action', 'name'=>'action','title'=>'','orderable'=>false,'searchable'=>false]);

        return view('authors.index') -> with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:authors',
        ];

        $messages = [
            'required' => 'Field harus di isi alias tidak boleh kosong',
            'unique' => 'Nama '.$request -> name.' sudah ada dalam database.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect() -> route('admin.authors.create')
                            ->withErrors($validator)
                            ->withInput();
        }

        $author = Author::create($request->all());
        Session::flash('flash_notif', ["level"=>"success", "message"=>"Berhasil menambahkan ".$author -> name." kedalam database"]);
        return redirect() -> route('admin.authors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $author = Author::find($id);
        return view('authors.edit')-> with(compact('author')); 
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

        $rules = [
            'name' => 'required|unique:authors,name,'.$id,
        ];

        $messages = [
            'required' => 'Field harus di isi alias tidak boleh kosong',
            'unique' => 'Nama '.$request -> name.' sudah ada dalam database.',
        ];

        $author = Author::find($id);

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back() ->withErrors($validator)->withInput();
        }

        $nama = $author -> name;
        $author->update($request->only('name'));
        Session::flash("flash_notif",[
            "level" => "success",
            "message" => "Berhasil menyimpan ".$nama." menjadi ".$author->name." kedalam database"
        ]);
        return redirect()->route('admin.authors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   $author = Author::find($id);
        $nama = $author -> name;
        
        if(!Author::destroy($id)) {
            return redirect() -> back();
        }

        Session::flash("flash_notif", [
            "level" => "success",
            "message" => "Penulis ".$nama." berhasil dihapus"
        ]);
        return redirect()->route('admin.authors.index');

    }
}
