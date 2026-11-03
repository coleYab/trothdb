import { Moon, Sun } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';

export function ThemeToggle() {
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        setMounted(true);
    }, []);

    const isDark = resolvedAppearance === 'dark';
    const label = mounted
        ? `Switch to ${isDark ? 'light' : 'dark'} mode`
        : 'Toggle color theme';

    return (
        <Button
            type="button"
            variant="ghost"
            size="icon"
            className="size-8"
            onClick={() => updateAppearance(isDark ? 'light' : 'dark')}
            aria-label={label}
            title={label}
        >
            <Sun className="hidden size-4 dark:block" />
            <Moon className="size-4 dark:hidden" />
            <span className="sr-only">Toggle theme</span>
        </Button>
    );
}
