<?php

namespace App\Jobs;

use App\Models\AccountHolder;
use DirectoryIterator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JsonImporterJB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dir = new DirectoryIterator(data_path(public_path()));
        foreach ($dir as $fileinfo) {
            $is_initiated = \App\Models\ProcessTracker::where('file_path', data_path().$fileinfo);

            if (!$fileinfo->isDot()) {
                $loaded_file = load_only_json_data_file(data_path(public_path()).$fileinfo);
                if (!$is_initiated->exists()) {
                    initiate_process_status(data_path().$fileinfo, count($loaded_file));
                    foreach ($loaded_file as $js) {
                        $account_holder = $this->getAccount_holder($js);
                        $account_holder->save();

                        $linked_card = $this->getLinked_card($account_holder, $js['credit_card']);
                        $linked_card->save();

                        $update_process = update_process_status(data_path().$fileinfo);

                        if ($update_process['completed_count'] >= count($loaded_file)) {
                            move_processed_delete(data_path(public_path()).$fileinfo, $fileinfo);
                        }

                    }
                } else {

                    $skip_size = $is_initiated->value('completed_count');
                    foreach (array_slice($loaded_file, $skip_size) as $js) {
                        $account_holder = $this->getAccount_holder($js);

                        if ($account_holder->save()) {
                            $linked_card = $this->getLinked_card($account_holder, $js['credit_card']);

                            if ($linked_card->save()) {
                                $update_process = update_process_status(data_path().$fileinfo);

                                if ($update_process['completed_count'] >= count($loaded_file)) {
                                    move_processed_delete(data_path(public_path()).$fileinfo, $fileinfo);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param  AccountHolder  $account_holder
     * @param $credit_card
     * @return \App\Models\LinkedCard
     */
    function getLinked_card(AccountHolder $account_holder, $credit_card): \App\Models\LinkedCard
    {
        $linked_card = new \App\Models\LinkedCard();
        $linked_card->account_holder_id = $account_holder->id;
        $linked_card->type = $credit_card['type'];
        $linked_card->number = $credit_card['number'];
        $linked_card->name = $credit_card['name'];
        $linked_card->expirationDate = $credit_card['expirationDate'];
        return $linked_card;
    }

    /**
     * @param $js
     * @return AccountHolder
     */
    function getAccount_holder($js): AccountHolder
    {
        $account_holder = new AccountHolder();
        $account_holder->name = $js['name'];
        $account_holder->checked = $js['checked'];
        $account_holder->address = $js['address'];
        $account_holder->description = $js['description'];
        $account_holder->interest = $js['interest'];
        $account_holder->date_of_birth = date('Y-m-d', strtotime($js['date_of_birth']));
        $account_holder->email = $js['email'];
        $account_holder->account = $js['account'];
        return $account_holder;
    }

}
