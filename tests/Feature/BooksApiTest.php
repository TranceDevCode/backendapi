<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */

    function can_get_all_books()
    {
        $books = Book::factory(4)->create();

        //dd(route('books.index'));

        $response = $this->getJson(route('books.index'));

        //Verificamos que vemos un fragmento en json

        $response->assertJsonFragment([
            'title' => $books[0]->title,
        ])->assertJsonFragment([
            'title' => $books[1]->title,
        ]);
    }

    /** @test */

    function can_get_one_book()
    {
        $book = Book::factory()->create();

        //a la ruta le pasamos el libro, al igual que route-binding
        $response = $this->getJson(route('books.show', $book));

        $response->assertJsonFragment([
            'title'  => $book->title
        ]);
    }

    /** @test */

    function can_create_books()
    {
        //validamos
        //Importante pasar postJson para que la respuesta del error la resivamos en Json tambien
        $this->postJson(route('books.store'), [])
            ->assertJsonValidationErrorFor('title');

        //primero pasamos la ruta y luego los datos que le vamos a enviar en el array
        $this->postJson(route('books.store'), [
            'title' => 'Trance Book'
        ])->assertJsonFragment([
            'title' => 'Trance Book'
        ]);

        //Verificamos en la base de datos

        $this->assertDatabaseHas('books', [
            'title' => 'Trance Book'
        ]);
    }

    /** @test */

    function can_update_books()
    {
        $book = Book::factory()->create();

        //validamos
        //Importante pasar postJson para que la respuesta del error la resivamos en Json tambien
        $this->patchJson(route('books.update', $book), [])
            ->assertJsonValidationErrorFor('title');

        $this->patchJson(route('books.update', $book), [
            'title' => 'Edited Book'
        ])->assertJsonFragment([
            'title' => 'Edited Book'
        ]);

        //verificamos que existe un libro con este title
        //Cuando pasamos assertDatabaseHas y books, hacemos referencia a la tabla books
        $this->assertDatabaseHas('books', [
            'title' => 'Edited Book'
        ]);
    }

    /** @test */

    function can_delete_books()
    {
        $book = Book::factory()->create();

        $this->deleteJson(route('books.destroy', $book))
        ->assertNoContent();

        //revisamos que la tabla books tenga cero registros
        $this->assertDatabaseCount('books', 0);
    }
}
