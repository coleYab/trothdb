import { Head, Link, setLayoutProps } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    Check,
    CheckCircle2,
    Circle,
    CircleHelp,
    Clock3,
    Copy,
    Cpu,
    MessageSquare,
    Play,
    RotateCcw,
    Send,
    Terminal,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { Callout } from '@/components/callout';
import {
    CodeBlock,
    ErrorState,
    PageHeader,
    Pill,
    SectionCard,
} from '@/components/hub-ui';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { useClipboard } from '@/hooks/use-clipboard';
import { lessonFor, trackBySlug } from '@/lib/learn-data';
import { highlightCpp } from '@/lib/highlight-cpp';
import {
    index as learnRoute,
    lesson as lessonRoute,
    show as trackRoute,
} from '@/routes/learn';
import { show as problemRoute } from '@/routes/problems';
import type { LessonBlock } from '@/types';
import { cn } from '@/lib/utils';

const lessonProblemMap: Record<
    string,
    { id: string; title: string; difficulty: string; summary: string }
> = {
    'value-categories': {
        id: 'move-semantics-audit',
        title: 'Move Semantics Audit',
        difficulty: 'Medium',
        summary:
            'Audit move constructors and eliminate silent deep copies in hot paths.',
    },
    'references-and-views': {
        id: 'ring-buffer',
        title: 'Ring Buffer',
        difficulty: 'Medium',
        summary:
            'Implement a lock-free SPSC ring buffer with cache-line alignment.',
    },
    'vector-and-iterators': {
        id: 'small-string-optimization',
        title: 'Small String Optimization',
        difficulty: 'Medium',
        summary:
            'Build an inline buffer type-erased string with zero dynamic allocations.',
    },
    'algorithms-and-ranges': {
        id: 'merge-intervals',
        title: 'Merge Intervals',
        difficulty: 'Medium',
        summary:
            'Sort and merge overlapping intervals with contiguous vector iterators.',
    },
    'benchmarking-basics': {
        id: 'hot-loop-optimization',
        title: 'Hot Loop Optimization',
        difficulty: 'Hard',
        summary:
            'Optimize a histogram compute kernel to run 3x faster without changing output.',
    },
    'profiling-with-perf': {
        id: 'lru-cache',
        title: 'LRU Cache',
        difficulty: 'Medium',
        summary:
            'Design an O(1) memory engine under high-frequency access patterns.',
    },
    'cache-friendly-code': {
        id: 'constexpr-matrix',
        title: 'Constexpr Matrix',
        difficulty: 'Hard',
        summary:
            'Cache-aware matrix multiplications and compile-time layout optimizations.',
    },
    'concepts-and-ranges': {
        id: 'concept-constrained-container',
        title: 'Concept-Constrained Container',
        difficulty: 'Hard',
        summary:
            'Enforce type traits and concept constraints on custom containers.',
    },
    'span-and-views': {
        id: 'span-overflow',
        title: 'Span Bounds Safety',
        difficulty: 'Medium',
        summary:
            'Safeguard non-owning contiguous memory views against buffer overruns.',
    },
    'expected-and-mdspan': {
        id: 'lock-free-stack',
        title: 'Lock-Free Stack',
        difficulty: 'Hard',
        summary:
            'Hazard pointers and atomic CAS operations for lock-free memory reclamation.',
    },
};

function readList(key: string): string[] {
    try {
        const value = JSON.parse(localStorage.getItem(key) ?? '[]');
        return Array.isArray(value) ? value : [];
    } catch {
        return [];
    }
}

function demoOutput(slug: string): string {
    const outputs: Record<string, string> = {
        'benchmarking-basics': 'median: 1.42 ms\nruns: 21\nwarm-up: complete\n',
        'profiling-with-perf':
            'samples: 12,408\nhot function: histogram(unsigned char const*, unsigned long)\nself time: 68.4%\n',
        'cache-friendly-code': 'aos: 84.1 ms\nsoa: 19.3 ms\nspeedup: 4.36x\n',
        'concepts-and-ranges': 'average: 3\n',
        'span-and-views': 'checksum: 0x2f4a1b\nbytes: 256\n',
        'expected-and-mdspan': 'parsed: 42\n',
    };

    return outputs[slug] ?? 'program exited with code 0\n';
}

