<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discussion;
use App\Models\Comment;
use App\Models\Suggestion;
use App\Models\Identification;

class DiscussionSeeder extends Seeder
{
    public function run()
    {
        $comments = Comment::all();
        $suggestions = Suggestion::all();
        $identifications = Identification::all();

        foreach ($comments as $comment) {
            Discussion::create([
                'user_id' => $comment->user_id,
                'checklist_id' => $comment->checklist_id,
                'fobi_checklist_id' => null, // Sesuaikan jika ada data
                'comment_id' => $comment->id,
                'suggestion_id' => null,
                'identification_id' => null,
            ]);
        }

        foreach ($suggestions as $suggestion) {
            Discussion::create([
                'user_id' => $suggestion->user_id,
                'checklist_id' => $suggestion->checklist_id, // Sesuaikan jika berbeda
                'fobi_checklist_id' => null, // Sesuaikan jika ada data
                'comment_id' => null,
                'suggestion_id' => $suggestion->id,
                'identification_id' => null,
            ]);
        }

        foreach ($identifications as $identification) {
            Discussion::create([
                'user_id' => $identification->user_id,
                'checklist_id' => $identification->checklist_id,
                'fobi_checklist_id' => null, // Sesuaikan jika ada data
                'comment_id' => null,
                'suggestion_id' => null,
                'identification_id' => $identification->id,
            ]);
        }
    }
}
