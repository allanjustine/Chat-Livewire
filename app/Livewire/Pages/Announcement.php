<?php

namespace App\Livewire\Pages;

use App\Events\NotificationPost;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Announcement as AnnouncementModel;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Announcement extends Component
{
    #[Title('Announcements')]

    public $post_content;
    public $post_attachment = [];
    public $post_title;
    public $post_category;
    public $load = 20;
    public $load_more = 10;
    public $isEditing = false;
    public $announcementToEdit;
    public $post_attachment_to_edit = [];
    public $allPost;
    public $comment_content;

    use WithFileUploads;

    public function index()
    {
        $announcements = AnnouncementModel::with(['likes.user', 'user', 'comments.user'])
            ->take($this->load)
            ->orderBy('created_at', 'desc')
            ->get();

        $updates = AnnouncementModel::where('post_category', 'updates')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();


        $post_trends = AnnouncementModel::withCount('likes')
            ->having('likes_count', '>', 0)
            ->orderBy('likes_count', 'desc')
            ->take(10)
            ->get();

        $this->allPost = AnnouncementModel::count();

        return compact('announcements', 'updates', 'post_trends');
    }

    public function loadMore()
    {
        $this->load += $this->load_more;
    }

    public function addPost()
    {
        $this->validate([
            'post_title'            =>              ['required', 'min:1', 'max:30', 'unique:announcements,post_title'],
            'post_content'          =>              ['required', 'min:1'],
            'post_attachment.*'     =>              ['max:5120', 'nullable', 'mimes:jpg,jpeg,png,gif,ico,webp,pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,html,css,php,js,ts,py,java,c,cpp,rb,go,swift,rs,scala,pl,r']
        ]);

        $attachmentPaths = [];

        foreach ($this->post_attachment as $attach) {
            $originalName = pathinfo($attach->getClientOriginalName(), PATHINFO_FILENAME);
            $originalExtension = $attach->getClientOriginalExtension();
            $fileName = $originalName . ' - ' . auth()->user()->name . '.' . $originalExtension;
            $attachmentPaths[] = $attach->storeAs(path: 'public/post/attachments', name: $fileName);
        }

        $announcementCreated = AnnouncementModel::create([
            'user_id'               =>              auth()->user()->id,
            'post_title'            =>              $this->post_title,
            'post_content'          =>              $this->post_content,
            'post_category'         =>              $this->post_category ?: 'post',
            'post_attachment'       =>              $attachmentPaths
        ]);

        $this->post_attachment = null;

        $this->reset([
            'post_attachment',
            'post_category',
            'post_content',
            'post_title'
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('formSubmitted');
        $this->dispatch('toastr', [
            'type'          =>          'success',
            'message'       =>          'Your post is posted successfully',
        ]);

        $users = User::where('id', '!=', auth()->user()->id)->get();

        if($announcementCreated->post_category === 'updates')
        {
            foreach($users as $user)
            {
                $user->notify(new AnnouncementNotification($announcementCreated));
            }
            event(new NotificationPost($announcementCreated));
        }
    }

    public function like($announcementId)
    {
        $like = AnnouncementModel::find($announcementId);

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
        $unlike = AnnouncementModel::find($announcementId);

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

    public function edit($announcementId)
    {
        $announcement = AnnouncementModel::find($announcementId);
        if ($announcement) {
            $this->isEditing = true;
            $this->announcementToEdit = $announcement;
            $this->post_attachment_to_edit = $announcement->post_attachment;
            $this->post_content = $announcement->post_content;
            $this->post_category = $announcement->post_category;
            $this->post_title = $announcement->post_title;
            $this->dispatch('setPostContent', $this->post_content);
        }
    }

    public function updatePost()
    {
        $userId = auth()->user()->id;

        $this->validate([
            'post_title'            =>              ['required', 'min:1', 'max:20'],
            'post_content'          =>              ['required', 'min:1'],
            'post_attachment.*'     =>              ['max:5120', 'nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,html,css,php,js,ts,py,java,c,cpp,rb,go,swift,rs,scala,pl,r']
        ]);

        if (!$this->announcementToEdit) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'No post found to update',
            ]);

            return;
        } elseif ($this->announcementToEdit->user_id != $userId) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Sorry only author can update in this post',
            ]);

            return;
        } else {

            $updateData = [
                'post_title'       => $this->post_title,
                'post_content'     => $this->post_content,
                'post_category'    => $this->post_category,
            ];

            if ($this->post_attachment && count($this->post_attachment) > 0) {
                if (is_array($this->announcementToEdit->post_attachment)) {
                    foreach ($this->announcementToEdit->post_attachment as $existingAttachment) {
                        if (Storage::exists($existingAttachment)) {
                            Storage::delete($existingAttachment);
                        }
                    }
                }

                $attachmentPaths = [];

                foreach ($this->post_attachment as $attach) {
                    if (is_string($attach)) {
                        $attachmentPaths[] = $attach;
                    } else {
                        $originalName = pathinfo($attach->getClientOriginalName(), PATHINFO_FILENAME);
                        $originalExtension = $attach->getClientOriginalExtension();
                        $fileName = $originalName . ' - ' . auth()->user()->name . '.' . $originalExtension;
                        $attachmentPaths[] = $attach->storeAs('public/post/attachments', $fileName);
                    }
                }

                $updateData['post_attachment'] = $attachmentPaths;
            } else {
                $updateData['post_attachment'] = $this->post_attachment_to_edit;
            }

            $this->announcementToEdit->update($updateData);

            $this->dispatch('close-modal');
            $this->dispatch('formSubmitted');
            $this->dispatch('toastr', [
                'type'          =>          'success',
                'message'       =>          'Your post is posted successfully',
            ]);
            $this->cancelEdit();
        }
    }

    public function delete($announcementId)
    {
        $user = auth()->user();

        $is_admin = $user->hasRole('admin');

        $toDelete = AnnouncementModel::find($announcementId);

        if (!$toDelete) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Post already deleted/not found',
            ]);
        } elseif ($toDelete->user_id != $user->id && !$is_admin) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Sorry only author/admin can delete in this post',
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

            $toDelete->delete();

            $this->dispatch('toastr', [
                'type'          =>          'success',
                'message'       =>          'Your post deleted successfully',
            ]);
        }
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->post_attachment = [];
        $this->post_category = '';
        $this->post_title = '';
        $this->post_content = '';
        $this->dispatch('setPostContent', $this->post_content);
    }

    public function postComment($postId)
    {
        $user = auth()->user();

        $post = AnnouncementModel::find($postId);

        $this->validate([
            'comment_content'       =>              ['required', 'min:1', 'max:255']
        ]);

        if (!$post) {
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

        if (!$comment) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Comment already deleted/not found',
            ]);
        } else {
            $comment->delete();
        }
    }

    public function share($postId)
    {
        $post = AnnouncementModel::find($postId);

        if(!$post)
        {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Post not found',
            ]);

            return;
        } else {
            $post->increment('shares');
        }
    }

    public function render()
    {
        return view('livewire.pages.announcement', $this->index());
    }
}
