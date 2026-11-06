import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { Difficulty } from '@/types';

const difficultyStyles: Record<Difficulty, string> = {
    Easy: 'border-emerald-600/30 bg-emerald-600/10 text-emerald-800 dark:text-emerald-400',
    Medium: 'border-amber-600/30 bg-amber-600/10 text-amber-800 dark:text-amber-400',
    Hard: 'border-rose-600/30 bg-rose-600/10 text-rose-800 dark:text-rose-400',
};

export function DifficultyBadge({
    difficulty,
    className,
}: {
    difficulty: Difficulty;
    className?: string;
}) {
    return (
        <Badge
            variant="outline"
            className={cn(
                'rounded-sm font-mono text-[10px] font-medium tracking-wide uppercase',
                difficultyStyles[difficulty],
                className,
            )}
        >
            {difficulty}
        </Badge>
    );
}
