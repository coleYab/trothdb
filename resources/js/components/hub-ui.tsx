import { Check, Copy, Inbox, Lock, TriangleAlert } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { useClipboard } from '@/hooks/use-clipboard';
import { cn } from '@/lib/utils';

type PageHeaderProps = {
    title: string;
    description?: string;
    eyebrow?: string;
    actions?: ReactNode;
    meta?: ReactNode;
};

export function PageHeader({
    title,
    description,
    eyebrow,
    actions,
    meta,
}: PageHeaderProps) {
    return (
        <header className="relative flex flex-col gap-5 border-b border-foreground/15 pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div className="min-w-0 space-y-3">
                {eyebrow && (
                    <p className="meta-label flex items-center gap-2">
                        <span className="inline-block size-1.5 bg-emerald-600" />
                        {eyebrow}
                    </p>
                )}
                <h1 className="display-title max-w-3xl text-4xl text-foreground sm:text-5xl">
                    {title}
                </h1>
                {description && (
                    <p className="max-w-2xl text-sm leading-6 text-muted-foreground">
                        {description}
                    </p>
                )}
                {meta && (
                    <div className="flex flex-wrap items-center gap-2 pt-1">
                        {meta}
                    </div>
                )}
            </div>
            {actions && (
                <div className="flex shrink-0 flex-wrap items-center gap-2">
                    {actions}
                </div>
            )}
        </header>
    );
}

export function SectionCard({
    title,
    description,
    icon: Icon,
    actions,
    children,
    className,
    contentClassName,
}: {
    title: string;
    description?: string;
    icon?: LucideIcon;
    actions?: ReactNode;
    children: ReactNode;
    className?: string;
    contentClassName?: string;
}) {
    return (
        <section
            className={cn(
                'overflow-hidden rounded-lg border border-border/90 bg-card',
                className,
            )}
        >
            <div className="flex flex-wrap items-start justify-between gap-3 border-b border-border/80 bg-muted/30 px-5 py-3.5">
                <div className="space-y-1">
                    <h2 className="flex items-center gap-2 text-sm font-semibold tracking-tight">
                        {Icon && (
                            <Icon className="size-4 text-muted-foreground" />
                        )}
                        {title}
                    </h2>
                    {description && (
                        <p className="max-w-2xl text-xs leading-5 text-muted-foreground">
                            {description}
                        </p>
                    )}
                </div>
                {actions && (
                    <div className="flex flex-wrap items-center gap-2">
                        {actions}
                    </div>
                )}
            </div>
            <div className={cn('p-5', contentClassName)}>{children}</div>
        </section>
    );
}

const progressTones = {
    emerald: 'bg-emerald-500',
    amber: 'bg-amber-500',
    sky: 'bg-sky-500',
    violet: 'bg-violet-500',
    slate: 'bg-slate-700 dark:bg-slate-300',
} as const;

export type ProgressTone = keyof typeof progressTones;

export function ProgressBar({
    value,
    tone = 'emerald',
    className,
    label,
}: {
    value: number;
    tone?: ProgressTone;
    className?: string;
    label?: string;
}) {
    const clamped = Math.max(0, Math.min(100, Math.round(value)));

    return (
        <div
            className={cn(
                'h-1.5 w-full overflow-hidden rounded-full bg-muted',
                className,
            )}
            role="progressbar"
            aria-valuenow={clamped}
            aria-valuemin={0}
            aria-valuemax={100}
            aria-label={label ?? 'Progress'}
        >
            <div
                className={cn(
                    'h-full rounded-full transition-[width]',
                    progressTones[tone],
                )}
                style={{ width: `${clamped}%` }}
            />
        </div>
    );
}

export function StatTile({
    label,
    value,
    hint,
    icon: Icon,
    tone = 'default',
}: {
    label: string;
    value: string;
    hint?: string;
    icon?: LucideIcon;
    tone?: 'default' | 'emerald' | 'amber' | 'sky' | 'violet';
}) {
    const toneClass = {
        default: 'text-foreground',
        emerald: 'text-emerald-600 dark:text-emerald-400',
        amber: 'text-amber-600 dark:text-amber-400',
        sky: 'text-sky-600 dark:text-sky-400',
        violet: 'text-violet-600 dark:text-violet-400',
    }[tone];

    return (
        <div className="border-t border-foreground/20 pt-3">
            <div className="flex items-center justify-between gap-2">
                <p className="meta-label">{label}</p>
                {Icon && <Icon className="size-3.5 text-muted-foreground" />}
            </div>
            <p
                className={cn(
                    'display-title mt-1.5 text-3xl tabular-nums sm:text-4xl',
                    toneClass,
                )}
            >
                {value}
            </p>
            {hint && (
                <p className="mt-1 text-[11px] leading-5 text-muted-foreground">
                    {hint}
                </p>
            )}
        </div>
    );
}

