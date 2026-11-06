import type { LucideIcon } from 'lucide-react';
import { BookOpen, FileText, MessageSquare, Trophy, Zap } from 'lucide-react';
import { useRef } from 'react';
import { cn } from '@/lib/utils';

export type ProblemTab =
    | 'description'
    | 'editorial'
    | 'submissions'
    | 'comments'
    | 'standings';

const tabs: Array<{
    value: ProblemTab;
    label: string;
    icon: LucideIcon;
}> = [
    { value: 'description', label: 'Description', icon: FileText },
    { value: 'submissions', label: 'Submissions', icon: Zap },
    { value: 'editorial', label: 'Editorial', icon: BookOpen },
    { value: 'comments', label: 'Comments', icon: MessageSquare },
    { value: 'standings', label: 'Standings', icon: Trophy },
];

export function ProblemTabBar({
    active,
    onChange,
    isSolved = false,
}: {
    active: ProblemTab;
    onChange: (tab: ProblemTab) => void;
    isSolved?: boolean;
}) {
    const buttonRefs = useRef<Array<HTMLButtonElement | null>>([]);
    const visibleTabs = tabs.filter(
        (tab) => tab.value !== 'editorial' || isSolved,
    );

    const handleKeyDown = (
        event: React.KeyboardEvent<HTMLButtonElement>,
        index: number,
    ) => {
        let nextIndex = index;

        if (event.key === 'ArrowRight') {
            nextIndex = (index + 1) % visibleTabs.length;
        } else if (event.key === 'ArrowLeft') {
            nextIndex = (index - 1 + visibleTabs.length) % visibleTabs.length;
        } else if (event.key === 'Home') {
            nextIndex = 0;
        } else if (event.key === 'End') {
            nextIndex = visibleTabs.length - 1;
        } else {
            return;
        }

        event.preventDefault();
        onChange(visibleTabs[nextIndex].value);
        buttonRefs.current[nextIndex]?.focus();
    };

    return (
        <nav
            aria-label="Problem sections"
            className="sticky top-0 z-10 -mx-4 border-b bg-background/95 px-4 backdrop-blur md:-mx-8 md:px-8"
        >
            <div role="tablist" className="flex gap-1 overflow-x-auto py-1">
                {visibleTabs.map((tab, index) => {
                    const Icon = tab.icon;
                    const isActive = active === tab.value;

                    return (
                        <button
                            key={tab.value}
                            ref={(element) => {
                                buttonRefs.current[index] = element;
                            }}
                            type="button"
                            role="tab"
                            aria-selected={isActive}
                            aria-controls={`problem-tab-${tab.value}`}
                            id={`problem-tab-trigger-${tab.value}`}
                            tabIndex={isActive ? 0 : -1}
                            onClick={() => onChange(tab.value)}
                            onKeyDown={(event) => handleKeyDown(event, index)}
                            className={cn(
                                'group relative flex shrink-0 items-center gap-2 rounded-md px-3 py-2.5 text-sm font-medium transition-colors',
                                isActive
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            )}
                        >
                            <Icon className="size-4" />
                            {tab.label}
                            <span
                                className={cn(
                                    'absolute inset-x-2 -bottom-1 h-0.5 rounded-full bg-primary transition-opacity',
                                    isActive ? 'opacity-100' : 'opacity-0',
                                )}
                            />
                        </button>
                    );
                })}
            </div>
        </nav>
    );
}
