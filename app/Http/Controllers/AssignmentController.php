<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use Illuminate\Support\Facades\Session;

class AssignmentController extends Controller
{
    const QUESTIONS = 'Add question';
    const CHECK_ANSWERS = 'Viewing previously entered answers';
    const PRACTICE = 'Practicing Question';
    const EXIT = 'exit';
    const BACK = 'back';
    const QUESTION_DONE = '  √ answered';

    /**
     * Get the first choices.
     *
     * @return array
     */
    public function getFirstChoices()
    {
        return $this->addOptions([self::QUESTIONS, self::CHECK_ANSWERS, self::PRACTICE]);
    }

    /**
     * Add exit and back option to the choices.
     *
     * @param array $choices
     * @return array
     */
    public function addOptions($choices)
    {
        return array_merge($choices, [self::BACK, self::EXIT]);;
    }

    /**
     * Return the available questions to progress.
     *
     * @return array|false
     */
    public function getavailableQuestions()
    {
        $allQuestion = Question::getAllQuestions();
        if (!empty($allQuestion->items)) {
            return $this->addOptions(
                $this->checkIfExistsInSession(
                    Question::getAllQuestions()
                )
            );
        }

        return false;
    }

    /**
     * Check if the user already answered to question.
     *
     * @param object $questions
     * @return array
     */
    public function checkIfExistsInSession(Object $questions)
    {
        foreach ($questions as $key => $question) {
            $choices[$key] = $this->makeTheQuestionDone($question);
        }

        return $choices;
    }

    /**
     * Handle user answers.
     *
     * @param string $userAnswer
     * @param string $choice
     * @return bool
     */
    public function handleUserAnswer(string $userAnswer, string $choice)
    {
        if ($userAnswer) {
            $question = Question::getSelectedQuestion($this->cleanString($choice));
            Answer::create(['question_id' => $question->getId(), 'answer' => $userAnswer]);
            $this->addSession($userAnswer, $question);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all answers.
     *
     * @return array
     */
    public function getLastAnswers()
    {
        $answers = [];
        foreach (Answer::all() as $key => $answer) {
            $answers[$key] = [
                Question::getQuestionLabel($answer->getQuestionId()), $answer->answer
            ];
        };

        return $answers;
    }

    /**
     * Remove answered from the choice.
     *
     * @param string $choice
     * @return string
     */
    public function cleanString(string $choice)
    {
        if (strpos($choice,  self::QUESTION_DONE)) {
            $choice = str_replace(self::QUESTION_DONE, "", $choice);
        }

        return $choice;
    }

    /**
     * Check if user finish all question.
     *
     * @return array
     */
    public function checkProgress()
    {
        if (count(Session::all()) === count(Question::getAllQuestions())) {
            return Session::all();
        }
    }

    /**
     * Add users answer to the session.
     * @param string $userAnswer
     * @param App\Question $question
     */
    public function addSession(string $userAnswer, Question $question)
    {
        Session::put($question->getId(), [
            $question->question,
            $userAnswer,
            $question->isCorrect($userAnswer)
        ]);
    }

    /**
     * Make the question answered to show progress.
     *
     * @param App\Question $question
     * @return string
     */
    public function makeTheQuestionDone(Question $question)
    {
        return  Session::has($question->getId()) ? $question->question . self::QUESTION_DONE : $question->question;
    }
}
