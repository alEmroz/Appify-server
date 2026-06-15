<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = User::all();

        $users->each(function ($user) use ($users) {
            $posts = Post::factory(25)
                ->make()
                ->each(function ($post, $index) use ($user) {
                    $post->user_id = $user->id;
                    $post->created_at = now()->subMinutes((25 - $index) * 15 + rand(0, 60));
                    $post->updated_at = $post->created_at;
                    $post->save();
                });

            $posts->each(function ($post) use ($users) {
                $this->seedComments($post, $users);
            });
        });
    }

    private function seedComments(Post $post, $users): void
    {
        $comments = Comment::factory(30)
            ->make()
            ->each(function ($comment, $index) use ($post, $users) {
                $comment->user_id = $users->random()->id;
                $comment->post_id = $post->id;
                $comment->created_at = $post->created_at->addMinutes($index * 10 + rand(0, 30));
                $comment->updated_at = $comment->created_at;
                $comment->save();
            });

        $comments
            ->take(10)
            ->each(function ($comment) use ($users, $post) {
                $this->seedReplies($comment, $users, $post);
            });
    }

    private function seedReplies(Comment $comment, $users, Post $post): void
    {
        $replyCount = rand(1, 3);

        Comment::factory($replyCount)
            ->make()
            ->each(function ($reply) use ($comment, $users, $post) {
                $reply->user_id = $users->random()->id;
                $reply->post_id = $post->id;
                $reply->parent_id = $comment->id;
                $reply->created_at = $comment->created_at->addMinutes(rand(5, 120));
                $reply->updated_at = $reply->created_at;
                $reply->save();
            });
    }
}
