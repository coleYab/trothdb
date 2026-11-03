import { Info, Lightbulb, TriangleAlert } from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type CalloutVariant = 'info' | 'warning' | 'tip';

const calloutStyles: Record<
    CalloutVariant,
    {
        container: string;
        icon: string;
        title: string;
        Icon: typeof Info;
    }
> = {
    info: {
        container:
            'border-sky-500/25 border-l-sky-500 bg-sky-500/5 dark:border-sky-400/20 dark:border-l-sky-400 dark:bg-sky-400/5',
        icon: 'text-sky-600 dark:text-sky-400',
        title: 'text-sky-800 dark:text-sky-300',
        Icon: Info,
    },
    warning: {
        container:
            'border-amber-500/25 border-l-amber-500 bg-amber-500/5 dark:border-amber-400/20 dark:border-l-amber-400 dark:bg-amber-400/5',
        icon: 'text-amber-600 dark:text-amber-400',
        title: 'text-amber-800 dark:text-amber-300',
        Icon: TriangleAlert,
    },
    tip: {
        container:
            'border-emerald-500/25 border-l-emerald-500 bg-emerald-500/5 dark:border-emerald-400/20 dark:border-l-emerald-400 dark:bg-emerald-400/5',
        icon: 'text-emerald-600 dark:text-emerald-400',
        title: 'text-emerald-800 dark:text-emerald-300',
        Icon: Lightbulb,
    },
};

export function Callout({
    variant = 'info',
    title,
    children,
    className,
}: {
    variant?: CalloutVariant;
    title?: string;
    children: ReactNode;
    className?: string;
}) {
    const { container, icon, title: titleColor, Icon } = calloutStyles[variant];

    return (
        <div
            className={cn(
                'rounded-r-lg border border-l-4 px-4 py-3.5',
                container,
                className,
            )}
        >
            {title && (
                <p
                    className={cn(
                        'flex items-center gap-2 text-sm font-semibold',
                        titleColor,
                    )}
                >
                    <Icon className={cn('size-4 shrink-0', icon)} />
                    {title}
                </p>
            )}
            <div className={cn('text-sm leading-7', title && 'mt-1.5')}>
                {children}
            </div>
        </div>
    );
}
