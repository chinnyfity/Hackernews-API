<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Story;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Hjob;
use App\Models\Poll;
use App\Models\Author;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;



class CronJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'fetch:apidata';

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


    protected function getItemDetails($items){
        $client = new \GuzzleHttp\Client();
        if(count($items) > 0){
            foreach($items as $item){
                try
                {
                    $response = $client->get(env('HACKER_URL')."item/$item.json?print=pretty");
                    $api_items = json_decode($response->getBody(), true);

                    $kid_items = "";
                    if(isset($api_items['kids']) && count($api_items['kids']) > 0){
                        foreach($api_items['kids'] as $kid){
                            $kid_items .= "$kid,";
                        }
                    }
                    $parts = "";
                    if(isset($api_items['parts']) && count($api_items['parts']) > 0){
                        foreach($api_items['parts'] as $parts){
                            $parts .= "$parts,";
                        }
                    }

                    // check isset because not all key have data
                    $author = isset($api_items['by']) ? $api_items['by'] : '';
                    $descendants = isset($api_items['descendants']) ? $api_items['descendants'] : 0;
                    $item_id = $api_items['id'];
                    $score = isset($api_items['score']) ? $api_items['score'] : 0;
                    $time = $api_items['time'];
                    $title = isset($api_items['title']) ? $api_items['title'] : '';
                    $url = isset($api_items['url']) ? $api_items['url'] : '';
                    $parent = isset($api_items['parent']) ? $api_items['parent'] : 0;
                    $texts = isset($api_items['text']) ? $api_items['text'] : '';

                    if($author != ""){ // every item must have an author

                        $isDuplicateName = Author::where('name', $author)->first();
                        if(!$isDuplicateName){
                            $insert_author = Author::create([ //create only author names for now
                                'name'      => $author,
                            ]);
                        }

                        $isDuplicate = Story::where('author', $author)->where('descendants', $descendants)->where('item_id', $item_id)->where('score', $score)->first();

                        if(!$isDuplicate){
                            $category_id = Category::where('name', $api_items['type'])->value('id');

                            if($api_items['type'] == "story"){
                                Story::create([
                                    'author'        => $author,
                                    'descendants'   => $descendants,
                                    'item_id'       => $item_id,
                                    'kids'          => $kid_items,
                                    'score'         => $score,
                                    'time'          => $time,
                                    'title'         => $title,
                                    'category'      => $category_id,
                                    'url'           => $url,
                                ]);
                            }else if($api_items['type'] == "comment"){
                                Comment::create([
                                    'author'        => $author,
                                    'item_id'       => $item_id,
                                    'kids'          => $kid_items,
                                    'parents'       => $parent,
                                    'text'          => $texts,
                                    'time'          => $time,
                                ]);
                            }else if($api_items['type'] == "job"){
                                Hjobs::create([
                                    'author'        => $author,
                                    'item_id'       => $item_id,
                                    'score'         => $score,
                                    'text'          => $texts,
                                    'time'          => $time,
                                    'title'         => $title,
                                    'url'           => $url,
                                ]);
                            }else if($api_items['type'] == "polls"){
                                Polls::create([
                                    'author'        => $author,
                                    'descendants'   => $descendants,
                                    'item_id'       => $item_id,
                                    'kids'          => $kid_items,
                                    'parts'         => $parts,
                                    'score'         => $score,
                                    'text'          => $texts,
                                    'time'          => $time,
                                    'title'         => $title,
                                ]);
                            }
                        }
                    }
                
                }catch(\Exception $e)
                {
                    return $e->getMessage();
                }
            }
        }
        return "No item ID found";
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $client = new \GuzzleHttp\Client();
    	try
    	{
    		$response = $client->get(env('HACKER_URL')."updates.json?print=pretty");
			$responses = json_decode($response->getBody(), true);

            $items = $responses['items'];
            // $profiles = $responses['profiles'];

            $fetch_items = $this->getItemDetails($items);
            
            if($fetch_items){
                \Log::info("Fetch item cron job running...");
                return;
            }
            \Log::info($fetch_items);
            return;
		
    	}catch(\Exception $e)
    	{
            \Log::info($e->getMessage());
            return;
    	}
    }
}
