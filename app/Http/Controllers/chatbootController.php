<?php

namespace App\Http\Controllers;

use App\Http\Chat\DialogConversation;
use App\Http\Controllers\Controller;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Incoming\Answer;

use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;
use React\EventLoop\Factory;

class chatbootController extends Controller
{
    //
    public function chatboot(Request $request){
        $botman = app('botman');
        // $this->startConversation($botman);
        $botman->startConversation(new DialogConversation);
        $botman->listen();
    }


    public function widgetchat(){
        return view('web.chatwidget');
    }
}
