<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class sendNotification implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $data;
  public $channel;
  public $event;

  public function __construct($data,$channel,$event)
  {
      $this->data = $data;
  }

  public function broadcastOn()
  {
      return [$this->channel];
  }

  public function broadcastAs()
  {
      return $this->event;
  }
}
