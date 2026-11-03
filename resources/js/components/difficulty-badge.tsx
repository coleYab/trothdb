import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { Difficulty } from '@/types';

const difficultyStyles: Record<Difficulty, string> = {
    Easy: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
    Medium: 'border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-400',
    Hard: 'border-rose-500/30 bg-rose-500/10 text-rose-700 dark:text-rose-400',
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
                'font-medium',
                difficultyStyles[difficulty],
                className,
            )}
        >
            {difficulty}
        </Badge>
    );
}
