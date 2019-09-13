<?php

namespace App\Console\Commands;

use App\Answer;
use App\Question;
use Illuminate\Console\Command;

class Reset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes all previous progresses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Question $question, Answer $answer)
    {
        parent::__construct();
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * Execute the console command to reset all from database.
     */
    public function handle()
    {
        $this->question->truncate();
        $this->answer->truncate();
    }
}
