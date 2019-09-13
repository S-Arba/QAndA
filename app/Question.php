<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question', 'answer'
    ];

    /**
     * Get all questions.
     *
     * @return object
     */
    public static function getAllQuestions()
    {
        return Question::all();
    }

    /**
     * get selected question.
     *
     * @return object
     */
    public static function getSelectedQuestion(string $question)
    {
        return Question::where('question', $question)->first();
    }

    /**
     * Get the question with the selected id.
     *
     * @param int $questionId
     * @return object
     */
    public static function getQuestionLabel(int $questionId)
    {
        return Question::find($questionId)->pluck('question')->first();
    }

    /**
     * Get question id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Check if answer is conrrect.
     *
     * @param string $userAnswer
     * @return string
     */
    public function isCorrect(string $userAnswer)
    {
        return $this->answer === $userAnswer ? 'correct' : 'wrong';
    }
}
