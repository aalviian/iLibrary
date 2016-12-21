<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\Book;
use App\BorrowLog;
use App\Exceptions\BookException;
use Mail;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use EntrustUserTrait;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function getAuthIdentifier() {
        return $this->getKey();
    }

    public function sendVerification() {
        $user = $this;
        $token = str_random(40);
        $user->verification_token = $token;
        $user->save();

        Mail::send('auth.emails.verification', compact('user', 'token'), function($mail) use ($user) {
            $mail -> to($user->email, $user->name) -> subject('Verifikasi Akun iLibrary');
        });
    }

    public function borrowLogs() {
        return $this->hasMany('App\borrowLog');
    }

    public function borrow(Book $book) {
        //cek apakah masih ada stok buku
        if($book->getStokAttribute() < 1) {
            throw new BookException("Buku $book->title sedang tidak tersedia");
        }
        
        //cek apakah buku ini sedang dipinjam oleh user yg bersangkutan
        if($this->borrowLogs()->where('book_id', $book->id) -> where('is_returned', 0) -> count() > 0) {
            throw new BookException("Kamu masih meminjam Buku $book->title .");
        }
        $borrowLog = BorrowLog::create(['user_id'=>$this->id, 'book_id'=>$book->id]);
        return $borrowLog;
    }

}
