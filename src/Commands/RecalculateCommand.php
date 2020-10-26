<?php

namespace MigrateToFlarum\RecalculateMeta\Commands;

use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RecalculateCommand extends Command
{
    protected $signature = 'migratetoflarum:recalculate-meta';
    protected $description = 'Re-calculate the stored meta values for users and discussions';

    public function __construct()
    {
        parent::__construct();

        $this->addOption('skip-users', null, null, 'Do not update users meta');
        $this->addOption('skip-user-comment-count', null, null, 'Do not update users comment count');
        $this->addOption('skip-user-discussion-count', null, null, 'Do not update users discussion count');
        $this->addOption('skip-discussions', null, null, 'Do not update discussions meta');
        $this->addOption('skip-discussion-last-post', null, null, 'Do not update discussions last post');
        $this->addOption('skip-discussion-comment-count', null, null, 'Do not update discussions comment count');
        $this->addOption('skip-discussion-participant-count', null, null, 'Do not update discussions participant count');

        $this->addOption('do-discussion-first-post', null, null, 'Re-calculate discussion first post. This option should not be used on an existing Flarum as it could set the wrong post as first post if the first post was deleted.');
        $this->addOption('do-discussion-slug', null, null, 'Re-calculate discussion slug using Flarum\'s built-in slugger. This option will not take into account custom discussion sluggers provided by extensions.');
        $this->addOption('do-discussion-number-index', null, null, 'Re-calculate the new post number for the discussion. This option should not be used on an existing Flarum website as it could cause deleted post numbers to be re-used.');
    }

    public function handle()
    {
        $this->handleUsers();
        $this->handleDiscussions();
    }

    protected function handleUsers()
    {
        if ($this->option('skip-users')) {
            return;
        }

        $this->info('Updating users');

        $query = User::query();

        $progress = $this->getOutput()->createProgressBar($query->count());

        $updateCount = 0;

        $query->each(function (User $user) use ($progress, &$updateCount) {
            if (!$this->option('skip-user-comment-count')) {
                $user->refreshCommentCount();
            }

            if (!$this->option('skip-user-discussion-count')) {
                $user->refreshDiscussionCount();
            }

            if ($user->isDirty()) {
                $updateCount++;
                $user->save();
            }

            $progress->advance();
        });

        $progress->finish();

        $this->info(''); // To force a newline
        $this->info($updateCount . ' user records updated');
    }

    protected function handleDiscussions()
    {
        if ($this->option('skip-discussions')) {
            return;
        }

        $this->info('Updating discussions');

        $query = Discussion::query();

        $progress = $this->getOutput()->createProgressBar($query->count());

        $updateCount = 0;

        $query->each(function (Discussion $discussion) use ($progress, &$updateCount) {
            if ($this->option('do-discussion-first-post')) {
                /** @var Post $firstPost */
                if ($firstPost = $discussion->comments()->oldest()->first()) {
                    $discussion->setFirstPost($firstPost);
                }
            }

            if ($this->option('do-discussion-slug')) {
                $discussion->slug = Str::slug($discussion->title);
            }

            if ($this->option('do-discussion-number-index')) {
                $discussion->post_number_index = $discussion->posts()->max('number') ?: 0;
            }

            if (!$this->option('skip-discussion-last-post')) {
                $discussion->refreshLastPost();
            }

            if (!$this->option('skip-discussion-comment-count')) {
                $discussion->refreshCommentCount();
            }

            if (!$this->option('skip-discussion-participant-count')) {
                $discussion->refreshParticipantCount();
            }

            if ($discussion->isDirty()) {
                $updateCount++;
                $discussion->save();
            }

            $progress->advance();
        });

        $progress->finish();

        $this->info(''); // To force a newline
        $this->info($updateCount . ' discussion records updated');
    }
}
