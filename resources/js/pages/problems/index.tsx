import { Head, Link } from '@inertiajs/react';
import { CheckCircle2, ListFilter, Search } from 'lucide-react';
import { useMemo, useState } from 'react';
import { DifficultyBadge } from '@/components/difficulty-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';
import { show as problemRoute } from '@/routes/problems';
import { index as problemsetRoute } from '@/routes/problemset';
import type { ProblemSummary } from '@/types';

const difficultyFilters = ['All', 'Easy', 'Medium', 'Hard'] as const;

type DifficultyFilter = (typeof difficultyFilters)[number];

export default function ProblemsetIndex({
    problems,
}: {
    problems: ProblemSummary[];
}) {
    const [query, setQuery] = useState('');
    const [difficulty, setDifficulty] = useState<DifficultyFilter>('All');

    const counts = useMemo(
        () => ({
            total: problems.length,
            Easy: problems.filter((problem) => problem.difficulty === 'Easy')
                .length,
            Medium: problems.filter(
                (problem) => problem.difficulty === 'Medium',
            ).length,
            Hard: problems.filter((problem) => problem.difficulty === 'Hard')
                .length,
        }),
        [problems],
    );

    const filteredProblems = useMemo(() => {
        const needle = query.trim().toLowerCase();

        return problems.filter((problem) => {
            const matchesDifficulty =
                difficulty === 'All' || problem.difficulty === difficulty;

            if (!matchesDifficulty) {
                return false;
            }

            if (needle === '') {
                return true;
            }

            return (
                problem.title.toLowerCase().includes(needle) ||
                problem.id.toLowerCase().includes(needle) ||
                problem.tags.some((tag) => tag.toLowerCase().includes(needle))
            );
        });
    }, [problems, query, difficulty]);

    return (
        <>
            <Head title="Problemset" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div className="space-y-1">
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Problemset
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Practise curated algorithm problems with detailed
                            statements, examples and judge instructions.
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                        <Badge
                            variant="outline"
                            className="gap-1.5 px-2.5 py-1"
                        >
                            <ListFilter className="size-3.5" />
                            {counts.total} problems
                        </Badge>
                        <Badge
                            variant="outline"
                            className="border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-emerald-700 dark:text-emerald-400"
                        >
                            {counts.Easy} easy
                        </Badge>
                        <Badge
                            variant="outline"
                            className="border-amber-500/30 bg-amber-500/10 px-2.5 py-1 text-amber-700 dark:text-amber-400"
                        >
                            {counts.Medium} medium
                        </Badge>
                        <Badge
                            variant="outline"
                            className="border-rose-500/30 bg-rose-500/10 px-2.5 py-1 text-rose-700 dark:text-rose-400"
                        >
                            {counts.Hard} hard
                        </Badge>
                    </div>
                </div>

                <div className="flex flex-col gap-3 rounded-xl border bg-card p-4 lg:flex-row lg:items-center lg:justify-between">
                    <div className="relative w-full lg:max-w-sm">
                        <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={query}
                            onChange={(event) => setQuery(event.target.value)}
                            placeholder="Search by title or tag..."
                            className="pl-9"
                            aria-label="Search problems"
                        />
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        {difficultyFilters.map((filter) => (
                            <Button
                                key={filter}
                                type="button"
                                size="sm"
                                variant={
                                    difficulty === filter
                                        ? 'default'
                                        : 'outline'
                                }
                                onClick={() => setDifficulty(filter)}
                            >
                                {filter}
                            </Button>
                        ))}
                    </div>
                </div>

                <div className="overflow-hidden rounded-xl border bg-card">
                    <div className="hidden grid-cols-[3.5rem_minmax(0,1fr)_11rem_7rem] gap-4 border-b bg-muted/50 px-5 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase lg:grid">
                        <span>#</span>
                        <span>Title</span>
                        <span>Acceptance</span>
                        <span className="text-right">Difficulty</span>
                    </div>

                    {filteredProblems.length === 0 ? (
                        <div className="flex flex-col items-center gap-2 px-6 py-16 text-center">
                            <p className="font-medium">No problems found</p>
                            <p className="text-sm text-muted-foreground">
                                Try a different search term or difficulty
                                filter.
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y">
                            {filteredProblems.map((problem) => (
                                <ProblemRow
                                    key={problem.id}
                                    problem={problem}
                                />
                            ))}
                        </div>
                    )}
                </div>

                <p className="text-xs text-muted-foreground">
                    Selecting a problem opens its full statement, worked
                    examples, starter code and the testing protocol used by the
                    judge.
                </p>
            </div>
        </>
    );
}

function ProblemRow({ problem }: { problem: ProblemSummary }) {
    return (
        <Link
            href={problemRoute(problem.id)}
            className="group grid grid-cols-[2.5rem_minmax(0,1fr)_auto] items-center gap-4 px-4 py-4 transition-colors hover:bg-muted/50 lg:grid-cols-[3.5rem_minmax(0,1fr)_11rem_7rem] lg:px-5"
        >
            <span className="self-start font-mono text-sm text-muted-foreground lg:self-center">
                {problem.number}
            </span>

            <div className="min-w-0">
                <div className="flex items-center gap-2">
                    <span className="font-medium transition-colors group-hover:text-primary">
                        {problem.title}
                    </span>
                    <CheckCircle2 className="size-4 shrink-0 text-muted-foreground/40 opacity-0 transition-opacity group-hover:opacity-100" />
                </div>

                <p className="mt-1 line-clamp-1 text-sm text-muted-foreground">
                    {problem.summary}
                </p>

                <div className="mt-2 flex flex-wrap items-center gap-1.5 lg:hidden">
                    {problem.tags.map((tag) => (
                        <Badge
                            key={tag}
                            variant="secondary"
                            className="text-[11px]"
                        >
                            {tag}
                        </Badge>
                    ))}
                    <span className="text-xs text-muted-foreground">
                        {formatAcceptance(problem.acceptance_rate)} accepted
                    </span>
                </div>

                <div className="mt-2 hidden flex-wrap gap-1.5 lg:flex">
                    {problem.tags.map((tag) => (
                        <Badge
                            key={tag}
                            variant="secondary"
                            className="text-[11px]"
                        >
                            {tag}
                        </Badge>
                    ))}
                </div>
            </div>

            <div className="hidden items-center gap-2 lg:flex">
                <div className="h-1.5 w-16 overflow-hidden rounded-full bg-muted">
                    <div
                        className={cn(
                            'h-full rounded-full',
                            problem.difficulty === 'Easy' && 'bg-emerald-500',
                            problem.difficulty === 'Medium' && 'bg-amber-500',
                            problem.difficulty === 'Hard' && 'bg-rose-500',
                        )}
                        style={{
                            width: `${Math.min(problem.acceptance_rate, 100)}%`,
                        }}
                    />
                </div>
                <span className="text-sm text-muted-foreground tabular-nums">
                    {formatAcceptance(problem.acceptance_rate)}
                </span>
            </div>

            <div className="flex justify-end self-start lg:self-center">
                <DifficultyBadge difficulty={problem.difficulty} />
            </div>
        </Link>
    );
}

function formatAcceptance(rate: number): string {
    return `${rate.toFixed(1)}%`;
}

ProblemsetIndex.layout = {
    breadcrumbs: [
        {
            title: 'Problemset',
            href: problemsetRoute(),
        },
    ],
};
