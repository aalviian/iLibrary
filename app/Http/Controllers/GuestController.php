<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\BookException;
use App\Http\Requests;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use App\Book;
use App\Author;
use App\BorrowLog;
use Entrust;
use Auth;
use Session;

class GuestController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder) {
    	if($request -> ajax()) {
	    	$books = Book::with('author');
            $status = BorrowLog::find(Auth::user()->id)->is_returned;
	    	return Datatables::of($books) 
                -> addColumn('stock', function($book){
                    return $book->stok;
                }) 
                -> addColumn('status', function($status){
                    return $status->is_returned;
                }) 
	    		-> addColumn('action', function($book){
	    		     if (Entrust::hasRole('admin')) return '';
	    		     return '<a class="btn btn-xs btn-primary" href="'.route('guest.books.borrow', $book -> id).'">Pinjam</a>';	
	    	    })-> make(true);
    	}	     
    	$html = $htmlBuilder 
    			-> addColumn(['data'=>'title','name'=>'title', 'title'=>'Judul'])
                -> addColumn(['data'=>'stock','name'=>'stock','title'=>'Stok','orderable'=>false,'searchable'=>false])
                -> addColumn(['data'=>'status','name'=>'status','title'=>'Status','orderable'=>false,'searchable'=>false])
    			-> addColumn(['data'=>'author.name','name'=>'author.name','title'=>'Penulis'])
    			-> addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

    	return view('guest.index')->with(compact('html'));		
    }

    public function borrow($id) {
        try {
            $book = Book::findorFail($id);
            Auth::user()->borrow($book);
            Session::flash("flash_notif",[
                "level" => "success",
                "message" => "Berhasil meminjam $book->title. Stok awal $book->amount menjadi $book->stok"
            ]);
        }
        catch (BookException $e) {
            Session::flash("flash_notif", [
                "level" => "danger",
                "message" => $e->getMessage()
            ]);
        }
        catch (ModelNotFoundException $e) {
            Session::flash("flash_notif", [
                "level" => "danger",
                "message" => "Buku tidak ditemukan"
            ]);
        }
        return redirect('/home');
    }

    public function returned($book_id) {
        $borrowLog = BorrowLog::where('user_id', Auth::user()->id)
            ->where('book_id', $book_id)
            ->where('is_returned', 0)
            ->first();

        if($borrowLog) {
            $borrowLog->is_returned = true;
            $borrowLog->save();

            Session::flash("flash_notif", [
                "level"=>"success",
                "message" => "Berhasil mengembalikan buku ". $borrowLog->book->title

            ]);
            return redirect('/');
        }
        else {
            Session::flash("flash_notif", [
                "level"=>"danger",
                "message" => "Gagal Mengembalikan Buku"
            ]);

            return redirect('/');
        }
    }
}
