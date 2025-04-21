<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FobiComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'checklist_taxa_id' => 'required|exists:fobi_checklist_taxas,id',
            'comment' => 'required|string'
        ]);

        $comment = FobiComment::create([
            'user_id' => auth()->id(),
            'checklist_taxa_id' => $validated['checklist_taxa_id'],
            'comment' => $validated['comment']
        ]);

        return response()->json($comment->load('user'));
    }

    public function getComments($checklistTaxaId)
    {
        $comments = FobiComment::with('user')
            ->where('checklist_taxa_id', $checklistTaxaId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }
}
