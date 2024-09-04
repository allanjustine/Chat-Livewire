<?php

namespace App\Livewire\Pages;

use App\Models\Announcement;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Title;
use Livewire\Component;

class AnnouncementSingle extends Component
{

    #[Title('Profile')]

    public $previous;
    public $updatesData;

    public function mount($postTitle)
    {
        $updates = Announcement::where('post_title', $postTitle)->first();

        $this->previous = URL::previous();

        if (!$updates) {
            $this->redirect($this->previous, navigate: true);
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

    public function render()
    {
        return view('livewire.pages.announcement-single');
    }
}
