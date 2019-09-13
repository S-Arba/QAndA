<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'question_id', 'answer'
    ];

    /**
     * Get question id.
     *
     * @return int
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }
}
