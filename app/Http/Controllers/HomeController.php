<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Story;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Hjob;
use App\Models\Poll;
use App\Models\Author;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class HomeController extends Controller
{

    public function updateItemAuthors($by){
        
        $client = new \GuzzleHttp\Client();
        if($by != ""){
            try
            {
                $response = $client->get(env('HACKER_URL')."user/$by.json?print=pretty");
                $api_items = json_decode($response->getBody(), true);
                // return $api_items;

                $about = isset($api_items['about']) ? $api_items['about'] : '';
                $created = isset($api_items['created']) ? $api_items['created'] : '';
                $karma = isset($api_items['karma']) ? $api_items['karma'] : 0;
                $delay = isset($api_items['delay']) ? $api_items['delay'] : 0;

                $isNames = Author::where('name', $by)->first();

                if($isNames){
                    $update_names = $isNames->update([
                        'karma'     => $karma,
                        'about'     => $about,
                        'delay'     => $delay,
                        'created'   => $created,
                    ]);
                    // if($update_names){
                    //     return true;
                    // }
                    // return 2;
                }
                // return 3;
            }catch(\Exception $e)
            {
                \Log::info($e->getMessage());
                return $e->getMessage();
            }
        }
        // return 4;
    }


    public function update_authors(){
        $authors = Author::where('karma', 0)->get();
        if(count($authors)){
            foreach($authors as $author){
                if($author->name != ""){
                    // update author details
                    // i didnt put this on the same getItemDetails method because it will be very slow
                    $this->updateItemAuthors($author->name); // get the details of this author
                }
            }
            return response()->json([
                'status' => "success",
                'message' => 'Data retrieved',
                'data' => ""
            ],200);
        }
        return response()->json([
            'status' => "error",
            'message' => 'No authors found',
            'data' => ""
        ],500);
    }


    public function getItemDetails($items){
        $client = new \GuzzleHttp\Client();
        $stories="";
        $comments="";
        $h_jobs="";
        $polls="";
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
                    $parts = substr($parts, 0, -1); // remove the last comma

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
                                $stories = Story::create([
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
                            }
                            if($api_items['type'] == "comment"){
                                $comments = Comment::create([
                                    'author'        => $author,
                                    'item_id'       => $item_id,
                                    'kids'          => $kid_items,
                                    'parents'       => $parent,
                                    'text'          => $texts,
                                    'time'          => $time,
                                ]);
                            }
                            if($api_items['type'] == "job"){
                                $h_jobs = Hjob::create([
                                    'author'        => $author,
                                    'item_id'       => $item_id,
                                    'score'         => $score,
                                    'text'          => $texts,
                                    'time'          => $time,
                                    'title'         => $title,
                                    'url'           => $url,
                                ]);
                            }
                            if($api_items['type'] == "polls"){
                                $polls = Poll::create([
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
            return response()->json([
                'data' => [
                    'stories'   => $stories ? "Stories fetched" : 'No stories fetched',
                    'comments'  => $comments ? "Comments fetched" : 'No comments fetched',
                    'h_jobs'    => $h_jobs ? "Jobs fetched" : 'No jobs fetched',
                    'polls'     => $polls ? "Polls fetched" : 'No polls fetched',
                ]
            ],200);

        }
        return "No item ID found";
    }


    public function fetch_data() {
        $client = new \GuzzleHttp\Client();
    	try
    	{
    		$response = $client->get(env('HACKER_URL')."updates.json?print=pretty");
			$responses = json_decode($response->getBody(), true);

            $items = $responses['items'];
            // $profiles = $responses['profiles'];

            $fetch_items = $this->getItemDetails($items); // gets each item ID
            
            if($fetch_items){
                return response()->json([
                    'status' => 'success',
                    'message' => "data retrieved",
                    'data' => $fetch_items
                ],200);
            }
            return response()->json([
                'status' => 'error',
                'message' => "data retrieved",
                'data' => $fetch_items
            ],500);
		
    	}catch(\Exception $e)
    	{
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => ''
            ],500);
    	}
    }


}
