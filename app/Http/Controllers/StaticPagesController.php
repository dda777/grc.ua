<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zoho;
use GuzzleHttp\Client;

class StaticPagesController extends Controller
{
    protected $main_url = 'https://www.zohoapis.com/crm/v2/';

    public function home()
    {
        return view('static.home', ['data' => NULL]);
    }

    public function store(Request $request)
    {
      $request->validate([
          'deal_name' => 'required',
          'task_subj' => 'required',
      ]);
      $token = Zoho::latest()->first()->access_token;

      $client = new Client([
          'base_uri' => $this->main_url,
          'headers' => ['Authorization' => 'Bearer '.$token, 'Content-Type' => 'application/json']
      ]);

      $url_chunk = 'Accounts/search';
      $query_param = ['criteria' => '((Account_Name:equals:King))'];
      $account_id = $this->get($client, $url_chunk, $query_param)->data[0]->id;

      $url_chunk = 'Deals';
      $body_param = [
        'data'=>[
            [
              'Closing_Date' => date('Y-m-d', strtotime('+3 day')),
              "Deal_Name" => $request->input('deal_name'),
              "Expected_Revenue" => 50000,
              "Stage" => "Negotiation/Review",
              "Amount" => 50000,
              "Probability" => 75,
              "Account_Name" => ["id" => $account_id],
            ]
        ]
      ];
      $current_deal = $this->post($client, $url_chunk, $body_param)->data[0]->details->id;

      $url_chunk = 'Tasks';
      $body_param = [
        'data'=>[
            [
              'Closing_Date' => date('Y-m-d', strtotime('+3 day')),
              'Subject' => $request->input('task_subj'),
              'Status' => 'Deferred',
              'What_Id'=>   $current_deal,
              '$se_module' => 'Deals',
            ]
        ]
      ];
      $current_task = $this->post($client, $url_chunk, $body_param)->data[0]->details->id;
      if ($current_task) {
        return redirect()->route('home')->with('success','Deal and Task added successfully');
      }else{
        return redirect()->route('home')->with('error','Deal and Task not added');
      }

    }

    private function get($client, $url_chunk, $query_param)
    {
      $params = [
        'query' => $query_param
      ];
      return json_decode($client->get($url_chunk, $params)->getBody());
    }

    private function post($client, $url_chunk, $body_param)
    {
      $body_param = json_encode($body_param);
      $params = [
        'body' => $body_param,
        'headers' => ['Content-Type' => 'application/json', 'Content-Length' => strlen($body_param)]
      ];
      return json_decode($client->post($url_chunk, $params)->getBody());
    }
}
