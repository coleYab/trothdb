import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    BookOpen,
    CheckCircle2,
    Clock3,
    GraduationCap,
    Play,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { EmptyState, PageHeader, Pill } from '@/components/hub-ui';
import { Button } from '@/components/ui/button';
import { tracks, trackBySlug, trackProgress } from '@/lib/learn-data';
import { lesson as lessonRoute, show as trackRoute } from '@/routes/learn';
import type { TrackLevel } from '@/types';

const levels = ['All', 'Beginner', 'Intermediate', 'Advanced'] as const;
type LevelFilter = (typeof levels)[number];

const levelTone: Record<TrackLevel, 'emerald' | 'amber' | 'rose'> = {
    Beginner: 'emerald',
    Intermediate: 'amber',
    Advanced: 'rose',
};

export default function LearnIndex() {
    const [level, setLevel] = useState<LevelFilter>('All');

    const filtered = useMemo(
        () =>
            tracks.filter((track) => level === 'All' || track.level === level),
        [level],
    );

    const totalLessons = tracks.reduce(
        (total, track) => total + track.lessons.length,
        0,
    );
    const totalItems = tracks.reduce(
        (total, track) =>
            total +
            track.modules
                .flatMap((module) => module.items)
                .filter((item) => item.completed).length,
        0,
    );

    const activeTrack = trackBySlug('performance-engineering') ?? tracks[0];
    const activeLesson =
        activeTrack?.lessons.find(
            (lesson) => lesson.slug === activeTrack.continueLesson,
        ) ?? activeTrack?.lessons[0];

    const ledger = [
        { label: 'Tracks', value: String(tracks.length), hint: 'structured paths' },
        { label: 'Lessons', value: String(totalLessons), hint: 'with runnable code' },
        { label: 'Completed', value: String(totalItems), hint: 'across all tracks' },
        {
            label: 'Effort',
            value: `${tracks.reduce((total, track) => total + track.hours, 0)}h`,
            hint: 'estimated',
        },
    ];

    return (
        <>
            <Head title="Tracks" />

            <div className="mx-auto w-full max-w-7xl flex-1 space-y-8 p-4 md:p-8">
                <PageHeader
                    eyebrow="Curriculum / C++"
                    title="Tracks"
                    description="Structured paths from language fundamentals to high-performance systems engineering. Read a lesson, run the snippet, then apply it to a judged problem."
                />

                {activeTrack && activeLesson && (
                    <section className="grid gap-6 rounded-lg border border-border/90 bg-card p-5 md:p-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-end">
                        <div className="space-y-3">
                            <div className="flex flex-wrap items-center gap-2">
                                <Pill tone="emerald">
                                    <Play className="size-3" />
                                    Continue
                                </Pill>
                                <span className="font-mono text-[10px] text-muted-foreground">
                                    {activeTrack.title} · {activeTrack.level}
                                </span>
                            </div>
                            <h2 className="display-title text-3xl">
                                {activeLesson.title}
                            </h2>
                            <p className="max-w-2xl text-sm leading-6 text-muted-foreground">
                                {activeLesson.summary}
                            </p>
                            <div className="flex flex-wrap items-center gap-4 font-mono text-[10px] text-muted-foreground">
                                <span className="inline-flex items-center gap-1.5">
                                    <Clock3 className="size-3" />
                                    {activeLesson.minutes} min
                                </span>
                                <span className="inline-flex items-center gap-1.5">
                                    <BookOpen className="size-3" />
                                    Lesson{' '}
                                    {activeTrack.lessons.findIndex(
                                        (lesson) =>
                                            lesson.slug === activeLesson.slug,
                                    ) + 1}{' '}
                                    of {activeTrack.lessons.length}
                                </span>
                            </div>
                        </div>

                        <div className="flex flex-col gap-2 lg:border-l lg:border-border/80 lg:pl-6">
                            <Button asChild className="gap-1.5">
                                <Link
                                    href={lessonRoute({
                                        track: activeTrack.slug,
                                        lesson: activeLesson.slug,
                                    })}
                                >
                                    Continue lesson
                                    <ArrowRight className="size-4" />
                                </Link>
                            </Button>
                            <Button
                                variant="outline"
                                asChild
                                className="bg-transparent"
                            >
                                <Link href={trackRoute(activeTrack.slug)}>
                                    View syllabus
                                </Link>
                            </Button>
                        </div>
                    </section>
                )}

                <section className="grid grid-cols-2 gap-4 border-y border-border/80 py-5 md:grid-cols-4">
                    {ledger.map((item) => (
                        <div key={item.label}>
                            <p className="meta-label">{item.label}</p>
                            <p className="display-title mt-1 text-3xl">
                                {item.value}
                            </p>
                            <p className="mt-0.5 font-mono text-[10px] text-muted-foreground">
                                {item.hint}
                            </p>
                        </div>
                    ))}
                </section>

                <section className="space-y-3">
                    <div className="flex items-center justify-between">
                        <span className="meta-label">Prerequisite path</span>
                        <span className="font-mono text-[10px] text-muted-foreground">
                            left → right
                        </span>
                    </div>

                    <div className="grid gap-px overflow-hidden rounded-md border border-border/90 bg-border md:grid-cols-3">
                        {tracks.map((track, index) => (
                            <Link
                                key={track.slug}
                                href={trackRoute(track.slug)}
                                className="group bg-card p-4 transition-colors hover:bg-muted/40"
                            >
                                <div className="flex items-center justify-between">
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        Step {String(index + 1).padStart(2, '0')}
                                    </span>
                                    <ArrowRight className="size-3.5 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100" />
                                </div>
                                <p className="display-title mt-2 text-xl">
                                    {track.title}
                                </p>
                                <p className="mt-1 font-mono text-[10px] text-muted-foreground">
                                    {track.prerequisites.join(' · ')}
                                </p>
                            </Link>
                        ))}
                    </div>
                </section>

                <div className="flex flex-wrap items-center justify-between gap-3 border-b border-border/80 pb-3">
                    <div className="flex flex-wrap items-center gap-1">
                        {levels.map((item) => (
                            <button
                                key={item}
                                type="button"
                                onClick={() => setLevel(item)}
                                className={
                                    level === item
                                        ? 'rounded-sm bg-foreground px-2.5 py-1.5 font-mono text-[11px] text-background'
                                        : 'rounded-sm px-2.5 py-1.5 font-mono text-[11px] text-muted-foreground transition-colors hover:text-foreground'
                                }
                            >
                                {item}
                            </button>
                        ))}
                    </div>
                    <p className="font-mono text-[10px] text-muted-foreground">
                        {filtered.length} track
                        {filtered.length === 1 ? '' : 's'}
                    </p>
                </div>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={GraduationCap}
                        title="No tracks at this level"
                        description="Try another level. Tracks are added as new lessons are published."
                        action={
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => setLevel('All')}
                            >
                                Show all tracks
                            </Button>
                        }
                    />
                ) : (
                    <div className="grid gap-5 lg:grid-cols-3">
                        {filtered.map((track, index) => {
                            const progress = trackProgress(track);
                            const completed = track.modules
                                .flatMap((module) => module.items)
                                .filter((item) => item.completed).length;
                            const items = track.modules.flatMap(
                                (module) => module.items,
                            ).length;

                            return (
                                <article
                                    key={track.slug}
                                    className="group flex flex-col overflow-hidden rounded-lg border border-border/90 bg-card"
                                >
                                    <div className="flex flex-1 flex-col p-5">
                                        <div className="flex items-start justify-between gap-2">
                                            <Pill tone={levelTone[track.level]}>
                                                {track.level}
                                            </Pill>
                                            <span className="font-mono text-[10px] text-muted-foreground">
                                                {String(index + 1).padStart(2, '0')} /{' '}
                                                {track.hours}h
                                            </span>
                                        </div>

                                        <h2 className="display-title mt-3 text-2xl">
                                            <Link
                                                href={trackRoute(track.slug)}
                                                className="transition-colors group-hover:text-emerald-700 dark:group-hover:text-emerald-400"
                                            >
                                                {track.title}
                                            </Link>
                                        </h2>
                                        <p className="mt-1.5 line-clamp-3 text-xs leading-5 text-muted-foreground">
                                            {track.summary}
                                        </p>

                                        <div className="mt-4 flex-1 space-y-1">
                                            {track.modules
                                                .slice(0, 3)
                                                .map((module, moduleIndex) => (
                                                    <div
                                                        key={module.title}
                                                        className="flex items-center gap-2 border-t border-border/60 py-1.5 text-[11px]"
                                                    >
                                                        <span className="font-mono text-[10px] text-muted-foreground">
                                                            {String(
                                                                moduleIndex + 1,
                                                            ).padStart(2, '0')}
                                                        </span>
                                                        <span className="min-w-0 flex-1 truncate text-muted-foreground">
                                                            {module.title}
                                                        </span>
                                                        <CheckCircle2 className="size-3 text-border group-hover:text-emerald-600/50" />
                                                    </div>
                                                ))}
                                        </div>

                                        <div className="mt-4 space-y-1.5">
                                            <div className="flex items-center justify-between font-mono text-[10px]">
                                                <span className="text-muted-foreground">
                                                    {completed}/{items} items
                                                </span>
                                                <span>{progress}%</span>
                                            </div>
                                            <div className="h-1 w-full overflow-hidden rounded-full bg-muted">
                                                <div
                                                    className="h-full bg-emerald-600"
                                                    style={{
                                                        width: `${progress}%`,
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between border-t border-border/80 px-5 py-2.5">
                                        <span className="font-mono text-[10px] text-muted-foreground">
                                            {track.lessons.length} lessons
                                        </span>
                                        <Link
                                            href={trackRoute(track.slug)}
                                            className="inline-flex items-center gap-1 font-mono text-[10px] text-foreground hover:text-emerald-700 dark:hover:text-emerald-400"
                                        >
                                            Open track
                                            <ArrowRight className="size-3" />
                                        </Link>
                                    </div>
                                </article>
                            );
                        })}
                    </div>
                )}
            </div>
        </>
    );
}

LearnIndex.layout = {
    breadcrumbs: [
        {
            title: 'Tracks',
            href: '/learn',
        },
    ],
};
