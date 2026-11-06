import { Head, Link, router } from '@inertiajs/react';
import {
    ArrowUpDown,
    ArrowUpRight,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Circle,
    Plus,
    RotateCcw,
    Search,
    Shuffle,
    X,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { DifficultyBadge } from '@/components/difficulty-badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';
import { show as problemRoute } from '@/routes/problems';
import type { Difficulty, ProblemSummary } from '@/types';

type SortOption = 'default' | 'success-asc' | 'success-desc' | 'title' | 'points';
type StatusFilter = 'All' | 'Todo' | 'Solved';

const PAGE_SIZE = 12;

const sortLabels: Record<SortOption, string> = {
    default: 'Problem number',
    'success-desc': 'Acceptance: high to low',
    'success-asc': 'Acceptance: low to high',
    title: 'Title A–Z',
    points: 'Points: high to low',
};

function readParam(key: string, fallback: string): string {
    if (typeof window === 'undefined') return fallback;
    return new URLSearchParams(window.location.search).get(key) ?? fallback;
}

export default function ProblemsetIndex({
    problems,
    solvedProblemIds = [],
}: {
    problems: ProblemSummary[];
    solvedProblemIds?: string[];
}) {
    const [query, setQuery] = useState(() => readParam('q', ''));
    const [difficulty, setDifficulty] = useState<Difficulty | 'All'>(
        () => readParam('difficulty', 'All') as Difficulty | 'All',
    );
    const [discipline, setDiscipline] = useState(() =>
        readParam('track', 'All'),
    );
    const [status, setStatus] = useState<StatusFilter>(
        () => readParam('status', 'Todo') as StatusFilter,
    );
    const [sortBy, setSortBy] = useState<SortOption>(
        () => readParam('sort', 'default') as SortOption,
    );
    const [page, setPage] = useState(1);

    const [solvedIds, setSolvedIds] = useState<string[]>(() => {
        let local: string[] = [];
        try {
            const raw = localStorage.getItem('solved_problems');
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) local = parsed;
            }
        } catch {}
        return Array.from(new Set([...solvedProblemIds, ...local]));
    });

    useEffect(() => {
        try {
            localStorage.setItem('solved_problems', JSON.stringify(solvedIds));
        } catch {}
    }, [solvedIds]);

    useEffect(() => {
        if (typeof window === 'undefined') return;
        const params = new URLSearchParams();
        if (query) params.set('q', query);
        if (difficulty !== 'All') params.set('difficulty', difficulty);
        if (discipline !== 'All') params.set('track', discipline);
        if (status !== 'Todo') params.set('status', status);
        if (sortBy !== 'default') params.set('sort', sortBy);
        const suffix = params.toString();
        window.history.replaceState(
            null,
            '',
            suffix ? `?${suffix}` : window.location.pathname,
        );
    }, [query, difficulty, discipline, status, sortBy]);

    useEffect(() => {
        setPage(1);
    }, [query, difficulty, discipline, status, sortBy]);

    const counts = useMemo(() => {
        const solved = new Set(solvedIds);
        const easy = problems.filter((problem) => problem.difficulty === 'Easy');
        const medium = problems.filter(
            (problem) => problem.difficulty === 'Medium',
        );
        const hard = problems.filter((problem) => problem.difficulty === 'Hard');

        return {
            total: problems.length,
            solved: problems.filter((problem) => solved.has(problem.id)).length,
            easy: `${easy.filter((problem) => solved.has(problem.id)).length}/${easy.length}`,
            medium: `${medium.filter((problem) => solved.has(problem.id)).length}/${medium.length}`,
            hard: `${hard.filter((problem) => solved.has(problem.id)).length}/${hard.length}`,
            points: problems.reduce(
                (total, problem) => total + (problem.total_points ?? 100),
                0,
            ),
        };
    }, [problems, solvedIds]);

    const disciplines = useMemo(() => {
        const set = new Set<string>();
        problems.forEach((problem) => {
            if (problem.discipline) set.add(problem.discipline);
        });
        return Array.from(set).sort();
    }, [problems]);

    const hasFilters =
        query !== '' ||
        difficulty !== 'All' ||
        discipline !== 'All' ||
        status !== 'Todo' ||
        sortBy !== 'default';

    const reset = () => {
        setQuery('');
        setDifficulty('All');
        setDiscipline('All');
        setStatus('All');
        setSortBy('default');
    };

    const filtered = useMemo(() => {
        const needle = query.trim().toLowerCase();
        const solved = new Set(solvedIds);

        const list = problems.filter((problem) => {
            if (difficulty !== 'All' && problem.difficulty !== difficulty) {
                return false;
            }
            if (
                discipline !== 'All' &&
                problem.discipline !== discipline
            ) {
                return false;
            }
            if (status === 'Solved' && !solved.has(problem.id)) return false;
            if (status === 'Todo' && solved.has(problem.id)) return false;
            if (!needle) return true;

            return (
                problem.title.toLowerCase().includes(needle) ||
                problem.id.toLowerCase().includes(needle) ||
                (problem.discipline ?? '').toLowerCase().includes(needle) ||
                problem.tags.some((tag) =>
                    tag.toLowerCase().includes(needle),
                )
            );
        });

        switch (sortBy) {
            case 'success-asc':
                return [...list].sort(
                    (a, b) => a.acceptance_rate - b.acceptance_rate,
                );
            case 'success-desc':
                return [...list].sort(
                    (a, b) => b.acceptance_rate - a.acceptance_rate,
                );
            case 'title':
                return [...list].sort((a, b) =>
                    a.title.localeCompare(b.title),
                );
            case 'points':
                return [...list].sort(
                    (a, b) => (b.total_points ?? 0) - (a.total_points ?? 0),
                );
            default:
                return list;
        }
    }, [problems, query, difficulty, discipline, status, sortBy, solvedIds]);

    const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
    const safePage = Math.min(page, totalPages);
    const visible = filtered.slice(
        (safePage - 1) * PAGE_SIZE,
        safePage * PAGE_SIZE,
    );

    const pageNumbers = useMemo(() => {
        const start = Math.max(1, Math.min(safePage - 2, totalPages - 4));
        return Array.from(
            { length: Math.min(5, totalPages) },
            (_, index) => start + index,
        );
    }, [safePage, totalPages]);

    const pickRandom = () => {
        const pool = filtered.length > 0 ? filtered : problems;
        if (pool.length > 0) {
            const random = pool[Math.floor(Math.random() * pool.length)];
            router.visit(problemRoute(random.id).url);
        }
    };

    const nextProblem =
        problems.find((problem) => !solvedIds.includes(problem.id)) ??
        problems[0];

    return (
        <>
            <Head title="Problemset" />

            <div className="mx-auto w-full max-w-7xl flex-1 space-y-6 p-4 md:p-8">
                <header className="flex flex-col gap-5 border-b border-foreground/15 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div className="space-y-3">
                        <p className="meta-label flex items-center gap-2">
                            <span className="inline-block size-1.5 bg-emerald-600" />
                            Index / {counts.total} drills · {counts.points} points
                        </p>
                        <h1 className="display-title text-4xl sm:text-5xl">
                            Problemset
                        </h1>
                        <p className="max-w-2xl text-sm leading-6 text-muted-foreground">
                            Algorithmic and low-latency systems problems. Every
                            submission is compiled as C++20 and graded by
                            individual assertion cases.
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            asChild
                            className="gap-1.5 bg-transparent"
                        >
                            <Link href="/problems/create">
                                <Plus className="size-3.5" />
                                New problem
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={pickRandom}
                            className="gap-1.5 bg-transparent"
                        >
                            <Shuffle className="size-3.5" />
                            Pick random
                        </Button>
                        <Button size="sm" asChild className="gap-1.5">
                            <Link href={problemRoute(nextProblem?.id ?? 'two-sum')}>
                                Resume solving
                                <ArrowUpRight className="size-3.5" />
                            </Link>
                        </Button>
                    </div>
                </header>

                <section className="grid grid-cols-2 gap-4 border-b border-border/80 pb-5 sm:grid-cols-3 lg:grid-cols-6">
                    <div>
                        <p className="meta-label">Solved</p>
                        <p className="display-title mt-1 text-3xl">
                            {counts.solved}
                            <span className="text-lg text-muted-foreground">
                                /{counts.total}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p className="meta-label">Easy</p>
                        <p className="display-title mt-1 text-3xl">
                            {counts.easy}
                        </p>
                    </div>
                    <div>
                        <p className="meta-label">Medium</p>
                        <p className="display-title mt-1 text-3xl">
                            {counts.medium}
                        </p>
                    </div>
                    <div>
                        <p className="meta-label">Hard</p>
                        <p className="display-title mt-1 text-3xl">
                            {counts.hard}
                        </p>
                    </div>
                    <div className="hidden lg:block">
                        <p className="meta-label">Judge</p>
                        <p className="mt-2 flex items-center gap-1.5 font-mono text-xs">
                            <span className="size-1.5 rounded-full bg-emerald-500" />
                            GCC 14 · C++20
                        </p>
                    </div>
                    <div className="hidden lg:block">
                        <p className="meta-label">Latency</p>
                        <p className="mt-2 font-mono text-xs">2s · 256 MB</p>
                    </div>
                </section>

                <div className="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div className="flex flex-wrap items-center gap-2">
                        <div className="flex items-center gap-1 rounded-md border border-border/90 bg-card p-0.5">
                            {(
                                [
                                    ['All', 'All'],
                                    ['Todo', 'Todo'],
                                    ['Solved', 'Solved'],
                                ] as const
                            ).map(([value, label]) => (
                                <button
                                    key={value}
                                    type="button"
                                    onClick={() => setStatus(value)}
                                    className={cn(
                                        'rounded-sm px-2.5 py-1.5 font-mono text-[11px] transition-colors',
                                        status === value
                                            ? 'bg-foreground text-background'
                                            : 'text-muted-foreground hover:text-foreground',
                                    )}
                                >
                                    {label}
                                </button>
                            ))}
                        </div>

                        <div className="flex items-center gap-1">
                            {(['Easy', 'Medium', 'Hard'] as Difficulty[]).map(
                                (level) => (
                                    <button
                                        key={level}
                                        type="button"
                                        onClick={() =>
                                            setDifficulty(
                                                difficulty === level
                                                    ? 'All'
                                                    : level,
                                            )
                                        }
                                        className={cn(
                                            'rounded-sm border px-2 py-1.5 font-mono text-[11px] transition-colors',
                                            difficulty === level
                                                ? 'border-foreground/40 bg-foreground/5 text-foreground'
                                                : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground',
                                        )}
                                    >
                                        {level}
                                    </button>
                                ),
                            )}
                        </div>

                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className={cn(
                                        'h-8 gap-1.5 bg-transparent font-mono text-[11px] font-normal',
                                        discipline !== 'All' &&
                                            'border-foreground/40',
                                    )}
                                >
                                    {discipline === 'All'
                                        ? 'All tracks'
                                        : discipline}
                                    <ChevronRight className="size-3 rotate-90 opacity-60" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent
                                align="start"
                                className="max-h-72 overflow-y-auto"
                            >
                                <DropdownMenuItem
                                    onClick={() => setDiscipline('All')}
                                >
                                    All tracks
                                </DropdownMenuItem>
                                {disciplines.map((item) => (
                                    <DropdownMenuItem
                                        key={item}
                                        onClick={() => setDiscipline(item)}
                                    >
                                        {item}
                                    </DropdownMenuItem>
                                ))}
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <div className="relative min-w-56 flex-1 sm:max-w-xs">
                            <Search className="pointer-events-none absolute top-1/2 left-2.5 size-3.5 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                value={query}
                                onChange={(event) =>
                                    setQuery(event.target.value)
                                }
                                placeholder="Search title, tag, or slug"
                                className="h-8 rounded-md border-border/90 bg-card pr-8 pl-8 font-mono text-xs"
                                aria-label="Search problems"
                            />
                            {query && (
                                <button
                                    type="button"
                                    onClick={() => setQuery('')}
                                    className="absolute top-1/2 right-2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    aria-label="Clear search"
                                >
                                    <X className="size-3.5" />
                                </button>
                            )}
                        </div>

                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="h-8 gap-1.5 bg-transparent font-mono text-[11px] font-normal"
                                >
                                    <ArrowUpDown className="size-3.5 opacity-60" />
                                    Sort
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                {(
                                    Object.keys(sortLabels) as SortOption[]
                                ).map((option) => (
                                    <DropdownMenuItem
                                        key={option}
                                        onClick={() => setSortBy(option)}
                                        className={cn(
                                            sortBy === option &&
                                                'bg-accent font-medium',
                                        )}
                                    >
                                        {sortLabels[option]}
                                    </DropdownMenuItem>
                                ))}
                            </DropdownMenuContent>
                        </DropdownMenu>

                        {hasFilters && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={reset}
                                className="h-8 gap-1.5 px-2 font-mono text-[11px] text-muted-foreground"
                            >
                                <RotateCcw className="size-3.5" />
                                Reset
                            </Button>
                        )}
                    </div>
                </div>

                <div className="overflow-hidden rounded-lg border border-border/90 bg-card">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-border/80 bg-muted/30">
                                    <th className="w-10 py-2.5 pl-4">
                                        <span className="sr-only">Status</span>
                                    </th>
                                    <th className="py-2.5 pr-3 font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase">
                                        #
                                    </th>
                                    <th className="py-2.5 pr-3 font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase">
                                        Problem
                                    </th>
                                    <th className="hidden py-2.5 pr-3 font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase md:table-cell">
                                        Track
                                    </th>
                                    <th className="py-2.5 pr-3 font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase">
                                        Level
                                    </th>
                                    <th className="hidden py-2.5 pr-3 text-right font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase sm:table-cell">
                                        Accepted
                                    </th>
                                    <th className="hidden py-2.5 pr-3 text-right font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase md:table-cell">
                                        Pts
                                    </th>
                                    <th className="w-8 pr-4" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/70">
                                {visible.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="px-6 py-16 text-center">
                                            <p className="text-sm font-medium">
                                                Nothing matches these filters
                                            </p>
                                            <p className="mt-1 text-xs text-muted-foreground">
                                                Clear a filter or search for a
                                                different tag.
                                            </p>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={reset}
                                                className="mt-4 bg-transparent"
                                            >
                                                Reset filters
                                            </Button>
                                        </td>
                                    </tr>
                                ) : (
                                    visible.map((problem) => {
                                        const isSolved = solvedIds.includes(
                                            problem.id,
                                        );

                                        return (
                                            <tr
                                                key={problem.id}
                                                onClick={() =>
                                                    router.visit(
                                                        problemRoute(problem.id)
                                                            .url,
                                                    )
                                                }
                                                className="group cursor-pointer transition-colors hover:bg-muted/40"
                                            >
                                                <td className="py-3 pl-4">
                                                    {isSolved ? (
                                                        <CheckCircle2 className="size-4 text-emerald-600 dark:text-emerald-400" />
                                                    ) : (
                                                        <Circle className="size-4 text-muted-foreground/30" />
                                                    )}
                                                </td>
                                                <td className="py-3 pr-3 font-mono text-[11px] text-muted-foreground tabular-nums">
                                                    {String(
                                                        problem.number,
                                                    ).padStart(2, '0')}
                                                </td>
                                                <td className="max-w-md py-3 pr-3">
                                                    <Link
                                                        href={problemRoute(
                                                            problem.id,
                                                        )}
                                                        onClick={(event) =>
                                                            event.stopPropagation()
                                                        }
                                                        className="block"
                                                    >
                                                        <span className="text-sm font-medium transition-colors group-hover:text-emerald-700 dark:group-hover:text-emerald-400">
                                                            {problem.title}
                                                        </span>
                                                        <span className="mt-0.5 block truncate text-[11px] text-muted-foreground">
                                                            {problem.topic ??
                                                                problem.tags[0]}
                                                            {' · '}
                                                            {problem.time_limit_ms ??
                                                                2000}
                                                            ms
                                                        </span>
                                                    </Link>
                                                </td>
                                                <td className="hidden py-3 pr-3 md:table-cell">
                                                    <span className="rounded-sm border border-border/80 bg-background px-2 py-0.5 font-mono text-[10px] text-muted-foreground">
                                                        {problem.discipline ??
                                                            'Algorithms'}
                                                    </span>
                                                </td>
                                                <td className="py-3 pr-3">
                                                    <DifficultyBadge
                                                        difficulty={
                                                            problem.difficulty
                                                        }
                                                    />
                                                </td>
                                                <td className="hidden py-3 pr-3 text-right font-mono text-[11px] text-muted-foreground tabular-nums sm:table-cell">
                                                    {problem.acceptance_rate.toFixed(
                                                        1,
                                                    )}
                                                    %
                                                </td>
                                                <td className="hidden py-3 pr-3 text-right font-mono text-[11px] tabular-nums md:table-cell">
                                                    {problem.total_points ?? 100}
                                                </td>
                                                <td className="pr-4">
                                                    <ArrowUpRight className="size-3.5 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100" />
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>

                    {filtered.length > 0 && (
                        <div className="flex flex-wrap items-center justify-between gap-3 border-t border-border/80 px-4 py-2.5">
                            <span className="font-mono text-[10px] text-muted-foreground">
                                Showing{' '}
                                {Math.min(
                                    (safePage - 1) * PAGE_SIZE + 1,
                                    filtered.length,
                                )}
                                –
                                {Math.min(
                                    safePage * PAGE_SIZE,
                                    filtered.length,
                                )}{' '}
                                of {filtered.length}
                            </span>

                            <div className="flex items-center gap-1">
                                <button
                                    type="button"
                                    onClick={() =>
                                        setPage((value) =>
                                            Math.max(1, value - 1),
                                        )
                                    }
                                    disabled={safePage <= 1}
                                    className="inline-flex size-7 items-center justify-center rounded-sm border border-border/80 text-muted-foreground transition-colors hover:bg-muted disabled:opacity-30"
                                    aria-label="Previous page"
                                >
                                    <ChevronLeft className="size-3.5" />
                                </button>

                                {pageNumbers.map((number) => (
                                    <button
                                        key={number}
                                        type="button"
                                        onClick={() => setPage(number)}
                                        className={cn(
                                            'inline-flex size-7 items-center justify-center rounded-sm border font-mono text-[11px] transition-colors',
                                            number === safePage
                                                ? 'border-foreground bg-foreground text-background'
                                                : 'border-border/80 text-muted-foreground hover:bg-muted',
                                        )}
                                    >
                                        {number}
                                    </button>
                                ))}

                                <button
                                    type="button"
                                    onClick={() =>
                                        setPage((value) =>
                                            Math.min(totalPages, value + 1),
                                        )
                                    }
                                    disabled={safePage >= totalPages}
                                    className="inline-flex size-7 items-center justify-center rounded-sm border border-border/80 text-muted-foreground transition-colors hover:bg-muted disabled:opacity-30"
                                    aria-label="Next page"
                                >
                                    <ChevronRight className="size-3.5" />
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

ProblemsetIndex.layout = {
    breadcrumbs: [
        {
            title: 'Problemset',
            href: '/problemset',
        },
    ],
};
