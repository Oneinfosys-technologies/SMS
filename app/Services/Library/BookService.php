<?php

namespace App\Services\Library;

use App\Enums\OptionType;
use App\Http\Resources\OptionResource;
use App\Models\Library\Book;
use App\Models\Library\BookRecord;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookService
{
    public function preRequisite(Request $request)
    {
        // $authors = OptionResource::collection(Option::query()
        //     ->byTeam()
        //     ->whereType(OptionType::BOOK_AUTHOR->value)
        //     ->get());

        // $publishers = OptionResource::collection(Option::query()
        //     ->byTeam()
        //     ->whereType(OptionType::BOOK_PUBLISHER->value)
        //     ->get());

        // $languages = OptionResource::collection(Option::query()
        //     ->byTeam()
        //     ->whereType(OptionType::BOOK_LANGUAGE->value)
        //     ->get());

        // $topics = OptionResource::collection(Option::query()
        //     ->byTeam()
        //     ->whereType(OptionType::BOOK_TOPIC->value)
        //     ->get());

        // return compact('authors', 'publishers', 'languages', 'topics');

        return [];
    }

    public function create(Request $request): Book
    {
        \DB::beginTransaction();

        $book = Book::forceCreate($this->formatParams($request));

        \DB::commit();

        return $book;
    }

    private function formatParams(Request $request, ?Book $book = null): array
    {
        $formatted = [
            'title' => $request->title,
            'author_id' => $request->author_id,
            'publisher_id' => $request->publisher_id,
            'language_id' => $request->language_id,
            'topic_id' => $request->topic_id,
            'sub_title' => $request->sub_title,
            'subject' => $request->subject,
            'year_published' => $request->year_published,
            'volume' => $request->volume,
            'isbn_number' => $request->isbn_number,
            'call_number' => $request->call_number,
            'edition' => $request->edition,
            'type' => $request->type,
            'page' => (int) $request->page,
            'price' => $request->price,
            'summary' => $request->summary,
        ];

        if (! $book) {
            $formatted['team_id'] = auth()->user()?->current_team_id;
        }

        return $formatted;
    }

    public function update(Request $request, Book $book): void
    {
        \DB::beginTransaction();

        $book->forceFill($this->formatParams($request, $book))->save();

        \DB::commit();
    }

    public function deletable(Book $book): void
    {
        $bookRecordExists = BookRecord::query()
            ->whereBookId($book->id)
            ->exists();

        if ($bookRecordExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('library.book_record.book_record'), 'dependency' => trans('library.book.book')])]);
        }
    }
}
