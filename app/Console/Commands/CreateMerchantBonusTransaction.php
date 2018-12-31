<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Transactions\TransactionLogic;

use Carbon\Carbon;

class CreateMerchantBonusTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create bonus for merchant';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionLogic $transaction)
    {
        parent::__construct();
        $this->transaction    = $transaction;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->transaction->createBonusTransaction();
    }
}