export default function LessonPage({
    track: trackSlug,
    lesson: lessonSlug,
}: {
    track: string;
    lesson: string;
}) {
    const track = trackBySlug(trackSlug);
    const lesson = track ? lessonFor(track, lessonSlug) : undefined;
    const storageKey = `${trackSlug}/${lessonSlug}`;

    const [completed, setCompleted] = useState(() =>
        readList('completed_lessons').includes(storageKey),
    );
    const [showAssembly, setShowAssembly] = useState(false);
    const [runningIndex, setRunningIndex] = useState<number | null>(null);
    const [outputs, setOutputs] = useState<Record<number, string>>({});
    const [quizChoice, setQuizChoice] = useState<number | null>(null);
    const [quizSubmitted, setQuizSubmitted] = useState(false);
    const [reply, setReply] = useState('');

    useEffect(() => {
        setLayoutProps({
            breadcrumbs: [
                { title: 'Learn', href: learnRoute() },
                {
                    title: track?.title ?? 'Track',
                    href: trackRoute(trackSlug),
                },
                {
                    title: lesson?.title ?? 'Lesson',
                    href: lessonRoute({
                        track: trackSlug,
                        lesson: lessonSlug,
                    }),
                },
            ],
        });
    }, [track?.title, lesson?.title, trackSlug, lessonSlug]);

    const lessonIndex = useMemo(
        () =>
            track
                ? track.lessons.findIndex((item) => item.slug === lessonSlug)
                : -1,
        [track, lessonSlug],
    );

    if (!track || !lesson) {
        return (
            <>
                <Head title="Lesson not found" />
                <div className="mx-auto w-full max-w-5xl flex-1 p-4 md:p-8">
                    <ErrorState
                        title="Lesson not found"
                        description={`No lesson "${lessonSlug}" exists in the "${trackSlug}" track.`}
                        action={
                            <Button size="sm" asChild>
                                <Link href={learnRoute()}>
                                    Browse all tracks
                                </Link>
                            </Button>
                        }
                    />
                </div>
            </>
        );
    }

    const previous = lessonIndex > 0 ? track.lessons[lessonIndex - 1] : null;
    const next =
        lessonIndex >= 0 && lessonIndex < track.lessons.length - 1
            ? track.lessons[lessonIndex + 1]
            : null;

    const linkedProblem = lessonProblemMap[lesson.slug];

    const toggleListValue = (key: string, value: string, active: boolean) => {
        const current = readList(key);
        const nextValue = active
            ? current.filter((item) => item !== value)
            : [...current, value];
        try {
            localStorage.setItem(key, JSON.stringify(nextValue));
        } catch {
            // Storage can be unavailable.
        }
    };

    const toggleComplete = () => {
        const value = !completed;
        setCompleted(value);
        toggleListValue('completed_lessons', storageKey, !value);
        toast(value ? 'Lesson marked complete' : 'Lesson marked incomplete', {
            description: value
                ? 'Progress updated. The next lesson is ready.'
                : undefined,
        });
    };

    const runSnippet = (index: number) => {
        setRunningIndex(index);
        setOutputs((current) => ({ ...current, [index]: '' }));
        window.setTimeout(() => {
            setRunningIndex(null);
            setOutputs((current) => ({
                ...current,
                [index]: demoOutput(lesson.slug),
            }));
        }, 700);
    };

    const quizCorrect = quizSubmitted && quizChoice === lesson.quiz.answer;

    return (
        <>
            <Head title={lesson.title} />

            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-8">
                <div className="flex flex-wrap items-center gap-2 font-mono text-[11px] text-muted-foreground">
                    <Link
                        href={learnRoute()}
                        className="inline-flex items-center gap-1.5 transition-colors hover:text-foreground"
                    >
                        <ArrowLeft className="size-3.5" />
                        Tracks
                    </Link>
                    <span className="text-border">/</span>
                    <Link
                        href={trackRoute(track.slug)}
                        className="transition-colors hover:text-foreground"
                    >
                        {track.title}
                    </Link>
                    <span className="text-border">/</span>
                    <span className="text-foreground">
                        Lesson {Math.max(lessonIndex + 1, 1)}
                    </span>
                </div>

                <PageHeader
                    eyebrow={`${track.level} · Lesson ${Math.max(lessonIndex + 1, 1)} of ${track.lessons.length}`}
                    title={lesson.title}
                    description={lesson.summary}
                    meta={
                        <>
                            <span className="inline-flex items-center gap-1.5 font-mono text-[11px] text-muted-foreground">
                                <Clock3 className="size-3.5" />
                                {lesson.minutes} min read
                            </span>
                            <span className="inline-flex items-center gap-1.5 font-mono text-[11px] text-muted-foreground">
                                <Cpu className="size-3.5" />
                                {lesson.blocks.length} sections
                            </span>
                            {completed && (
                                <Pill tone="emerald">
                                    <CheckCircle2 className="size-3" />
                                    Completed
                                </Pill>
                            )}
                        </>
                    }
                    actions={
                        <Button
                            variant={completed ? 'secondary' : 'default'}
                            size="sm"
                            onClick={toggleComplete}
                        >
                            <Check className="size-4" />
                            {completed ? 'Completed' : 'Mark complete'}
                        </Button>
                    }
                />

                <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] lg:gap-10">
                    <article className="min-w-0 max-w-3xl space-y-8">
                        {lesson.blocks.map((block, index) => (
                            <LessonBlockView
                                key={index}
                                block={block}
                                index={index}
                                running={runningIndex === index}
                                output={outputs[index]}
                                onRun={() => runSnippet(index)}
                            />
                        ))}

                        <section className="space-y-4 border-t border-foreground/15 pt-8">
                            <div className="flex items-center gap-2">
                                <span className="font-mono text-[10px] text-muted-foreground">
                                    CHECK
                                </span>
                                <h2 className="display-title text-2xl">
                                    Check your understanding
                                </h2>
                            </div>

                            <div className="rounded-lg border border-border/90 bg-card p-5">
                                <p className="text-sm font-medium">
                                    {lesson.quiz.question}
                                </p>

                                <div
                                    className="mt-4 space-y-2"
                                    role="radiogroup"
                                    aria-label="Quiz options"
                                >
                                    {lesson.quiz.options.map(
                                        (option, index) => {
                                            const selected =
                                                quizChoice === index;
                                            const isAnswer =
                                                index === lesson.quiz.answer;

                                            return (
                                                <button
                                                    key={option}
                                                    type="button"
                                                    role="radio"
                                                    aria-checked={selected}
                                                    onClick={() => {
                                                        setQuizChoice(index);
                                                        setQuizSubmitted(false);
                                                    }}
                                                    className={cn(
                                                        'flex w-full items-start gap-3 rounded-sm border px-3.5 py-2.5 text-left text-sm transition-colors',
                                                        selected
                                                            ? 'border-foreground/50 bg-muted/40'
                                                            : 'border-border/80 hover:bg-muted/30',
                                                        quizSubmitted &&
                                                            isAnswer &&
                                                            'border-emerald-600/40 bg-emerald-600/5',
                                                        quizSubmitted &&
                                                            selected &&
                                                            !isAnswer &&
                                                            'border-rose-600/40 bg-rose-600/5',
                                                    )}
                                                >
                                                    <span
                                                        className={cn(
                                                            'mt-0.5 flex size-4 shrink-0 items-center justify-center rounded-full border',
                                                            selected &&
                                                                'border-foreground',
                                                        )}
                                                    >
                                                        {selected && (
                                                            <span className="size-2 rounded-full bg-foreground" />
                                                        )}
                                                    </span>
                                                    <span className="leading-5">
                                                        {option}
                                                    </span>
                                                </button>
                                            );
                                        },
                                    )}
                                </div>

                                <div className="mt-4 flex flex-wrap items-center gap-2">
                                    <Button
                                        size="sm"
                                        onClick={() => {
                                            if (quizChoice === null) {
                                                toast.error(
                                                    'Choose an answer first',
                                                );
                                                return;
                                            }
                                            setQuizSubmitted(true);
                                        }}
                                        disabled={quizChoice === null}
                                    >
                                        Check answer
                                    </Button>
                                    {quizSubmitted && (
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            onClick={() => {
                                                setQuizChoice(null);
                                                setQuizSubmitted(false);
                                            }}
                                        >
                                            <RotateCcw className="size-4" />
                                            Reset
                                        </Button>
                                    )}
                                    {quizSubmitted && (
                                        <Pill
                                            tone={
                                                quizCorrect ? 'emerald' : 'rose'
                                            }
                                        >
                                            {quizCorrect
                                                ? 'Correct'
                                                : 'Not quite'}
                                        </Pill>
                                    )}
                                </div>

                                {quizSubmitted && (
                                    <div
                                        className={cn(
                                            'mt-3 rounded-sm border px-3.5 py-2.5 text-xs leading-5',
                                            quizCorrect
                                                ? 'border-emerald-600/25 bg-emerald-600/5 text-emerald-800 dark:text-emerald-300'
                                                : 'border-amber-600/25 bg-amber-600/5 text-amber-800 dark:text-amber-300',
                                        )}
                                    >
                                        {lesson.quiz.explanation}
                                    </div>
                                )}
                            </div>
                        </section>

                        <SectionCard
                            title="Assembly view"
                            description="Compare the source with what the compiler emits at -O2."
                            icon={Cpu}
                            actions={
                                <Button
                                    variant={
                                        showAssembly ? 'secondary' : 'outline'
                                    }
                                    size="sm"
                                    onClick={() =>
                                        setShowAssembly((value) => !value)
                                    }
                                    aria-pressed={showAssembly}
                                >
                                    <Terminal className="size-4" />
                                    {showAssembly
                                        ? 'Hide assembly'
                                        : 'Show assembly'}
                                </Button>
                            }
                            contentClassName={showAssembly ? 'p-5' : 'p-0'}
                        >
                            {showAssembly ? (
                                <CodeBlock
                                    value={lesson.assembly}
                                    title={`${lesson.slug}.s`}
                                />
                            ) : (
                                <p className="px-5 pb-5 text-xs leading-5 text-muted-foreground">
                                    The assembly is generated with GCC 14 at{' '}
                                    <code className="rounded-sm bg-muted px-1 py-0.5 font-mono text-[11px]">
                                        -std=c++20 -O2
                                    </code>
                                    . Instruction sequences vary between
                                    compilers and standards.
                                </p>
                            )}
                        </SectionCard>

                        <section className="space-y-4">
                            <div className="flex items-center justify-between border-b border-foreground/15 pb-2">
                                <div className="flex items-center gap-2">
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        TALK
                                    </span>
                                    <h2 className="display-title text-2xl">
                                        Discussion
                                    </h2>
                                </div>
                                <span className="font-mono text-[10px] text-muted-foreground">
                                    {lesson.discussion.length} notes
                                </span>
                            </div>

                            <div className="space-y-2">
                                {lesson.discussion.map((comment) => (
                                    <article
                                        key={comment.author}
                                        className="rounded-lg border border-border/90 bg-card p-4"
                                    >
                                        <div className="flex flex-wrap items-center gap-2 font-mono text-[11px]">
                                            <span className="font-semibold">
                                                {comment.author}
                                            </span>
                                            <span className="text-muted-foreground">
                                                · {comment.role}
                                            </span>
                                            <span className="ml-auto text-muted-foreground">
                                                {comment.votes} helpful
                                            </span>
                                        </div>
                                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                            {comment.body}
                                        </p>
                                    </article>
                                ))}
                            </div>

                            <div className="rounded-lg border border-border/90 bg-card p-4">
                                <label
                                    htmlFor="lesson-reply"
                                    className="text-sm font-medium"
                                >
                                    Add a comment
                                </label>
                                <textarea
                                    id="lesson-reply"
                                    value={reply}
                                    onChange={(event) =>
                                        setReply(event.target.value)
                                    }
                                    rows={3}
                                    placeholder="Ask a question or share what helped..."
                                    className="mt-2 w-full resize-y rounded-sm border border-border bg-background px-3 py-2 font-sans text-sm leading-6 outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50"
                                />
                                <div className="mt-2 flex justify-end">
                                    <Button
                                        size="sm"
                                        onClick={() => {
                                            if (reply.trim() === '') {
                                                toast.error(
                                                    'Write a comment first',
                                                );
                                                return;
                                            }
                                            setReply('');
                                            toast.success('Comment posted');
                                        }}
                                    >
                                        <Send className="size-4" />
                                        Post
                                    </Button>
                                </div>
                            </div>
                        </section>

                        {linkedProblem && (
                            <section className="flex flex-col gap-4 rounded-lg border border-emerald-600/30 bg-emerald-600/5 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                                <div>
                                    <span className="meta-label text-emerald-800 dark:text-emerald-400">
                                        Apply it / judged drill
                                    </span>
                                    <h3 className="mt-1 text-base font-semibold text-foreground">
                                        {linkedProblem.title}
                                    </h3>
                                    <p className="mt-0.5 max-w-xl text-xs leading-5 text-muted-foreground">
                                        {linkedProblem.summary}
                                    </p>
                                </div>
                                <Button
                                    size="sm"
                                    asChild
                                    className="shrink-0 gap-1.5 self-start sm:self-center"
                                >
                                    <Link
                                        href={problemRoute(linkedProblem.id)}
                                    >
                                        <span>Practice in Arena</span>
                                        <ArrowRight className="size-3.5" />
                                    </Link>
                                </Button>
                            </section>
                        )}

                        <nav className="flex flex-col gap-2 border-t border-foreground/15 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            {previous ? (
                                <Button variant="outline" size="sm" asChild>
                                    <Link
                                        href={lessonRoute({
                                            track: track.slug,
                                            lesson: previous.slug,
                                        })}
                                    >
                                        <ArrowLeft className="size-4" />
                                        {previous.title}
                                    </Link>
                                </Button>
                            ) : (
                                <span />
                            )}

                            {next ? (
                                <Button size="sm" asChild>
                                    <Link
                                        href={lessonRoute({
                                            track: track.slug,
                                            lesson: next.slug,
                                        })}
                                    >
                                        {next.title}
                                        <ArrowRight className="size-4" />
                                    </Link>
                                </Button>
                            ) : (
                                <Button size="sm" asChild>
                                    <Link href={trackRoute(track.slug)}>
                                        Finish track
                                        <ArrowRight className="size-4" />
                                    </Link>
                                </Button>
                            )}
                        </nav>
                    </article>

                    <aside className="hidden lg:block">
                        <div className="sticky top-20 space-y-4">
                            <div className="overflow-hidden rounded-lg border border-border/90 bg-card">
                                <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-4 py-2.5">
                                    <span className="meta-label">Syllabus</span>
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        {Math.max(lessonIndex + 1, 1)}/
                                        {track.lessons.length}
                                    </span>
                                </div>
                                <nav className="p-1.5">
                                    {track.lessons.map((item, index) => {
                                        const active = item.slug === lesson.slug;
                                        const done = readList(
                                            'completed_lessons',
                                        ).includes(
                                            `${track.slug}/${item.slug}`,
                                        );

                                        return (
                                            <Link
                                                key={item.slug}
                                                href={lessonRoute({
                                                    track: track.slug,
                                                    lesson: item.slug,
                                                })}
                                                className={cn(
                                                    'flex items-start gap-2 rounded-sm px-2 py-1.5 text-[11px] leading-4 transition-colors',
                                                    active
                                                        ? 'bg-foreground/5 font-medium text-foreground'
                                                        : 'text-muted-foreground hover:bg-muted/50 hover:text-foreground',
                                                )}
                                            >
                                                {done ? (
                                                    <CheckCircle2 className="mt-0.5 size-3 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                                ) : (
                                                    <Circle className="mt-0.5 size-3 shrink-0 opacity-40" />
                                                )}
                                                <span className="min-w-0 flex-1">
                                                    <span className="mr-1 font-mono opacity-60">
                                                        {String(
                                                            index + 1,
                                                        ).padStart(2, '0')}
                                                    </span>
                                                    {item.title}
                                                </span>
                                            </Link>
                                        );
                                    })}
                                </nav>
                            </div>

                            {linkedProblem && (
                                <div className="rounded-lg border border-border/90 bg-card p-4">
                                    <p className="meta-label">Linked drill</p>
                                    <p className="mt-1 text-sm font-medium">
                                        {linkedProblem.title}
                                    </p>
                                    <p className="mt-0.5 font-mono text-[10px] text-muted-foreground">
                                        {linkedProblem.difficulty} · C++20
                                    </p>
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        asChild
                                        className="mt-3 w-full gap-1.5 bg-transparent"
                                    >
                                        <Link href={problemRoute(linkedProblem.id)}>
                                            Open problem
                                            <ArrowRight className="size-3.5" />
                                        </Link>
                                    </Button>
                                </div>
                            )}

                            <div className="rounded-lg border border-border/90 bg-card p-4">
                                <p className="meta-label">Mark progress</p>
                                <p className="mt-1 text-xs leading-5 text-muted-foreground">
                                    Completed lessons unlock the linked drill
                                    and count toward the track.
                                </p>
                                <Button
                                    size="sm"
                                    variant={completed ? 'secondary' : 'default'}
                                    onClick={toggleComplete}
                                    className="mt-3 w-full gap-1.5"
                                >
                                    <Check className="size-3.5" />
                                    {completed ? 'Completed' : 'Mark complete'}
                                </Button>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </>
    );
}

