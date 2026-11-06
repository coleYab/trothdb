import { Head, Link, setLayoutProps } from '@inertiajs/react';
import {
    ArrowRight,
    BookOpen,
    CheckCircle2,
    Circle,
    Clock3,
    GraduationCap,
    Play,
    Wrench,
} from 'lucide-react';
import { useEffect, useMemo } from 'react';
import { ErrorState, PageHeader, Pill } from '@/components/hub-ui';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { trackBySlug, trackProgress } from '@/lib/learn-data';
import {
    index as learnRoute,
    lesson as lessonRoute,
    show as trackRoute,
} from '@/routes/learn';
import { show as problemRoute } from '@/routes/problems';
import type { SyllabusItem } from '@/types';
import { cn } from '@/lib/utils';

const itemIcons = {
    lesson: BookOpen,
    project: Wrench,
    assessment: GraduationCap,
} as const;

const linkedChallenges: Record<string, string> = {
    'f-types-project': 'dangling-reference',
    'f-stl-project': 'ring-buffer',
    'p-cache-project': 'hot-loop-optimization',
    'm23-project': 'concept-constrained-container',
};

const levelTone = {
    Beginner: 'emerald',
    Intermediate: 'amber',
    Advanced: 'rose',
} as const;

export default function TrackDetail({ track: trackSlug }: { track: string }) {
    const track = trackBySlug(trackSlug);

    useEffect(() => {
        setLayoutProps({
            breadcrumbs: [
                { title: 'Tracks', href: learnRoute() },
                {
                    title: track?.title ?? 'Track',
                    href: trackRoute(trackSlug),
                },
            ],
        });
    }, [track?.title, trackSlug]);

    const progress = useMemo(() => (track ? trackProgress(track) : 0), [track]);

    if (!track) {
        return (
            <>
                <Head title="Track not found" />
                <div className="mx-auto w-full max-w-5xl flex-1 p-4 md:p-8">
                    <ErrorState
                        title="Track not found"
                        description={`No learning track exists with the slug "${trackSlug}".`}
                        action={
                            <Button size="sm" asChild>
                                <Link href={learnRoute()}>Browse all tracks</Link>
                            </Button>
                        }
                    />
                </div>
            </>
        );
    }

    const items = track.modules.flatMap((module) => module.items);
    const completed = items.filter((item) => item.completed).length;
    const continueLesson = track.lessons.find(
        (lesson) => lesson.slug === track.continueLesson,
    );

    const counts = {
        lessons: items.filter((item) => item.type === 'lesson').length,
        projects: items.filter((item) => item.type === 'project').length,
        assessments: items.filter((item) => item.type === 'assessment').length,
    };

    return (
        <>
            <Head title={track.title} />

            <div className="mx-auto w-full max-w-6xl flex-1 space-y-8 p-4 md:p-8">
                <PageHeader
                    eyebrow={`Track / ${track.level}`}
                    title={track.title}
                    description={track.description}
                    meta={
                        <>
                            <Pill tone={levelTone[track.level]}>
                                {track.level}
                            </Pill>
                            <span className="font-mono text-[10px] text-muted-foreground">
                                {track.hours}h · {track.modules.length} modules ·{' '}
                                {completed}/{items.length} complete
                            </span>
                        </>
                    }
                    actions={
                        continueLesson ? (
                            <Button size="sm" asChild className="gap-1.5">
                                <Link
                                    href={lessonRoute({
                                        track: track.slug,
                                        lesson: continueLesson.slug,
                                    })}
                                >
                                    <Play className="size-3.5" />
                                    Continue lesson
                                </Link>
                            </Button>
                        ) : undefined
                    }
                />

                <section className="grid grid-cols-2 gap-4 border-y border-border/80 py-5 md:grid-cols-4">
                    {[
                        ['Lessons', String(counts.lessons), 'reading + snippets'],
                        ['Projects', String(counts.projects), 'judged drills'],
                        [
                            'Assessments',
                            String(counts.assessments),
                            'checkpoints',
                        ],
                        [
                            'Progress',
                            `${progress}%`,
                            `${completed}/${items.length} items`,
                        ],
                    ].map(([label, value, hint]) => (
                        <div key={label}>
                            <p className="meta-label">{label}</p>
                            <p className="display-title mt-1 text-3xl">
                                {value}
                            </p>
                            <p className="mt-0.5 font-mono text-[10px] text-muted-foreground">
                                {hint}
                            </p>
                        </div>
                    ))}
                </section>

                <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_15rem]">
                    <div className="min-w-0 space-y-5">
                        {track.modules.map((module, moduleIndex) => {
                            const moduleCompleted = module.items.filter(
                                (item) => item.completed,
                            ).length;

                            return (
                                <section
                                    key={module.title}
                                    className="overflow-hidden rounded-lg border border-border/90 bg-card"
                                >
                                    <div className="flex flex-wrap items-start justify-between gap-3 border-b border-border/80 bg-muted/30 px-5 py-3.5">
                                        <div className="flex items-start gap-3">
                                            <span className="display-title mt-0.5 text-2xl text-muted-foreground">
                                                {String(moduleIndex + 1).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </span>
                                            <div>
                                                <h2 className="text-sm font-semibold">
                                                    {module.title}
                                                </h2>
                                                <p className="mt-0.5 max-w-2xl text-xs leading-5 text-muted-foreground">
                                                    {module.summary}
                                                </p>
                                            </div>
                                        </div>
                                        <span className="font-mono text-[10px] text-muted-foreground">
                                            {moduleCompleted}/{module.items.length}{' '}
                                            done
                                        </span>
                                    </div>

                                    <div className="divide-y divide-border/70">
                                        {module.items.map((item) => (
                                            <SyllabusRow
                                                key={item.id}
                                                item={item}
                                                trackSlug={track.slug}
                                            />
                                        ))}
                                    </div>
                                </section>
                            );
                        })}
                    </div>

                    <aside className="hidden lg:block">
                        <div className="sticky top-20 space-y-4">
                            <div className="rounded-lg border border-border/90 bg-card p-4">
                                <p className="meta-label">Course progress</p>
                                <p className="display-title mt-1 text-4xl">
                                    {progress}%
                                </p>
                                <div className="mt-2 h-1 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        className="h-full bg-emerald-600"
                                        style={{ width: `${progress}%` }}
                                    />
                                </div>
                                <p className="mt-2 font-mono text-[10px] text-muted-foreground">
                                    {completed} of {items.length} items
                                </p>
                            </div>

                            <nav className="rounded-lg border border-border/90 bg-card p-2">
                                <p className="meta-label px-2 py-1.5">Modules</p>
                                {track.modules.map((module, index) => (
                                    <a
                                        key={module.title}
                                        href={`#module-${index + 1}`}
                                        className="block rounded-sm px-2 py-1.5 text-[11px] text-muted-foreground transition-colors hover:bg-muted/50 hover:text-foreground"
                                    >
                                        {String(index + 1).padStart(2, '0')}{' '}
                                        {module.title}
                                    </a>
                                ))}
                            </nav>
                        </div>
                    </aside>
                </div>
            </div>
        </>
    );
}

