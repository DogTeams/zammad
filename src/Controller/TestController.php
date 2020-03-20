<?php
/**
 * @package Zammad API Wrapper
 * @author  Jordan GOBLET <jordan.goblet.pro@gmail.com>
 */
namespace Dogteam\Zammad\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Dogteam\Zammad\Zammad;

class TestController extends Controller {
    
    private $client;

    public function __construct(){
        $this->client = new Zammad();
    }

    public function createTicket(Request $request){

        $data = [
            'title'            => $request->post('title'),
            'customer'         => $request->post('customer'),
            'group'            => $request->post('group'),
            'article'          => [
                'from'         => $request->post('from'),
                'subject'      => $request->post('subject'),
                'body'         => $request->post('body'),
                'cc'           => $request->post('cc'),
                'to'           => $request->post('to'),
                'from'         => $request->post('from'),
                'type'         => $request->post('type'),
            ]
        ];
        $this->client->createTicket($data);
        echo("Ticket créé.");

    }

    public function find(Request $request){

    }

    public function search(Request $request){
        
    }
    
    public function all(Request $request){
        
    }

    public function update(Request $request){
        
    }
}