function LessonBlockView({
    block,
    index,
    running,
    output,
    onRun,
}: {
    block: LessonBlock;
    index: number;
    running: boolean;
    output?: string;
    onRun: () => void;
}) {
    if (block.type === 'paragraph') {
        return (
            <p className="text-[15px] leading-8 text-foreground/90">
                {block.text}
            </p>
        );
    }

    if (block.type === 'list') {
        return (
            <div className="space-y-2">
                {block.title && (
                    <h3 className="display-title text-xl">{block.title}</h3>
                )}
                <ul className="divide-y divide-border/70 rounded-md border border-border/90 bg-card">
                    {block.items.map((item) => (
                        <li
                            key={item}
                            className="flex items-start gap-2.5 px-3.5 py-2.5 text-sm leading-6 text-muted-foreground"
                        >
                            <span className="mt-2.5 size-1 shrink-0 rounded-full bg-muted-foreground/50" />
                            {item}
                        </li>
                    ))}
                </ul>
            </div>
        );
    }

    if (block.type === 'callout') {
        return (
            <Callout variant={block.variant} title={block.title}>
                {block.text}
            </Callout>
        );
    }

    return (
        <Snippet
            code={block.code}
            title={block.title}
            index={index}
            running={running}
            output={output}
            onRun={onRun}
        />
    );
}

