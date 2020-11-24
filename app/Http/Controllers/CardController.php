<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $columns = auth()->user()->columns()->with('cards')->get();

//        dd($cards->toArray());

        return view('columns.index', compact('columns'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'description' => ['nullable', 'string'],
            'column_id' => ['required', 'exists:columns,id']
        ]);

        return $request->user()
            ->cards()
            ->create($request->only('title', 'description', 'column_id'));
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array']
        ]);

        foreach ($request->get('columns') as $column) {
            foreach ($column['cards'] as $i => $card) {
                $order = $i + 1;
                if ($card['column_id'] !== $card['id'] || $card['order'] !== $order) {
                    request()->user()->cards()
                        ->find($card['id'])
                        ->update(['column_id' => $column['id'], 'order' => $order]);
                }
            }
        }

        return $request->user()->columns()->with('cards')->get();
    }
}
