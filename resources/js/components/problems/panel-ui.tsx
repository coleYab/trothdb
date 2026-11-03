import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

const toneClasses = {
    primary: 'bg-primary',
    sky: 'bg-sky-500',
    emerald: 'bg-emerald-500',
    amber: 'bg-amber-500',
    violet: 'bg-violet-500',
    rose: 'bg-rose-500',
} as const;

export type BarTone = keyof typeof toneClasses;

export type BarDatum = {
    label: string;
    value: string | number;
    percent: number;
    meta?: string;
};

export function PanelHeader({
    icon: Icon,
    title,
    description,
    badge,
}: {
    icon: LucideIcon;
    title: string;
    description: string;
    badge?: string;
}) {
    return (
        <div className="flex flex-wrap items-start justify-between gap-3">
            <div className="space-y-1">
                <h2 className="flex items-center gap-2 text-xl font-semibold tracking-tight">
                    <Icon className="size-5 text-muted-foreground" />
                    {title}
                </h2>
                <p className="max-w-2xl text-sm leading-7 text-muted-foreground">
                    {description}
                </p>
            </div>

            {badge && (
                <Badge
                    variant="outline"
                    className="border-amber-500/30 bg-amber-500/10 text-[11px] text-amber-700 dark:text-amber-400"
                >
                    {badge}
                </Badge>
            )}
        </div>
    );
}

export function StatCard({
    label,
    value,
    hint,
    accent = false,
}: {
    label: string;
    value: string;
    hint?: string;
    accent?: boolean;
}) {
    return (
        <div
            className={cn(
                'rounded-xl border bg-card p-4',
                accent && 'border-emerald-500/25 bg-emerald-500/5',
            )}
        >
            <p className="text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                {label}
            </p>
            <p className="mt-1.5 text-xl font-semibold tabular-nums">{value}</p>
            {hint && (
                <p className="mt-1 text-xs text-muted-foreground">{hint}</p>
            )}
        </div>
    );
}

export function ChartCard({
    title,
    description,
    children,
    className,
}: {
    title: string;
    description?: string;
    children: ReactNode;
    className?: string;
}) {
    return (
        <section className={cn('rounded-xl border bg-card p-4', className)}>
            <h3 className="text-sm font-semibold">{title}</h3>
            {description && (
                <p className="mt-0.5 text-xs text-muted-foreground">
                    {description}
                </p>
            )}
            <div className="mt-4">{children}</div>
        </section>
    );
}

export function BarChart({
    data,
    tone = 'primary',
}: {
    data: BarDatum[];
    tone?: BarTone;
}) {
    return (
        <div className="space-y-3">
            {data.map((item) => (
                <div key={item.label} className="space-y-1.5">
                    <div className="flex items-baseline justify-between gap-3 text-xs">
                        <span className="truncate font-medium">
                            {item.label}
                        </span>
                        <span className="shrink-0 text-right text-muted-foreground tabular-nums">
                            {item.value}
                            {item.meta ? ` · ${item.meta}` : ''}
                        </span>
                    </div>
                    <div className="h-2 overflow-hidden rounded-full bg-muted">
                        <div
                            className={cn(
                                'h-full rounded-full transition-[width] duration-300',
                                toneClasses[tone],
                            )}
                            style={{
                                width: `${Math.max(2, Math.min(100, item.percent))}%`,
                            }}
                        />
                    </div>
                </div>
            ))}
        </div>
    );
}