export function EmptyState({
    icon: Icon = Inbox,
    title,
    description,
    action,
    className,
}: {
    icon?: LucideIcon;
    title: string;
    description?: string;
    action?: ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'flex flex-col items-center justify-center gap-3 rounded-lg border border-dashed border-border/90 bg-card/60 px-6 py-14 text-center',
                className,
            )}
        >
            <span className="flex size-10 items-center justify-center rounded-sm border border-border/80 bg-muted/50 text-muted-foreground">
                <Icon className="size-4" />
            </span>
            <div className="space-y-1">
                <p className="text-sm font-semibold">{title}</p>
                {description && (
                    <p className="mx-auto max-w-md text-xs leading-5 text-muted-foreground">
                        {description}
                    </p>
                )}
            </div>
            {action}
        </div>
    );
}

export function ErrorState({
    title = 'Something went wrong',
    description,
    action,
}: {
    title?: string;
    description?: string;
    action?: ReactNode;
}) {
    return (
        <div className="flex flex-col items-center justify-center gap-3 rounded-lg border border-rose-600/25 bg-rose-600/5 px-6 py-14 text-center">
            <span className="flex size-10 items-center justify-center rounded-sm border border-rose-600/30 bg-rose-600/10 text-rose-700 dark:text-rose-400">
                <TriangleAlert className="size-4" />
            </span>
            <div className="space-y-1">
                <p className="text-sm font-semibold">{title}</p>
                {description && (
                    <p className="mx-auto max-w-md text-xs leading-5 text-muted-foreground">
                        {description}
                    </p>
                )}
            </div>
            {action}
        </div>
    );
}

export function PermissionState({
    title = 'You do not have access yet',
    description,
    action,
}: {
    title?: string;
    description?: string;
    action?: ReactNode;
}) {
    return (
        <div className="flex flex-col items-center justify-center gap-3 rounded-lg border border-amber-600/25 bg-amber-600/5 px-6 py-14 text-center">
            <span className="flex size-10 items-center justify-center rounded-sm border border-amber-600/30 bg-amber-600/10 text-amber-700 dark:text-amber-400">
                <Lock className="size-4" />
            </span>
            <div className="space-y-1">
                <p className="text-sm font-semibold">{title}</p>
                {description && (
                    <p className="mx-auto max-w-md text-xs leading-5 text-muted-foreground">
                        {description}
                    </p>
                )}
            </div>
            {action}
        </div>
    );
}

export function LoadingState({
    rows = 4,
    className,
}: {
    rows?: number;
    className?: string;
}) {
    return (
        <div
            className={cn('space-y-3 rounded-lg border border-border/90 bg-card p-5', className)}
            aria-busy="true"
            aria-label="Loading content"
        >
            <Skeleton className="h-4 w-1/3" />
            <Skeleton className="h-3 w-2/3" />
            {Array.from({ length: rows }).map((_, index) => (
                <Skeleton key={index} className="h-12 w-full" />
            ))}
        </div>
    );
}

export function CodeBlock({
    value,
    title,
    className,
    copyLabel = 'Copy',
    language,
}: {
    value: string;
    title?: string;
    className?: string;
    copyLabel?: string;
    language?: string;
}) {
    const [copiedText, copy] = useClipboard();
    const copied = copiedText === value && value !== '';

    return (
        <div
            className={cn(
                'overflow-hidden rounded-lg border border-slate-800 bg-slate-950',
                className,
            )}
        >
            {(title || language) && (
                <div className="flex items-center justify-between gap-3 border-b border-slate-800 bg-slate-900/70 px-3 py-2">
                    <span className="truncate font-mono text-[11px] font-medium tracking-wide text-slate-400 uppercase">
                        {title ?? language}
                    </span>
                </div>
            )}
            <div className="relative">
                <pre className="overflow-x-auto px-4 py-3 pr-16 font-mono text-[12.5px] leading-6 whitespace-pre-wrap text-slate-100">
                    <code>{value}</code>
                </pre>
                <button
                    type="button"
                    onClick={() => void copy(value)}
                    className="absolute top-2.5 right-2.5 inline-flex items-center gap-1 rounded-md border border-slate-700 bg-slate-900/90 px-2 py-1 font-mono text-[11px] font-medium text-slate-300 transition-colors hover:bg-slate-800 hover:text-white"
                    aria-label={copied ? 'Copied' : copyLabel}
                >
                    {copied ? (
                        <>
                            <Check className="size-3 text-emerald-400" />
                            <span className="text-emerald-400">Copied</span>
                        </>
                    ) : (
                        <>
                            <Copy className="size-3" />
                            <span>{copyLabel}</span>
                        </>
                    )}
                </button>
            </div>
        </div>
    );
}

