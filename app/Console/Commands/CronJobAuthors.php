<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Story;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Hjobs;
use App\Models\Polls;
use App\Models\Author;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;



class CronJobAuthors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'update:authors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function updateItemAuthors($by){
        $client = new \GuzzleHttp\Client();
        try
        {
            $response = $client->get(env('HACKER_URL')."user/$by.json?print=pretty");
            $api_items = json_decode($response->getBody(), true);

            $about = isset($api_items['about']) ? $api_items['about'] : '';
            $created = isset($api_items['created']) ? $api_items['created'] : '';
            $karma = isset($api_items['karma']) ? $api_items['karma'] : 0;
            $delay = isset($api_items['delay']) ? $api_items['delay'] : 0;

            $isNames = Author::where('name', $by)->first();

            // if($isNames){
                $update_names = $isNames->update([
                    'karma'     => $karma,
                    'about'     => $about,
                    'delay'     => $delay,
                    'created'   => $created,
                ]);
                // if($update_names){
                //     return true;
                // }
                // return false;
            // }            
        }catch(\Exception $e)
        {
            \Log::info($e->getMessage());
            // return $e->getMessage();
        }
            
        
        // return false;
    }
    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $authors = Author::where('karma', 0)->get();
        if(count($authors)){
            foreach($authors as $author){
                if($author->name != ""){
                    // update author details
                    // i didnt put this on the same getItemDetails method because it will be very slow
                    $author_updated = $this->updateItemAuthors($author->name);
                    if($author_updated){
                        \Log::info("Author details have been updated");
                    }
                    \Log::info("no updates");
                }
            }
        }
        \Log::info("No authors found");
        return;
    }
}
