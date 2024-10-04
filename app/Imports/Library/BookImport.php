<?php

namespace App\Imports\Library;

use App\Concerns\ItemImport;
use App\Enums\OptionType;
use App\Models\Library\Book;
use App\Models\Library\BookAddition;
use App\Models\Library\BookCopy;
use App\Models\Option;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BookImport implements ToCollection, WithHeadingRow
{
    use ItemImport;

    protected $limit = 1000;

    public function collection(Collection $rows)
    {
        if (count($rows) > $this->limit) {
            throw ValidationException::withMessages(['message' => trans('general.errors.max_import_limit_crossed', ['attribute' => $this->limit])]);
        }

        $logFile = $this->getLogFile('book');

        $errors = $this->validate($rows);

        $this->checkForErrors('book', $errors);

        if (! request()->boolean('validate') && ! \Storage::disk('local')->exists($logFile)) {
            $this->import($rows);
        }
    }

    private function import(Collection $rows)
    {
        $authors = Option::query()
            ->byTeam()
            ->whereType(OptionType::BOOK_AUTHOR->value)
            ->get();

        $publishers = Option::query()
            ->byTeam()
            ->whereType(OptionType::BOOK_PUBLISHER->value)
            ->get();

        $languages = Option::query()
            ->byTeam()
            ->whereType(OptionType::BOOK_LANGUAGE->value)
            ->get();

        $topics = Option::query()
            ->byTeam()
            ->whereType(OptionType::BOOK_TOPIC->value)
            ->get();

        $conditions = Option::query()
            ->byTeam()
            ->whereType(OptionType::BOOK_CONDITION->value)
            ->get();

        activity()->disableLogging();

        // $rows = $rows->unique('number')->values();

        $rows = $rows->groupBy('title');

        foreach ($rows as $title => $row) {
            $book = Book::firstOrCreate([
                'team_id' => auth()->user()?->current_team_id,
                'title' => Str::title($title),
            ]);

            $firstRow = $row->first();

            $authorName = Str::title(Arr::get($firstRow, 'author'));
            $publisherName = Str::title(Arr::get($firstRow, 'publisher'));
            $languageName = Str::title(Arr::get($firstRow, 'language'));
            $topicName = Str::title(Arr::get($firstRow, 'topic'));

            if ($authorName) {
                $author = Option::query()
                    ->byTeam()
                    ->whereType(OptionType::BOOK_AUTHOR->value)
                    ->whereName($authorName)
                    ->first();
                $book->author_id = $author?->id ?? Option::forceCreate([
                    'team_id' => auth()->user()?->current_team_id,
                    'type' => OptionType::BOOK_AUTHOR->value,
                    'name' => $authorName,
                ])->id;
            }

            if ($publisherName) {
                $publisher = Option::query()
                    ->byTeam()
                    ->whereType(OptionType::BOOK_PUBLISHER->value)
                    ->whereName($publisherName)
                    ->first();
                $book->publisher_id = $publisher?->id ?? Option::forceCreate([
                    'team_id' => auth()->user()?->current_team_id,
                    'type' => OptionType::BOOK_PUBLISHER->value,
                    'name' => $publisherName,
                ])->id;
            }

            if ($languageName) {
                $language = Option::query()
                    ->byTeam()
                    ->whereType(OptionType::BOOK_LANGUAGE->value)
                    ->whereName($languageName)
                    ->first();
                $book->language_id = $language?->id ?? Option::forceCreate([
                    'team_id' => auth()->user()?->current_team_id,
                    'type' => OptionType::BOOK_LANGUAGE->value,
                    'name' => $languageName,
                ])->id;
            }

            if ($topicName) {
                $topic = Option::query()
                    ->byTeam()
                    ->whereType(OptionType::BOOK_TOPIC->value)
                    ->whereName($topicName)
                    ->first();
                $book->topic_id = $topic?->id ?? Option::forceCreate([
                    'team_id' => auth()->user()?->current_team_id,
                    'type' => OptionType::BOOK_TOPIC->value,
                    'name' => $topicName,
                ])->id;
            }

            $book->price = is_int(Arr::get($firstRow, 'price')) ? Arr::get($firstRow, 'price', 0) : 0;
            $book->page = is_int(Arr::get($firstRow, 'page')) ? Arr::get($firstRow, 'page', 0) : 0;
            $book->sub_title = Str::title(Arr::get($firstRow, 'sub_title'));
            $book->isbn_number = Arr::get($firstRow, 'isbn_number');
            $book->year_published = Arr::get($firstRow, 'year_published');
            $book->volume = Arr::get($firstRow, 'volume');
            $book->call_number = Arr::get($firstRow, 'call_number');
            $book->edition = Arr::get($firstRow, 'edition');
            $book->save();

            $bookAddition = BookAddition::forceCreate([
                'team_id' => auth()->user()?->current_team_id,
                'date' => today()->toDateString(),
            ]);

            foreach ($row as $item) {
                $number = Arr::get($item, 'number');

                $condition = Arr::get($item, 'condition');
                $condition = $conditions->firstWhere('name', $condition);

                BookCopy::forceCreate([
                    'book_addition_id' => $bookAddition->id,
                    'book_id' => $book->id,
                    'number' => $number,
                    'condition_id' => $condition?->id,
                ]);
            }
        }

        activity()->enableLogging();
    }

    private function validate(Collection $rows)
    {
        // $rows = $rows->unique('number')->values();

        $existingTitles = Book::query()
            ->byTeam()
            ->pluck('title')
            ->all();

        $existingNumbers = BookCopy::query()
            ->whereHas('book', function ($query) {
                $query->byTeam();
            })
            ->pluck('number')
            ->all();

        $errors = [];

        $newTitles = [];
        $newNumbers = [];
        foreach ($rows as $index => $row) {
            $rowNo = $index + 2;

            $title = Arr::get($row, 'title');
            $number = Arr::get($row, 'number');
            $page = Arr::get($row, 'page');
            $price = Arr::get($row, 'price');

            if (! $title) {
                $errors[] = $this->setError($rowNo, trans('library.book.props.title'), 'required');
            } elseif (strlen($title) < 2 || strlen($title) > 200) {
                $errors[] = $this->setError($rowNo, trans('library.book.props.title'), 'min_max', ['min' => 2, 'max' => 200]);
                // } elseif (in_array($title, $existingTitles)) {
                //     $errors[] = $this->setError($rowNo, trans('library.book.props.title'), 'exists');
                // } elseif (in_array($title, $newTitles)) {
                //     $errors[] = $this->setError($rowNo, trans('library.book.props.title'), 'duplicate');
            }

            if (! $number) {
                $errors[] = $this->setError($rowNo, trans('library.book.props.number'), 'required');
            } elseif (! is_int($number)) {
                $errors[] = $this->setError($rowNo, trans('library.book.props.number'), 'required');
            } elseif (in_array($number, $existingNumbers)) {
                $errors[] = $this->setError($rowNo, trans('library.book.props.number'), 'exists');
            } elseif (in_array($number, $newNumbers)) {
                $errors[] = $this->setError($rowNo, trans('library.book.props.number'), 'duplicate');
            }

            // if ($page) {
            //     if (! is_integer($page)) {
            //         $errors[] = $this->setError($rowNo, trans('library.book.props.page'), 'integer');
            //     }
            // }

            // if ($price) {
            //     if (! is_integer($price)) {
            //         $errors[] = $this->setError($rowNo, trans('library.book.props.price'), 'integer');
            //     }
            // }

            $newTitles[] = $title;
            $newNumbers[] = $number;
        }

        return $errors;
    }
}
