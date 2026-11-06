import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    ArrowUpRight,
    Check,
    CheckCircle2,
    Circle,
    Copy,
    Flame,
    GraduationCap,
    Play,
    Target,
    Terminal,
    Timer,
    Trophy,
} from 'lucide-react';
import { DifficultyBadge } from '@/components/difficulty-badge';
import { Button } from '@/components/ui/button';
import { useClipboard } from '@/hooks/use-clipboard';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import { index as learnRoute, lesson as lessonRoute } from '@/routes/learn';
import { show as problemRoute } from '@/routes/problems';
import { index as problemsetRoute } from '@/routes/problemset';
import { index as setupRoute } from '@/routes/setup';
import type { Difficulty, ProblemSummary } from '@/types';

type Stats = {
    total_problems: number;
    solved: number;
    submissions: number;
    accepted: number;
    accuracy: number;
    points: number;
    streak: number;
};

type RecentSubmission = {
    id: number;
    problem_id: string;
    title: string;
    status: string;
    score: number | null;
    language: string;
    created_at: string | null;
};

type ContinueLesson = {
    id: string;
    track: string;
    title: string;
    summary: string;
    minutes: number;
};

type TrackProgress = {
    title: string;
    level: string;
    completed: number;
    total: number;
};

function relativeDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);
    const diff = Date.now() - date.getTime();
    const minutes = Math.round(diff / 60000);

    if (minutes < 1) return 'just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (minutes < 1440) return `${Math.round(minutes / 60)}h ago`;

    return date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
    });
}

function statusLabel(status: string): string {
    return status.replace(/_/g, ' ');
}

