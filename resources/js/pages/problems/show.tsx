import { Head, Link, setLayoutProps } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    Check,
    CheckCircle2,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Clock,
    Copy,
    Cpu,
    HardDrive,
    Lightbulb,
    ListChecks,
    Lock,
    SquareTerminal,
    Unlock,
    Wrench,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import { Callout } from '@/components/callout';
import { DifficultyBadge } from '@/components/difficulty-badge';
import { CommentsPanel } from '@/components/problems/comments-panel';
import { EditorialPanel } from '@/components/problems/editorial-panel';
import { ProblemTabBar } from '@/components/problems/problem-tab-bar';
import type { ProblemTab } from '@/components/problems/problem-tab-bar';
import { StandingsPanel } from '@/components/problems/standings-panel';
import { SubmissionsPanel } from '@/components/problems/submissions-panel';
import type { UserCodeSubmission } from '@/components/problems/submissions-panel';
import { Button } from '@/components/ui/button';
import { useClipboard } from '@/hooks/use-clipboard';
import { cn } from '@/lib/utils';
import { show as problemRoute } from '@/routes/problems';
import { index as problemsetRoute } from '@/routes/problemset';
import { index as setupRoute } from '@/routes/setup';
import type { Difficulty, Problem } from '@/types';

type ProblemNav = {
    id: string;
    number: number;
    title: string;
    difficulty: Difficulty;
};

type SuiteCase = {
    id: number;
    case_id: string;
    name: string;
    group: string;
    points: number;
    time_limit_ms: number;
    hidden: boolean;
    code?: string | null;
};

type UserSubmission = {
    id: number;
    language: string;
    status: string;
    runtime_ms: number;
    memory_mb: number;
    created_at: string;
    score?: number | null;
};

interface ProblemShowProps {
    problem: Problem;
    isSolved?: boolean;
    submissions?: UserCodeSubmission[];
    suiteCases?: SuiteCase[];
    prevProblem?: ProblemNav | null;
    nextProblem?: ProblemNav | null;
    position?: number;
    totalProblems?: number;
    initialSubmissions?: UserSubmission[];
}

