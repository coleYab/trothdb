import { Check, ChevronDown, ChevronUp, Code2, Copy, Users } from 'lucide-react';
import { useMemo, useState } from 'react';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useClipboard } from '@/hooks/use-clipboard';
import type { ProblemSummary } from '@/types';

export type UserCodeSubmission = {
    id: number;
    user_id?: number;
    user_name: string;
    username: string;
    language: string;
    code: string;
    score?: number | null;
    created_at: string;
};

function getInitials(name: string): string {
    return name
        .split(/[\s_]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('');
}

export function SubmissionsPanel({
    problem,
    submissions = [],
}: {
    problem: ProblemSummary;
    submissions?: UserCodeSubmission[];
}) {
    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b pb-4">
                <div className="flex items-center gap-3">
                    <div className="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <Users className="size-5" />
                    </div>
                    <div>
                        <h2 className="text-lg font-semibold tracking-tight text-foreground">
                            C++ Submissions ({submissions.length})
                        </h2>
                        <p className="text-xs text-muted-foreground">
                            Working C++ implementations submitted by users for {problem.title}.
                        </p>
                    </div>
                </div>

                <div className="flex items-center gap-2">
                    <Badge variant="outline" className="border-emerald-500/30 bg-emerald-500/10 font-mono text-xs text-emerald-600 dark:text-emerald-400">
                        C++ 20 Exclusive
                    </Badge>
                </div>
            </div>

            {/* Submissions List */}
            {submissions.length === 0 ? (
                <div className="rounded-xl border border-dashed p-10 text-center">
                    <Code2 className="mx-auto size-8 text-muted-foreground/60" />
                    <h3 className="mt-2 text-sm font-semibold text-foreground">
                        No submissions yet
                    </h3>
                    <p className="mt-1 text-xs text-muted-foreground">
                        Be the first to submit a C++ solution for this problem!
                    </p>
                </div>
            ) : (
                <div className="space-y-5">
                    {submissions.map((sub) => (
                        <UserSubmissionCard key={sub.id} submission={sub} />
                    ))}
                </div>
            )}
        </div>
    );
}

function UserSubmissionCard({ submission }: { submission: UserCodeSubmission }) {
    const [copiedText, copy] = useClipboard();
    const [isExpanded, setIsExpanded] = useState(false);
    const copied = copiedText === submission.code && submission.code !== '';

    const lines = useMemo(
        () => submission.code.split('\n'),
        [submission.code],
    );
    const isLong = lines.length > 18;
    const displayedCode =
        isLong && !isExpanded
            ? lines.slice(0, 18).join('\n')
            : submission.code;

    const formattedDate = useMemo(() => {
        try {
            return new Date(submission.created_at).toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
            });
        } catch {
            return '';
        }
    }, [submission.created_at]);

    return (
        <article className="overflow-hidden rounded-xl border bg-card shadow-xs transition-colors">
            {/* User & Meta Header */}
            <div className="flex flex-wrap items-center justify-between gap-3 border-b bg-muted/20 px-4 py-3 sm:px-5">
                <div className="flex items-center gap-3">
                    <Avatar className="size-8 border">
                        <AvatarFallback className="bg-primary/10 text-xs font-semibold text-primary">
                            {getInitials(submission.user_name)}
                        </AvatarFallback>
                    </Avatar>

                    <div>
                        <div className="flex items-center gap-2">
                            <span className="text-sm font-semibold text-foreground">
                                {submission.user_name}
                            </span>
                            <span className="font-mono text-xs text-muted-foreground">
                                @{submission.username}
                            </span>
                        </div>
                        {formattedDate && (
                            <p className="text-[11px] text-muted-foreground">
                                Submitted on {formattedDate}
                            </p>
                        )}
                    </div>
                </div>

                <div className="flex items-center gap-2">
                    <Badge variant="secondary" className="font-mono text-xs">
                        C++ 20
                    </Badge>

                    {submission.score != null && (
                        <Badge
                            variant="outline"
                            className="border-emerald-500/30 bg-emerald-500/10 font-mono text-xs text-emerald-600 dark:text-emerald-400"
                        >
                            Score: {Math.round(submission.score)}/100
                        </Badge>
                    )}

                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={() => void copy(submission.code)}
                        className="h-8 gap-1.5 px-2.5 text-xs text-muted-foreground hover:text-foreground"
                        title="Copy code"
                    >
                        {copied ? (
                            <>
                                <Check className="size-3 text-emerald-500" />
                                <span className="text-emerald-500">Copied</span>
                            </>
                        ) : (
                            <>
                                <Copy className="size-3" />
                                <span>Copy</span>
                            </>
                        )}
                    </Button>
                </div>
            </div>

            {/* Code Body */}
            <div className="relative overflow-x-auto bg-slate-950 p-4 font-mono text-xs leading-6 text-slate-100 sm:text-sm">
                <pre>
                    <code>{displayedCode}</code>
                </pre>

                {isLong && !isExpanded && (
                    <div className="absolute inset-x-0 bottom-0 flex justify-center bg-linear-to-t from-slate-950 to-transparent p-3 pt-8">
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            onClick={() => setIsExpanded(true)}
                            className="h-7 gap-1 rounded-full text-xs shadow-md"
                        >
                            <ChevronDown className="size-3.5" />
                            <span>Show all {lines.length} lines</span>
                        </Button>
                    </div>
                )}
            </div>

            {isLong && isExpanded && (
                <div className="border-t border-slate-800 bg-slate-900/60 px-4 py-2 text-center">
                    <button
                        type="button"
                        onClick={() => setIsExpanded(false)}
                        className="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-200"
                    >
                        <ChevronUp className="size-3.5" />
                        <span>Collapse code</span>
                    </button>
                </div>
            )}
        </article>
    );
}
