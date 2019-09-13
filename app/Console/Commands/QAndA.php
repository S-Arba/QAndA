<?php

namespace App\Console\Commands;

use App\Answer;
use App\Http\Controllers\AssignmentController;
use App\Qanda as AppQanda;
use App\Question;
use Illuminate\Console\Command;


class QAndA extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs an interactive command line based Q And A system.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AssignmentController $assignmentController, Question $question)
    {
        parent::__construct();
        $this->assignmentController = $assignmentController;
        $this->question = $question;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->welcomeToApp();
    }

    /**
     * Show the first questions.
     */
    public function welcomeToApp()
    {
        $plan = $this->choice(
            'What is your plan?',
            $this->assignmentController->getFirstChoices()
        );

        $this->nextStep($plan);
    }

    /**
     * Redirect the user to the selected choice.
     * 
     * @return void
     */
    public function nextStep($plan)
    {
        switch ($plan) {
            case $this->assignmentController::QUESTIONS:
                $this->addQuestion();
                break;
            case $this->assignmentController::CHECK_ANSWERS:
                $this->checkLastAnswers();
                break;
            case $this->assignmentController::PRACTICE:
                $this->practice();
                break;
            case $this->assignmentController::EXIT:
                die();
            case $this->assignmentController::BACK:
                $this->welcomeToApp();
                break;
            default:
                return true;
        }
    }

    /**
     * Flow to add new question.
     * 
     * @return void
     */
    public function addQuestion()
    {
        $question = $this->ask('what is your question?');
        $answer   = $this->ask('what is your answer for: ' . $question);
        $this->question->create(['question' => $question, 'answer' => $answer]);

        $this->nextStep(
            $this->choice(
                'Do you want to continue?',
                $this->assignmentController->addOptions([$this->assignmentController::QUESTIONS])
            )
        );
    }

    /**
     * Flow to apractice questions.
     */
    public function practice()
    {
        if (!$this->assignmentController->getavailableQuestions()) {
            $this->error('We don\'t have any question for you');
            $this->addQuestion();
        }

        $choice = $this->choice('Choose your question?',  $this->assignmentController->getavailableQuestions());
        $this->nextStep($choice);

        $userAnswer = $this->ask($choice);

        if ($this->assignmentController->handleUserAnswer($userAnswer, $choice)) {
            if ($getSession = $this->assignmentController->checkProgress()) {
                $this->table(['Question', 'Your answer', 'Validation'], $getSession);
                $this->welcomeToApp();
            } else {
                $this->practice();
            }
        } else {
            $this->error('Answers should not be empty!');
            $this->ask($choice);
        }
    }

    /**
     * Show the lastest answers.
     */
    public function checkLastAnswers()
    {
        $this->table(
            ['Question', 'Answers'],
            $this->assignmentController->getLastAnswers()
        );
        $this->welcomeToApp();
    }
}
