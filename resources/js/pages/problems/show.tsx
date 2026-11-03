import { Head, setLayoutProps } from '@inertiajs/react';
import {
    Check,
    ChevronRight,
    Clock,
    Copy,
    Cpu,
    FlaskConical,
    HardDrive,
    Lightbulb,
    ListChecks,
    Send,
    ShieldCheck,
    Terminal,
} from 'lucide-react';
import { useEffect, useMemo, useState, type ReactNode } from 'react';
import { toast } from 'sonner';
import { Callout } from '@/components/callout';
import { CommentsPanel } from '@/components/problems/comments-panel';
import { EditorialPanel } from '@/components/problems/editorial-panel';
import { ProblemTabBar } from '@/components/problems/problem-tab-bar';
import type { ProblemTab } from '@/components/problems/problem-tab-bar';
import { StandingsPanel } from '@/components/problems/standings-panel';
import { SubmissionsPanel } from '@/components/problems/submissions-panel';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useClipboard } from '@/hooks/use-clipboard';
import { cn } from '@/lib/utils';
import { show as problemRoute } from '@/routes/problems';
import { index as problemsetRoute } from '@/routes/problemset';
import type { Problem } from '@/types';

const languageLabels: Record<string, string> = {
    cpp: 'C++',
    python: 'Python',
    javascript: 'JavaScript',
    java: 'Java',
};

const codeKeywords: Record<string, Set<string>> = {
    cpp: new Set([
        'alignas',
        'auto',
        'bool',
        'break',
        'case',
        'catch',
        'char',
        'class',
        'const',
        'constexpr',
        'continue',
        'default',
        'delete',
        'do',
        'double',
        'else',
        'enum',
        'explicit',
        'false',
        'float',
        'for',
        'friend',
        'if',
        'inline',
        'int',
        'long',
        'namespace',
        'new',
        'noexcept',
        'nullptr',
        'operator',
        'pair',
        'private',
        'protected',
        'public',
        'queue',
        'return',
        'short',
        'signed',
        'size_t',
        'sizeof',
        'stack',
        'static',
        'string',
        'struct',
        'switch',
        'template',
        'this',
        'throw',
        'true',
        'try',
        'typedef',
        'typename',
        'union',
        'unordered_map',
        'unordered_set',
        'unsigned',
        'using',
        'vector',
        'virtual',
        'void',
        'volatile',
        'while',
    ]),
    python: new Set([
        'False',
        'None',
        'True',
        'and',
        'as',
        'assert',
        'bool',
        'break',
        'class',
        'continue',
        'def',
        'del',
        'dict',
        'elif',
        'else',
        'enumerate',
        'except',
        'finally',
        'float',
        'for',
        'from',
        'global',
        'if',
        'import',
        'in',
        'int',
        'is',
        'lambda',
        'len',
        'list',
        'nonlocal',
        'not',
        'or',
        'pass',
        'range',
        'raise',
        'return',
        'self',
        'set',
        'sorted',
        'str',
        'try',
        'tuple',
        'while',
        'with',
        'yield',
    ]),
    javascript: new Set([
        'async',
        'await',
        'break',
        'case',
        'catch',
        'class',
        'const',
        'continue',
        'debugger',
        'default',
        'delete',
        'do',
        'else',
        'export',
        'extends',
        'false',
        'finally',
        'for',
        'from',
        'function',
        'if',
        'import',
        'in',
        'instanceof',
        'let',
        'new',
        'null',
        'of',
        'return',
        'super',
        'switch',
        'this',
        'throw',
        'true',
        'try',
        'typeof',
        'undefined',
        'var',
        'void',
        'while',
        'yield',
    ]),
};

