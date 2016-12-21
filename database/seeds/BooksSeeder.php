<?php

use Illuminate\Database\Seeder;
use App\Author;
use App\Book;
use App\BorrowLog;
use Faker\Provider\Base;


class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $faker = Faker\Factory::create('id_ID');

        for($i = 0; $i < 10; $i++) {
            $author=Author::create([
                'name' => $faker->name
            ]);

            $book = Book::create([
                'title' => $faker->realText($maxNbChars = 25, $indexSize = 2),
                'author_id' => $author->id,
                'amount' => 5
            ]);
        }

        //sample peminjaman buku
        BorrowLog::create(['user_id'=>1, 'book_id'=>1, 'is_returned'=>0]);
        BorrowLog::create(['user_id'=>3, 'book_id'=>3, 'is_returned'=>0]);
        BorrowLog::create(['user_id'=>5, 'book_id'=>5, 'is_returned'=>0]);
          //   	//sample penulis
          //      $author1 = Author::create(['name' => 'Alvian']);
          //      $author2 = Author::create(['name'=>'Salim']);
        	 //   $author3 = Author::create(['name'=>'Aamir']);

        	 //   // Sample buku
        		// $book1 = Book::create(['title'=>'Kupinang Engkau dengan Hamdalah',
        		// 'amount'=>3, 'author_id'=>$author1->id]);
        		// $book2 = Book::create(['title'=>'Jalan Cinta Para Pejuang',
        		// 'amount'=>2, 'author_id'=>$author2->id]);
        		// $book3 = Book::create(['title'=>'Membingkai Surga dalam Rumah Tangga',
        		// 'amount'=>4, 'author_id'=>$author3->id]);
        		// $book4 = Book::create(['title'=>'Cinta & Seks Rumah Tangga Muslim',
        		// 'amount'=>3, 'author_id'=>$author3->id]);
    }
}
