<?php

namespace App\Jobs;

use App\Models\Set;
use App\Models\User;
use App\Notifications\CommentNotification;
use App\Notifications\NewFollowNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CommentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $set;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Set $set)
    {
        $this->user=$user;
        $this->set=$set;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data=[
            'message' => 'bài viết mới vừa được tạo',
            'set' => $this->set->name,
            'owner_id' => $this->set->user_id,
            'avatar'=>$this->user->avatar,
            'user_name' => $this->user->name,
            'set_id'=>$this->set->id,
            'type' => 'new_comment',
        ];
        $this->user->notify(new CommentNotification($data));
    }
}