function SyllabusRow({
    item,
    trackSlug,
}: {
    item: SyllabusItem;
    trackSlug: string;
}) {
    const Icon = itemIcons[item.type];
    const linked = linkedChallenges[item.id];

    const href =
        item.type === 'lesson' && item.lessonSlug
            ? lessonRoute({
                  track: trackSlug,
                  lesson: item.lessonSlug,
              })
            : linked
              ? problemRoute(linked)
              : undefined;

    const content = (
        <div className="flex flex-wrap items-center gap-3">
            <span
                className={cn(
                    'flex size-7 shrink-0 items-center justify-center rounded-sm border',
                    item.completed
                        ? 'border-emerald-600/30 bg-emerald-600/10 text-emerald-700 dark:text-emerald-400'
                        : 'border-border/80 bg-muted/30 text-muted-foreground',
                )}
            >
                {item.completed ? (
                    <CheckCircle2 className="size-3.5" />
                ) : (
                    <Icon className="size-3.5" />
                )}
            </span>

            <div className="min-w-0 flex-1">
                <div className="flex flex-wrap items-center gap-2">
                    <span className="text-sm font-medium">{item.title}</span>
                    <Badge
                        variant="outline"
                        className="rounded-sm font-mono text-[9px] tracking-wide uppercase"
                    >
                        {item.type}
                    </Badge>
                </div>
                <p className="mt-0.5 flex items-center gap-1 font-mono text-[10px] text-muted-foreground">
                    <Clock3 className="size-3" />
                    {item.minutes} min
                    {item.completed && (
                        <span className="ml-1 text-emerald-700 dark:text-emerald-400">
                            · completed
                        </span>
                    )}
                </p>
            </div>

            {href ? (
                <span className="flex items-center gap-1 font-mono text-[10px] text-muted-foreground group-hover:text-emerald-700 dark:group-hover:text-emerald-400">
                    {item.type === 'project' ? 'open drill' : 'open'}
                    <ArrowRight className="size-3" />
                </span>
            ) : (
                <Circle className="size-3.5 text-muted-foreground/30" />
            )}
        </div>
    );

    if (!href) {
        return <div className="px-5 py-3">{content}</div>;
    }

    return (
        <Link
            href={href}
            className="group block px-5 py-3 transition-colors hover:bg-muted/40"
        >
            {content}
        </Link>
    );
}

TrackDetail.layout = {
    breadcrumbs: [
        {
            title: 'Tracks',
            href: '/learn',
        },
    ],
};
