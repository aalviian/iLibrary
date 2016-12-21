<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use App\Exceptions\BookException;
use App\Book;
use App\BorrowLog;
use Input;
use Session;
use Validator; 
use File;
 
class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request -> ajax()) {
            $books = Book::with('author');
            return Datatables::of($books) -> addColumn('action2', function($book){
                return view('datatable.action', [
                    'model'    => $book, 
                    'edit_url' => route('admin.books.edit', $book->id),    
                    'delete_url' => route('admin.books.destroy', $book->id),
                    'confirm_message' => 'Yakin mau menghapus ' . $book->title . '?',
                ]);
            }) -> make(true);
        }

        $html = $htmlBuilder -> addColumn(['data' => 'title', 'name'=>'title', 'title' => 'Judul'])
            -> addColumn(['data' => 'amount', 'name'=>'amount','title'=>'Jumlah'])
            -> addColumn(['data'=>'author.name', 'name'=>'author.name', 'title'=>'Penulis'])
            -> addColumn(['data' => 'action2', 'name'=>'action2','title'=>'','orderable'=>false,'searchable'=>false]);

        return view('books.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $request)
    {
        /*
        $rules = [
            'title' => 'required|unique:books,title',
            'author_id' => 'required|exists:authors,id',
            'amount' => 'required|numeric',
            'cover' => 'required|image'
        ];

        $messages = [
            'required' => 'Field harus di isi alias tidak boleh kosong',
            'unique' => 'Nama '.$request -> title.' sudah ada dalam database.',
            'exists' => 'Author tidak ada dalam database',
            'numeric' => 'Isi field amount harus angka',
            'image' => 'Isi field cover harus image',
            'max' => 'Image tidak boleh lebih dari 2 mb'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect() -> route('admin.books.create')
                            ->withErrors($validator)
                            ->withInput();
        }
        */

        $book = Book::create($request->except('cover'));

        //isi field cover jika ada cover yang diupload

        if ($request->hasFile('cover')) {
            // Mengambil file yang diupload
            $uploaded_cover = $request->file('cover');
            // mengambil extension file
            $extension = $uploaded_cover->getClientOriginalExtension();
            // membuat nama file random berikut extension
            $filename = md5(time()) . '.' . $extension;
            // menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_cover->move($destinationPath, $filename);
            // mengisi field cover di book dengan filename yang baru dibuat
            $book->cover = $filename;
            $book->save();
        }

        Session::flash('flash_notif', [
            "level" => "success",
            "message" => "Berhasil menyimpan $book->title kedalam database"
        ]);

        return redirect() -> route('admin.books.index');
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
        $books = Book::find($id);
        return view('books.edit')-> with(compact('books'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, $id)
    {
        /*
        $rules = [
            'title' => 'required|unique:books,title,'.$id,
            'author_id' => 'required|exists:authors,id',
            'amount' => 'required|numeric',
            'cover' => 'required'
        ];

        $messages = [
            'required' => 'Field harus di isi alias tidak boleh kosong',
            'unique' => 'Nama '.$request -> title.' sudah ada dalam database.',
            'exists' => 'Author tidak ada dalam database',
            'numeric' => 'Isi field amount harus angka',
            'image' => 'Isi field cover harus image'
        ];


        $validator = Validator::make($request -> all(), $rules, $messages);

        if($validator -> fails()) {
            return back()
                        -> withErrors($validator)
                        -> withInput();
        }
        */

        $book = Book::find($id);
        $oldbook = $book -> title;
        if(!$book->update($request->all())) return redirect()->back();

        if($request -> hasFile('cover')) {
            //mengambil cover yang diupload berikut ekstensinya
            $filename = null;
            $uploaded_cover = $request -> file('cover');
            $extension = $uploaded_cover -> getClientOriginalExtension();

            //membuat nama file random dengan extension
            $filename = md5(time()). '.' .$extension;
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';

            //memindahkan file ke folder public/img
            $uploaded_cover->move($destinationPath, $filename);

            // hapus cover lama, jika ada
            if ($book->cover) {
                $old_cover = $book->cover;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
                    . DIRECTORY_SEPARATOR . $book->cover;
                try {
                    File::delete($filepath);
                } 
                catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                }
            }

            //ganti field cover dengan cover baru
            $book->cover = $filename;
            $book -> save();
        }
        
        Session::flash('flash_notif', [
            "level" => "success",
            "message" => "Berhasil mengubah ".$oldbook." menjadi ".$request -> title
        ]);

        return redirect() -> route('admin.books.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        $oldbook = $book -> title;
        $cover = $book->cover;
        if(!$book->delete()) return redirect()->back();
        
        if($cover) {
            $oldcover = $book -> cover;
            $filepath = public_path() . DIRECTORY_SEPARATOR. 'img' . DIRECTORY_SEPARATOR . $book->cover;
            try{
                File::delete($filepath);
            }
            catch(FileNotFoundException $e) {
                //file sudah tidak ad
            }
        }

        Session::flash('flash_notif', [
            "level" => "success",
            "message" => "Berhasil menghapus ".$oldbook
        ]);

        return redirect() -> route('admin.books.index');
    }

}
