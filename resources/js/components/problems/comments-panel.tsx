import { MessageSquare, Send, ThumbsUp } from 'lucide-react';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { toast } from 'sonner';
import { cn } from '@/lib/utils';
import { buildComments } from '@/lib/problem-community';
import type { Problem } from '@/types';
import type { ProblemComment } from '@/lib/problem-community';
import { PanelHeader } from '@/components/problems/panel-ui';

function initials(name: string): string {
    return name
        .split(/[\s_]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('');
}

export function CommentsPanel({ problem }: { problem: Problem }) {
    const { auth } = usePage().props;
    const [comments, setComments] = useState<ProblemComment[]>(() =>
        buildComments(problem),
    );
    const [draft, setDraft] = useState('');
    const [voted, setVoted] = useState<Set<string>>(() => new Set());

    const toggleVote = (id: string) => {
        setVoted((previous) => {
            const next = new Set(previous);

            if (next.has(id)) {
                next.delete(id);
            } else {
                next.add(id);
            }

            return next;
        });
    };

    const submitComment = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const body = draft.trim();

        if (body === '') {
            return;
        }

        const author = auth.user?.username || auth.user?.name || 'You';

        setComments((previous) => [
            {
                id: `local-${Date.now()}`,
                author,
                initials: initials(author),
                time: 'Just now',
                body,
                votes: 0,
            },
            ...previous,
        ]);

        setDraft('');
        toast.success('Comment added to the local preview.');
    };

    return (
        <div className="space-y-6">
            <PanelHeader
                icon={MessageSquare}
                title="Comments"
                description="Discussion and follow-up questions from people working through the problem."
                badge="Sample data"
            />

            <form
                onSubmit={submitComment}
                className="rounded-xl border bg-card p-4"
            >
                <div className="flex items-start gap-3">
                    <Avatar className="size-9 shrink-0">
                        <AvatarFallback>
                            {initials(
                                auth.user?.username || auth.user?.name || 'You',
                            )}
                        </AvatarFallback>
                    </Avatar>

                    <div className="min-w-0 flex-1 space-y-3">
                        <textarea
                            value={draft}
                            onChange={(event) => setDraft(event.target.value)}
                            placeholder="Add a comment, edge case, or question..."
                            rows={3}
                            className="w-full resize-y rounded-lg border bg-background px-3 py-2.5 text-sm leading-6 transition-colors outline-none placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring"
                        />

                        <div className="flex flex-wrap items-center justify-between gap-2">
                            <p className="text-xs text-muted-foreground">
                                Markdown is not rendered in this preview.
                            </p>
                            <Button
                                type="submit"
                                size="sm"
                                disabled={draft.trim() === ''}
                                className="gap-2"
                            >
                                <Send className="size-3.5" />
                                Post comment
                            </Button>
                        </div>
                    </div>
                </div>
            </form>

            <div className="space-y-4">
                {comments.map((comment) => {
                    const voteCount =
                        comment.votes + (voted.has(comment.id) ? 1 : 0);

                    return (
                        <article
                            key={comment.id}
                            className="rounded-xl border bg-card/40 p-4"
                        >
                            <div className="flex items-start gap-3">
                                <Avatar className="size-9 shrink-0">
                                    <AvatarFallback>
                                        {comment.initials}
                                    </AvatarFallback>
                                </Avatar>

                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <span className="text-sm font-medium">
                                            {comment.author}
                                        </span>
                                        <span className="text-xs text-muted-foreground">
                                            {comment.time}
                                        </span>
                                        <Badge
                                            variant="outline"
                                            className="text-[10px]"
                                        >
                                            Discussion
                                        </Badge>
                                    </div>

                                    <p className="mt-2 text-sm leading-7 text-foreground/90">
                                        {comment.body}
                                    </p>

                                    <div className="mt-3 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                toggleVote(comment.id)
                                            }
                                            className={cn(
                                                'inline-flex items-center gap-1.5 rounded-md px-2 py-1 transition-colors hover:bg-muted',
                                                voted.has(comment.id) &&
                                                    'text-primary',
                                            )}
                                            aria-pressed={voted.has(comment.id)}
                                        >
                                            <ThumbsUp className="size-3.5" />
                                            {voteCount}
                                        </button>
                                        <span className="inline-flex items-center gap-1.5">
                                            <MessageSquare className="size-3.5" />
                                            Reply
                                        </span>
                                    </div>

                                    {comment.reply && (
                                        <div className="mt-4 rounded-lg border bg-muted/30 p-3">
                                            <div className="flex items-center gap-2">
                                                <Avatar className="size-7 shrink-0">
                                                    <AvatarFallback className="text-[10px]">
                                                        {comment.reply.initials}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <span className="text-sm font-medium">
                                                    {comment.reply.author}
                                                </span>
                                                <span className="text-xs text-muted-foreground">
                                                    {comment.reply.time}
                                                </span>
                                            </div>
                                            <p className="mt-2 pl-9 text-sm leading-7 text-foreground/90">
                                                {comment.reply.body}
                                            </p>
                                            <div className="mt-2 pl-9 text-xs text-muted-foreground">
                                                {comment.reply.votes} upvotes
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </article>
                    );
                })}
            </div>

            <p className="text-xs text-muted-foreground">
                Comments are stored locally for this preview.
            </p>
        </div>
    );
}
