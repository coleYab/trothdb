import { Trophy } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { useMemo } from 'react';
import { buildStandings } from '@/lib/problem-community';
import type { Problem } from '@/types';
import type { StandingRow } from '@/lib/problem-community';
import {
    BarChart,
    ChartCard,
    PanelHeader,
    StatCard,
} from '@/components/problems/panel-ui';

const medalColors = ['text-amber-500', 'text-slate-400', 'text-amber-700'];

export function StandingsPanel({ problem }: { problem: Problem }) {
    const standings: StandingRow[] = useMemo(
        () => buildStandings(problem),
        [problem],
    );

    const top = standings.slice(0, 6);
    const maximumScore = Math.max(...standings.map((row) => row.score), 1);
    const totalSolved = standings.reduce((sum, row) => sum + row.solved, 0);

    return (
        <div className="space-y-6">
            <PanelHeader
                icon={Trophy}
                title="Standings"
                description="Sample contest-style standings for people who have solved this problem."
                badge="Sample data"
            />

            <div className="grid gap-3 sm:grid-cols-3">
                <StatCard
                    label="Participants"
                    value={standings.length.toString()}
                    hint="Sample leaderboard"
                />
                <StatCard
                    label="Total solves"
                    value={totalSolved.toString()}
                    hint="Across the leaderboard"
                />
                <StatCard
                    label="Top score"
                    value={standings[0]?.score.toString() ?? '0'}
                    hint={`${standings[0]?.user ?? ''} leads`}
                    accent
                />
            </div>

            <div className="grid gap-4 lg:grid-cols-[1.15fr_1fr]">
                <ChartCard
                    title="Top scores"
                    description="Relative scores across the top six standings."
                >
                    <BarChart
                        data={top.map((row) => ({
                            label: row.user,
                            value: row.score,
                            percent: (row.score / maximumScore) * 100,
                            meta: `#${row.rank}`,
                        }))}
                        tone="amber"
                    />
                </ChartCard>

                <ChartCard
                    title="Leader snapshot"
                    description="The top three solvers at a glance."
                >
                    <div className="grid gap-3">
                        {standings.slice(0, 3).map((row, index) => (
                            <div
                                key={row.user}
                                className="flex items-center gap-3 rounded-lg border bg-card/40 p-3"
                            >
                                <Trophy
                                    className={cn(
                                        'size-5 shrink-0',
                                        medalColors[index],
                                    )}
                                />
                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center justify-between gap-2">
                                        <span className="truncate text-sm font-medium">
                                            {row.user}
                                        </span>
                                        <span className="font-mono text-sm tabular-nums">
                                            {row.score}
                                        </span>
                                    </div>
                                    <div className="mt-0.5 flex items-center justify-between gap-2 text-xs text-muted-foreground">
                                        <span>
                                            {row.solved} solved · {row.penalty}{' '}
                                            min penalty
                                        </span>
                                        <span>{row.lastAccepted}</span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </ChartCard>
            </div>

            <section className="space-y-3">
                <h3 className="text-base font-semibold">Leaderboard</h3>
                <div className="overflow-x-auto rounded-xl border bg-card">
                    <table className="w-full min-w-[620px] text-sm">
                        <thead className="border-b bg-muted/40 text-xs text-muted-foreground">
                            <tr>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    Rank
                                </th>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    User
                                </th>
                                <th className="px-4 py-2.5 text-right font-medium">
                                    Score
                                </th>
                                <th className="px-4 py-2.5 text-right font-medium">
                                    Solved
                                </th>
                                <th className="px-4 py-2.5 text-right font-medium">
                                    Penalty
                                </th>
                                <th className="px-4 py-2.5 text-left font-medium">
                                    Last accepted
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y">
                            {standings.map((row) => (
                                <tr
                                    key={row.user}
                                    className="transition-colors hover:bg-muted/30"
                                >
                                    <td className="px-4 py-2.5 font-mono text-xs tabular-nums">
                                        #{row.rank}
                                    </td>
                                    <td className="px-4 py-2.5">
                                        <div className="flex items-center gap-2">
                                            <Badge
                                                variant="secondary"
                                                className="size-7 justify-center rounded-full p-0 text-[10px]"
                                            >
                                                {row.initials}
                                            </Badge>
                                            <span className="font-medium">
                                                {row.user}
                                            </span>
                                        </div>
                                    </td>
                                    <td className="px-4 py-2.5 text-right font-mono tabular-nums">
                                        {row.score}
                                    </td>
                                    <td className="px-4 py-2.5 text-right tabular-nums">
                                        {row.solved}
                                    </td>
                                    <td className="px-4 py-2.5 text-right text-muted-foreground tabular-nums">
                                        {row.penalty} min
                                    </td>
                                    <td className="px-4 py-2.5 text-xs text-muted-foreground">
                                        {row.lastAccepted}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    );
}
