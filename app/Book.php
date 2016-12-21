<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Author;
use Session;

class Book extends Model
{
    public static function boot() {
    	parent::boot();

    	//mengecek perubahan amount buku tidak boleh kurang dari jumlah buku yang dipinjam
    	self::updating(function($book) {
	    	if($book->amount < $book->borrowed) {
	    		Session::flash("flash_notif", [
	    			"level" => "danger",
	    			"message" => "Jumlah buku $book->title harus >= $book->borrowed"
	    		]);
	    		return false;
	    	}
	    });

	    self::deleting(function($book) {
	    	if($book->borrowLogs() -> borrowed()) {
				Session::flash("flash_notif", [
					"level"=>"danger",
					"message"=>"Buku $book->title sedang dipinjam."
				]);
				return false;
	    	}	
	    });
    }

    protected $fillable = ['title', 'author_id', 'amount'];

    public function getBorrowedAttribute() {
    	return $this -> borrowLogs() -> borrowed()->count();
    }

    public function author() {
    	return $this -> belongsTo('App\Author'); 
    } 
 
	public function borrowLogs() {
		return $this->hasMany('App\BorrowLog');
	}
 
    public function getStokAttribute() {
		$borrowed = $this->borrowLogs()->borrowed()->count();
		$stock = $this->amount - $borrowed;
		return $stock;
    }

}
