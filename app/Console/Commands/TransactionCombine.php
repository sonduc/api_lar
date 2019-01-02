<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CompareCheckings\CompareChecking;
use App\Repositories\Transactions\TransactionLogic;
use Carbon\Carbon;

class TransactionCombine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:combine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Combine all transactions everyday';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionLogic $transaction)
    {
        parent::__construct();
        $this->transaction = $transaction;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->transaction->combineTransaction();
    }
}
