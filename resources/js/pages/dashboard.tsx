import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Code2, Cpu, Sparkles, Trophy } from 'lucide-react';
import { dashboard } from '@/routes';
import { index as problemsetRoute } from '@/routes/problemset';
import { show as problemRoute } from '@/routes/problems';

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                {/* Quick start guide Header Section */}
                <div className="rounded-2xl border border-black/5 bg-card p-6 shadow-xs md:p-8 dark:border-white/10">
                    <div className="flex flex-col gap-2">
                        <div className="flex items-center gap-2">
                            <span className="flex size-6 items-center justify-center rounded-lg bg-emerald-500/15 font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                &gt;_
                            </span>
                            <span className="font-mono text-xs font-bold tracking-wider text-emerald-600 uppercase dark:text-emerald-400">
                                Getting Started
                            </span>
                        </div>
                        <h1 className="font-mono text-2xl font-normal tracking-tight text-foreground md:text-3xl">
                            Quick start guide
                        </h1>
                        <p className="max-w-3xl text-sm leading-relaxed text-muted-foreground md:text-base">
                            Welcome to learnCP. Follow these steps to start
                            practicing algorithmic challenges, test code against
                            the judge, and track your contest progress.
                        </p>
                    </div>

                    {/* 3 Step Cards */}
                    <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <div className="flex flex-col justify-between rounded-xl border border-black/5 bg-muted/30 p-4 transition-all hover:border-black/15 dark:border-white/10 dark:hover:border-white/20">
                            <div>
                                <div className="flex size-8 items-center justify-center rounded-lg bg-emerald-500/15 font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    01
                                </div>
                                <h3 className="mt-3 font-mono text-sm font-semibold text-foreground">
                                    Pick a Problem
                                </h3>
                                <p className="mt-1 text-xs leading-relaxed text-muted-foreground">
                                    Explore the curated problemset spanning
                                    arrays, intervals, caches, and graph theory.
                                </p>
                            </div>
                            <Link
                                href={problemsetRoute()}
                                className="mt-4 inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-emerald-600 hover:underline dark:text-emerald-400"
                            >
                                <span>Browse problemset</span>
                                <ArrowRight className="size-3" />
                            </Link>
                        </div>

                        <div className="flex flex-col justify-between rounded-xl border border-black/5 bg-muted/30 p-4 transition-all hover:border-black/15 dark:border-white/10 dark:hover:border-white/20">
                            <div>
                                <div className="flex size-8 items-center justify-center rounded-lg bg-emerald-500/15 font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    02
                                </div>
                                <h3 className="mt-3 font-mono text-sm font-semibold text-foreground">
                                    Read &amp; Code
                                </h3>
                                <p className="mt-1 text-xs leading-relaxed text-muted-foreground">
                                    Analyze constraints, leverage starter code
                                    in C++, Python, or JavaScript, and test with
                                    sample cases.
                                </p>
                            </div>
                            <Link
                                href={problemRoute('two-sum')}
                                className="mt-4 inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-emerald-600 hover:underline dark:text-emerald-400"
                            >
                                <span>Try Two Sum</span>
                                <ArrowRight className="size-3" />
                            </Link>
                        </div>

                        <div className="flex flex-col justify-between rounded-xl border border-black/5 bg-muted/30 p-4 transition-all hover:border-black/15 dark:border-white/10 dark:hover:border-white/20">
                            <div>
                                <div className="flex size-8 items-center justify-center rounded-lg bg-emerald-500/15 font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    03
                                </div>
                                <h3 className="mt-3 font-mono text-sm font-semibold text-foreground">
                                    Benchmark &amp; Learn
                                </h3>
                                <p className="mt-1 text-xs leading-relaxed text-muted-foreground">
                                    Review step-by-step editorials, inspect
                                    runtime percentile distributions, and climb
                                    the leaderboard.
                                </p>
                            </div>
                            <Link
                                href={problemRoute('lru-cache')}
                                className="mt-4 inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-emerald-600 hover:underline dark:text-emerald-400"
                            >
                                <span>Inspect LRU Cache</span>
                                <ArrowRight className="size-3" />
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Progress / Stat Metrics */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div className="rounded-xl border border-black/5 bg-card p-4 shadow-xs dark:border-white/10">
                        <div className="flex items-center justify-between">
                            <span className="font-mono text-xs font-semibold text-muted-foreground uppercase">
                                Problems Available
                            </span>
                            <Code2 className="size-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div className="mt-2 font-mono text-2xl font-bold text-foreground">
                            8
                        </div>
                        <p className="mt-1 text-xs text-muted-foreground">
                            Curated algorithmic challenges
                        </p>
                    </div>

                    <div className="rounded-xl border border-black/5 bg-card p-4 shadow-xs dark:border-white/10">
                        <div className="flex items-center justify-between">
                            <span className="font-mono text-xs font-semibold text-muted-foreground uppercase">
                                Execution Latency
                            </span>
                            <Cpu className="size-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div className="mt-2 font-mono text-2xl font-bold text-foreground">
                            &lt; 10ms
                        </div>
                        <p className="mt-1 text-xs text-muted-foreground">
                            Online judge verification speed
                        </p>
                    </div>

                    <div className="rounded-xl border border-black/5 bg-card p-4 shadow-xs dark:border-white/10">
                        <div className="flex items-center justify-between">
                            <span className="font-mono text-xs font-semibold text-muted-foreground uppercase">
                                Practice Track
                            </span>
                            <Sparkles className="size-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div className="mt-2 font-mono text-2xl font-bold text-foreground">
                            Quant &amp; CP
                        </div>
                        <p className="mt-1 text-xs text-muted-foreground">
                            High-performance systems
                        </p>
                    </div>

                    <div className="rounded-xl border border-black/5 bg-card p-4 shadow-xs dark:border-white/10">
                        <div className="flex items-center justify-between">
                            <span className="font-mono text-xs font-semibold text-muted-foreground uppercase">
                                Arena Status
                            </span>
                            <Trophy className="size-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div className="mt-2 font-mono text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            Active
                        </div>
                        <p className="mt-1 text-xs text-muted-foreground">
                            Judge ready for submissions
                        </p>
                    </div>
                </div>

                {/* Featured Problem Quick Links */}
                <div className="rounded-2xl border border-black/5 bg-card p-6 shadow-xs dark:border-white/10">
                    <div className="flex items-center justify-between border-b border-border/70 pb-4">
                        <div>
                            <h2 className="font-mono text-lg font-bold text-foreground">
                                Recommended Challenges
                            </h2>
                            <p className="text-xs text-muted-foreground">
                                Hand-picked algorithmic and systems engineering
                                problems
                            </p>
                        </div>
                        <Link
                            href={problemsetRoute()}
                            className="inline-flex items-center gap-1 font-mono text-xs font-semibold text-emerald-600 hover:underline dark:text-emerald-400"
                        >
                            <span>View all</span>
                            <ArrowRight className="size-3" />
                        </Link>
                    </div>

                    <div className="mt-4 divide-y divide-border/60">
                        <div className="flex items-center justify-between py-3">
                            <div className="flex items-center gap-3">
                                <span className="font-mono text-xs text-muted-foreground">
                                    #01
                                </span>
                                <div>
                                    <Link
                                        href={problemRoute('two-sum')}
                                        className="font-mono text-sm font-semibold text-foreground hover:text-emerald-600 hover:underline dark:hover:text-emerald-400"
                                    >
                                        Two Sum
                                    </Link>
                                    <p className="text-xs text-muted-foreground">
                                        Hash map lookup with O(n) time
                                        complexity
                                    </p>
                                </div>
                            </div>
                            <span className="rounded-full bg-emerald-500/10 px-2.5 py-0.5 font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                Easy
                            </span>
                        </div>

                        <div className="flex items-center justify-between py-3">
                            <div className="flex items-center gap-3">
                                <span className="font-mono text-xs text-muted-foreground">
                                    #56
                                </span>
                                <div>
                                    <Link
                                        href={problemRoute('merge-intervals')}
                                        className="font-mono text-sm font-semibold text-foreground hover:text-emerald-600 hover:underline dark:hover:text-emerald-400"
                                    >
                                        Merge Intervals
                                    </Link>
                                    <p className="text-xs text-muted-foreground">
                                        Interval sorting and continuous range
                                        merging
                                    </p>
                                </div>
                            </div>
                            <span className="rounded-full bg-amber-500/10 px-2.5 py-0.5 font-mono text-xs font-bold text-amber-600 dark:text-amber-400">
                                Medium
                            </span>
                        </div>

                        <div className="flex items-center justify-between py-3">
                            <div className="flex items-center gap-3">
                                <span className="font-mono text-xs text-muted-foreground">
                                    #146
                                </span>
                                <div>
                                    <Link
                                        href={problemRoute('lru-cache')}
                                        className="font-mono text-sm font-semibold text-foreground hover:text-emerald-600 hover:underline dark:hover:text-emerald-400"
                                    >
                                        LRU Cache
                                    </Link>
                                    <p className="text-xs text-muted-foreground">
                                        Doubly linked list with O(1) eviction
                                        and hash map handles
                                    </p>
                                </div>
                            </div>
                            <span className="rounded-full bg-rose-500/10 px-2.5 py-0.5 font-mono text-xs font-bold text-rose-600 dark:text-rose-400">
                                Hard
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