export function Pill({
    children,
    tone = 'default',
    className,
}: {
    children: ReactNode;
    tone?: 'default' | 'emerald' | 'amber' | 'sky' | 'violet' | 'rose';
    className?: string;
}) {
    const tones = {
        default: 'border-border bg-muted/40 text-muted-foreground',
        emerald:
            'border-emerald-500/25 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
        amber: 'border-amber-500/25 bg-amber-500/10 text-amber-700 dark:text-amber-400',
        sky: 'border-sky-500/25 bg-sky-500/10 text-sky-700 dark:text-sky-400',
        violet: 'border-violet-500/25 bg-violet-500/10 text-violet-700 dark:text-violet-400',
        rose: 'border-rose-500/25 bg-rose-500/10 text-rose-700 dark:text-rose-400',
    } as const;

    return (
        <span
            className={cn(
                'inline-flex items-center gap-1 rounded-sm border px-2 py-0.5 font-mono text-[11px] font-medium',
                tones[tone],
                className,
            )}
        >
            {children}
        </span>
    );
}

export type SegmentedTab<T extends string> = {
    value: T;
    label: string;
    icon?: LucideIcon;
    count?: number;
    locked?: boolean;
};

export function SegmentedTabs<T extends string>({
    items,
    value,
    onChange,
    ariaLabel,
    className,
}: {
    items: Array<SegmentedTab<T>>;
    value: T;
    onChange: (value: T) => void;
    ariaLabel: string;
    className?: string;
}) {
    return (
        <div
            role="tablist"
            aria-label={ariaLabel}
            className={cn(
                'flex gap-1 overflow-x-auto border-b border-border/70',
                className,
            )}
        >
            {items.map((item) => {
                const Icon = item.icon;
                const active = item.value === value;

                return (
                    <button
                        key={item.value}
                        type="button"
                        role="tab"
                        aria-selected={active}
                        tabIndex={active ? 0 : -1}
                        onClick={() => onChange(item.value)}
                        className={cn(
                            'relative flex shrink-0 items-center gap-1.5 px-3 py-2.5 text-sm font-medium transition-colors',
                            active
                                ? 'text-foreground'
                                : 'text-muted-foreground hover:text-foreground',
                        )}
                    >
                        {Icon && <Icon className="size-4" />}
                        {item.label}
                        {typeof item.count === 'number' && (
                            <Badge
                                variant="secondary"
                                className="ml-0.5 px-1.5 text-[10px] tabular-nums"
                            >
                                {item.count}
                            </Badge>
                        )}
                        {item.locked && <Lock className="size-3 opacity-60" />}
                        <span
                            className={cn(
                                'absolute inset-x-2 -bottom-px h-0.5 rounded-full bg-primary transition-opacity',
                                active ? 'opacity-100' : 'opacity-0',
                            )}
                        />
                    </button>
                );
            })}
        </div>
    );
}

export function ImpactBadge({ value }: { value: number }) {
    return (
        <Badge
            variant="outline"
            className="border-emerald-500/30 bg-emerald-500/10 text-[10px] text-emerald-700 dark:text-emerald-400"
        >
            +{value} XP
        </Badge>
    );
}

export function IconButtonLabel({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return <span className={cn('sr-only', className)}>{children}</span>;
}

export function InlineAction({
    onClick,
    children,
    className,
}: {
    onClick: () => void;
    children: ReactNode;
    className?: string;
}) {
    return (
        <Button
            type="button"
            variant="ghost"
            size="sm"
            className={cn('h-7 px-2 text-xs', className)}
            onClick={onClick}
        >
            {children}
        </Button>
    );
}