export default function Dashboard({
    solvedProblemIds = [],
    stats,
    featured,
    recommended = [],
    recent = [],
    continueLesson,
    trackProgress,
}: {
    solvedProblemIds?: string[];
    stats: Stats;
    featured: ProblemSummary | null;
    recommended: ProblemSummary[];
    recent: RecentSubmission[];
    continueLesson: ContinueLesson | null;
    trackProgress: TrackProgress | null;
}) {
    const [copiedText, copy] = useClipboard();
    const solved = new Set(solvedProblemIds);
    const nextProblem = recommended[0] ?? featured;
    const progress =
        trackProgress && trackProgress.total > 0
            ? Math.round(
                  (trackProgress.completed / trackProgress.total) * 100,
              )
            : 0;

    const ledger = [
        {
            label: 'Solved',
            value: `${stats.solved}/${stats.total_problems}`,
            hint: 'unique problems',
            icon: CheckCircle2,
        },
        {
            label: 'Points',
            value: String(stats.points),
            hint: 'judge score',
            icon: Trophy,
        },
        {
            label: 'Accuracy',
            value: `${stats.accuracy}%`,
            hint: `${stats.accepted} accepted`,
            icon: Target,
        },
        {
            label: 'Streak',
            value: `${stats.streak}d`,
            hint: 'consecutive days',
            icon: Flame,
        },
        {
            label: 'Submissions',
            value: String(stats.submissions),
            hint: 'all attempts',
            icon: Timer,
        },
    ];

    return (
        <>
            <Head title="Today" />

            <div className="mx-auto w-full max-w-7xl flex-1 space-y-8 p-4 md:p-8">
                <section className="relative overflow-hidden rounded-lg border border-border/90 bg-card">
                    <div className="paper-grid pointer-events-none absolute inset-0 opacity-70" />

                    <div className="relative flex flex-col gap-8 p-6 md:p-8 lg:flex-row lg:items-end lg:justify-between">
                        <div className="max-w-2xl space-y-4">
                            <div className="flex items-center gap-2">
                                <span className="size-1.5 bg-emerald-600" />
                                <span className="meta-label">
                                    Session /{' '}
                                    {new Date().toLocaleDateString(undefined, {
                                        weekday: 'long',
                                        month: 'short',
                                        day: 'numeric',
                                    })}
                                </span>
                            </div>

                            <h1 className="display-title text-4xl text-foreground sm:text-5xl">
                                One more problem
                                <br />
                                before the day ends.
                            </h1>

                            <p className="max-w-xl text-sm leading-6 text-muted-foreground">
                                {nextProblem
                                    ? `Pick up #${nextProblem.number} ${nextProblem.title}. Test locally, then dispatch a verified C++20 solution.`
                                    : 'Every problem is solved. Revisit an editorial or run a timed re-solve to keep the edge sharp.'}
                            </p>

                            <div className="flex flex-wrap items-center gap-2 pt-1">
                                {nextProblem && (
                                    <Button asChild className="gap-2">
                                        <Link href={problemRoute(nextProblem.id)}>
                                            Continue #{nextProblem.number}
                                            <ArrowRight className="size-4" />
                                        </Link>
                                    </Button>
                                )}
                                <Button
                                    variant="outline"
                                    asChild
                                    className="gap-2 bg-transparent"
                                >
                                    <Link href={problemsetRoute()}>
                                        Browse problemset
                                    </Link>
                                </Button>
                            </div>
                        </div>

                        <div className="grid w-full max-w-xs shrink-0 grid-cols-2 gap-x-6 gap-y-4 border-t border-foreground/15 pt-5 lg:w-72 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8">
                            <div>
                                <p className="meta-label">Judge</p>
                                <p className="mt-1 flex items-center gap-1.5 font-mono text-xs">
                                    <span className="size-1.5 rounded-full bg-emerald-500" />
                                    online
                                </p>
                            </div>
                            <div>
                                <p className="meta-label">Runtime</p>
                                <p className="mt-1 font-mono text-xs">
                                    GCC 14 · O2
                                </p>
                            </div>
                            <div>
                                <p className="meta-label">Sandbox</p>
                                <p className="mt-1 font-mono text-xs">
                                    ASan · UBSan
                                </p>
                            </div>
                            <div>
                                <p className="meta-label">Language</p>
                                <p className="mt-1 font-mono text-xs">
                                    C++20 only
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    aria-label="Account stats"
                    className="grid grid-cols-2 divide-x divide-y divide-border/80 border-y border-border/80 sm:grid-cols-3 md:grid-cols-5 md:divide-y-0"
                >
                    {ledger.map((item) => (
                        <div
                            key={item.label}
                            className="flex items-start justify-between gap-3 px-4 py-4 first:pl-0"
                        >
                            <div>
                                <p className="meta-label">{item.label}</p>
                                <p className="display-title mt-1 text-3xl tabular-nums">
                                    {item.value}
                                </p>
                                <p className="mt-0.5 font-mono text-[10px] text-muted-foreground">
                                    {item.hint}
                                </p>
                            </div>
                            <item.icon className="size-4 text-muted-foreground/60" />
                        </div>
                    ))}
                </section>

                <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                    <div className="min-w-0 space-y-8">
                        {featured && (
                            <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                                <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-5 py-2.5">
                                    <span className="meta-label">
                                        Featured drill / #{featured.number}
                                    </span>
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        {featured.acceptance_rate.toFixed(1)}%
                                        accepted
                                    </span>
                                </div>

                                <div className="space-y-4 p-5 md:p-6">
                                    <div className="flex flex-wrap items-start justify-between gap-3">
                                        <div className="space-y-1.5">
                                            <h2 className="display-title text-3xl">
                                                {featured.title}
                                            </h2>
                                            <p className="max-w-xl text-sm leading-6 text-muted-foreground">
                                                {featured.summary}
                                            </p>
                                        </div>
                                        <DifficultyBadge
                                            difficulty={
                                                featured.difficulty as Difficulty
                                            }
                                        />
                                    </div>

                                    <div className="flex flex-wrap gap-1.5">
                                        {featured.tags.slice(0, 4).map((tag) => (
                                            <span
                                                key={tag}
                                                className="rounded-sm border border-border/80 bg-background px-2 py-0.5 font-mono text-[10px] text-muted-foreground"
                                            >
                                                {tag}
                                            </span>
                                        ))}
                                    </div>

                                    <div className="flex flex-col gap-2 rounded-md border border-border/80 bg-background/70 p-3 sm:flex-row sm:items-center sm:justify-between">
                                        <code className="flex items-center gap-2 overflow-x-auto font-mono text-xs">
                                            <span className="select-none text-emerald-700 dark:text-emerald-400">
                                                $
                                            </span>
                                            arena init {featured.id}
                                        </code>
                                        <button
                                            type="button"
                                            onClick={() =>
                                                void copy(
                                                    `arena init ${featured.id}`,
                                                )
                                            }
                                            className="inline-flex shrink-0 items-center gap-1 self-start font-mono text-[10px] text-muted-foreground hover:text-foreground sm:self-auto"
                                        >
                                            {copiedText ===
                                            `arena init ${featured.id}` ? (
                                                <>
                                                    <Check className="size-3 text-emerald-600" />
                                                    Copied
                                                </>
                                            ) : (
                                                <>
                                                    <Copy className="size-3" />
                                                    Copy
                                                </>
                                            )}
                                        </button>
                                    </div>

                                    <Button asChild className="gap-2">
                                        <Link href={problemRoute(featured.id)}>
                                            Open problem
                                            <ArrowRight className="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </section>
                        )}

                        <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                            <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-5 py-2.5">
                                <span className="meta-label">Practice queue</span>
                                <Link
                                    href={problemsetRoute()}
                                    className="font-mono text-[10px] text-muted-foreground hover:text-foreground"
                                >
                                    view all →
                                </Link>
                            </div>

                            <div className="divide-y divide-border/70">
                                {recommended.map((problem) => {
                                    const isSolved = solved.has(problem.id);

                                    return (
                                        <Link
                                            key={problem.id}
                                            href={problemRoute(problem.id)}
                                            className="group flex items-center gap-3 px-5 py-3 transition-colors hover:bg-muted/40"
                                        >
                                            <span className="w-8 shrink-0 font-mono text-[11px] text-muted-foreground">
                                                {String(problem.number).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </span>
                                            {isSolved ? (
                                                <CheckCircle2 className="size-3.5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                            ) : (
                                                <Circle className="size-3.5 shrink-0 text-muted-foreground/40" />
                                            )}
                                            <span className="min-w-0 flex-1 truncate text-sm font-medium">
                                                {problem.title}
                                            </span>
                                            <span className="hidden font-mono text-[10px] text-muted-foreground sm:inline">
                                                {problem.discipline}
                                            </span>
                                            <DifficultyBadge
                                                difficulty={problem.difficulty}
                                            />
                                            <ArrowUpRight className="size-3.5 shrink-0 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100" />
                                        </Link>
                                    );
                                })}
                            </div>
                        </section>

                        <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                            <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-5 py-2.5">
                                <span className="meta-label">
                                    Recent submissions
                                </span>
                                <span className="font-mono text-[10px] text-muted-foreground">
                                    {stats.submissions} total
                                </span>
                            </div>

                            {recent.length === 0 ? (
                                <div className="px-5 py-10 text-center">
                                    <p className="text-sm font-medium">
                                        No submissions yet
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">
                                        Run{' '}
                                        <code className="rounded bg-muted px-1 py-0.5 font-mono text-[10px]">
                                            arena submit
                                        </code>{' '}
                                        to send your first solution.
                                    </p>
                                </div>
                            ) : (
                                <div className="divide-y divide-border/70">
                                    {recent.map((submission) => (
                                        <Link
                                            key={submission.id}
                                            href={problemRoute(
                                                submission.problem_id,
                                            )}
                                            className="flex flex-wrap items-center gap-3 px-5 py-2.5 transition-colors hover:bg-muted/40"
                                        >
                                            <span
                                                className={cn(
                                                    'w-20 shrink-0 font-mono text-[10px] uppercase',
                                                    submission.status ===
                                                        'accepted'
                                                        ? 'text-emerald-700 dark:text-emerald-400'
                                                        : 'text-rose-700 dark:text-rose-400',
                                                )}
                                            >
                                                {statusLabel(submission.status)}
                                            </span>
                                            <span className="min-w-0 flex-1 truncate text-xs font-medium">
                                                {submission.title}
                                            </span>
                                            <span className="font-mono text-[10px] text-muted-foreground">
                                                {submission.score != null
                                                    ? `${Math.round(submission.score)}/100`
                                                    : '—'}
                                            </span>
                                            <span className="w-16 text-right font-mono text-[10px] text-muted-foreground">
                                                {relativeDate(
                                                    submission.created_at,
                                                )}
                                            </span>
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </section>
                    </div>

                    <aside className="space-y-6">
                        <section className="rounded-lg border border-border/90 bg-card p-5">
                            <div className="flex items-center justify-between">
                                <span className="meta-label">Learning track</span>
                                <GraduationCap className="size-4 text-muted-foreground" />
                            </div>

                            <h2 className="display-title mt-2 text-2xl">
                                {trackProgress?.title ?? 'C++ Foundations'}
                            </h2>
                            <p className="mt-1 text-xs text-muted-foreground">
                                {trackProgress
                                    ? `${trackProgress.completed}/${trackProgress.total} lessons complete · ${trackProgress.level}`
                                    : 'Objects, lifetime, and the STL mental model.'}
                            </p>

                            <div className="mt-4 space-y-1.5">
                                <div className="flex justify-between font-mono text-[10px] text-muted-foreground">
                                    <span>Progress</span>
                                    <span>{progress}%</span>
                                </div>
                                <div className="h-1 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        className="h-full bg-emerald-600"
                                        style={{ width: `${progress}%` }}
                                    />
                                </div>
                            </div>

                            {continueLesson ? (
                                <>
                                    <div className="mt-4 border-t border-border/70 pt-3">
                                        <p className="meta-label">Next lesson</p>
                                        <p className="mt-1 text-sm font-medium">
                                            {continueLesson.title}
                                        </p>
                                        <p className="mt-0.5 font-mono text-[10px] text-muted-foreground">
                                            {continueLesson.minutes} min
                                        </p>
                                    </div>
                                    <Button
                                        asChild
                                        size="sm"
                                        className="mt-4 w-full gap-1.5"
                                    >
                                        <Link
                                            href={lessonRoute({
                                                track: continueLesson.track,
                                                lesson: continueLesson.id,
                                            })}
                                        >
                                            <Play className="size-3.5" />
                                            Continue lesson
                                        </Link>
                                    </Button>
                                </>
                            ) : (
                                <Button
                                    asChild
                                    size="sm"
                                    className="mt-4 w-full"
                                >
                                    <Link href={learnRoute()}>
                                        Browse tracks
                                    </Link>
                                </Button>
                            )}
                        </section>

                        <section className="rounded-lg border border-border/90 bg-card p-5">
                            <div className="flex items-center justify-between">
                                <span className="meta-label">Local workflow</span>
                                <Terminal className="size-4 text-muted-foreground" />
                            </div>

                            <h2 className="display-title mt-2 text-2xl">
                                arena CLI
                            </h2>
                            <p className="mt-1 text-xs leading-5 text-muted-foreground">
                                Fetch the workspace, compile the public suite,
                                then dispatch your solution.
                            </p>

                            <div className="mt-4 space-y-2">
                                {[
                                    `arena init ${nextProblem?.id ?? 'two-sum'}`,
                                    'arena run',
                                    'arena submit',
                                ].map((command) => (
                                    <div
                                        key={command}
                                        className="flex items-center justify-between gap-2 rounded-sm border border-border/80 bg-background/70 px-2.5 py-1.5"
                                    >
                                        <code className="overflow-x-auto font-mono text-[11px]">
                                            <span className="mr-1.5 text-emerald-700 dark:text-emerald-400">
                                                $
                                            </span>
                                            {command}
                                        </code>
                                        <button
                                            type="button"
                                            onClick={() => void copy(command)}
                                            className="shrink-0 text-muted-foreground hover:text-foreground"
                                            aria-label={`Copy ${command}`}
                                        >
                                            {copiedText === command ? (
                                                <Check className="size-3 text-emerald-600" />
                                            ) : (
                                                <Copy className="size-3" />
                                            )}
                                        </button>
                                    </div>
                                ))}
                            </div>

                            <Button
                                asChild
                                variant="outline"
                                size="sm"
                                className="mt-4 w-full bg-transparent"
                            >
                                <Link href={setupRoute()}>
                                    Setup guide
                                    <ArrowRight className="size-3.5" />
                                </Link>
                            </Button>
                        </section>
                    </aside>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Today',
            href: dashboard(),
        },
    ],
};
