<?php

namespace App\Livewire\Pages;

use App\Models\Announcement;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Title;
use Livewire\Component;

class AnnouncementSingle extends Component
{

    #[Title('Announcement')]

    public $updatesData;
    public $comment_content;

    public function mount($postTitle)
    {
        $updates = Announcement::with(['likes.user', 'user', 'comments'])->where('post_title', $postTitle)->first();

        if (!$updates) {

            $this->redirect('/updates/post-not-found-or-deleted/404', navigate: true);
        }
        $this->updatesData = $updates;
    }

    public function like($announcementId)
    {
        $like = Announcement::find($announcementId);

        if (!$like) {
            $this->dispatch('toastr', [
                'type'          =>          'warning',
                'message'       =>          'Post not found',
            ]);

            return;
        } else {

            $alreadyLiked = Like::where('announcement_id', $announcementId)
                ->where('user_id', auth()->user()->id)
                ->exists();

            if ($alreadyLiked) {
                $this->dispatch('toastr', [
                    'type'          =>          'warning',
                    'message'       =>          'You already liked this post',
                ]);

                return;
            } else {
                Like::create([
                    'user_id'                   =>          auth()->user()->id,
                    'announcement_id'           =>          $like->id
                ]);
            }
        }
    }

    public function unlike($announcementId)
    {
        $unlike = Announcement::find($announcementId);

        if (!$unlike) {
            $this->dispatch('toastr', [
                'type'          =>          'warning',
                'message'       =>          'Post not found',
            ]);

            return;
        } else {
            $liked = Like::where('announcement_id', $announcementId)
                ->where('user_id', auth()->user()->id)
                ->first();
            if (!$liked) {
                $this->dispatch('toastr', [
                    'type'          =>          'warning',
                    'message'       =>          'You have not liked this post yet',
                ]);

                return;
            } else {
                $liked->delete();
            }
        }
    }

    public function delete($announcementId)
    {
        $toDelete = Announcement::find($announcementId);

        if (!$toDelete) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Post already deleted/not found',
            ]);
        } else {
            if ($toDelete->post_attachment && count($toDelete->post_attachment) > 0) {
                if (is_array($toDelete->post_attachment)) {
                    foreach ($toDelete->post_attachment as $existingAttachment) {
                        if (Storage::exists($existingAttachment)) {
                            Storage::delete($existingAttachment);
                        }
                    }
                }
            }

            $this->dispatch('toastr', [
                'type'          =>          'success',
                'message'       =>          'Your post deleted successfully',
            ]);

            $toDelete->delete();

            return $this->redirect('/announcement', navigate: true);
        }
    }

    public function postComment($postId)
    {
        $user = auth()->user();

        $post = Announcement::find($postId);

        $this->validate([
            'comment_content'       =>              ['required', 'min:1', 'max:255']
        ]);

        $this->validate([
            'comment_content'       =>              ['required', 'min:1', 'max:255']
        ]);

        if(!$post)
        {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to comment no post found',
            ]);
        } else {
            Comment::create([
                'user_id'                   =>                 $user->id,
                'announcement_id'           =>                 $postId,
                'comment_content'           =>                 $this->comment_content
            ]);

            $this->reset('comment_content');
        }
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);

        if(!$comment)
        {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Comment already deleted/not found',
            ]);
        } else {
            $comment->delete();
        }
    }

    public function render()
    {
        return view('livewire.pages.announcement-single');
    }
}
