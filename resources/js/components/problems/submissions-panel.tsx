import { Activity, Trophy } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { useMemo } from 'react';
import { buildSubmissionOverview } from '@/lib/problem-community';
import type { ProblemSummary } from '@/types';
import type {
    SubmissionOverview,
    SubmissionStatus,
} from '@/lib/problem-community';
import {
    BarChart,
    ChartCard,
    PanelHeader,
    StatCard,
} from '@/components/problems/panel-ui';

const statusStyles: Record<SubmissionStatus, string> = {
    Accepted:
        'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
    'Wrong Answer':
        'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30',
    'Time Limit Exceeded':
        'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30',
    'Memory Limit Exceeded':
        'bg-violet-500/10 text-violet-700 dark:text-violet-400 border-violet-500/30',
    'Runtime Error':
        'bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-500/30',
    'Compile Error':
        'bg-slate-500/10 text-slate-700 dark:text-slate-300 border-slate-500/30',
};

const statusDotColors: Record<SubmissionStatus, string> = {
    Accepted: 'bg-emerald-500',
    'Wrong Answer': 'bg-rose-500',
    'Time Limit Exceeded': 'bg-amber-500',
    'Memory Limit Exceeded': 'bg-violet-500',
    'Runtime Error': 'bg-sky-500',
    'Compile Error': 'bg-slate-500',
};

export function SubmissionsPanel({ problem }: { problem: ProblemSummary }) {
    const overview: SubmissionOverview = useMemo(
        () => buildSubmissionOverview(problem),
        [problem],
    );

    return (
        <div className="space-y-6">
            <PanelHeader
                icon={Activity}
                title="Submissions"
                description="A sample of how submissions and runtimes look once the online judge is connected."
                badge="Sample data"
            />

            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    label="Total submissions"
                    value={overview.total.toLocaleString()}
                    hint="Across all accepted languages"
                />
                <StatCard
                    label="Accepted"
                    value={`${overview.acceptance.toFixed(1)}%`}
                    hint={`${overview.accepted.toLocaleString()} accepted`}
                    accent
                />
                <StatCard
                    label="Median runtime"
                    value={`${overview.medianRuntime} ms`}
                    hint="Among accepted submissions"
                />
                <StatCard
                    label="Fastest"
                    value={`${overview.bestRuntime} ms`}
                    hint="Top accepted submission"
                />
            </div>

            <div className="grid gap-4 lg:grid-cols-[1.4fr_1fr]">
                <ChartCard
                    title="Runtime distribution"
                    description="Accepted submissions grouped by runtime."
                >
                    <BarChart
                        data={overview.runtimeBuckets.map((bucket) => ({
                            label: bucket.label,
                            value: bucket.count.toLocaleString(),
                            percent: bucket.percent,
                        }))}
                        tone="sky"
                    />
                </ChartCard>

                <ChartCard
                    title="Status breakdown"
                    description="All submissions by final verdict."
                >
                    <div className="flex h-3 overflow-hidden rounded-full bg-muted">
                        {overview.statuses.map((status) => (
                            <div
                                key={status.status}
                                className={cn(
                                    'h-full',
                                    statusDotColors[status.status],
                                )}
                                style={{
                                    width: `${Math.max(0, status.percent)}%`,
                                }}
                                title={`${status.status}: ${status.count}`}
                            />
                        ))}
                    </div>
                    <ul className="mt-4 space-y-2">
                        {overview.statuses.map((status) => (
                            <li
                                key={status.status}
                                className="flex items-center justify-between gap-3 text-xs"
                            >
                                <div className="flex items-center gap-2">
                                    <span
                                        className={cn(
                                            'size-2 rounded-full',
                                            statusDotColors[status.status],
                                        )}
                                    />
                                    <span className="font-medium">
                                        {status.status}
                                    </span>
                                </div>
                                <span className="text-muted-foreground tabular-nums">
                                    {status.count.toLocaleString()} ·{' '}
                                    {status.percent.toFixed(1)}%
                                </span>
                            </li>
                        ))}
                    </ul>
                </ChartCard>
            </div>

            <section className="space-y-3">
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <h3 className="text-base font-semibold">
                        Top accepted submissions
                    </h3>
                    <Badge variant="secondary" className="text-[11px]">
                        Fastest runtimes
                    </Badge>
                </div>

                <div className="overflow-x-auto rounded-xl border bg-card">
                    <table className="w-full min-w-[680px] text-sm">
                        <thead className="border-b bg-muted/40 text-xs text-muted-foreground">
                            <tr>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    Rank
                                </th>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    User
                                </th>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    Language
                                </th>
                                <th className="px-4 py-2.5 text-right font-medium">
                                    Runtime
                                </th>
                                <th className="px-4 py-2.5 text-right font-medium">
                                    Memory
                                </th>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    Submitted
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y">
                            {overview.top.map((submission) => (
                                <tr
                                    key={`${submission.rank}-${submission.user}`}
                                    className="transition-colors hover:bg-muted/30"
                                >
                                    <td className="px-4 py-2.5 font-mono text-xs tabular-nums">
                                        #{submission.rank}
                                    </td>
                                    <td className="px-4 py-2.5">
                                        <div className="flex items-center gap-2">
                                            <span className="font-medium">
                                                {submission.user}
                                            </span>
                                            <Badge
                                                variant="outline"
                                                className={cn(
                                                    'text-[10px]',
                                                    statusStyles[
                                                        submission.status
                                                    ],
                                                )}
                                            >
                                                {submission.status}
                                            </Badge>
                                        </div>
                                    </td>
                                    <td className="px-4 py-2.5 text-xs text-muted-foreground">
                                        {submission.language}
                                    </td>
                                    <td className="px-4 py-2.5 text-right font-mono tabular-nums">
                                        {submission.runtime} ms
                                    </td>
                                    <td className="px-4 py-2.5 text-right font-mono text-muted-foreground tabular-nums">
                                        {submission.memory.toFixed(1)} MB
                                    </td>
                                    <td className="px-4 py-2.5 text-xs text-muted-foreground">
                                        {submission.submitted}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </section>

            <section className="space-y-3">
                <h3 className="text-base font-semibold">Language usage</h3>
                <div className="grid gap-3 sm:grid-cols-3">
                    {overview.languages.map((language) => (
                        <div
                            key={language.name}
                            className="rounded-lg border bg-card/40 p-3"
                        >
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-sm font-medium">
                                    {language.name}
                                </span>
                                <span className="text-xs text-muted-foreground tabular-nums">
                                    {language.percent.toFixed(0)}%
                                </span>
                            </div>
                            <div className="mt-2 h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    className="h-full rounded-full bg-primary"
                                    style={{
                                        width: `${language.percent}%`,
                                    }}
                                />
                            </div>
                            <p className="mt-2 text-xs text-muted-foreground tabular-nums">
                                {language.count.toLocaleString()} submissions
                            </p>
                        </div>
                    ))}
                </div>
            </section>

            <p className="text-xs text-muted-foreground">
                <Trophy className="mr-1 inline size-3.5 align-middle" />
                Submission data is illustrative until the judge is connected.
            </p>
        </div>
    );
}