function Snippet({
    code,
    title,
    index,
    running,
    output,
    onRun,
}: {
    code: string;
    title: string;
    index: number;
    running: boolean;
    output?: string;
    onRun: () => void;
}) {
    const [copiedText, copy] = useClipboard();
    const copied = copiedText === code;

    return (
        <div className="space-y-2">
            <div className="overflow-hidden rounded-md border border-slate-800 bg-slate-950">
                <div className="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 bg-slate-900 px-3 py-2">
                    <span className="font-mono text-[10px] font-medium tracking-wider text-slate-400 uppercase">
                        {title}
                    </span>
                    <div className="flex items-center gap-1">
                        <button
                            type="button"
                            onClick={() => void copy(code)}
                            className="inline-flex items-center gap-1.5 rounded-sm px-2 py-1 font-mono text-[10px] text-slate-400 transition-colors hover:bg-slate-800 hover:text-slate-200"
                            aria-label={copied ? 'Copied' : `Copy ${title}`}
                        >
                            {copied ? (
                                <Check className="size-3" />
                            ) : (
                                <Copy className="size-3" />
                            )}
                            {copied ? 'Copied' : 'Copy'}
                        </button>
                        <button
                            type="button"
                            onClick={onRun}
                            className="inline-flex items-center gap-1.5 rounded-sm bg-slate-800 px-2.5 py-1 font-mono text-[10px] font-medium text-slate-100 transition-colors hover:bg-slate-700"
                            aria-label={`Run snippet ${index + 1}`}
                        >
                            <Play className="size-3" />
                            Run
                        </button>
                    </div>
                </div>
                <pre className="overflow-x-auto p-4 font-mono text-[12.5px] leading-6 text-slate-100">
                    <code>{highlightCpp(code)}</code>
                </pre>
            </div>

            {running && (
                <div className="space-y-2 rounded-md border border-slate-800 bg-slate-950 p-3">
                    <Skeleton className="h-3 w-1/3 bg-slate-800" />
                    <Skeleton className="h-3 w-1/2 bg-slate-800" />
                </div>
            )}

            {!running && output !== undefined && (
                <div className="overflow-hidden rounded-md border border-slate-800 bg-slate-950">
                    <div className="flex items-center gap-2 border-b border-slate-800 bg-slate-900/70 px-3 py-1.5 font-mono text-[10px] tracking-wider text-slate-400 uppercase">
                        <Terminal className="size-3" />
                        Output
                    </div>
                    <pre className="p-3 font-mono text-[12px] leading-6 whitespace-pre-wrap text-emerald-300">
                        {output}
                    </pre>
                </div>
            )}
        </div>
    );
}

LessonPage.layout = {
    breadcrumbs: [
        {
            title: 'Tracks',
            href: '/learn',
        },
    ],
};