export default function ProblemShow({
    problem,
    isSolved: initialIsSolved = false,
    submissions = [],
    suiteCases = [],
    prevProblem,
    nextProblem,
    position = 1,
    totalProblems = 18,
    initialSubmissions = [],
}: ProblemShowProps) {
    const [activeTab, setActiveTab] = useState<ProblemTab>('description');
    const [communitySubmissions] = useState<UserCodeSubmission[]>(submissions);

    const [isSolved] = useState<boolean>(() => {
        if (initialIsSolved) return true;
        if (typeof window === 'undefined') return false;
        try {
            const solved = JSON.parse(
                localStorage.getItem('solved_problems') || '[]',
            );
            return Array.isArray(solved) && solved.includes(problem.id);
        } catch {
            return false;
        }
    });

    const starterCppCode = problem.starter_code?.cpp ?? '';

    const suite = useMemo(() => {
        const publicCases = suiteCases.filter((item) => !item.hidden);
        const hiddenCases = suiteCases.filter((item) => item.hidden);
        const points = suiteCases.reduce(
            (total, item) => total + item.points,
            0,
        );

        return {
            publicCases,
            hiddenCases,
            total: suiteCases.length,
            points: points || (problem.total_points ?? 100),
        };
    }, [suiteCases, problem.total_points]);

    useEffect(() => {
        setLayoutProps({
            breadcrumbs: [
                {
                    title: 'Problemset',
                    href: problemsetRoute(),
                },
                {
                    title: problem.title,
                    href: problemRoute(problem.id),
                },
            ],
        });
    }, [problem.id, problem.title]);

    return (
        <>
            <Head title={problem.title} />

            <div className="mx-auto flex w-full max-w-[86rem] flex-1 flex-col gap-6 p-4 md:p-6 lg:p-8">
                <div className="flex flex-wrap items-center justify-between gap-3 border-b border-border/80 pb-3">
                    <div className="flex flex-wrap items-center gap-2.5">
                        <Link
                            href={problemsetRoute()}
                            className="inline-flex items-center gap-1.5 font-mono text-[11px] text-muted-foreground transition-colors hover:text-foreground"
                        >
                            <ArrowLeft className="size-3.5" />
                            Problemset
                        </Link>
                        <span className="text-border">/</span>
                        <span className="font-mono text-[11px] text-foreground">
                            {String(position).padStart(2, '0')}
                            <span className="text-muted-foreground">
                                /{String(totalProblems).padStart(2, '0')}
                            </span>
                        </span>
                        <DifficultyBadge difficulty={problem.difficulty} />
                        {isSolved && (
                            <span className="inline-flex items-center gap-1 rounded-sm border border-emerald-600/30 bg-emerald-600/10 px-2 py-0.5 font-mono text-[10px] tracking-wider text-emerald-800 uppercase dark:text-emerald-400">
                                <CheckCircle2 className="size-3" />
                                Solved
                            </span>
                        )}
                    </div>

                    <div className="flex items-center gap-1.5">
                        <Button
                            variant="ghost"
                            size="sm"
                            asChild
                            className="h-7 gap-1 px-2 font-mono text-[11px] text-muted-foreground"
                        >
                            <Link
                                href={
                                    prevProblem
                                        ? problemRoute(prevProblem.id)
                                        : problemRoute(problem.id)
                                }
                                aria-disabled={!prevProblem}
                                className={cn(
                                    !prevProblem && 'pointer-events-none opacity-40',
                                )}
                            >
                                <ChevronLeft className="size-3.5" />
                                Prev
                            </Link>
                        </Button>
                        <Button
                            variant="ghost"
                            size="sm"
                            asChild
                            className="h-7 gap-1 px-2 font-mono text-[11px] text-muted-foreground"
                        >
                            <Link
                                href={
                                    nextProblem
                                        ? problemRoute(nextProblem.id)
                                        : problemRoute(problem.id)
                                }
                                aria-disabled={!nextProblem}
                                className={cn(
                                    !nextProblem && 'pointer-events-none opacity-40',
                                )}
                            >
                                Next
                                <ChevronRight className="size-3.5" />
                            </Link>
                        </Button>
                    </div>
                </div>

                <header className="space-y-3">
                    <div className="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <span className="font-mono text-[11px] text-muted-foreground">
                            Problem {problem.number}
                        </span>
                        <span className="font-mono text-[11px] text-muted-foreground">
                            · {problem.discipline}
                        </span>
                    </div>
                    <h1 className="display-title max-w-3xl text-4xl text-foreground sm:text-5xl">
                        {problem.title}
                    </h1>
                    <p className="max-w-3xl text-sm leading-6 text-muted-foreground">
                        {problem.summary}
                    </p>

                    <div className="flex flex-wrap items-center gap-x-5 gap-y-2 pt-1 font-mono text-[11px] text-muted-foreground">
                        <span className="inline-flex items-center gap-1.5">
                            <TrophyIcon />
                            {suite.points} points
                        </span>
                        <span className="inline-flex items-center gap-1.5">
                            <ListChecks className="size-3.5" />
                            {suite.total} assertion cases
                        </span>
                        <span className="inline-flex items-center gap-1.5">
                            <Clock className="size-3.5" />
                            {problem.testing?.time_limit ?? '2s limit'}
                        </span>
                        <span className="inline-flex items-center gap-1.5">
                            <HardDrive className="size-3.5" />
                            {problem.testing?.memory_limit ?? '256 MB'}
                        </span>
                        <span className="inline-flex items-center gap-1.5">
                            <Cpu className="size-3.5" />
                            C++20
                        </span>
                        <span>{problem.acceptance_rate.toFixed(1)}% accepted</span>
                    </div>

                    {problem.tags.length > 0 && (
                        <div className="flex flex-wrap gap-1.5 pt-1">
                            {problem.tags.map((tag) => (
                                <span
                                    key={tag}
                                    className="rounded-sm border border-border/80 bg-card px-2 py-0.5 font-mono text-[10px] text-muted-foreground"
                                >
                                    {tag}
                                </span>
                            ))}
                        </div>
                    )}
                </header>

                {isSolved && (
                    <section className="flex flex-col gap-3 rounded-lg border border-emerald-600/25 bg-emerald-600/5 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div className="flex items-center gap-3">
                            <CheckCircle2 className="size-4 text-emerald-700 dark:text-emerald-400" />
                            <p className="text-xs text-foreground">
                                Solved. Editorial and community solutions are
                                unlocked.
                            </p>
                        </div>
                        <div className="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => setActiveTab('editorial')}
                                className="h-7 bg-transparent text-xs"
                            >
                                Read editorial
                            </Button>
                            {nextProblem && (
                                <Button size="sm" asChild className="h-7 text-xs">
                                    <Link href={problemRoute(nextProblem.id)}>
                                        Next problem
                                        <ArrowRight className="size-3.5" />
                                    </Link>
                                </Button>
                            )}
                        </div>
                    </section>
                )}

                {/* Mobile action bar: keep the CLI loop one tap away while reading */}
                <div className="flex items-center gap-2 rounded-md border border-border/90 bg-card p-2 xl:hidden">
                    <CopyCommand command={`arena init ${problem.id}`} className="flex-1" />
                    <Button
                        variant="outline"
                        size="sm"
                        asChild
                        className="h-8 shrink-0 bg-transparent"
                    >
                        <Link href={setupRoute()}>Setup</Link>
                    </Button>
                </div>

                <ProblemTabBar
                    active={activeTab}
                    onChange={setActiveTab}
                    isSolved={isSolved}
                />

                {activeTab === 'description' ? (
                    <div className="grid gap-8 xl:grid-cols-[minmax(0,1fr)_21rem] xl:gap-10">
                        <article className="min-w-0 space-y-10">
                            <section className="scroll-mt-20 space-y-4" id="description">
                                <SectionTitle index="01" title="Description" />
                                <div className="space-y-4 text-[14.5px] leading-7 text-foreground/90">
                                    {problem.description.map((paragraph, index) => (
                                        <p key={index}>{paragraph}</p>
                                    ))}
                                </div>
                            </section>

                            <section className="scroll-mt-20 space-y-4" id="examples">
                                <SectionTitle index="02" title="Examples" />
                                <div className="space-y-4">
                                    {problem.examples.map((example, index) => (
                                        <div
                                            key={index}
                                            className="overflow-hidden rounded-md border border-border/90"
                                        >
                                            <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-3.5 py-2">
                                                <span className="meta-label">
                                                    Example {index + 1}
                                                </span>
                                                {example.explanation && (
                                                    <span className="hidden max-w-md truncate text-[11px] text-muted-foreground sm:block">
                                                        {example.explanation}
                                                    </span>
                                                )}
                                            </div>

                                            <div className="grid divide-y divide-slate-800 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                                                <div className="bg-slate-950 p-3.5">
                                                    <div className="mb-1.5 flex items-center justify-between">
                                                        <span className="font-mono text-[10px] tracking-wider text-slate-500 uppercase">
                                                            Input
                                                        </span>
                                                        <CopyIcon value={example.input} />
                                                    </div>
                                                    <pre className="overflow-x-auto font-mono text-xs leading-6 whitespace-pre-wrap text-slate-100">
                                                        <code>{example.input}</code>
                                                    </pre>
                                                </div>
                                                <div className="bg-slate-900 p-3.5">
                                                    <div className="mb-1.5 flex items-center justify-between">
                                                        <span className="font-mono text-[10px] tracking-wider text-slate-500 uppercase">
                                                            Output
                                                        </span>
                                                        <CopyIcon value={example.output} />
                                                    </div>
                                                    <pre className="overflow-x-auto font-mono text-xs leading-6 whitespace-pre-wrap text-emerald-200">
                                                        <code>{example.output}</code>
                                                    </pre>
                                                </div>
                                            </div>

                                            {example.explanation && (
                                                <p className="border-t border-border/80 px-3.5 py-2 text-xs leading-5 text-muted-foreground sm:hidden">
                                                    {example.explanation}
                                                </p>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </section>

                            <section className="scroll-mt-20 space-y-4" id="constraints">
                                <SectionTitle index="03" title="Constraints" />
                                <ul className="divide-y divide-border/70 rounded-md border border-border/90 bg-card">
                                    {problem.constraints.map((constraint) => (
                                        <li
                                            key={constraint}
                                            className="flex items-start gap-3 px-3.5 py-2.5"
                                        >
                                            <span className="mt-2 size-1 shrink-0 rounded-full bg-muted-foreground/50" />
                                            <code className="font-mono text-xs leading-6 text-foreground/90">
                                                {constraint}
                                            </code>
                                        </li>
                                    ))}
                                </ul>

                                {problem.follow_up && (
                                    <Callout variant="info" title="Follow-up">
                                        {problem.follow_up}
                                    </Callout>
                                )}
                            </section>

                            {starterCppCode && (
                                <section className="scroll-mt-20 space-y-4" id="starter">
                                    <SectionTitle
                                        index="04"
                                        title="Starter signature"
                                        aside={
                                            <span className="font-mono text-[10px] text-muted-foreground">
                                                solution.cpp
                                            </span>
                                        }
                                    />

                                    <p className="text-xs leading-6 text-muted-foreground">
                                        The workspace ships this file. Keep the
                                        class and method names; the public
                                        assertion suite compiles directly
                                        against your implementation.
                                    </p>

                                    <div className="overflow-hidden rounded-md border border-slate-800">
                                        <div className="flex items-center justify-between border-b border-slate-800 bg-slate-900 px-3 py-1.5">
                                            <span className="font-mono text-[10px] tracking-wider text-slate-400">
                                                ./{problem.id}/solution.cpp
                                            </span>
                                            <CopyIcon
                                                value={starterCppCode}
                                                dark
                                            />
                                        </div>
                                        <pre className="overflow-x-auto bg-slate-950 p-4 font-mono text-xs leading-6 text-slate-100">
                                            <code>{starterCppCode}</code>
                                        </pre>
                                    </div>
                                </section>
                            )}

                            {problem.hints && problem.hints.length > 0 && (
                                <section className="scroll-mt-20 space-y-3" id="hints">
                                    <SectionTitle index="05" title="Hints" />
                                    <p className="text-xs text-muted-foreground">
                                        Open one at a time. Each hint narrows the
                                        bound without giving away the
                                        implementation.
                                    </p>
                                    <div className="divide-y divide-border/70 rounded-md border border-border/90 bg-card">
                                        {problem.hints.map((hint, index) => (
                                            <details
                                                key={hint}
                                                className="group px-3.5 py-2.5"
                                            >
                                                <summary className="flex cursor-pointer list-none items-center gap-2 text-xs font-medium">
                                                    <ChevronDown className="size-3.5 text-muted-foreground transition-transform group-open:rotate-180" />
                                                    <Lightbulb className="size-3.5 text-amber-600" />
                                                    Hint {index + 1}
                                                </summary>
                                                <p className="mt-2 pl-5 text-xs leading-6 text-muted-foreground">
                                                    {hint}
                                                </p>
                                            </details>
                                        ))}
                                    </div>
                                </section>
                            )}
                        </article>

                        <aside className="min-w-0 space-y-5 xl:sticky xl:top-20 xl:self-start">
                            <TerminalWorkflow problemId={problem.id} />

                            <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                                <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-4 py-2.5">
                                    <span className="meta-label">Test suite</span>
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        {suite.points} pts
                                    </span>
                                </div>

                                <div className="space-y-3 p-4">
                                    <div className="flex items-end justify-between">
                                        <div>
                                            <p className="meta-label">
                                                Public cases
                                            </p>
                                            <p className="display-title text-2xl">
                                                {suite.publicCases.length}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="meta-label">
                                                Hidden cases
                                            </p>
                                            <p className="display-title text-2xl">
                                                {suite.hiddenCases.length}
                                            </p>
                                        </div>
                                    </div>

                                    <div className="space-y-1">
                                        {suiteCases.map((testCase) => (
                                            <div
                                                key={testCase.id}
                                                className="flex items-center gap-2 border-t border-border/60 py-1.5"
                                            >
                                                {testCase.hidden ? (
                                                    <Lock className="size-3 shrink-0 text-muted-foreground" />
                                                ) : (
                                                    <Unlock className="size-3 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                                )}
                                                <span className="min-w-0 flex-1 truncate font-mono text-[10px] text-muted-foreground">
                                                    {testCase.hidden
                                                        ? `${testCase.group} / hidden`
                                                        : testCase.name}
                                                </span>
                                                <span className="shrink-0 font-mono text-[10px] text-foreground">
                                                    {testCase.points}
                                                </span>
                                            </div>
                                        ))}
                                    </div>

                                    <p className="border-t border-border/70 pt-2 text-[10px] leading-4 text-muted-foreground">
                                        Hidden cases run on the judge. Public
                                        cases are included in the fetched
                                        workspace.
                                    </p>
                                </div>
                            </section>

                            <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                                <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-4 py-2.5">
                                    <span className="meta-label">
                                        Your attempts
                                    </span>
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        {initialSubmissions.length}
                                    </span>
                                </div>

                                <div className="p-4">
                                    {initialSubmissions.length === 0 ? (
                                        <p className="text-[11px] leading-5 text-muted-foreground">
                                            No attempts yet. Submit with{' '}
                                            <code className="rounded bg-muted px-1 py-0.5 font-mono text-[10px]">
                                                arena submit
                                            </code>{' '}
                                            after the public suite passes.
                                        </p>
                                    ) : (
                                        <div className="space-y-1">
                                            {initialSubmissions
                                                .slice(0, 4)
                                                .map((submission) => (
                                                    <div
                                                        key={submission.id}
                                                        className="flex items-center gap-2 border-t border-border/60 py-1.5 first:border-t-0 first:pt-0"
                                                    >
                                                        <span
                                                            className={cn(
                                                                'font-mono text-[10px] uppercase',
                                                                submission.status ===
                                                                    'accepted'
                                                                    ? 'text-emerald-700 dark:text-emerald-400'
                                                                    : 'text-rose-700 dark:text-rose-400',
                                                            )}
                                                        >
                                                            {submission.status.replace(
                                                                /_/g,
                                                                ' ',
                                                            )}
                                                        </span>
                                                        <span className="ml-auto font-mono text-[10px] text-muted-foreground">
                                                            {submission.score !=
                                                            null
                                                                ? `${Math.round(submission.score)}/100`
                                                                : '—'}
                                                        </span>
                                                    </div>
                                                ))}
                                        </div>
                                    )}
                                </div>
                            </section>

                            <section className="rounded-lg border border-border/90 bg-card p-4">
                                <p className="meta-label">Judge profile</p>
                                <dl className="mt-2 space-y-1.5 text-[11px]">
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Compiler
                                        </dt>
                                        <dd className="font-mono">GCC 14</dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Standard
                                        </dt>
                                        <dd className="font-mono">C++20</dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Flags
                                        </dt>
                                        <dd className="font-mono">-O2 -pipe</dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Sanitizers
                                        </dt>
                                        <dd className="font-mono">ASan / UBSan</dd>
                                    </div>
                                </dl>
                            </section>
                        </aside>
                    </div>
                ) : (
                    <div className="max-w-5xl">
                        {activeTab === 'submissions' && (
                            <div
                                id="problem-tab-submissions"
                                role="tabpanel"
                                aria-labelledby="problem-tab-trigger-submissions"
                            >
                                <SubmissionsPanel
                                    problem={problem}
                                    submissions={communitySubmissions}
                                />
                            </div>
                        )}

                        {activeTab === 'editorial' && (
                            <div
                                id="problem-tab-editorial"
                                role="tabpanel"
                                aria-labelledby="problem-tab-trigger-editorial"
                            >
                                <EditorialPanel problem={problem} />
                            </div>
                        )}

                        {activeTab === 'comments' && (
                            <div
                                id="problem-tab-comments"
                                role="tabpanel"
                                aria-labelledby="problem-tab-trigger-comments"
                            >
                                <CommentsPanel problem={problem} />
                            </div>
                        )}

                        {activeTab === 'standings' && (
                            <div
                                id="problem-tab-standings"
                                role="tabpanel"
                                aria-labelledby="problem-tab-trigger-standings"
                            >
                                <StandingsPanel problem={problem} />
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
}

function TrophyIcon() {
    return (
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            className="size-3.5"
            aria-hidden="true"
        >
            <path d="M8 4h8v4a4 4 0 0 1-8 0V4Z" />
            <path d="M8 6H5v1a3 3 0 0 0 3 3M16 6h3v1a3 3 0 0 1-3 3" />
            <path d="M12 12v5M9 20h6M10 17h4" />
        </svg>
    );
}

function SectionTitle({
    index,
    title,
    aside,
}: {
    index: string;
    title: string;
    aside?: ReactNode;
}) {
    return (
        <div className="flex items-end justify-between gap-3 border-b border-foreground/15 pb-2">
            <h2 className="flex items-baseline gap-2.5">
                <span className="font-mono text-[10px] text-muted-foreground">
                    {index}
                </span>
                <span className="display-title text-2xl text-foreground">
                    {title}
                </span>
            </h2>
            {aside}
        </div>
    );
}

function CopyIcon({ value, dark }: { value: string; dark?: boolean }) {
    const [copiedText, copy] = useClipboard();
    const copied = copiedText === value && value !== '';

    return (
        <button
            type="button"
            onClick={() => void copy(value)}
            className={cn(
                'inline-flex items-center gap-1 font-mono text-[10px] transition-colors',
                dark
                    ? 'text-slate-400 hover:text-slate-100'
                    : 'text-slate-500 hover:text-slate-100',
            )}
            aria-label={copied ? 'Copied' : 'Copy code'}
        >
            {copied ? (
                <>
                    <Check className="size-3 text-emerald-400" />
                    Copied
                </>
            ) : (
                <>
                    <Copy className="size-3" />
                    Copy
                </>
            )}
        </button>
    );
}

function CopyCommand({
    command,
    className,
}: {
    command: string;
    className?: string;
}) {
    const [copiedText, copy] = useClipboard();
    const copied = copiedText === command;

    return (
        <button
            type="button"
            onClick={() => void copy(command)}
            className={cn(
                'flex items-center gap-2 rounded-sm border border-border/80 bg-background px-2.5 py-1.5 text-left font-mono text-[11px] transition-colors hover:border-foreground/30',
                className,
            )}
        >
            <span className="text-emerald-700 dark:text-emerald-400">$</span>
            <span className="min-w-0 flex-1 truncate">{command}</span>
            {copied ? (
                <Check className="size-3 shrink-0 text-emerald-600" />
            ) : (
                <Copy className="size-3 shrink-0 text-muted-foreground" />
            )}
        </button>
    );
}

function TerminalWorkflow({ problemId }: { problemId: string }) {
    const steps = [
        {
            label: 'Fetch workspace',
            command: `arena init ${problemId}`,
        },
        {
            label: 'Implement solution',
            command: `code ./${problemId}`,
        },
        {
            label: 'Run public suite',
            command: 'arena run',
        },
        {
            label: 'Submit to judge',
            command: 'arena submit',
        },
    ];

    return (
        <section className="overflow-hidden rounded-lg border border-foreground/20 bg-card">
            <div className="flex items-center justify-between border-b border-foreground/15 bg-foreground px-4 py-2.5 text-background">
                <span className="font-mono text-[10px] tracking-[0.16em] uppercase">
                    Solve locally
                </span>
                <SquareTerminal className="size-3.5 opacity-70" />
            </div>

            <div className="divide-y divide-border/70">
                {steps.map((step, index) => (
                    <div key={step.command} className="px-3.5 py-2.5">
                        <div className="flex items-center gap-2">
                            <span className="font-mono text-[10px] text-muted-foreground">
                                {String(index + 1).padStart(2, '0')}
                            </span>
                            <span className="text-[11px] font-medium">
                                {step.label}
                            </span>
                        </div>
                        <div className="mt-1.5">
                            <CopyCommand command={step.command} />
                        </div>
                    </div>
                ))}
            </div>

            <div className="border-t border-border/80 px-3.5 py-2.5">
                <p className="text-[10px] leading-4 text-muted-foreground">
                    First time? Install the CLI and authenticate this device.
                </p>
                <Button
                    variant="outline"
                    size="sm"
                    asChild
                    className="mt-2 h-7 w-full gap-1.5 bg-transparent text-[11px]"
                >
                    <Link href={setupRoute()}>
                        <Wrench className="size-3" />
                        CLI setup guide
                    </Link>
                </Button>
            </div>
        </section>
    );
}

ProblemShow.layout = {
    breadcrumbs: [
        {
            title: 'Problemset',
            href: '/problemset',
        },
    ],
};