export default function ProblemShow({ problem }: { problem: Problem }) {
    const languages = Object.keys(problem.starter_code);
    const [language, setLanguage] = useState(languages[0] ?? '');
    const [copiedText, copy] = useClipboard();
    const [activeTab, setActiveTab] = useState<ProblemTab>('description');
    const [isSolved, setIsSolved] = useState<boolean>(() => {
        if (typeof window === 'undefined') {
            return false;
        }
        try {
            const solved = JSON.parse(
                localStorage.getItem('solved_problems') || '[]',
            );
            return Array.isArray(solved) && solved.includes(problem.id);
        } catch {
            return false;
        }
    });

    const code = problem.starter_code[language] ?? '';
    const copied = copiedText === code && code !== '';

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

    const handleSubmit = () => {
        setIsSolved(true);
        try {
            const solved = JSON.parse(
                localStorage.getItem('solved_problems') || '[]',
            );
            if (!solved.includes(problem.id)) {
                localStorage.setItem(
                    'solved_problems',
                    JSON.stringify([...solved, problem.id]),
                );
            }
        } catch {}

        toast.success('Solution submitted & accepted!', {
            description:
                'Problem marked as solved. Submissions, Comments, Standings, and Editorial unlocked.',
        });
    };

    return (
        <>
            <Head title={problem.title} />

            <div className="mx-auto flex w-full max-w-4xl flex-1 flex-col gap-8 p-4 md:p-8">
                {/* Header: Clean Problem Title & Summary */}
                <header className="space-y-4">
                    <div className="min-w-0 space-y-3">
                        <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                            {problem.title}
                        </h1>

                        <p className="max-w-2xl text-[15px] leading-7 text-muted-foreground">
                            {problem.summary}
                        </p>
                    </div>
                </header>

                {/* Tabs Bar: Hidden until the user has solved the problem */}
                {isSolved && (
                    <ProblemTabBar
                        active={activeTab}
                        onChange={setActiveTab}
                        isSolved={isSolved}
                    />
                )}

                <div className="min-h-[28rem]">
                    {(!isSolved || activeTab === 'description') && (
                        <article
                            id="problem-tab-description"
                            role="tabpanel"
                            aria-labelledby="problem-tab-trigger-description"
                            className="space-y-12"
                        >
                            <section className="space-y-4">
                                <SectionHeading
                                    icon={ListChecks}
                                    title="Description"
                                />
                                <div className="space-y-4 text-[15px] leading-8 text-foreground/90">
                                    {problem.description.map(
                                        (paragraph, index) => (
                                            <p key={index}>{paragraph}</p>
                                        ),
                                    )}
                                </div>
                            </section>

                            <section className="space-y-6">
                                <SectionHeading
                                    icon={Terminal}
                                    title="Examples"
                                />

                                <div className="space-y-8">
                                    {problem.examples.map((example, index) => (
                                        <div key={index} className="space-y-4">
                                            <div className="flex items-center gap-3">
                                                <h3 className="shrink-0 text-sm font-semibold">
                                                    Example {index + 1}
                                                </h3>
                                                <div className="h-px flex-1 bg-border" />
                                            </div>

                                            <div className="space-y-2">
                                                <p className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase">
                                                    Input
                                                </p>
                                                <CodeSnippet
                                                    value={example.input}
                                                />
                                            </div>

                                            <div className="space-y-2">
                                                <p className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase">
                                                    Output
                                                </p>
                                                <CodeSnippet
                                                    value={example.output}
                                                />
                                            </div>

                                            {example.explanation && (
                                                <p className="text-sm leading-7 text-muted-foreground">
                                                    <span className="font-medium text-foreground">
                                                        Explanation:{' '}
                                                    </span>
                                                    {example.explanation}
                                                </p>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </section>

                            <section className="space-y-4">
                                <SectionHeading
                                    icon={ListChecks}
                                    title="Constraints"
                                />

                                <ul className="space-y-2.5">
                                    {problem.constraints.map((constraint) => (
                                        <li
                                            key={constraint}
                                            className="flex items-start gap-3"
                                        >
                                            <span className="mt-2.5 size-1.5 shrink-0 rounded-full bg-muted-foreground/50" />
                                            <code className="rounded-md bg-muted px-2 py-0.5 font-mono text-[13px] leading-6 text-foreground">
                                                {constraint}
                                            </code>
                                        </li>
                                    ))}
                                </ul>

                                {problem.follow_up && (
                                    <Callout
                                        variant="info"
                                        title="Follow-up"
                                        className="mt-5"
                                    >
                                        {problem.follow_up}
                                    </Callout>
                                )}
                            </section>

                            <section className="space-y-4">
                                <SectionHeading
                                    icon={Cpu}
                                    title="Starter code"
                                />

                                <p className="text-sm leading-7 text-muted-foreground">
                                    Complete the function or class below. The
                                    judge calls it with the input described in
                                    the statement and reads only what the
                                    statement asks you to return or print.
                                </p>

                                <StarterCodePanel
                                    languages={languages}
                                    language={language}
                                    onLanguageChange={setLanguage}
                                    code={code}
                                    copied={copied}
                                    onCopy={() => void copy(code)}
                                />
                            </section>

                            <section className="space-y-5">
                                <SectionHeading
                                    icon={FlaskConical}
                                    title="How your solution is tested"
                                />

                                <p className="text-[15px] leading-8 text-foreground/90">
                                    {problem.testing.judge}
                                </p>

                                <div className="grid gap-3 sm:grid-cols-3">
                                    <MetaBlock
                                        icon={Clock}
                                        label="Time limit"
                                        value={problem.testing.time_limit}
                                    />
                                    <MetaBlock
                                        icon={HardDrive}
                                        label="Memory limit"
                                        value={problem.testing.memory_limit}
                                    />
                                    <MetaBlock
                                        icon={Cpu}
                                        label="Languages"
                                        value={problem.testing.languages.join(
                                            ', ',
                                        )}
                                    />
                                </div>

                                <Callout variant="warning" title="Judge notes">
                                    <ul className="space-y-2">
                                        {problem.testing.notes.map((note) => (
                                            <li
                                                key={note}
                                                className="flex items-start gap-2.5"
                                            >
                                                <span className="mt-2.5 size-1.5 shrink-0 rounded-full bg-current opacity-50" />
                                                <span>{note}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </Callout>

                                <div className="space-y-4">
                                    <h3 className="text-base font-semibold">
                                        Sample tests
                                    </h3>

                                    <div className="space-y-4">
                                        {problem.testing.sample_tests.map(
                                            (test) => (
                                                <div
                                                    key={test.name}
                                                    className="rounded-xl border bg-card/40 p-4"
                                                >
                                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                                        <span className="text-sm font-medium">
                                                            {test.name}
                                                        </span>
                                                        <Badge
                                                            variant="outline"
                                                            className={cn(
                                                                'text-[11px]',
                                                                test.hidden
                                                                    ? 'border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-400'
                                                                    : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                                                            )}
                                                        >
                                                            {test.hidden
                                                                ? 'Hidden test'
                                                                : 'Visible test'}
                                                        </Badge>
                                                    </div>

                                                    <div className="mt-3 grid gap-3 sm:grid-cols-2">
                                                        <div className="space-y-1.5">
                                                            <p className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase">
                                                                Input
                                                            </p>
                                                            <CodeSnippet
                                                                value={
                                                                    test.input
                                                                }
                                                            />
                                                        </div>
                                                        <div className="space-y-1.5">
                                                            <p className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase">
                                                                Expected output
                                                            </p>
                                                            <CodeSnippet
                                                                value={
                                                                    test.expected
                                                                }
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            ),
                                        )}
                                    </div>
                                </div>
                            </section>

                            <section className="space-y-3">
                                <SectionHeading
                                    icon={Lightbulb}
                                    title="Hints"
                                />

                                <p className="text-sm leading-7 text-muted-foreground">
                                    Open the hints one at a time. They progress
                                    from a gentle nudge to the full intended
                                    strategy.
                                </p>

                                <div className="space-y-2">
                                    {problem.hints.map((hint, index) => (
                                        <details
                                            key={hint}
                                            className="rounded-lg border bg-card/40 px-4 py-3"
                                        >
                                            <summary className="flex cursor-pointer list-none items-center gap-2 text-sm font-medium">
                                                <ChevronRight className="hint-chevron size-4 text-muted-foreground transition-transform" />
                                                <Lightbulb className="size-4 text-amber-500" />
                                                Hint {index + 1}
                                            </summary>
                                            <p className="mt-2 pl-10 text-sm leading-7 text-muted-foreground">
                                                {hint}
                                            </p>
                                        </details>
                                    ))}
                                </div>
                            </section>
                        </article>
                    )}

                    {isSolved && activeTab === 'editorial' && (
                        <div
                            id="problem-tab-editorial"
                            role="tabpanel"
                            aria-labelledby="problem-tab-trigger-editorial"
                        >
                            <EditorialPanel problem={problem} />
                        </div>
                    )}

                    {isSolved && activeTab === 'submissions' && (
                        <div
                            id="problem-tab-submissions"
                            role="tabpanel"
                            aria-labelledby="problem-tab-trigger-submissions"
                        >
                            <SubmissionsPanel problem={problem} />
                        </div>
                    )}

                    {isSolved && activeTab === 'comments' && (
                        <div
                            id="problem-tab-comments"
                            role="tabpanel"
                            aria-labelledby="problem-tab-trigger-comments"
                        >
                            <CommentsPanel problem={problem} />
                        </div>
                    )}

                    {isSolved && activeTab === 'standings' && (
                        <div
                            id="problem-tab-standings"
                            role="tabpanel"
                            aria-labelledby="problem-tab-trigger-standings"
                        >
                            <StandingsPanel problem={problem} />
                        </div>
                    )}
                </div>

                {/* Bottom Before You Submit Section */}
                <section className="rounded-xl border border-emerald-500/25 bg-emerald-500/5 p-5 md:p-6">
                    <div className="flex flex-wrap items-start justify-between gap-4">
                        <div className="space-y-1">
                            <h2 className="flex items-center gap-2 text-lg font-semibold">
                                <ShieldCheck className="size-5 text-emerald-500" />
                                Before you submit
                            </h2>
                            <p className="text-sm text-muted-foreground">
                                Run through this checklist so the judge sees
                                only the output it expects.
                            </p>
                        </div>

                        <Button
                            type="button"
                            className="gap-2"
                            onClick={handleSubmit}
                        >
                            <Send className="size-4" />
                            Submit solution
                        </Button>
                    </div>

                    <ul className="mt-4 grid gap-2.5 sm:grid-cols-2">
                        {[
                            'Handle empty inputs and single-element inputs.',
                            'Check the upper bounds for overflow.',
                            'Use the stated time and memory limits to estimate the required complexity.',
                            'Remove debug output before submitting.',
                        ].map((item) => (
                            <li
                                key={item}
                                className="flex items-start gap-2.5 text-sm leading-6 text-muted-foreground"
                            >
                                <Check className="mt-0.5 size-4 shrink-0 text-emerald-500" />
                                {item}
                            </li>
                        ))}
                    </ul>
                </section>
            </div>
        </>
    );
}

function SectionHeading({
    icon: Icon,
    title,
}: {
    icon: typeof ListChecks;
    title: string;
}) {
    return (
        <h2 className="flex items-center gap-2 border-b pb-3 text-xl font-semibold tracking-tight">
            <Icon className="size-5 text-muted-foreground" />
            {title}
        </h2>
    );
}

function CodeSnippet({
    value,
    className,
}: {
    value: string;
    className?: string;
}) {
    const [copiedText, copy] = useClipboard();
    const copied = copiedText === value && value !== '';

    return (
        <div className="group relative">
            <pre
                className={cn(
                    'overflow-x-auto rounded-lg border border-slate-800 bg-slate-950 px-4 py-3 pr-20 font-mono text-sm leading-6 break-words whitespace-pre-wrap text-slate-100',
                    className,
                )}
            >
                <code>{value}</code>
            </pre>

            <button
                type="button"
                onClick={() => void copy(value)}
                className="absolute top-2.5 right-2.5 inline-flex items-center gap-1 rounded-md border border-slate-700 bg-slate-900/90 px-2 py-1 font-mono text-[11px] font-medium text-slate-300 shadow-sm transition-colors hover:bg-slate-800 hover:text-white"
                title="Copy sample"
                aria-label="Copy sample"
            >
                {copied ? (
                    <>
                        <Check className="size-3 text-emerald-400" />
                        <span className="text-emerald-400">Copied</span>
                    </>
                ) : (
                    <>
                        <Copy className="size-3" />
                        <span>Copy</span>
                    </>
                )}
            </button>
        </div>
    );
}

function StarterCodePanel({
    languages,
    language,
    onLanguageChange,
    code,
    copied,
    onCopy,
}: {
    languages: string[];
    language: string;
    onLanguageChange: (language: string) => void;
    code: string;
    copied: boolean;
    onCopy: () => void;
}) {
    const highlighted = useMemo(
        () => highlightCode(code, language),
        [code, language],
    );

    return (
        <div className="overflow-hidden rounded-xl border border-slate-800 bg-slate-950 shadow-sm">
            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 bg-slate-900 px-3 py-2">
                <div className="flex flex-wrap gap-1">
                    {languages.map((key) => (
                        <button
                            key={key}
                            type="button"
                            onClick={() => onLanguageChange(key)}
                            className={cn(
                                'rounded-md px-2.5 py-1 text-xs font-medium transition-colors',
                                language === key
                                    ? 'bg-slate-700 text-slate-100'
                                    : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200',
                            )}
                        >
                            {languageLabels[key] ?? key}
                        </button>
                    ))}
                </div>

                <button
                    type="button"
                    onClick={onCopy}
                    disabled={code === ''}
                    className="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-medium text-slate-400 transition-colors hover:bg-slate-800 hover:text-slate-200 disabled:pointer-events-none disabled:opacity-50"
                >
                    {copied ? (
                        <Check className="size-3.5" />
                    ) : (
                        <Copy className="size-3.5" />
                    )}
                    {copied ? 'Copied' : 'Copy'}
                </button>
            </div>

            <pre className="overflow-x-auto p-4 text-sm leading-6 text-slate-100">
                <code className="font-mono">{highlighted}</code>
            </pre>
        </div>
    );
}

function MetaBlock({
    icon: Icon,
    label,
    value,
}: {
    icon: typeof Clock;
    label: string;
    value: string;
}) {
    return (
        <div className="rounded-lg border bg-muted/30 p-3.5">
            <div className="flex items-center gap-1.5 text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                <Icon className="size-3.5" />
                {label}
            </div>
            <p className="mt-1.5 text-sm leading-6 font-medium">{value}</p>
        </div>
    );
}

function highlightCode(code: string, language: string): ReactNode[] {
    const keywords = codeKeywords[language] ?? new Set<string>();
    const commentPattern =
        language === 'python' ? '#[^\\n]*' : '/\\*[\\s\\S]*?\\*/|\\/\\/[^\\n]*';
    const preprocessorPattern = language === 'cpp' ? '|#[A-Za-z]+' : '';

    const tokenPattern = new RegExp(
        '(' +
            commentPattern +
            preprocessorPattern +
            '|"(?:\\\\.|[^"\\\\])*"' +
            "|'(?:\\\\.|[^'\\\\])*'" +
            '|`(?:\\\\.|[^`\\\\])*`' +
            '|\\b\\d+(?:\\.\\d+)?\\b' +
            '|[A-Za-z_$][\\w$]*)',
        'g',
    );

    return code.split(tokenPattern).map((token, index) => {
        if (token === '') {
            return null;
        }

        if (
            token.startsWith('//') ||
            token.startsWith('/*') ||
            (language === 'python' && token.startsWith('#'))
        ) {
            return (
                <span key={index} className="text-slate-500 italic">
                    {token}
                </span>
            );
        }

        if (language === 'cpp' && token.startsWith('#')) {
            return (
                <span key={index} className="text-fuchsia-300">
                    {token}
                </span>
            );
        }

        if (
            token.startsWith('"') ||
            token.startsWith("'") ||
            token.startsWith('`')
        ) {
            return (
                <span key={index} className="text-emerald-300">
                    {token}
                </span>
            );
        }

        if (/^\d/.test(token)) {
            return (
                <span key={index} className="text-amber-300">
                    {token}
                </span>
            );
        }

        if (keywords.has(token)) {
            return (
                <span key={index} className="text-sky-300">
                    {token}
                </span>
            );
        }

        return <span key={index}>{token}</span>;
    });
}
